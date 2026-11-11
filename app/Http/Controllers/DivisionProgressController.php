<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStationProgress;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DivisionProgressController extends Controller
{
    // Rantai urutan estafet mutlak
    private array $stageFlow = ['kaprod', 'bahan', 'print', 'jahit', 'finishing', 'qc', 'selesai'];

    private array $divisionConfigs = [
        'kaprod' => [
            'name'       => 'KEPALA PRODUKSI',
            'sub'        => 'Verifikasi Spek, Kain, File & ACC Naik Produksi',
            'badge_bg'   => 'bg-blue-600',
            'prev_label' => 'Admin OK',
            'next_label' => 'Ka Prod OK',
            'sop_title'  => 'SOP Ka Prod:',
            'sop_text'   => 'Cek kesesuaian order SPK & stok kain • Validasi file cetak dan deadline • ACC pesanan untuk masuk meja potong Bahan.',
        ],
        'bahan' => [
            'name'       => 'DIVISI BAHAN',
            'sub'        => 'Pemotongan & Persiapan Lembar Kain Sesuai Pola',
            'badge_bg'   => 'bg-cyan-600',
            'prev_label' => 'Ka Prod OK',
            'next_label' => 'Bahan OK',
            'sop_title'  => 'SOP Divisi Bahan:',
            'sop_text'   => 'Cek kesesuaian jenis & warna roll kain • Ukur pola sesuai size breakdown pesanan • Pastikan potongan presisi sebelum diserah-terimakan ke Printing.',
        ],
        'print' => [
            'name'       => 'DIVISI PRINT',
            'sub'        => 'Pencetakan Kertas Sublim & Pengepresan Roll Kain',
            'badge_bg'   => 'bg-amber-600',
            'prev_label' => 'Bahan OK',
            'next_label' => 'Print OK',
            'sop_title'  => 'SOP Divisi Print:',
            'sop_text'   => 'Cek kesesuaian file cetak & nomor punggung • Pastikan suhu mesin press stabil • Hindari noda tinta atau kain melar sebelum ke Jahit.',
        ],
        'jahit' => [
            'name'       => 'DIVISI JAHIT',
            'sub'        => 'Assembling Badan, Lengan, Kerah & Overdeck',
            'badge_bg'   => 'bg-sky-600',
            'prev_label' => 'Print OK',
            'next_label' => 'Jahit OK',
            'sop_title'  => 'SOP Divisi Jahit:',
            'sop_text'   => 'Gunakan benang senada dengan kain • Periksa kerapian bisban leher dan overdeck bawah • Samakan jumlah potongan per size sebelum diserahkan ke Finishing.',
        ],
        'finishing' => [
            'name'       => 'DIVISI FINISHING',
            'sub'        => 'Setrika Uap, Tagging & Pemasangan Label Size',
            'badge_bg'   => 'bg-lime-600',
            'prev_label' => 'Jahit OK',
            'next_label' => 'Finishing OK',
            'sop_title'  => 'SOP Divisi Finishing:',
            'sop_text'   => 'Setrika uap hingga lipatan rapi • Pasang label size kerah & hangtag • Serahkan ke QC untuk pemeriksaan akhir.',
        ],
        'qc' => [
            'name'       => 'DIVISI QC',
            'sub'        => 'Verifikasi Kualitas Pesanan & Packing Plastik OPP Siap Kirim',
            'badge_bg'   => 'bg-emerald-600',
            'prev_label' => 'Finishing OK',
            'next_label' => 'QC OK',
            'sop_title'  => 'SOP Divisi QC:',
            'sop_text'   => 'Standar Pemeriksaan: Kerapian jahitan leher & kerah • Kesesuaian size breakdown per OPP • Ketajaman warna cetak & noda press • Lolos QC langsung masuk antrean Siap Diambil.',
        ],
    ];

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role ?? 'admin';

        // Tentukan divisi yang dilihat
        $activeDivision = in_array($userRole, ['kaprod', 'bahan', 'print', 'jahit', 'finishing', 'qc'])
            ? $userRole
            : ($request->query('div') ?? 'bahan');

        $config = $this->divisionConfigs[$activeDivision] ?? $this->divisionConfigs['bahan'];

        $orders = Order::with(['customer', 'stationProgress'])
            ->where('cabang_id', $user->cabang_id ?? 1)
            ->where('current_status', '!=', 'selesai')
            ->orderBy('target_deadline', 'asc')
            ->get();

        return view('divisions.index', compact('orders', 'activeDivision', 'config', 'user'));
    }

    // Toggle Centang Status / Rollback
    public function toggle(Request $request, Order $order)
    {
        $user = auth()->user();
        $division = $request->input('division');
        $isCleared = $request->boolean('is_cleared');

        $flow = $this->stageFlow; // ['kaprod', 'bahan', 'print', 'jahit', 'finishing', 'qc', 'selesai']
        $divIndex = array_search($division, $flow);
        $currentIndex = array_search($order->current_status, $flow);

        if ($divIndex === false || $currentIndex === false) {
            return response()->json(['status' => 'error', 'message' => 'Tahap divisi tidak valid.'], 422);
        }

        // ================= VALIDASI MAJU (CENTANG) =================
        if ($isCleared) {
            // Syarat mutlak: Order harus berada persis di divisi ini
            if ($currentIndex < $divIndex) {
                $prevDiv = $flow[$divIndex - 1];
                return response()->json([
                    'status'  => 'error',
                    'message' => "Tidak bisa dicentang! Tahap sebelumnya ({$prevDiv}) belum selesai.",
                ], 422);
            }

            if ($currentIndex > $divIndex) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Pesanan ini sudah melewati tahap {$division}.",
                ], 422);
            }

            $nextStatus = $flow[$divIndex + 1];

            DB::transaction(function () use ($order, $division, $nextStatus, $user) {
                // Update station progress jika tabel fisik mencatat divisi ini
                OrderStationProgress::where('order_id', $order->id)
                    ->where('division', $division)
                    ->update([
                        'is_cleared'   => 1,
                        'finished_at'  => now(),
                        'user_id'      => $user->id,
                    ]);

                $order->update(['current_status' => $nextStatus]);

                OrderStatusLog::create([
                    'order_id'    => $order->id,
                    'cabang_id'   => $order->cabang_id,
                    'from_status' => $division,
                    'to_status'   => $nextStatus,
                    'user_id'     => $user->id,
                    'action_type' => 'forward',
                    'note'        => "Divisi {$division} selesai dicentang oleh {$user->name}",
                ]);
            });

            return response()->json([
                'status'  => 'success',
                'message' => "Status {$division} selesai! Diteruskan ke {$nextStatus}.",
            ]);
        }

        // ================= VALIDASI MUNDUR (BATAL / UNCHECK) =================
        else {
            // Syarat mutlak rollback: Divisi setelahnya belum memajukan status
            // Artinya, status order saat ini harus tepat berada di posisi tepat setelah divisi ini
            $expectedCurrentForRollback = $flow[$divIndex + 1];

            if ($order->current_status !== $expectedCurrentForRollback) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Tidak bisa dibatalkan! Divisi berikutnya sudah memproses order ini.",
                ], 422);
            }

            $rollbackStatus = $division;

            DB::transaction(function () use ($order, $division, $rollbackStatus, $user) {
                OrderStationProgress::where('order_id', $order->id)
                    ->where('division', $division)
                    ->update([
                        'is_cleared'   => 0,
                        'finished_at'  => null,
                        'user_id'      => $user->id,
                    ]);

                $order->update(['current_status' => $rollbackStatus]);

                OrderStatusLog::create([
                    'order_id'    => $order->id,
                    'cabang_id'   => $order->cabang_id,
                    'from_status' => $order->current_status,
                    'to_status'   => $rollbackStatus,
                    'user_id'     => $user->id,
                    'action_type' => 'rollback',
                    'note'        => "Status {$division} dibatalkan (uncheck) oleh {$user->name}",
                ]);
            });

            return response()->json([
                'status'  => 'success',
                'message' => "Status {$division} dibatalkan, kembali menunggu antrean.",
            ]);
        }
    }

    public function updateProgress(Request $request, Order $order)
    {
        $validated = $request->validate([
            'division'      => 'required|string',
            'completed_qty' => 'nullable|integer|min:0',
            'issue_note'    => 'nullable|string',
        ]);

        $user = auth()->user();

        OrderStationProgress::where('order_id', $order->id)
            ->where('division', $validated['division'])
            ->update([
                'completed_qty' => $validated['completed_qty'],
                'issue_note'    => $validated['issue_note'],
                'user_id'       => $user->id,
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Catatan & progres berhasil disimpan!',
        ]);
    }

    

    /**
     * Finalisasi serah terima pesanan ke customer (Status Final: selesai).
     */
    public function toggleComplete(Request $request, Order $order)
    {
        $user = auth()->user();
        $isComplete = $request->boolean('is_complete');

        // Validasi: Status hanya bisa diselesaikan jika sudah lolos QC
        if ($isComplete && $order->current_status !== 'qc') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan belum selesai di tahap QC!',
            ], 422);
        }

        DB::transaction(function () use ($order, $isComplete, $user) {
            $fromStatus = $order->current_status;
            $toStatus = $isComplete ? 'selesai' : 'qc';

            $order->update([
                'current_status' => $toStatus,
            ]);

            OrderStatusLog::create([
                'order_id'    => $order->id,
                'cabang_id'   => $order->cabang_id,
                'from_status' => $fromStatus,
                'to_status'   => $toStatus,
                'user_id'     => $user->id,
                'action_type' => $isComplete ? 'forward' : 'rollback',
                'note'        => $isComplete 
                    ? "Pesanan telah diserahkan ke pemesan & ditutup (Closed) oleh Admin {$user->name}" 
                    : "Status selesai dibatalkan kembali ke QC oleh Admin {$user->name}",
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'message' => $isComplete 
                ? 'Pesanan berhasil diserahkan dan ditutup (Selesai)!' 
                : 'Status pesanan dikembalikan ke antrean QC.',
        ]);
    }
}
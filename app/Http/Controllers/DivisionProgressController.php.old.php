<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStationProgress;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionProgressController extends Controller
{
    // Konfigurasi Divisi & Teks SOP Bawah
    private array $divisionConfigs = [
        'bahan' => [
            'name'       => 'DIVISI BAHAN',
            'sub'        => 'Pemotongan & Persiapan Lembar Kain Sesuai Pola',
            'badge_bg'   => 'bg-cyan-600',
            'prev_label' => 'Ka Prod OK',
            'next_label' => 'Bahan Beres',
            'next_step'  => 'proses_print',
            'sop_title'  => 'SOP Divisi Bahan:',
            'sop_text'   => 'Cek kesesuaian jenis & warna roll kain • Ukur pola sesuai size breakdown pesanan • Pastikan potongan presisi sebelum diserah-terimakan ke Printing.',
        ],
        'print' => [
            'name'       => 'DIVISI PRINT',
            'sub'        => 'Pencetakan Kertas Sublim & Pengepresan Roll Kain',
            'badge_bg'   => 'bg-amber-600',
            'prev_label' => 'Bahan OK',
            'next_label' => 'Print Beres',
            'next_step'  => 'proses_jahit',
            'sop_title'  => 'SOP Divisi Print:',
            'sop_text'   => 'Cek kesesuaian file cetak & nomor punggung • Pastikan suhu mesin press stabil 200°C • Hindari noda tinta gosong atau kain melar sebelum ke Jahit.',
        ],
        'jahit' => [
            'name'       => 'DIVISI JAHIT',
            'sub'        => 'Assembling Badan, Lengan, Kerah & Overdeck',
            'badge_bg'   => 'bg-sky-600',
            'prev_label' => 'Print OK',
            'next_label' => 'Jahit Beres',
            'next_step'  => 'proses_qc',
            'sop_title'  => 'SOP Divisi Jahit:',
            'sop_text'   => 'Gunakan benang senada dengan kain • Periksa kerapian bisban leher dan overdeck bawah • Samakan jumlah potongan per size sebelum diserahkan ke QC.',
        ],
        'qc' => [
            'name'       => 'DIVISI QC',
            'sub'        => 'Verifikasi Kualitas Pesanan Siap Kirim',
            'badge_bg'   => 'bg-emerald-600',
            'prev_label' => 'Jahit OK',
            'next_label' => 'Lolos QC',
            'next_step'  => 'proses_finishing',
            'sop_title'  => 'SOP Divisi QC:',
            'sop_text'   => 'Standar Pemeriksaan: Kerapian jahitan leher & kerah • Kesesuaian jumlah size breakdown per OPP • Ketajaman warna cetak & noda press • Lolos QC langsung masuk Finishing.',
        ],
        'finishing' => [
            'name'       => 'DIVISI FINISHING',
            'sub'        => 'Setrika Uap, Tagging & Packing Plastik OPP',
            'badge_bg'   => 'bg-lime-600',
            'prev_label' => 'QC OK',
            'next_label' => 'Packing Beres',
            'next_step'  => 'siap_ambil',
            'sop_title'  => 'SOP Divisi Finishing:',
            'sop_text'   => 'Setrika uap hingga lipatan rapi • Beri stiker size pada plastik OPP bening • Masukkan ke kardus/packing utama untuk diserahkan ke Admin (Siap Diambil).',
        ],
    ];

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role ?? 'admin';

        // Tentukan divisi aktif: Jika operator divisi, kunci ke role-nya. Jika admin/superadmin, bisa pilih lewat query ?div=
        $activeDivision = in_array($userRole, ['bahan', 'print', 'jahit', 'qc', 'finishing'])
            ? $userRole
            : ($request->query('div') ?? 'qc');

        $config = $this->divisionConfigs[$activeDivision] ?? $this->divisionConfigs['qc'];

        // Ambil order yang aktif di cabang user dan belum ditutup (closed)
        $orders = Order::with(['customer', 'stationProgress'])
            ->where('cabang_id', $user->cabang_id)
            ->where('current_status', '!=', 'closed')
            ->orderBy('target_deadline', 'asc')
            ->get();

        return view('divisions.index', compact('orders', 'activeDivision', 'config', 'user'));
    }

    // Toggle Checkbox Centang Selesai / Rollback
    public function toggle(Request $request, Order $order)
    {
        $user = auth()->user();
        $division = $request->input('division');
        $isCleared = $request->boolean('is_cleared');

        $flow = ['bahan', 'print', 'jahit', 'qc', 'finishing'];
        $idx = array_search($division, $flow);

        if ($idx === false) {
            return response()->json(['status' => 'error', 'message' => 'Divisi tidak valid'], 422);
        }

        DB::transaction(function () use ($order, $division, $isCleared, $user, $flow, $idx) {
            // 1. Simpan ke order_station_progress
            OrderStationProgress::where('order_id', $order->id)
                ->where('division', $division)
                ->update([
                    'is_cleared'   => $isCleared ? 1 : 0,
                    'finished_at'  => $isCleared ? now() : null,
                    'user_id'      => $user->id,
                ]);

            // 2. Update status header Order & Catat Log
            if ($isCleared) {
                // Tentukan status berikutnya
                $nextStatus = ($idx === count($flow) - 1) ? 'siap_ambil' : 'proses_' . $flow[$idx + 1];
                $fromStatus = $order->current_status;

                $order->update(['current_status' => $nextStatus]);

                OrderStatusLog::create([
                    'order_id'    => $order->id,
                    'cabang_id'   => $order->cabang_id,
                    'from_status' => $fromStatus,
                    'to_status'   => $nextStatus,
                    'user_id'     => $user->id,
                    'action_type' => 'forward',
                    'note'        => "Divisi {$division} dinyatakan selesai oleh {$user->name}",
                ]);
            } else {
                // Rollback status
                $rollbackStatus = 'proses_' . $division;
                $fromStatus = $order->current_status;

                $order->update(['current_status' => $rollbackStatus]);

                OrderStatusLog::create([
                    'order_id'    => $order->id,
                    'cabang_id'   => $order->cabang_id,
                    'from_status' => $fromStatus,
                    'to_status'   => $rollbackStatus,
                    'user_id'     => $user->id,
                    'action_type' => 'rollback',
                    'note'        => "Status divisi {$division} dibatalkan (rollback) oleh {$user->name}",
                ]);
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => $isCleared ? "Status berhasil diupdate, {$division} selesai!" : "Status dibatalkan, {$division} belum selesai!",
        ]);
    }

    // Modal Edit Qty Selesai & Catatan Kendala
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
}
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $cabangId = $user->cabang_id ?? 1;

        // Ambil order aktif, urutkan dari deadline terdekat
        $orders = Order::with(['customer', 'stationProgress'])
            ->where('cabang_id', $cabangId)
            ->orderByRaw("CASE WHEN current_status = 'selesai' THEN 1 ELSE 0 END ASC")
            ->orderBy('target_deadline', 'asc')
            ->get()
            ->map(function ($order) {
                // Tentukan step_active (1 s/d 7)
                // 1: Admin, 2: Ka Prod, 3: Bahan, 4: Print, 5: Jahit, 6: Finishing, 7: QC / Selesai
                $stepMap = [
                    'kaprod'    => 2,
                    'bahan'     => 3,
                    'print'     => 4,
                    'jahit'     => 5,
                    'finishing' => 6,
                    'qc'        => 7,
                    'selesai'   => 7,
                ];
                $stepActive = $stepMap[$order->current_status] ?? 2;

                $colorMap = [
                    'kaprod'    => '#5170ff',
                    'bahan'     => '#0cc0df',
                    'print'     => '#ff914d',
                    'jahit'     => '#5ce7ff',
                    'finishing' => '#74cc00',
                    'qc'        => '#00bf63',
                    'selesai'   => '#00bf63',
                ];
                $divColor = $colorMap[$order->current_status] ?? '#6366f1';

                $currentJobMap = [
                    'kaprod'    => 'Review kesiapan spek kain, deadline & ACC produksi naik meja potong.',
                    'bahan'     => 'Penggelaran kain dan pemotongan pola badan sesuai size breakdown.',
                    'print'     => 'Proses transfer cetak kertas sublim & pengepresan roll kain jersey.',
                    'jahit'     => 'Proses assembling badan, jahit obras, kerah rib & overdeck lengan.',
                    'finishing' => 'Pemasangan label size leher & proses setrika uap sebelum QC.',
                    'qc'        => 'Pemeriksaan detail jahitan, ketajaman cetak, dan kelengkapan setelan.',
                    'selesai'   => 'Pesanan telah selesai dipacking rapi dan siap diserahkan ke pelanggan.',
                ];

                $activeProgress = $order->stationProgress->firstWhere('division', $order->current_status);
                $completedQty = $activeProgress?->completed_qty ?? 0;
                $isClosed = ($order->current_status === 'selesai');

                $percent = $isClosed 
                    ? 100 
                    : ($order->qty > 0 ? round(($stepActive / 7) * 100) : 10);

                // Deadline urgent check (hari ini atau terlewat)
                $deadlineDate = \Carbon\Carbon::parse($order->target_deadline);
                $isUrgent = !$isClosed && ($deadlineDate->isPast() || $deadlineDate->isToday());

                return [
                    'id'                => $order->id,
                    'so'                => $order->no_so,
                    'customer'          => $order->customer->name ?? $order->customer_name,
                    'type'              => $order->order_type ?? 'Atasan Fullprint',
                    'fabric'            => $order->fabric_type ?? '-',
                    'order_in'          => $order->created_at->translatedFormat('d F Y'),
                    'order_out'         => $deadlineDate->translatedFormat('d M Y'),
                    'deadline_note'     => $isClosed ? 'Sudah Selesai' : $deadlineDate->diffForHumans(),
                    'is_urgent'         => $isUrgent,
                    'is_closed'         => $isClosed,
                    'total_qty'         => $order->qty,
                    'description'       => $order->notes ?? 'Tidak ada catatan khusus.',
                    'current_div'       => strtoupper($order->status_info['label'] ?? $order->current_status),
                    'current_div_color' => $divColor,
                    'current_job'       => $currentJobMap[$order->current_status] ?? 'Sedang dalam antrean produksi.',
                    'progress_pcs'      => "{$completedQty} / {$order->qty} pcs",
                    'progress_percent'  => $percent,
                    'spk_image'         => $order->spk_photo_path ? asset('storage/' . $order->spk_photo_path) : null,
                    'step_active'       => $stepActive,
                ];
            });

        return view('dashboard.monitoring', compact('orders'));
    }

    public function summary()
    {
        $user = auth()->user();
        $cabangId = $user->cabang_id ?? 1;

        // Total order masuk bulan berjalan
        $totalBulanIni = Order::where('cabang_id', $cabangId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Sedang aktif di stasiun produksi (kaprod s/d qc)
        $sedangProses = Order::where('cabang_id', $cabangId)
            ->whereIn('current_status', ['kaprod', 'bahan', 'print', 'jahit', 'finishing'])
            ->count();

        // Deadline hari ini / mendesak yang belum selesai
        $deadlineHariIni = Order::where('cabang_id', $cabangId)
            ->where('current_status', '!=', 'selesai')
            ->whereDate('target_deadline', '<=', today())
            ->count();

        // Siap Diambil / Kirim (sudah lolos QC tapi belum ditutup selesai oleh admin)
        $siapKirim = Order::where('cabang_id', $cabangId)
            ->where('current_status', 'qc')
            ->count();

        // Selesai (Closed)
        $totalSelesai = Order::where('cabang_id', $cabangId)
            ->where('current_status', 'selesai')
            ->count();

        // Breakdown antrean per stasiun kerja
        $perStasiun = [
            'kaprod'    => Order::where('cabang_id', $cabangId)->where('current_status', 'kaprod')->count(),
            'bahan'     => Order::where('cabang_id', $cabangId)->where('current_status', 'bahan')->count(),
            'print'     => Order::where('cabang_id', $cabangId)->where('current_status', 'print')->count(),
            'jahit'     => Order::where('cabang_id', $cabangId)->where('current_status', 'jahit')->count(),
            'finishing' => Order::where('cabang_id', $cabangId)->where('current_status', 'finishing')->count(),
            'qc'        => $siapKirim,
        ];

        // 5 Order paling mendesak untuk ditampilkan di tabel ringkasan
        $urgentOrders = Order::with('customer')
            ->where('cabang_id', $cabangId)
            ->where('current_status', '!=', 'selesai')
            ->orderBy('target_deadline', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBulanIni',
            'sedangProses',
            'deadlineHariIni',
            'siapKirim',
            'totalSelesai',
            'perStasiun',
            'urgentOrders'
        ));
    }
    
    public function summary_old()
    {
        // Hitung metrik sederhana
        $totalBulanIni = Order::whereMonth('created_at', now()->month)->count();
        $sedangProses = Order::whereNotIn('current_status', ['draft_admin', 'closed'])->count();
        $deadlineHariIni = Order::whereDate('target_deadline', today())->count();
        $siapKirim = Order::where('current_status', 'proses_finishing')->count();

        return view('dashboard', compact(
            'totalBulanIni',
            'sedangProses',
            'deadlineHariIni',
            'siapKirim'
        ));
    }
}
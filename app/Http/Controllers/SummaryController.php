<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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
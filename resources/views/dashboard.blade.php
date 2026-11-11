@extends('layouts.app', ['title' => 'ONION CREATIVE - Dashboard Eksekutif'])

@section('content')
<div class="max-w-7xl w-full mx-auto space-y-6">

    <!-- Top Action & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-black tracking-tight">Executive Dashboard</h1>
            <p class="text-xs font-bold text-slate-500">Statistik performa lini produksi konveksi & cetak kain</p>
        </div>

        <a href="{{ route('monitoring') }}" class="px-4 py-2.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-xs flex items-center gap-2 cursor-pointer transition-all self-start sm:self-auto">
            <i class="ph-bold ph-monitor text-base text-emerald-400"></i>
            <span>Buka Monitoring</span>
        </a>
    </div>

    <!-- 4 Metric Cards Dinamis -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Bulan Ini -->
        <div class="bg-white p-5 rounded-3xl border border-slate-300 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider">TOTAL ORDER BULAN INI</span>
            <div class="my-2">
                <span class="text-3xl font-black text-black">{{ $totalBulanIni }}</span>
                <span class="text-xs font-bold text-slate-500 ml-1">SPK</span>
            </div>
            <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1">
                <i class="ph-bold ph-calendar text-xs text-indigo-600"></i> Periode {{ now()->translatedFormat('F Y') }}
            </span>
        </div>

        <!-- 2. Sedang Proses -->
        <div class="bg-white p-5 rounded-3xl border border-slate-300 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider">SEDANG PROSES PRODUKSI</span>
            <div class="my-2">
                <span class="text-3xl font-black text-amber-500">{{ $sedangProses }}</span>
                <span class="text-xs font-bold text-slate-500 ml-1">SPK</span>
            </div>
            <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1">
                <i class="ph-bold ph-arrows-clockwise text-xs text-amber-500"></i> Di 5 Divisi kerja
            </span>
        </div>

        <!-- 3. Deadline Urgent -->
        <div class="bg-white p-5 rounded-3xl border border-slate-300 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider">DEADLINE MENDESAK</span>
            <div class="my-2">
                <span class="text-3xl font-black {{ $deadlineHariIni > 0 ? 'text-rose-600' : 'text-slate-700' }}">{{ $deadlineHariIni }}</span>
                <span class="text-xs font-bold text-slate-500 ml-1">SPK</span>
            </div>
            <span class="text-[11px] {{ $deadlineHariIni > 0 ? 'text-rose-600 font-black' : 'text-slate-400 font-bold' }} flex items-center gap-1">
                <i class="ph-bold ph-warning-circle text-xs"></i> 
                {{ $deadlineHariIni > 0 ? 'Hari ini / terlewat target' : 'Semua aman terkendali' }}
            </span>
        </div>

        <!-- 4. Siap Diambil / QC OK -->
        <div class="bg-white p-5 rounded-3xl border border-slate-300 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-black uppercase text-slate-400 tracking-wider">SIAP DIAMBIL (QC OK)</span>
            <div class="my-2">
                <span class="text-3xl font-black text-emerald-600">{{ $siapKirim }}</span>
                <span class="text-xs font-bold text-slate-500 ml-1">SPK</span>
            </div>
            <span class="text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                <i class="ph-bold ph-check-circle text-xs text-emerald-500"></i> Menunggu serah terima
            </span>
        </div>
    </div>

    <!-- Pipeline Antrean Antar Divisi -->
    <div class="bg-white rounded-3xl border border-slate-300 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-black text-black uppercase tracking-wider">Beban Antrean Per Divisi</h3>
                <p class="text-xs font-bold text-slate-400">Distribusi fisik pesanan yang sedang dikerjakan saat ini</p>
            </div>
            <span class="text-xs font-black text-slate-600">Total Selesai: <strong class="text-black">{{ $totalSelesai }} SPK</strong></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="p-3 bg-blue-50/60 border border-blue-200 rounded-2xl text-center">
                <span class="text-[10px] font-black text-blue-700 uppercase block">1. Ka Prod</span>
                <span class="text-xl font-black text-blue-900">{{ $perStasiun['kaprod'] }}</span>
                <span class="text-[10px] text-slate-500 font-bold block">SPK</span>
            </div>
            <div class="p-3 bg-cyan-50/60 border border-cyan-200 rounded-2xl text-center">
                <span class="text-[10px] font-black text-cyan-700 uppercase block">2. Bahan</span>
                <span class="text-xl font-black text-cyan-900">{{ $perStasiun['bahan'] }}</span>
                <span class="text-[10px] text-slate-500 font-bold block">SPK</span>
            </div>
            <div class="p-3 bg-amber-50/60 border border-amber-200 rounded-2xl text-center">
                <span class="text-[10px] font-black text-amber-700 uppercase block">3. Print</span>
                <span class="text-xl font-black text-amber-900">{{ $perStasiun['print'] }}</span>
                <span class="text-[10px] text-slate-500 font-bold block">SPK</span>
            </div>
            <div class="p-3 bg-sky-50/60 border border-sky-200 rounded-2xl text-center">
                <span class="text-[10px] font-black text-sky-700 uppercase block">4. Jahit</span>
                <span class="text-xl font-black text-sky-900">{{ $perStasiun['jahit'] }}</span>
                <span class="text-[10px] text-slate-500 font-bold block">SPK</span>
            </div>
            <div class="p-3 bg-lime-50/60 border border-lime-200 rounded-2xl text-center">
                <span class="text-[10px] font-black text-lime-700 uppercase block">5. Finishing</span>
                <span class="text-xl font-black text-lime-900">{{ $perStasiun['finishing'] }}</span>
                <span class="text-[10px] text-slate-500 font-bold block">SPK</span>
            </div>
            <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-2xl text-center">
                <span class="text-[10px] font-black text-emerald-700 uppercase block">6. Lolos QC</span>
                <span class="text-xl font-black text-emerald-900">{{ $perStasiun['qc'] }}</span>
                <span class="text-[10px] text-slate-500 font-bold block">SPK</span>
            </div>
        </div>
    </div>

    <!-- Tabel 5 Antrean Paling Mendesak -->
    <div class="bg-white rounded-3xl border border-slate-300 shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-black">Antrean Prioritas Deadline Terdekat</h3>
                <p class="text-xs font-bold text-slate-400">Pesanan aktif yang mendekati target order out</p>
            </div>
            <a href="{{ route('monitoring') }}" class="text-xs font-black text-indigo-600 hover:text-black">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-black uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">NO. SO & CUSTOMER</th>
                        <th class="py-3 px-4">EVENT / JUDUL</th>
                        <th class="py-3 px-4">QTY</th>
                        <th class="py-3 px-4">DEADLINE</th>
                        <th class="py-3 px-4 text-center">POSISI DIVISI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($urgentOrders as $item)
                    @php
                        $isLate = \Carbon\Carbon::parse($item->target_deadline)->isPast();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4">
                            <p class="font-black text-black text-sm">{{ $item->customer->name ?? $item->customer_name }}</p>
                            <span class="font-mono text-slate-500 font-bold">{{ $item->no_so }}</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-700">
                            {{ $item->order_title ?? '-' }}
                        </td>
                        <td class="py-3 px-4 font-black text-indigo-700">
                            {{ $item->qty }} {{ strtoupper($item->satuan) }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-black {{ $isLate ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ \Carbon\Carbon::parse($item->target_deadline)->translatedFormat('d M Y') }}
                            </span>
                            <span class="block text-[10px] font-bold {{ $isLate ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ \Carbon\Carbon::parse($item->target_deadline)->diffForHumans() }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                @if($item->current_status === 'kaprod') bg-blue-100 text-blue-800 border border-blue-200
                                @elseif($item->current_status === 'bahan') bg-cyan-100 text-cyan-800 border border-cyan-200
                                @elseif($item->current_status === 'print') bg-amber-100 text-amber-800 border border-amber-200
                                @elseif($item->current_status === 'jahit') bg-sky-100 text-sky-800 border border-sky-200
                                @elseif($item->current_status === 'finishing') bg-lime-100 text-lime-800 border border-lime-200
                                @elseif($item->current_status === 'qc') bg-emerald-100 text-emerald-800 border border-emerald-200
                                @else bg-slate-100 text-slate-700 border border-slate-300 @endif">
                                {{ strtoupper($item->current_status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400 font-bold">
                            Tidak ada pesanan antrean aktif.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
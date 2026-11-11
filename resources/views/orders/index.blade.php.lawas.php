@extends('layouts.app', ['title' => 'ONION CREATIVE - Daftar Sales Order'])

@section('content')
<div class="max-w-7xl w-full mx-auto space-y-5">
    
    <!-- Header Halaman -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-black tracking-tight">Daftar Sales Order</h1>
            <p class="text-xs font-bold text-slate-500">Seluruh riwayat dan antrean Sales Order (SO) yang terdaftar</p>
        </div>
        
        <!-- Tombol Input SO Baru -->
        <a href="{{ route('orders.create') }}" class="px-4 py-2.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black flex items-center gap-2 shadow-xs transition-all cursor-pointer">
            <i class="ph-bold ph-plus text-base text-emerald-400"></i>
            <span>Input SO Baru</span>
        </a>
    </div>

    <!-- Tabel Daftar SO -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead class="bg-slate-100 border-b border-slate-300 text-black text-xs font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-3 text-center w-12">No</th>
                    <th class="py-3 px-3">No. SO & Customer</th>
                    <th class="py-3 px-3">Bahan & Qty</th>
                    <th class="py-3 px-3">Deadline (Out)</th>
                    <th class="py-3 px-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm font-medium">
                @forelse($orders as $idx => $order)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-3 text-center font-bold text-slate-500">
                        {{ $orders->firstItem() + $idx }}
                    </td>
                    <td class="py-3 px-3">
                        <p class="font-black text-black leading-tight">
                            {{ $order->customer->name ?? $order->customer_name }}
                            @if(optional($order->customer)->pic_name)
                                <span class="text-xs font-semibold text-slate-500">(PIC: {{ $order->customer->pic_name }})</span>
                            @endif
                        </p>
                        <span class="text-xs font-mono font-bold text-slate-500">{{ $order->no_so }}</span>
                        <span class="text-xs text-slate-400">&bull; {{ $order->order_title }}</span>
                    </td>
                    <td class="py-3 px-3">
                        <p class="text-xs font-bold text-slate-800">{{ $order->fabric_type ?? '-' }}</p>
                        <p class="text-xs font-black text-indigo-700 leading-tight">
                            {{ $order->qty }} {{ strtoupper($order->satuan) }}
                        </p>
                    </td>
                    <td class="py-3 px-3">
                        <span class="text-xs font-black text-rose-600">
                            {{ \Carbon\Carbon::parse($order->target_deadline)->translatedFormat('d M Y') }}
                        </span>
                    </td>
                    <td class="py-3 px-3 text-center">
                        <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-300">
                            {{ str_replace('_', ' ', $order->current_status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-xs font-bold text-slate-400">
                        Belum ada data Sales Order. Silakan klik tombol "Input SO Baru".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
        <div class="p-3 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
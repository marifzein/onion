@extends('layouts.app', ['title' => 'ONION CREATIVE - Master Customer'])

@section('content')
<!-- Driver.js Assets (CSS & JS Lokal) -->
<link rel="stylesheet" href="{{ asset('css/driver.css') }}">
<script src="{{ asset('js/driver.js.iife.js') }}"></script>

<style>
    /* Animasi Berdenyut Tombol Panduan Oranye */
    @keyframes pulseOrange {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.65); }
        70% { transform: scale(1.08); box-shadow: 0 0 0 14px rgba(249, 115, 22, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }
    .btn-pulse-orange {
        animation: pulseOrange 2s infinite;
    }
    .btn-pulse-orange:hover {
        animation: none;
        transform: scale(1.1);
    }

    /* Styling Popover Driver.js ala TataKas */
    .driver-popover .driver-popover-next-btn,
    .driver-popover .driver-popover-done-btn {
        background-color: #10b981 !important;
        color: #ffffff !important;
        border: none !important;
        text-shadow: none !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
    }
    .driver-popover .driver-popover-next-btn:hover,
    .driver-popover .driver-popover-done-btn:hover {
        background-color: #059669 !important;
    }
    .driver-popover .driver-popover-prev-btn {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
    }
    .driver-popover {
        border-radius: 16px !important;
        padding: 16px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }
    .driver-popover-title {
        font-weight: 700 !important;
        color: #1e293b !important;
        font-size: 15px !important;
    }
    .driver-popover-description {
        color: #64748b !important;
        font-size: 13px !important;
        line-height: 1.5 !important;
    }
</style>

<div class="max-w-7xl w-full mx-auto space-y-5">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div id="tour-cust-header">
            <h1 class="text-2xl font-black text-black tracking-tight">Master Customer</h1>
            <p class="text-xs font-bold text-slate-500">Database kontak pelanggan & instansi pemesan</p>
        </div>

        <div class="flex items-center gap-3">
            <form id="tour-cust-search" action="{{ route('customers.index') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama, no telp, alamat..."
                    class="pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black outline-none focus:border-black shadow-2xs"
                />
                <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </form>

            <a id="tour-btn-add-cust" href="{{ route('customers.create') }}" class="px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black flex items-center gap-2 shadow-xs transition-all">
                <i class="ph-bold ph-plus text-emerald-400 text-sm"></i>
                <span>Tambah Customer</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
        <i class="ph-bold ph-check-circle text-base text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div id="tour-cust-table" class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead class="bg-slate-100 border-b border-slate-300 text-black text-xs font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-4 w-12 text-center">No</th>
                    <th id="tour-col-cust-name" class="py-3 px-4">Nama Customer / Instansi</th>
                    <th class="py-3 px-4">PIC</th>
                    <th id="tour-col-cust-phone" class="py-3 px-4 w-44">No. Telepon / WA</th>
                    <th id="tour-col-cust-addr" class="py-3 px-4">Alamat</th>
                    <th id="tour-col-cust-status" class="py-3 px-4 w-24 text-center">Status</th>
                    <th id="tour-col-cust-action" class="py-3 px-4 w-24 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm font-medium">
                @forelse($customers as $idx => $c)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-500">
                        {{ $customers->firstItem() + $idx }}
                    </td>
                    <td class="py-3 px-4 font-black text-black">
                        {{ $c->name }}
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-black text-black leading-tight">{{ $c->name }}</p>
                        @if($c->pic_name)
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1 mt-0.5">
                                <i class="ph-bold ph-identification-badge text-indigo-600"></i> PIC: {{ $c->pic_name }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-xs font-mono font-bold text-indigo-700">
                        {{ $c->phone ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-xs text-slate-600 font-semibold truncate max-w-xs">
                        {{ $c->address ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($c->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('customers.edit', $c->id) }}" class="p-1.5 text-slate-600 hover:text-black hover:bg-slate-100 rounded-lg" title="Edit">
                                <i class="ph-bold ph-pencil-simple text-base"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-xs font-bold text-slate-400">
                        Belum ada database customer. Silakan klik tombol "Tambah Customer".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
        <div class="p-3 border-t border-slate-200">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- TOMBOL BANTUAN "?" MELAYANG ORANYE BERDENYUT DI POJOK KANAN BAWAH -->
<button 
    type="button" 
    onclick="jalankanTourCustomerIndex()" 
    title="Panduan Master Customer"
    class="btn-pulse-orange fixed bottom-6 right-6 z-[9999] w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors"
>
    ?
</button>

<script>
function jalankanTourCustomerIndex() {
    const driverFactory = window.driver?.js?.driver || window.driver;
    if (!driverFactory) {
        alert('Panduan sedang memuat, silakan klik sekali lagi.');
        return;
    }

    const driverObj = driverFactory({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(0, 0, 0, 0.75)',
        nextBtnText: 'Lanjut &rarr;',
        prevBtnText: '&larr; Kembali',
        doneBtnText: 'Selesai',
        steps: [
            { 
                element: '#tour-cust-header',
                popover: { 
                    title: 'Master Customer', 
                    description: 'Halaman ini menyimpan seluruh data database pelanggan dan instansi pemesan jersey.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-cust-search',
                popover: { 
                    title: 'Pencarian Pelanggan', 
                    description: 'Cari data pelanggan dengan cepat berdasarkan nama, nomor telepon, atau alamat.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-btn-add-cust',
                popover: { 
                    title: 'Tambah Customer Baru', 
                    description: 'Klik untuk mendaftarkan pelanggan atau instansi baru ke dalam sistem.', 
                    side: "bottom", 
                    align: 'end' 
                } 
            },
            { 
                element: '#tour-cust-table',
                popover: { 
                    title: 'Daftar Kontak Customer', 
                    description: 'Menampilkan nama perusahaan, PIC, nomor WhatsApp, alamat, dan status keaktifan akun pelanggan.', 
                    side: "top", 
                    align: 'start' 
                } 
            }
        ]
    });

    driverObj.drive();
}

document.addEventListener('DOMContentLoaded', function () {
    const hasSeenTour = localStorage.getItem('onion_customer_index_tour_seen');
    if (!hasSeenTour) {
        setTimeout(() => {
            jalankanTourCustomerIndex();
            localStorage.setItem('onion_customer_index_tour_seen', 'true');
        }, 600);
    }
});
</script>
@endsection
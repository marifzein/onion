@extends('layouts.app', ['title' => 'ONION CREATIVE - Master Bahan Kain'])

@section('content')

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
    
    <!-- Top Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div id="tour-fabric-header">
            <h1 class="text-2xl font-black text-black tracking-tight">Master Bahan Kain</h1>
            <p class="text-xs font-bold text-slate-500">Database spesifikasi bahan untuk pilihan otomatis saat input SO</p>
        </div>

        <div class="flex items-center gap-3">
            <form id="tour-fabric-search" action="{{ route('fabrics.index') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama atau kode kain..."
                    class="pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black outline-none focus:border-black shadow-2xs"
                />
                <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </form>

            <a id="tour-btn-add-fabric" href="{{ route('fabrics.create') }}" class="px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black flex items-center gap-2 shadow-xs transition-all">
                <i class="ph-bold ph-plus text-emerald-400 text-sm"></i>
                <span>Tambah Bahan</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
        <i class="ph-bold ph-check-circle text-base text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Tabel Data Bahan -->
    <div id="tour-fabric-table" class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead class="bg-slate-100 border-b border-slate-300 text-black text-xs font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-4 w-12 text-center">No</th>
                    <th id="tour-col-name" class="py-3 px-4">Nama Bahan</th>
                    <th id="tour-col-gramasi" class="py-3 px-4 w-32">Gramasi</th>
                    <th id="tour-col-desc" class="py-3 px-4">Catatan Karakteristik</th>
                    <th id="tour-col-status" class="py-3 px-4 w-24 text-center">Status</th>
                    <th id="tour-col-action" class="py-3 px-4 w-24 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm font-medium">
                @forelse($fabrics as $idx => $f)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-500">
                        {{ $fabrics->firstItem() + $idx }}
                    </td>
                    
                    <td class="py-3 px-4 font-black text-black">
                        {{ $f->name }}
                    </td>
                    <td class="py-3 px-4 text-xs font-bold text-slate-600">
                        {{ $f->gramasi ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-xs text-slate-500 font-semibold truncate max-w-xs">
                        {{ $f->description ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($f->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('fabrics.edit', $f->id) }}" class="p-1.5 text-slate-600 hover:text-black hover:bg-slate-100 rounded-lg" title="Edit">
                                <i class="ph-bold ph-pencil-simple text-base"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-xs font-bold text-slate-400">
                        Belum ada database bahan. Silakan klik tombol "Tambah Bahan".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($fabrics->hasPages())
        <div class="p-3 border-t border-slate-200">
            {{ $fabrics->links() }}
        </div>
        @endif
    </div>

</div>

<!-- TOMBOL BANTUAN "?" MELAYANG ORANYE BERDENYUT DI POJOK KANAN BAWAH -->
<button 
    type="button" 
    onclick="jalankanTourFabricIndex()" 
    title="Panduan Master Bahan"
    class="btn-pulse-orange fixed bottom-6 right-6 z-[9999] w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors"
>
    ?
</button>

<script>
function jalankanTourFabricIndex() {
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
                element: '#tour-fabric-header',
                popover: { 
                    title: 'Master Bahan Kain', 
                    description: 'Halaman ini mengelola daftar spesifikasi kain yang nantinya muncul otomatis saat Anda menginput Sales Order (SO).', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-fabric-search',
                popover: { 
                    title: 'Pencarian Bahan', 
                    description: 'Gunakan kolom pencarian ini untuk menemukan jenis kain tertentu dengan cepat.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-btn-add-fabric',
                popover: { 
                    title: 'Tambah Bahan Baru', 
                    description: 'Klik tombol ini untuk mendaftarkan jenis kain atau bahan baru ke dalam sistem.', 
                    side: "bottom", 
                    align: 'end' 
                } 
            },
            { 
                element: '#tour-fabric-table',
                popover: { 
                    title: 'Tabel Daftar Bahan', 
                    description: 'Berisi informasi lengkap mengenai nama bahan, gramasi/ketebalan, karakteristik potong, hingga status keaktifannya.', 
                    side: "top", 
                    align: 'start' 
                } 
            }
        ]
    });

    driverObj.drive();
}

document.addEventListener('DOMContentLoaded', function () {
    const hasSeenTour = localStorage.getItem('onion_fabric_index_tour_seen');
    if (!hasSeenTour) {
        setTimeout(() => {
            jalankanTourFabricIndex();
            localStorage.setItem('onion_fabric_index_tour_seen', 'true');
        }, 600);
    }
});
</script>
@endsection
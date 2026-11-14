@extends('layouts.app', ['title' => 'Tambah Master Bahan'])

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

<div class="max-w-2xl w-full mx-auto space-y-5">
    <div id="tour-create-fabric-header" class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div>
            <h1 class="text-xl font-black text-black tracking-tight">Tambah Master Bahan</h1>
            <p class="text-xs font-bold text-slate-500">Pendaftaran jenis kain baru untuk antrean produksi</p>
        </div>
        <a href="{{ route('fabrics.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-300 shadow-xs p-6">
        <form action="{{ route('fabrics.store') }}" method="POST" class="space-y-4">
            @csrf

            <div id="tour-create-fabric-name">
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Nama Bahan / Fabric <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Ntech Dibalik / Milano"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                    />
                    @error('name') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div id="tour-create-fabric-gramasi">
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                    Gramasi / Ketebalan (Opsional)
                </label>
                <input
                    type="text"
                    name="gramasi"
                    value="{{ old('gramasi') }}"
                    placeholder="Contoh: 130 gsm / 140 gsm"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
            </div>

            <div id="tour-create-fabric-desc">
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                    Catatan Khusus Potong / Karakteristik
                </label>
                <textarea
                    name="description"
                    rows="3"
                    placeholder="Contoh: Permukaan timbul dipasang di depan, susut melar 1.5 cm saat press sublim."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50"
                >{{ old('description') }}</textarea>
            </div>

            <div id="tour-create-fabric-status" class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded text-black focus:ring-0">
                <label for="is_active" class="text-xs font-black text-slate-800 cursor-pointer">Status Aktif (Tampil di form input SO)</label>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <a href="{{ route('fabrics.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black">Batal</a>
                <button id="tour-create-fabric-submit" type="submit" class="px-6 py-2.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-md">Simpan Bahan</button>
            </div>
        </form>
    </div>
</div>

<!-- TOMBOL BANTUAN "?" MELAYANG ORANYE BERDENYUT DI POJOK KANAN BAWAH -->
<button 
    type="button" 
    onclick="jalankanTourFabricCreate()" 
    title="Panduan Tambah Master Bahan"
    class="btn-pulse-orange fixed bottom-6 right-6 z-[9999] w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors"
>
    ?
</button>

<script>
function jalankanTourFabricCreate() {
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
                element: '#tour-create-fabric-header',
                popover: { 
                    title: 'Formulir Tambah Bahan', 
                    description: 'Daftarkan jenis kain baru yang biasa digunakan dalam produksi jersey atau sablon.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-fabric-name',
                popover: { 
                    title: 'Nama Bahan / Fabric', 
                    description: 'Masukkan nama resmi kain (contoh: Milano, Ntech Dibalik, Drifit Benzema). Kolom ini wajib diisi.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-fabric-gramasi',
                popover: { 
                    title: 'Gramasi / Ketebalan', 
                    description: 'Informasi berat kain per meter persegi (misal: 130 gsm) untuk referensi tim produksi.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-fabric-desc',
                popover: { 
                    title: 'Catatan Karakteristik', 
                    description: 'Tulis instruksi khusus saat proses potong atau press (misalnya tingkat susut kain saat terkena suhu mesin heat press).', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-fabric-status',
                popover: { 
                    title: 'Status Keaktifan', 
                    description: 'Jika dicentang, bahan ini akan langsung muncul dalam pilihan dropdown saat operator membuat Sales Order baru.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-fabric-submit',
                popover: { 
                    title: 'Simpan Data Bahan', 
                    description: 'Klik tombol ini untuk merekam data bahan baru ke dalam database.', 
                    side: "top", 
                    align: 'end' 
                } 
            }
        ]
    });

    driverObj.drive();
}

document.addEventListener('DOMContentLoaded', function () {
    const hasSeenTour = localStorage.getItem('onion_fabric_create_tour_seen');
    if (!hasSeenTour) {
        setTimeout(() => {
            jalankanTourFabricCreate();
            localStorage.setItem('onion_fabric_create_tour_seen', 'true');
        }, 600);
    }
});
</script>
@endsection
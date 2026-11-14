@extends('layouts.app', ['title' => 'Tambah Customer'])

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

<div class="max-w-2xl w-full mx-auto space-y-5">
    <div id="tour-create-cust-header" class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div>
            <h1 class="text-xl font-black text-black tracking-tight">Tambah Customer Baru</h1>
            <p class="text-xs font-bold text-slate-500">Pendaftaran pelanggan baru untuk data pemesanan</p>
        </div>
        <a href="{{ route('customers.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-300 shadow-xs p-6">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
            @csrf

            <div id="tour-create-cust-name">
                <label class="block text-xs font-black uppercase text-slate-500 mb-1.5">
                    Nama Lengkap Customer / Instansi <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Contoh: PLN Jakarta"
                    required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
                @error('name') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
            </div>

            <div id="tour-create-cust-pic">
                <label class="block text-xs font-black uppercase text-slate-500 mb-1.5">
                    Nama PIC / Kontak Person (Opsional)
                </label>
                <input
                    type="text"
                    name="pic_name"
                    value="{{ old('pic_name', $customer->pic_name ?? '') }}"
                    placeholder="Contoh: Bpk. Hendra (Divisi Promosi)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
            </div>

            <div id="tour-create-cust-phone">
                <label class="block text-xs font-black uppercase text-slate-500 mb-1.5">
                    No. Telepon / WhatsApp (Opsional)
                </label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="Contoh: 081234567xxx"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
            </div>

            <div id="tour-create-cust-addr">
                <label class="block text-xs font-black uppercase text-slate-500 mb-1.5">
                    Alamat Lengkap (Opsional)
                </label>
                <textarea
                    name="address"
                    rows="3"
                    placeholder="Masukkan alamat pengiriman atau kantor..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50"
                >{{ old('address') }}</textarea>
            </div>

            <div id="tour-create-cust-status" class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded text-black focus:ring-0">
                <label for="is_active" class="text-xs font-black text-slate-800 cursor-pointer">Status Aktif</label>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black">Batal</a>
                <button id="tour-create-cust-submit" type="submit" class="px-6 py-2.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-md">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- TOMBOL BANTUAN "?" MELAYANG ORANYE BERDENYUT DI POJOK KANAN BAWAH -->
<button 
    type="button" 
    onclick="jalankanTourCustomerCreate()" 
    title="Panduan Tambah Customer"
    class="btn-pulse-orange fixed bottom-6 right-6 z-[9999] w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors"
>
    ?
</button>

<script>
function jalankanTourCustomerCreate() {
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
                element: '#tour-create-cust-header',
                popover: { 
                    title: 'Form Tambah Customer', 
                    description: 'Daftarkan pelanggan atau instansi baru agar tersimpan dalam sistem pemesanan.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cust-name',
                popover: { 
                    title: 'Nama Customer / Instansi', 
                    description: 'Masukkan nama resmi perusahaan, instansi, atau nama perseorangan (wajib diisi).', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cust-pic',
                popover: { 
                    title: 'Nama PIC / Kontak Person', 
                    description: 'Nama orang yang dapat dihubungi secara langsung (misal: Bpk. Hendra dari Divisi Promosi).', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cust-phone',
                popover: { 
                    title: 'No. Telepon / WhatsApp', 
                    description: 'Nomor aktif yang bisa dihubungi untuk konfirmasi pesanan atau pengiriman.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cust-addr',
                popover: { 
                    title: 'Alamat Lengkap', 
                    description: 'Alamat kantor atau tujuan pengiriman paket jersey jadi.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cust-status',
                popover: { 
                    title: 'Status Keaktifan', 
                    description: 'Pastikan tercentang agar customer ini muncul saat dipilih di form pencarian SO.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cust-submit',
                popover: { 
                    title: 'Simpan Customer', 
                    description: 'Klik tombol ini untuk merekam data pelanggan baru ke database.', 
                    side: "top", 
                    align: 'end' 
                } 
            }
        ]
    });

    driverObj.drive();
}

document.addEventListener('DOMContentLoaded', function () {
    const hasSeenTour = localStorage.getItem('onion_customer_create_tour_seen');
    if (!hasSeenTour) {
        setTimeout(() => {
            jalankanTourCustomerCreate();
            localStorage.setItem('onion_customer_create_tour_seen', 'true');
        }, 600);
    }
});
</script>
@endsection
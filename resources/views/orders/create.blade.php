@extends('layouts.app', ['title' => 'ONION CREATIVE - Form Input Sales Order'])

@section('content')
<!-- QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<!-- PeerJS P2P -->
<script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
<!-- Driver.js Assets (CSS & JS) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css"/>
<script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>

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

<div
    x-data="orderForm()"
    x-init="initP2PReceiver()"
    class="max-w-4xl w-full mx-auto p-2 md:p-4 flex flex-col justify-center"
>
    <div class="bg-white rounded-3xl border border-slate-300 shadow-xs p-6 md:p-8">
        <!-- Header Form -->
        <div id="tour-create-header" class="border-b border-slate-200 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-black tracking-tight">Formulir Pesanan (Sales Order)</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                    Pesanan otomatis diteruskan ke antrean <strong class="text-indigo-600">Ka Prod &bull; Bahan &bull; Print &bull; Jahit &bull; QC &bull; Finishing</strong>
                </p>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-bold text-slate-400 block uppercase">No. Registrasi SO</span>
                <span class="font-mono text-base font-black text-indigo-700" x-text="form.no_so"></span>
            </div>
        </div>

        <form @submit.prevent="submitOrder()" class="space-y-5">
            <!-- Row 0: Pilih Cabang -->
            <div id="tour-create-cabang" class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                <span class="text-xs font-black uppercase text-slate-700 flex items-center gap-1.5">
                    <i class="ph-bold ph-storefront text-indigo-600 text-base"></i> Cabang Produksi
                </span>
                <select x-model="form.cabang_id" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl font-black text-xs text-black outline-none cursor-pointer">
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Row 1: Customer (dengan Tombol Tambah Cepat) & Judul Order -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Dropdown Pencarian Customer Otomatis POS -->
                <div id="tour-create-customer" class="relative" @click.outside="customerDropdownOpen = false">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase text-slate-700">
                            Nama Customer / Instansi <span class="text-rose-500">*</span>
                        </label>
                        <button
                            id="tour-btn-quick-cust"
                            type="button"
                            @click="openCustomerModal = true"
                            class="text-xs font-black text-indigo-600 hover:text-indigo-800 flex items-center gap-1 cursor-pointer"
                        >
                            <i class="ph-bold ph-plus-circle text-sm"></i> + Tambah Pelanggan Baru
                        </button>
                    </div>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="form.customer_name"
                            @focus="searchCustomer(); customerDropdownOpen = true"
                            @input.debounce.250ms="searchCustomer(); customerDropdownOpen = true"
                            @keydown.arrow-down.prevent="navigateCustomer(1)"
                            @keydown.arrow-up.prevent="navigateCustomer(-1)"
                            @keydown.enter.prevent="selectCurrentCustomer()"
                            @keydown.escape="customerDropdownOpen = false"
                            placeholder="Ketik nama customer / instansi (cth: Semen Gresik)..."
                            required
                            autocomplete="off"
                            class="w-full pl-4 pr-9 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                        />
                        <div class="absolute right-3 top-3 text-slate-400 pointer-events-none">
                            <i class="ph-bold ph-magnifying-glass"></i>
                        </div>
                    </div>

                    <!-- Dropdown Hasil Pencarian Customer -->
                    <div
                        x-show="customerDropdownOpen && customerItems.length > 0"
                        x-cloak
                        class="absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-2xl shadow-xl max-h-56 overflow-y-auto z-50 divide-y divide-slate-100"
                    >
                        <template x-for="(c, index) in customerItems" :key="c.id">
                            <div
                                @click="selectCustomerItem(c)"
                                @mouseenter="customerHighlightIndex = index"
                                :class="customerHighlightIndex === index ? 'bg-indigo-50 text-indigo-900 font-black' : 'text-slate-800'"
                                class="px-4 py-2.5 cursor-pointer flex items-center justify-between text-xs transition-colors"
                            >
                                <div>
                                    <span class="block text-xs font-black" x-text="c.name"></span>
                                    <span class="text-[10px] text-slate-400" x-text="(c.pic_name ? 'PIC: ' + c.pic_name : '') + (c.phone ? ' • ' + c.phone : '')"></span>
                                </div>
                                <i class="ph-bold ph-arrow-bend-down-left text-slate-400" x-show="customerHighlightIndex === index"></i>
                            </div>
                        </template>
                    </div>
                </div>

                <div id="tour-create-title">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Judul Order / Event <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="form.order_title"
                            placeholder="Misal: Reuni Akbar ..."
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50 placeholder:font-normal placeholder:text-slate-400"
                        />
                    </div>
                </div>
            </div>

            <!-- Row 2: Qty & Satuan -->
            <div id="tour-create-qty" class="p-4 bg-indigo-50/40 rounded-2xl border border-indigo-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-900 mb-1.5 flex items-center gap-1.5">
                            <i class="ph-bold ph-hash text-indigo-600"></i> Jumlah / Qty <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            min="1"
                            x-model.number="form.qty"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-300 focus:border-black outline-none font-black text-lg text-slate-900 bg-white"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-900 mb-1.5 flex items-center gap-1.5">
                            <i class="ph-bold ph-tag text-indigo-600"></i> Satuan <span class="text-rose-500">*</span>
                        </label>
                        <select
                            x-model="form.satuan"
                            class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-300 focus:border-black outline-none font-black text-base text-slate-900 bg-white cursor-pointer"
                        >
                            <option value="pcs">pcs</option>
                            <option value="stel">stel</option>
                        </select>
                    </div>
                </div>
                <div class="mt-2.5 flex items-center gap-2 text-xs font-bold text-slate-600">
                    <span>Preview Total:</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white font-black text-xs" x-text="form.qty + ' ' + form.satuan"></span>
                </div>
            </div>

            <!-- Row 3: Bahan / Fabric & Target Deadline (1 Baris - 2 Kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Dropdown Bahan POS -->
                <div id="tour-create-fabric" class="relative" @click.outside="fabricDropdownOpen = false">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Bahan / Fabric <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            x-model="form.fabric_type"
                            @focus="searchFabric(); fabricDropdownOpen = true"
                            @input.debounce.250ms="searchFabric(); fabricDropdownOpen = true"
                            @keydown.arrow-down.prevent="navigateFabric(1)"
                            @keydown.arrow-up.prevent="navigateFabric(-1)"
                            @keydown.enter.prevent="selectCurrentFabric()"
                            @keydown.escape="fabricDropdownOpen = false"
                            placeholder="Ketik nama bahan (cth: Ntech / Milano)..."
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                        />
                        <div class="absolute right-3 top-3 text-slate-400 pointer-events-none">
                            <i class="ph-bold ph-caret-down"></i>
                        </div>
                    </div>

                    <div
                        x-show="fabricDropdownOpen && fabricItems.length > 0"
                        x-cloak
                        class="absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-2xl shadow-xl max-h-56 overflow-y-auto z-50 divide-y divide-slate-100"
                    >
                        <template x-for="(item, index) in fabricItems" :key="item.id">
                            <div
                                @click="selectFabricItem(item)"
                                @mouseenter="fabricHighlightIndex = index"
                                :class="fabricHighlightIndex === index ? 'bg-indigo-50 text-indigo-900 font-black' : 'text-slate-800'"
                                class="px-4 py-2.5 cursor-pointer flex items-center justify-between text-xs transition-colors"
                            >
                                <div>
                                    <span class="block text-xs font-black" x-text="item.name"></span>
                                    <span class="text-[10px] text-slate-400" x-text="item.gramasi ? item.gramasi : ''"></span>
                                </div>
                                <i class="ph-bold ph-arrow-bend-down-left text-slate-400" x-show="fabricHighlightIndex === index"></i>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Target Deadline (Order Out) -->
                <div id="tour-create-deadline">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Target Deadline (Order Out) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        x-model="form.target_deadline"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-black text-sm bg-slate-50/50 text-rose-600"
                    />
                </div>
            </div>

            <!-- Row 4: Tipe Pesanan (Baris Tersendiri - Colspan Penuh) -->
            <div id="tour-create-type">
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Tipe Pesanan</label>
                <input
                    type="text"
                    x-model="form.order_type"
                    placeholder="Misal : Atasan Fullprint"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50 placeholder:font-normal placeholder:text-slate-400"
                />
            </div>

            <!-- Row 5: Deskripsi / Catatan -->
            <div id="tour-create-notes">
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Deskripsi & Instruksi Pengerjaan (Opsional)</label>
                <textarea
                    x-model="form.notes"
                    rows="3"
                    placeholder="Contoh: Kerah V-Neck merah, list ijo ...dsb."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50 placeholder:font-normal placeholder:text-slate-400"
                ></textarea>
            </div>

            <!-- Row 6: Foto Form SO Fisik & QR Camera P2P -->
            <div id="tour-create-photo" class="p-4 bg-slate-50 rounded-2xl border border-slate-300">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                        <i class="ph-bold ph-camera text-indigo-600 text-base"></i> Foto Formulir SO Fisik (Ttd Customer)
                    </label>
                    <button
                        type="button"
                        @click="openQrModal = true; renderQrCode()"
                        class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-black flex items-center gap-1.5 cursor-pointer"
                    >
                        <i class="ph-bold ph-qr-code text-indigo-600 text-base"></i>
                        <span>Foto Lewat HP (Scan QR)</span>
                    </button>
                </div>

                <div
                    @paste.window="handlePaste($event)"
                    @click="$refs.fileInput.click()"
                    class="relative border-2 border-dashed rounded-xl p-4 transition-all flex flex-col items-center justify-center text-center cursor-pointer bg-white"
                    :class="spkPreview ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-300 hover:border-slate-400'"
                >
                    <input type="file" x-ref="fileInput" @change="handleFileInput($event)" accept="image/*" class="hidden" />

                    <template x-if="!spkPreview">
                        <div class="py-3 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-1.5">
                                <i class="ph-bold ph-image text-2xl"></i>
                            </div>
                            <p class="text-xs font-black text-slate-800">
                                Klik upload file, atau <span class="text-indigo-600 underline">Paste (Ctrl + V)</span> dari WhatsApp Web
                            </p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Foto teroptimasi (&le; 250 KB)</p>
                        </div>
                    </template>

                    <template x-if="spkPreview">
                        <div class="w-full flex items-center justify-between gap-4" @click.stop>
                            <div class="flex items-center gap-3">
                                <img :src="spkPreview" class="w-14 h-14 object-cover rounded-xl border border-slate-300" />
                                <div class="text-left">
                                    <span class="text-xs font-black text-emerald-600 uppercase">✓ Terkompresi WebP</span>
                                    <p class="text-[11px] text-slate-500 font-bold" x-text="fileMeta"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-bold text-slate-700">Ganti</button>
                                <button type="button" @click="removePhoto()" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg">
                                    <i class="ph-bold ph-trash text-base"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                    <i class="ph-bold ph-shield-check text-emerald-600"></i> Disimpan sebagai arsip digital
                </span>
                <button
                    id="tour-create-submit"
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-6 py-2.5 rounded-xl bg-black hover:bg-slate-800 text-white font-black text-xs shadow-md cursor-pointer flex items-center gap-2"
                >
                    <i class="ph-bold ph-paper-plane-tilt text-base"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan & Kirim ke Ka Prod'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- ==================== MODAL TAMBAH PELANGGAN CEPAT (LENGKAP DENGAN PIC) ==================== -->
    <div
        x-show="openCustomerModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="openCustomerModal = false"
    >
        <div
            @click.outside="openCustomerModal = false"
            class="bg-white rounded-3xl border border-slate-300 shadow-2xl w-full max-w-md overflow-hidden"
        >
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2 text-indigo-600">
                        <i class="ph-bold ph-user-plus text-xl"></i>
                        <h3 class="text-base font-black text-black">Tambah Pelanggan Baru</h3>
                    </div>
                    <button
                        type="button"
                        @click="openCustomerModal = false"
                        class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-black transition-colors"
                    >
                        <i class="ph-bold ph-x text-lg"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form @submit.prevent="submitQuickCustomer()" class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">
                            Nama Pelanggan / Perusahaan <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            x-model="newCustomer.name"
                            placeholder="Contoh: PT Semen Gresik "
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 outline-none font-bold text-sm bg-slate-50/50"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">
                            Nama PIC / Kontak Person (Opsional)
                        </label>
                        <input
                            type="text"
                            x-model="newCustomer.pic_name"
                            placeholder="Contoh: Bpk. Bambang "
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 outline-none font-bold text-sm bg-slate-50/50"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">
                            No. Telepon / HP / WA
                        </label>
                        <input
                            type="text"
                            x-model="newCustomer.phone"
                            placeholder="Contoh: 081234567xx"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 outline-none font-bold text-sm bg-slate-50/50"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">
                            Alamat Lengkap
                        </label>
                        <textarea
                            x-model="newCustomer.address"
                            rows="2"
                            placeholder="Masukkan alamat kantor / kirim..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 outline-none font-bold text-xs bg-slate-50/50"
                        ></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2">
                        <button
                            type="button"
                            @click="openCustomerModal = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="savingCustomer"
                            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition-colors flex items-center gap-1.5 cursor-pointer"
                        >
                            <i class="ph-bold ph-floppy-disk text-base"></i>
                            <span x-text="savingCustomer ? 'Menyimpan...' : 'Simpan Pelanggan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL QR CODE P2P HP CAMERA -->
    <div x-show="openQrModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="openQrModal = false" class="bg-white rounded-3xl border border-slate-300 shadow-2xl w-full max-w-sm p-6 text-center">
            <h3 class="text-sm font-black text-black">Foto Lembar SO dari HP</h3>
            <p class="text-xs text-green-500 mt-1">Scan QR code ini langsung dari kamera smartphone:</p>
            
            <div class="my-4 p-4 bg-slate-50 border-2 border-dashed border-indigo-200 rounded-2xl flex flex-col items-center justify-center">
                <div id="qrcodeCanvas" class="p-2 bg-white rounded-xl shadow-xs"></div>
                <span class="mt-2 text-xs font-black text-indigo-700" x-text="'Kode Sesi: ' + sessionToken"></span>
            </div>

            <button type="button" @click="openQrModal = false" class="w-full py-2 bg-slate-100 rounded-xl text-xs font-black text-slate-700">Tutup</button>
        </div>
    </div>
</div>

<!-- TOMBOL BANTUAN "?" MELAYANG ORANYE BERDENYUT DI POJOK KANAN BAWAH -->
<button 
    type="button" 
    onclick="jalankanTourOrderCreate()" 
    title="Panduan Input Sales Order"
    class="btn-pulse-orange fixed bottom-6 right-6 z-[9999] w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors"
>
    ?
</button>

<script>
function jalankanTourOrderCreate() {
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
        doneBtnText: 'Paham!',
        steps: [
            { 
                element: '#tour-create-header',
                popover: { 
                    title: 'Formulir Input Sales Order (SO)', 
                    description: 'Nomor SO dibuat otomatis oleh sistem. Pesanan yang disimpan di sini akan langsung masuk ke antrean Ka Prod.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-cabang',
                popover: { 
                    title: 'Pilih Cabang Produksi', 
                    description: 'Tentukan lokasi cabang workshop tempat pesanan ini diproduksi.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-customer',
                popover: { 
                    title: 'Pilih atau Tambah Pelanggan', 
                    description: 'Ketik untuk mencari nama pelanggan/instansi yang sudah terdaftar, atau klik "+ Tambah Pelanggan Baru" jika pelanggan belum ada.', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-title',
                popover: { 
                    title: 'Judul Order / Event', 
                    description: 'Masukkan nama kegiatan, tim, atau event pesanan (contoh: Jersey Futsal PLN / Reuni Akbar).', 
                    side: "bottom", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-qty',
                popover: { 
                    title: 'Jumlah Qty & Satuan', 
                    description: 'Isi total kuantitas pesanan dan pilih satuannya (pcs untuk atasan saja, atau stel untuk setelan lengkap).', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-fabric',
                popover: { 
                    title: 'Jenis Bahan / Kain', 
                    description: 'Ketik dan pilih jenis kain yang digunakan dari daftar master bahan (contoh: Milano, NTech, MicroCool).', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-deadline',
                popover: { 
                    title: 'Target Deadline (Order Out)', 
                    description: 'Tentukan tanggal batas waktu pesanan wajib selesai dan siap diambil pelanggan.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-photo',
                popover: { 
                    title: 'Upload Foto Lembar SO Fisik', 
                    description: 'Lampirkan foto bukti lembar SO fisik. Anda bisa upload dari komputer, Paste (Ctrl+V) dari WhatsApp, atau Scan QR untuk memotret langsung dari HP.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
            { 
                element: '#tour-create-submit',
                popover: { 
                    title: 'Simpan & Teruskan ke Ka Prod', 
                    description: 'Klik tombol ini setelah seluruh data lengkap untuk menyimpan SO dan memasukkannya ke antrean produksi.', 
                    side: "top", 
                    align: 'end' 
                } 
            }
        ]
    });

    driverObj.drive();
}

document.addEventListener('DOMContentLoaded', function () {
    const hasSeenTour = localStorage.getItem('onion_orders_create_tour_seen');
    if (!hasSeenTour) {
        setTimeout(() => {
            jalankanTourOrderCreate();
            localStorage.setItem('onion_orders_create_tour_seen', 'true');
        }, 600);
    }
});

function orderForm() {
    return {
        openQrModal: false,
        openCustomerModal: false,
        savingCustomer: false,
        isSubmitting: false,
        spkPreview: null,
        fileMeta: '',
        sessionToken: 'SO' + Math.random().toString(36).substring(2, 7).toUpperCase(),
        peer: null,

        fabricDropdownOpen: false,
        fabricHighlightIndex: 0,
        fabricItems: [],

        newCustomer: {
            name: '',
            pic_name: '',
            phone: '',
            address: '',
        },

        customerDropdownOpen: false,
        customerHighlightIndex: 0,
        customerItems: [],

        form: {
            cabang_id: "{{ $cabangs->first()->id ?? 1 }}",
            customer_id: null,
            no_so: "{{ $noSo }}",
            customer_name: "",
            order_title: "",
            qty: 0,
            satuan: "pcs",
            fabric_type: "",
            order_type: "",
            target_deadline: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            notes: "",
            spk_photo_base64: null,
        },

        searchCustomer() {
            if (!this.form.customer_name || this.form.customer_name.trim().length === 0) {
                this.customerItems = [];
                return;
            }

            fetch(`/api/customers/search?q=${encodeURIComponent(this.form.customer_name)}`)
                .then(res => res.json())
                .then(data => {
                    this.customerItems = data;
                    this.customerHighlightIndex = 0;
                })
                .catch(() => {});
        },

        navigateCustomer(step) {
            if (!this.customerDropdownOpen || this.customerItems.length === 0) return;
            this.customerHighlightIndex = (this.customerHighlightIndex + step + this.customerItems.length) % this.customerItems.length;
        },

        selectCurrentCustomer() {
            if (this.customerDropdownOpen && this.customerItems.length > 0) {
                this.selectCustomerItem(this.customerItems[this.customerHighlightIndex]);
            }
        },

        selectCustomerItem(item) {
            this.form.customer_id = item.id;
            if (item.pic_name) {
                this.form.customer_name = `${item.name} (PIC: ${item.pic_name})`;
            } else {
                this.form.customer_name = item.name;
            }
            this.customerDropdownOpen = false;
        },
        
        submitQuickCustomer() {
            if (!this.newCustomer.name) return;
            this.savingCustomer = true;

            fetch("{{ route('customers.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.newCustomer)
            })
            .then(res => res.json())
            .then(data => {
                this.savingCustomer = false;
                if (data.status === 'success') {
                    this.form.customer_id = data.customer.id;
                    if (data.customer.pic_name) {
                        this.form.customer_name = `${data.customer.name} (PIC: ${data.customer.pic_name})`;
                    } else {
                        this.form.customer_name = data.customer.name;
                    }

                    this.openCustomerModal = false;
                    this.newCustomer = { name: '', pic_name: '', phone: '', address: '' };

                    Swal.fire({
                        title: 'Tersimpan!',
                        text: 'Customer terpilih otomatis pada form SO',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal menyimpan customer', 'error');
                }
            })
            .catch(() => {
                this.savingCustomer = false;
                Swal.fire('Error', 'Gagal menghubungi server database', 'error');
            });
        },

        searchFabric() {
            fetch(`/api/fabrics/search?q=${encodeURIComponent(this.form.fabric_type || '')}`)
                .then(res => res.json())
                .then(data => {
                    this.fabricItems = data;
                    this.fabricHighlightIndex = 0;
                })
                .catch(() => {});
        },

        navigateFabric(step) {
            if (!this.fabricDropdownOpen || this.fabricItems.length === 0) return;
            this.fabricHighlightIndex = (this.fabricHighlightIndex + step + this.fabricItems.length) % this.fabricItems.length;
        },

        selectCurrentFabric() {
            if (this.fabricDropdownOpen && this.fabricItems.length > 0) {
                this.selectFabricItem(this.fabricItems[this.fabricHighlightIndex]);
            }
        },

        selectFabricItem(item) {
            this.form.fabric_type = item.name;
            this.fabricDropdownOpen = false;
        },

        initP2PReceiver() {
            try {
                this.peer = new Peer(this.sessionToken, {
                    config: { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] }
                });

                this.peer.on('connection', (conn) => {
                    conn.on('data', (data) => {
                        if (data && data.type === 'SPK_PHOTO') {
                            this.spkPreview = data.imageData;
                            this.form.spk_photo_base64 = data.imageData;
                            this.fileMeta = 'Diterima langsung dari HP';
                            this.openQrModal = false;
                            Swal.fire({ title: 'Foto SO Terpasang!', icon: 'success', timer: 2000, confirmButtonColor: '#00bf63' });
                        }
                    });
                });
            } catch(e) {
                console.warn(e);
            }
        },

        renderQrCode() {
            this.$nextTick(() => {
                const container = document.getElementById("qrcodeCanvas");
                if (container) {
                    container.innerHTML = "";
                    const url = `${window.location.origin}/admin_input_order.html?spk_peer=${this.sessionToken}`;
                    new QRCode(container, { text: url, width: 160, height: 160 });
                }
            });
        },

        handlePaste(e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let item of items) {
                if (item.type.includes('image')) {
                    this.compress(item.getAsFile());
                    break;
                }
            }
        },

        handleFileInput(e) {
            const file = e.target.files[0];
            if (file) this.compress(file);
        },

        compress(file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const max = 1400;
                    let w = img.width, h = img.height;
                    if (w > h && w > max) { h = (h * max) / w; w = max; }
                    else if (h > max) { w = (w * max) / h; h = max; }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                    const webpData = canvas.toDataURL('image/webp', 0.75);
                    this.spkPreview = webpData;
                    this.form.spk_photo_base64 = webpData;
                    this.fileMeta = `${(file.size / 1024).toFixed(0)} KB → WebP ${(webpData.length * 0.75 / 1024).toFixed(0)} KB`;
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        },

        removePhoto() {
            this.spkPreview = null;
            this.form.spk_photo_base64 = null;
            this.$refs.fileInput.value = '';
        },

        submitOrder() {
            this.isSubmitting = true;
            fetch("{{ route('orders.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.form)
            })
            .then(res => res.json())
            .then(data => {
                this.isSubmitting = false;
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Tersimpan!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#000000',
                    }).then(() => {
                        window.location.href = "{{ route('orders.index') }}";
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan validasi', 'error');
                }
            })
            .catch(() => {
                this.isSubmitting = false;
                Swal.fire('Error', 'Gagal menghubungi server database', 'error');
            });
        }
    };
}
</script>
@endsection
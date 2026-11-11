<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ONION CREATIVE - Form Input Sales Order</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet" />
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- QRCode.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- PeerJS P2P -->
    <script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', "sans-serif"] },
                },
            },
        };
    </script>
</head>
<body
    x-data="orderForm()"
    x-init="initP2PReceiver()"
    class="bg-[#F4F6FB] text-slate-800 antialiased min-h-screen flex flex-col font-sans select-none"
>
    <!-- HEADER -->
    <header class="bg-white border-b border-slate-300 px-6 py-3 flex items-center justify-between shrink-0 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-black text-white rounded-xl flex items-center justify-center font-black text-xs shrink-0">
                ONION
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-black tracking-tight text-black uppercase leading-none">ONION CREATIVE</h1>
                    <span class="text-[11px] px-2.5 py-0.5 rounded-full bg-slate-900 text-white font-extrabold flex items-center gap-1">
                        <i class="ph-bold ph-storefront"></i>
                        <span x-text="selectedCabangName"></span>
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-500 mt-0.5">Entry Sales Order &bull; Alur Produksi Digital Printing</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-slate-100 border border-slate-300 px-3 py-1.5 rounded-xl text-xs font-bold">
                <span class="text-slate-500">Cabang:</span>
                <select x-model="form.cabang_id" @change="updateCabangName()" class="bg-transparent font-black text-black outline-none cursor-pointer">
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <a href="/admin" class="px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-black flex items-center gap-1.5 transition-colors">
                <i class="ph-bold ph-lock-key"></i> Panel Admin
            </a>
        </div>
    </header>

    <!-- MAIN FORM -->
    <main class="flex-1 max-w-4xl w-full mx-auto p-4 md:p-6 flex flex-col justify-center">
        <div class="bg-white rounded-3xl border border-slate-300 shadow-sm p-6 md:p-8">
            <div class="border-b border-slate-200 pb-4 mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-black tracking-tight">Formulir Pesanan (Sales Order)</h2>
                    <p class="text-xs text-slate-500 font-semibold mt-0.5">
                        Pesanan otomatis diteruskan ke antrean <strong class="text-indigo-600">Ka Prod &bull; Bahan &bull; Print &bull; Jahit &bull; QC &bull; Finishing</strong>
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">No. Registrasi SPK</span>
                    <span class="font-mono text-base font-black text-indigo-700" x-text="form.no_so"></span>
                </div>
            </div>

            <form @submit.prevent="submitOrder()" class="space-y-5">
                <!-- Customer & Nama Order (dengan Voice-to-Text) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                            Nama Customer / Instansi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="form.customer_name"
                                placeholder="Contoh: ME SITA / INDOSPRING"
                                required
                                class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                            />
                            <button
                                type="button"
                                @click="listenVoice('customer_name')"
                                :class="recordingField === 'customer_name' ? 'text-rose-600 animate-pulse' : 'text-slate-400 hover:text-indigo-600'"
                                class="absolute right-2.5 top-2.5 text-lg p-0.5 cursor-pointer"
                                title="Bicara untuk mengisi teks"
                            >
                                <i class="ph-bold ph-microphone"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                            Judul Order / Event <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="form.order_title"
                                placeholder="Contoh: INDOSPRING IJO"
                                required
                                class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                            />
                            <button
                                type="button"
                                @click="listenVoice('order_title')"
                                :class="recordingField === 'order_title' ? 'text-rose-600 animate-pulse' : 'text-slate-400 hover:text-indigo-600'"
                                class="absolute right-2.5 top-2.5 text-lg p-0.5 cursor-pointer"
                                title="Bicara untuk mengisi teks"
                            >
                                <i class="ph-bold ph-microphone"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Qty & Satuan -->
                <div class="p-4 bg-indigo-50/40 rounded-2xl border border-indigo-100">
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
                                <option value="pcs">pcs (Potong / Atasan Jersey)</option>
                                <option value="stel">stel (Setelan Baju + Celana)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2.5 flex items-center gap-2 text-xs font-bold text-slate-600">
                        <span>Preview Total:</span>
                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white font-black text-xs" x-text="form.qty + ' ' + form.satuan"></span>
                    </div>
                </div>

                <!-- Foto SPK Fisik & QR Camera P2P -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-300">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                            <i class="ph-bold ph-camera text-indigo-600 text-base"></i> Foto Formulir SPK Fisik (Ttd Customer)
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

                    <!-- Dropzone / Paste Area -->
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
                                <p class="text-[10px] text-slate-400 mt-0.5">Otomatis terkompresi ke WebP (&le; 250 KB)</p>
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

                <!-- Fabric, Tipe Order, & Deadline -->
                {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Bahan / Fabric <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="form.fabric_type" placeholder="Ntech Dibalik / Milano" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50" />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Tipe Pesanan</label>
                        <input type="text" x-model="form.order_type" placeholder="Atasan Fullprint" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50" />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Target Deadline <span class="text-rose-500">*</span></label>
                        <input type="date" x-model="form.target_deadline" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-black text-sm bg-slate-50/50 text-rose-600" />
                    </div>
                </div> --}}

                <!-- Input Model POS: Ketik cari, panah atas/bawah, enter -->
                <div 
                    x-data="fabricSearch()" 
                    @click.outside="isOpen = false" 
                    class="relative"
                >
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Bahan / Fabric <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">
                        <input
                            type="text"
                            x-model="searchQuery"
                            @focus="search(); isOpen = true"
                            @input.debounce.250ms="search(); isOpen = true"
                            @keydown.arrow-down.prevent="navigate(1)"
                            @keydown.arrow-up.prevent="navigate(-1)"
                            @keydown.enter.prevent="selectCurrent()"
                            @keydown.escape="isOpen = false"
                            placeholder="Ketik nama bahan (cth: Ntech / Milano)..."
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                        />
                        <div class="absolute right-3 top-3 text-slate-400 pointer-events-none">
                            <i class="ph-bold ph-caret-down"></i>
                        </div>
                    </div>

                    <!-- Dropdown Menu Pilihan Hasil Search -->
                    <div
                        x-show="isOpen && items.length > 0"
                        x-cloak
                        class="absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-2xl shadow-xl max-h-56 overflow-y-auto z-50 divide-y divide-slate-100"
                    >
                        <template x-for="(item, index) in items" :key="item.id">
                            <div
                                @click="selectItem(item)"
                                @mouseenter="highlightIndex = index"
                                :class="highlightIndex === index ? 'bg-indigo-50 text-indigo-900 font-black' : 'text-slate-800'"
                                class="px-4 py-2.5 cursor-pointer flex items-center justify-between text-xs transition-colors"
                            >
                                <div>
                                    <span class="block text-xs font-black" x-text="item.name"></span>
                                    <span class="text-[10px] text-slate-400" x-text="item.code ? item.code + ' • ' + (item.gramasi || '') : (item.gramasi || '')"></span>
                                </div>
                                <i class="ph-bold ph-arrow-bend-down-left text-slate-400" x-show="highlightIndex === index"></i>
                            </div>
                        </template>
                    </div>
                </div>





                <!-- Deskripsi / Catatan -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Deskripsi & Instruksi Pengerjaan (Opsional)</label>
                    <textarea x-model="form.notes" rows="3" placeholder="Contoh: Kerah V-Neck merah, label size Indospring, dsb." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                        <i class="ph-bold ph-shield-check text-emerald-600"></i> Disimpan langsung ke MySQL
                    </span>
                    <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 rounded-xl bg-black hover:bg-slate-800 text-white font-black text-xs shadow-md cursor-pointer flex items-center gap-2">
                        <i class="ph-bold ph-paper-plane-tilt text-base"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan & Kirim ke Ka Prod'"></span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- MODAL QR CODE P2P HP CAMERA -->
    <div x-show="openQrModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.outside="openQrModal = false" class="bg-white rounded-3xl border border-slate-300 shadow-2xl w-full max-w-sm p-6 text-center">
            <h3 class="text-sm font-black text-black">Jepret Lembar SPK dari HP</h3>
            <p class="text-xs text-slate-500 mt-1">Scan QR code ini langsung dari kamera smartphone:</p>
            
            <div class="my-4 p-4 bg-slate-50 border-2 border-dashed border-indigo-200 rounded-2xl flex flex-col items-center justify-center">
                <div id="qrcodeCanvas" class="p-2 bg-white rounded-xl shadow-xs"></div>
                <span class="mt-2 text-xs font-black text-indigo-700" x-text="'Kode Sesi: ' + sessionToken"></span>
            </div>

            <button type="button" @click="openQrModal = false" class="w-full py-2 bg-slate-100 rounded-xl text-xs font-black text-slate-700">Tutup</button>
        </div>
    </div>

    <!-- SCRIPT LOGIC JAVASCRIPT & ALPINE -->
    <script>
    function orderForm() {
        return {
            selectedCabangName: "Bojonegoro (Pusat)",
            openQrModal: false,
            isSubmitting: false,
            recordingField: null,
            spkPreview: null,
            fileMeta: '',
            sessionToken: 'SO' + Math.random().toString(36).substring(2, 7).toUpperCase(),
            peer: null,

            form: {
                cabang_id: "{{ $cabangs->first()->id ?? 1 }}",
                no_so: "{{ $noSo }}",
                customer_name: "",
                order_title: "",
                qty: 35,
                satuan: "pcs",
                fabric_type: "",
                order_type: "Atasan Fullprint",
                target_deadline: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                notes: "",
                spk_photo_base64: null,
            },

            // WebRTC P2P Receiver dari Kamera Smartphone
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
                                Swal.fire({ title: 'Foto SPK Terpasang!', icon: 'success', timer: 2000, confirmButtonColor: '#00bf63' });
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

            // Voice to Text (Web Speech API)
            listenVoice(fieldName) {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SpeechRecognition) {
                    alert("Browser Anda belum mendukung input suara. Gunakan Google Chrome.");
                    return;
                }

                if (this.recordingField === fieldName) {
                    this.recordingField = null;
                    return;
                }

                const recognition = new SpeechRecognition();
                recognition.lang = 'id-ID';
                recognition.interimResults = false;

                recognition.onstart = () => { this.recordingField = fieldName; };
                recognition.onresult = (event) => {
                    const speechResult = event.results[0][0].transcript;
                    this.form[fieldName] = speechResult;
                    this.recordingField = null;
                };
                recognition.onerror = () => { this.recordingField = null; };
                recognition.onend = () => { this.recordingField = null; };

                recognition.start();
            },

            // Paste Handler (Ctrl + V dari WhatsApp Web)
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
                        else if (h > max) { width = (w * max) / h; h = max; }
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

            updateCabangName() {
                const sel = document.querySelector('select[x-model="form.cabang_id"]');
                this.selectedCabangName = sel.options[sel.selectedIndex].text;
            },

            // Submit Data ke Backend Laravel via AJAX JSON
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
                            window.location.reload();
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
    <script>
        function fabricSearch() {
            return {
                isOpen: false,
                searchQuery: '',
                highlightIndex: 0,
                items: [],

                search() {
                    fetch(`/api/fabrics/search?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(res => res.json())
                        .then(data => {
                            this.items = data;
                            this.highlightIndex = 0;
                        });
                },

                navigate(step) {
                    if (!this.isOpen || this.items.length === 0) return;
                    this.highlightIndex = (this.highlightIndex + step + this.items.length) % this.items.length;
                },

                selectCurrent() {
                    if (this.isOpen && this.items.length > 0) {
                        this.selectItem(this.items[this.highlightIndex]);
                    }
                },

                selectItem(item) {
                    this.searchQuery = item.name;
                    // Kirim nilai nama bahan terpilih ke form utama
                    orderFormInstance.form.fabric_type = item.name;
                    this.isOpen = false;
                }
            };
        }

    </script>
</body>
</html>
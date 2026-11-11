<div
    x-data="spkUploader({
        state: $wire.entangle('{{ $getStatePath() }}'),
        hostingUrl: '{{ config('app.url') }}'
    })"
    x-init="initReceiver()"
    class="p-4 bg-slate-50 dark:bg-gray-900/50 rounded-2xl border border-slate-300 dark:border-gray-700 space-y-3"
>
    <!-- Header Area Upload -->
    <div class="flex items-center justify-between">
        <label class="block text-xs font-black uppercase tracking-wider text-slate-800 dark:text-gray-200 flex items-center gap-2">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Foto Formulir SPK Fisik (Ttd Customer)
        </label>

        <!-- Tombol Buka Modal QR -->
        <button
            type="button"
            @click="openQrModal = true; renderQr();"
            class="px-3 py-1.5 bg-primary-50 dark:bg-primary-950/50 hover:bg-primary-100 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800 rounded-xl text-xs font-black flex items-center gap-1.5 shadow-xs transition-all cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            <span>Foto Lewat HP (Scan QR)</span>
        </button>
    </div>

    <!-- Dropzone / Preview Area -->
    <div
        @click="$refs.fileInput.click()"
        @paste.window="handlePaste($event)"
        class="relative border-2 border-dashed rounded-xl p-4 transition-all flex flex-col items-center justify-center text-center cursor-pointer bg-white dark:bg-gray-800"
        :class="previewUrl ? 'border-emerald-500 bg-emerald-50/10' : 'border-slate-300 dark:border-gray-600 hover:border-primary-500'"
    >
        <input type="file" x-ref="fileInput" accept="image/*" class="hidden" @change="handleFile($event)" />

        <!-- Jika Belum Ada Foto -->
        <template x-if="!previewUrl">
            <div class="py-3 flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mb-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-xs font-black text-slate-700 dark:text-gray-200">
                    Klik upload file, atau <span class="text-primary-600 underline">Paste (Ctrl + V)</span> langsung dari WhatsApp Web
                </p>
                <p class="text-[10px] text-slate-500 mt-0.5">Otomatis terkompresi ke WebP (&le; 250 KB)</p>
            </div>
        </template>

        <!-- Jika Sudah Ada Foto -->
        <template x-if="previewUrl">
            <div class="w-full flex items-center justify-between gap-4" @click.stop>
                <div class="flex items-center gap-3">
                    <img :src="previewUrl" class="w-14 h-14 object-cover rounded-xl border border-slate-200 shadow-xs" />
                    <div class="text-left">
                        <span class="text-xs font-black text-emerald-600 uppercase flex items-center gap-1">
                            ✓ Foto SPK Siap Tersimpan (WebP)
                        </span>
                        <p class="text-[11px] text-slate-500" x-text="fileMeta"></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="$refs.fileInput.click()" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">Ganti</button>
                    <button type="button" @click="hapusFoto()" class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- MODAL POPUP QR CODE (P2P PEERJS) -->
    <div x-show="openQrModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.outside="openQrModal = false" class="bg-white dark:bg-gray-900 rounded-3xl p-6 max-w-sm w-full text-center border border-slate-200 dark:border-gray-700 shadow-2xl">
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Jepret Langsung Pakai HP</h3>
            <p class="text-xs text-slate-500 mt-1">Scan QR di bawah, kamera HP akan langsung terbuka:</p>

            <div class="my-4 p-3 bg-slate-50 dark:bg-gray-800 rounded-2xl flex flex-col items-center justify-center border border-dashed border-slate-300">
                <div id="filamentQrContainer" class="p-2 bg-white rounded-xl shadow-xs"></div>
                <span class="mt-2 text-[11px] font-bold text-primary-600" x-text="'Kode Sesi: ' + sessionToken"></span>
            </div>

            <button type="button" @click="openQrModal = false" class="w-full py-2 bg-slate-100 dark:bg-gray-800 text-slate-700 dark:text-gray-300 rounded-xl text-xs font-black">
                Tutup
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
<script>
function spkUploader(config) {
    return {
        previewUrl: null,
        fileMeta: '',
        openQrModal: false,
        sessionToken: 'SO' + Math.random().toString(36).substring(2, 7).toUpperCase(),
        peer: null,

        initReceiver() {
            try {
                this.peer = new Peer(this.sessionToken, {
                    config: { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] }
                });

                this.peer.on('connection', (conn) => {
                    conn.on('data', (data) => {
                        if (data && data.type === 'SPK_PHOTO') {
                            this.previewUrl = data.imageData;
                            config.state = data.imageData; // Kirim biner/Base64 ke Livewire Filament
                            this.fileMeta = 'Diterima via Kamera HP';
                            this.openQrModal = false;
                        }
                    });
                });
            } catch (e) {
                console.warn(e);
            }
        },

        renderQr() {
            this.$nextTick(() => {
                const container = document.getElementById('filamentQrContainer');
                if (container) {
                    container.innerHTML = '';
                    const targetUrl = `${config.hostingUrl}/admin_input_order.html?spk_peer=${this.sessionToken}`;
                    new QRCode(container, { text: targetUrl, width: 160, height: 160 });
                }
            });
        },

        handleFile(e) {
            const file = e.target.files[0];
            if (file) this.compress(file);
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
                    this.previewUrl = webpData;
                    config.state = webpData; // Terhubung ke form Livewire
                    this.fileMeta = `${(file.size / 1024).toFixed(0)} KB → WebP ${(webpData.length * 0.75 / 1024).toFixed(0)} KB`;
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        },

        hapusFoto() {
            this.previewUrl = null;
            config.state = null;
            this.$refs.fileInput.value = '';
        }
    };
}
</script>
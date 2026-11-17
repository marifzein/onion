<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jepret SPK - ONION CREATIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col items-center justify-between p-5 text-center font-sans">
    
    <!-- Header Minimalis -->
    <div class="mt-2">
        <div id="statusBadge" class="inline-block px-3 py-1 bg-amber-500/20 text-amber-400 font-black rounded-full text-[11px] uppercase mb-1.5 tracking-wider">
            ● Menghubungkan ke PC...
        </div>
        <h1 class="text-lg font-black uppercase tracking-tight text-white">Onion Creative</h1>
        <p class="text-[11px] text-slate-400">Pindai & Kirim SPK Instan</p>
    </div>

    <!-- Area Preview / Panduan -->
    <div class="w-full max-w-xs h-72 border border-slate-800 rounded-3xl flex flex-col items-center justify-center p-3 bg-slate-900 shadow-inner overflow-hidden relative my-auto">
        <img id="mobileImgPreview" class="hidden w-full h-full object-contain rounded-2xl" />
        <div id="mobilePlaceholder" class="text-slate-400 text-xs flex flex-col items-center p-4 space-y-2">
            <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <span class="font-bold text-slate-300">Siap Mengambil Foto SPK</span>
            <span class="text-[10px] text-slate-500">Tekan tombol di bawah untuk membuka kamera</span>
        </div>
    </div>

    <!-- Tombol Aksi Utama -->
    <div class="w-full max-w-xs space-y-2.5 mb-2">
        <!-- Input file tersembunyi dengan capture environment agar langsung buka kamera belakang HP -->
        <input type="file" id="mobileCamInput" accept="image/*" capture="environment" class="hidden" />
        
        <button id="btnBukaKamera" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-black rounded-2xl shadow-lg text-base cursor-pointer transition-all flex items-center justify-center gap-2">
            <span>📸 Buka Kamera & Jepret</span>
        </button>
        
        <button id="btnKirimPc" class="hidden w-full py-4 bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-black rounded-2xl shadow-lg text-base cursor-pointer transition-all flex items-center justify-center gap-2">
            <span>🚀 Kirim ke Layar PC</span>
        </button>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const peerTargetId = urlParams.get('spk_peer');

        if (!peerTargetId) {
            alert("Sesi P2P tidak ditemukan. Silakan scan ulang QR Code dari komputer.");
        }

        const mobilePeer = new Peer({
            config: {
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' },
                    { urls: 'stun:global.stun.twilio.com:3478' }
                ]
            }
        });

        let mobileConn = null;
        let currentBlobUrl = null;

        mobilePeer.on('open', () => {
            mobileConn = mobilePeer.connect(peerTargetId);
        });

        mobilePeer.on('connection', (conn) => {
            mobileConn = conn;
        });

        setTimeout(() => {
            const badge = document.getElementById('statusBadge');
            badge.className = "inline-block px-3 py-1 bg-emerald-500/20 text-emerald-400 font-black rounded-full text-[11px] uppercase mb-1.5 tracking-wider";
            badge.innerText = "● Terkoneksi ke PC";
        }, 1200);

        const camInput = document.getElementById('mobileCamInput');
        const btnBukaKamera = document.getElementById('btnBukaKamera');

        btnBukaKamera.onclick = () => {
            camInput.click();
        };

        camInput.onchange = (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const maxDim = 1200;
                    let w = img.width, h = img.height;
                    if (w > h && w > maxDim) { h = (h * maxDim) / w; w = maxDim; }
                    else if (h > maxDim) { w = (w * maxDim) / h; h = maxDim; }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    currentBlobUrl = canvas.toDataURL('image/webp', 0.75);

                    const prevImg = document.getElementById('mobileImgPreview');
                    prevImg.src = currentBlobUrl;
                    prevImg.classList.remove('hidden');
                    document.getElementById('mobilePlaceholder').classList.add('hidden');
                    
                    // Ubah teks tombol kamera jadi "Ulangi Jepret" & munculkan tombol kirim
                    btnBukaKamera.innerText = "🔄 Ulangi Jepret";
                    btnBukaKamera.className = "w-full py-3 bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-300 font-bold rounded-2xl text-sm cursor-pointer transition-all";
                    document.getElementById('btnKirimPc').classList.remove('hidden');
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        };

        document.getElementById('btnKirimPc').onclick = () => {
            if (mobileConn && currentBlobUrl) {
                const btnKirim = document.getElementById('btnKirimPc');
                btnKirim.innerText = "Mengirim...";
                btnKirim.disabled = true;

                mobileConn.send({
                    type: 'SPK_PHOTO',
                    imageData: currentBlobUrl,
                    fileName: 'SPK_HP_' + Date.now() + '.webp'
                });

                setTimeout(() => {
                    alert("Sukses! Foto berhasil dikirim ke komputer.");
                    window.close();
                }, 500);
            } else {
                alert("Koneksi ke PC belum siap. Pastikan komputer masih membuka form input SO.");
            }
        };
    </script>
</body>
</html>
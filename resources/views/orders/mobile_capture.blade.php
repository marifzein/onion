<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Jepret SPK - ONION CREATIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-slate-950 text-white h-[100dvh] w-screen overflow-hidden flex flex-col justify-between items-center p-3 text-center font-sans select-none">
    
    <!-- Header Sangat Kompak -->
    <div class="shrink-0 pt-1">
        <div id="statusBadge" class="inline-block px-2 py-0.5 bg-amber-500/20 text-amber-400 font-black rounded-full text-[9px] uppercase mb-0.5 tracking-wider">
            ● Menghubungkan ke PC...
        </div>
        <h1 class="text-sm font-black uppercase tracking-tight text-white leading-none">Onion Creative</h1>
        <p class="text-[9px] text-slate-400">Pindai & Kirim SPK Instan</p>
    </div>

    <!-- Area Preview (Fleksibel & Pas di Layar) -->
    <div class="w-full max-w-xs flex-1 max-h-[40vh] border border-slate-800 rounded-2xl flex flex-col items-center justify-center p-2 bg-slate-900 shadow-inner overflow-hidden relative my-1">
        <img id="mobileImgPreview" class="hidden w-full h-full object-contain rounded-xl" />
        <div id="mobilePlaceholder" class="text-slate-400 text-xs flex flex-col items-center justify-center p-1.5 space-y-1">
            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center">
                <i class="ph-bold ph-camera text-xl text-slate-400"></i>
            </div>
            <span class="font-bold text-slate-300 text-[11px]">Siap Mengambil Foto SPK</span>
            <span class="text-[9px] text-slate-500">Tekan tombol di bawah untuk membuka kamera</span>
        </div>
    </div>

    <!-- Tombol Aksi Utama (Dijamin Muncul Sepenuhnya Tanpa Ngumpet) -->
    <div class="w-full max-w-xs shrink-0 space-y-1.5 pb-2">
        <input type="file" id="mobileCamInput" accept="image/*" capture="environment" class="hidden" />
        
        <button id="btnBukaKamera" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-black rounded-xl shadow-md text-xs cursor-pointer transition-all flex items-center justify-center gap-2">
            <i class="ph-bold ph-camera text-base"></i>
            <span>Buka Kamera & Jepret</span>
        </button>
        
        <button id="btnKirimPc" class="hidden w-full py-3 bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-black rounded-xl shadow-md text-xs cursor-pointer transition-all flex items-center justify-center gap-2">
            <i class="ph-bold ph-paper-plane-tilt text-base"></i>
            <span>Kirim ke Layar PC</span>
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
            badge.className = "inline-block px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-black rounded-full text-[9px] uppercase mb-0.5 tracking-wider";
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
                    
                    btnBukaKamera.innerHTML = '<i class="ph-bold ph-arrow-counter-clockwise text-sm"></i> Ulangi Jepret';
                    btnBukaKamera.className = "w-full py-2.5 bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-300 font-bold rounded-xl text-xs cursor-pointer transition-all flex items-center justify-center gap-1.5";
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
                }, 400);
            } else {
                alert("Koneksi ke PC belum siap. Pastikan komputer masih membuka form input SO.");
            }
        };
    </script>
</body>
</html>
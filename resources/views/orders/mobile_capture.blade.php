<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jepret SPK - ONION CREATIVE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/peerjs@1.5.4/dist/peerjs.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex flex-col items-center justify-between p-6 text-center font-sans">
    <div class="mt-4">
        <div id="statusBadge" class="inline-block px-3 py-1 bg-amber-500/20 text-amber-400 font-black rounded-full text-xs uppercase mb-2">
            ● Menghubungkan ke PC...
        </div>
        <h1 class="text-xl font-black uppercase text-amber-400">Onion Creative</h1>
        <p class="text-xs text-slate-300 mt-1">Kamera Jepret Lembar SPK</p>
    </div>

    <div class="w-full max-w-xs h-72 border-2 border-dashed border-slate-600 rounded-2xl flex flex-col items-center justify-center p-2 bg-slate-800/50 overflow-hidden relative">
        <img id="mobileImgPreview" class="hidden w-full h-full object-contain rounded-xl" />
        <div id="mobilePlaceholder" class="text-slate-400 text-xs flex flex-col items-center p-4">
            <svg class="w-12 h-12 mb-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Arahkan kamera ke lembar fisik SPK</span>
        </div>
    </div>

    <div class="w-full max-w-xs space-y-3 mb-6">
        <input type="file" id="mobileCamInput" accept="image/*" capture="environment" class="hidden" />
        <button id="btnBukaKamera" class="w-full py-4 bg-emerald-600 active:scale-95 text-white font-black rounded-2xl shadow-lg text-base cursor-pointer">
            📷 Buka Kamera & Jepret
        </button>
        <button id="btnKirimPc" class="hidden w-full py-3.5 bg-indigo-600 active:scale-95 text-white font-black rounded-2xl shadow-lg text-sm cursor-pointer">
            🚀 Kirim Langsung ke Layar PC
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
            badge.className = "inline-block px-3 py-1 bg-emerald-500/20 text-emerald-400 font-black rounded-full text-xs uppercase mb-2";
            badge.innerText = "● Terkoneksi ke PC";
        }, 1500);

        document.getElementById('btnBukaKamera').onclick = () => {
            document.getElementById('mobileCamInput').click();
        };

        document.getElementById('mobileCamInput').onchange = (e) => {
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
                    document.getElementById('btnKirimPc').classList.remove('hidden');
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        };

        document.getElementById('btnKirimPc').onclick = () => {
            if (mobileConn && currentBlobUrl) {
                document.getElementById('btnKirimPc').innerText = "Mengirim...";
                mobileConn.send({
                    type: 'SPK_PHOTO',
                    imageData: currentBlobUrl,
                    fileName: 'SPK_HP_' + Date.now() + '.webp'
                });
                alert("Sukses! Foto langsung masuk ke layar form PC Anda.");
                setTimeout(() => window.close(), 1000);
            } else {
                alert("Koneksi ke PC belum stabil. Coba jepret ulang atau pastikan PC masih membuka halaman form.");
            }
        };
    </script>
</body>
</html>
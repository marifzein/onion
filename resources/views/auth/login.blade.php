<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ONION CREATIVE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{--  tailwind 3 --}}
    <script src="{{ asset('js/tailwindcss.js') }}"></script>

    
    <!-- Phosphor Icons CDN Stable -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Alpine.js Lokal (Tetap gunakan defer) -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>

    <!-- SweetAlert2 Lokal -->
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>

    <!-- Driver.js Assets (CSS & JS via CDN) -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css"/>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script> --}}
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    <script src="{{ asset('js/driver.js.iife.js') }}"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* 1. Tombol 'Lanjut' dan 'Selesai' (Warna Emerald / Biru bersih ala TataKas) */
        .driver-popover .driver-popover-next-btn,
        .driver-popover .driver-popover-done-btn {
            background-color: #10b981 !important; /* Emerald 500 */
            color: #ffffff !important;
            border: none !important;
            text-shadow: none !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            padding: 6px 14px !important;
            border-radius: 8px !important;
            transition: background-color 0.2s ease !important;
        }

        .driver-popover .driver-popover-next-btn:hover,
        .driver-popover .driver-popover-done-btn:hover {
            background-color: #059669 !important; /* Emerald 600 */
        }

        /* 2. Tombol 'Kembali' (Warna Slate Abu-abu Halus) */
        .driver-popover .driver-popover-prev-btn {
            background-color: #f1f5f9 !important; /* Slate 100 */
            color: #475569 !important;            /* Slate 600 */
            border: 1px solid #cbd5e1 !important; /* Slate 300 */
            font-weight: 600 !important;
            font-size: 12px !important;
            padding: 6px 14px !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }

        .driver-popover .driver-popover-prev-btn:hover {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        /* 3. Popover Card & Judul agar lebih membulat dan modern */
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
</head>
<body class="bg-[#F4F6FB] min-h-screen flex items-center justify-center p-4 md:p-6 font-sans select-none">

    <!-- Container Utama Split Layout -->
    <div class="bg-white rounded-[2.5rem] border border-slate-300 shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">
        
        <!-- SISI KIRI: Banner Alur Proses -->
        <div id="awalun" class="w-full md:w-1/2 h-64 md:h-auto relative bg-slate-950 flex flex-col justify-end overflow-hidden">
            <img 
                src="/img/login.webp" 
                alt="Alur Proses Onion Creative" 
                class="absolute inset-0 w-full h-full object-cover object-center opacity-90"
                onerror="this.src='/img/onion_logo.jpg'"
            >
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/10 to-transparent"></div>
            <div class="relative z-10 p-6 md:p-10 space-y-4 md:space-y-6 mt-auto"></div>
        </div>

        <!-- SISI KANAN: Form Input Login -->
        <div x-data="{ showPassword: false }" class="w-full md:w-1/2 p-6 md:p-12 flex flex-col justify-center bg-white relative">
            
            <!-- Header Judul Form -->
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    Log in
                </h1>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Manajemen Antrean & Alur Produksi.</p>
            </div>

            <!-- Notifikasi Error dari Validation Laravel -->
            @if($errors->any())
                <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="ph-bold ph-warning-circle text-base shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form Autentikasi -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1">Nama Akun / Username</label>
                    <div id="step-username" class="relative">
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus
                            placeholder="contoh: admin / kaprod / jahit / qc / bahan"
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold outline-none focus:border-black focus:bg-white focus:ring-1 focus:ring-black/10 transition-all text-black"
                        >
                        <i class="ph-bold ph-user absolute left-3.5 top-3.5 text-slate-400 text-base"></i>
                    </div>
                </div>

                <!-- Input Password dengan Toggle Mata -->
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1">Password</label>
                    <div id="step-password" class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="passwordInput"
                            name="password" 
                            required 
                            placeholder="Masukkan password..."
                            class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold outline-none focus:border-black focus:bg-white focus:ring-1 focus:ring-black/10 transition-all text-black"
                        >
                        <i class="ph-bold ph-lock-key absolute left-3.5 top-3.5 text-slate-400 text-base"></i>
                        
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute right-3.5 top-3 text-slate-400 hover:text-black transition-colors focus:outline-none cursor-pointer"
                            title="Tampilkan / Sembunyikan Password"
                        >
                            <i class="ph-bold text-lg" :class="showPassword ? 'ph-eye' : 'ph-eye-slash'"></i>
                        </button>
                    </div>
                </div>

                <!-- Checkbox Ingat Saya -->
                <div class="flex items-center justify-between pt-0.5 text-xs">
                    <label id="step-remember" class="flex items-center gap-2 font-bold text-slate-600 cursor-pointer hover:text-black select-none">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 rounded text-black border-slate-300 focus:ring-black accent-black cursor-pointer"
                        >
                        <span>Ingat saya</span>
                    </label>
                </div>
                
                <!-- Tombol Submit Form Login -->
                <button 
                    id="step-submit"
                    type="submit" 
                    class="w-full py-3.5 bg-blue-400 hover:bg-blue-500 text-white rounded-xl text-medium font-semibold shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
                >
                    <i class="ph-bold ph-sign-in text-base text-slate-100"></i>
                    <span>Log In</span>
                </button>

                <div class="relative my-6 text-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <span class="relative bg-white px-2.5 text-[10px] text-slate-400 uppercase tracking-wider">Or with</span>
                </div>

                <div class="flex justify-center space-x-3">
                    <button type="button" class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-sm shadow hover:opacity-90 transition">
                        <i class="ph-bold ph-google-logo"></i>
                    </button>
                    <button type="button" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm shadow hover:opacity-90 transition">
                        <i class="ph-bold ph-facebook-logo"></i>
                    </button>
                    <button type="button" class="w-8 h-8 rounded-full bg-sky-400 text-white flex items-center justify-center text-sm shadow hover:opacity-90 transition">
                        <i class="ph-bold ph-x-logo"></i>
                    </button>
                </div>

            </form>
        </div>

    </div>

    <!-- Script Driver.js & Floating Button "?" Persis TataKas -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.driver !== 'undefined') {
                const driverObj = window.driver.js.driver({
                    showProgress: true,
                    allowClose: true,
                    overlayColor: 'rgba(0, 0, 0, 0.75)',
                    nextBtnText: 'Lanjut &rarr;',
                    prevBtnText: '&larr; Kembali',
                    doneBtnText: 'Selesai',
                    steps: [
                        { 
                            element: '#awalun',
                            popover: { 
                                title: 'Selamat Datang di Onion Creative!', 
                                description: 'Ini adalah portal manajemen antrean & alur produksi jersey printing. Mari kita kenali bagian utamanya.', 
                                side: "right", 
                                align: 'start' 
                            } 
                        },
                        { 
                            element: '#step-username',
                            popover: { 
                                title: 'Input Username Divisi', 
                                description: 'Masukkan nama akun divisi Anda (contoh: admin, kaprod, bahan, print, jahit, qc, atau finishing).', 
                                side: "top", 
                                align: 'start' 
                            } 
                        },
                        { 
                            element: '#step-password',
                            popover: { 
                                title: 'Input Password', 
                                description: 'Masukkan kata sandi akun divisi Anda. Gunakan ikon mata di sebelah kanan untuk melihat ketikan password.', 
                                side: "top", 
                                align: 'start' 
                            } 
                        },
                        { 
                            element: '#step-remember',
                            popover: { 
                                title: 'Ingat Saya', 
                                description: 'Centang opsi ini agar sesi login divisi tetap tersimpan di perangkat ini.', 
                                side: "top", 
                                align: 'start' 
                            } 
                        },
                        { 
                            element: '#step-submit',
                            popover: { 
                                title: 'Tombol Log In', 
                                description: 'Setelah username dan password terisi benar, klik tombol ini untuk masuk ke ruang kerja divisi Anda.', 
                                side: "bottom", 
                                align: 'start' 
                            } 
                        }
                    ]
                });

                window.jalankanTour = () => {
                    driverObj.drive();
                };

                // Jalankan otomatis sekali untuk user baru
                const hasSeenTour = localStorage.getItem('onion_login_tour_seen');
                if (!hasSeenTour) {
                    setTimeout(() => {
                        window.jalankanTour();
                        localStorage.setItem('onion_login_tour_seen', 'true');
                    }, 600);
                }

                // Pasang Style Keyframe Animasi Berdenyut Oranye
                if (!document.getElementById('pulse-tour-style')) {
                    const style = document.createElement('style');
                    style.id = 'pulse-tour-style';
                    style.innerHTML = `
                        @keyframes pulseOrange {
                            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.6); }
                            70% { transform: scale(1.06); box-shadow: 0 0 0 12px rgba(249, 115, 22, 0); }
                            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
                        }
                    `;
                    document.head.appendChild(style);
                }

                // Tombol Bantuan "?" Melayang Oranye & Animasi Denyut di Pojok Kanan Bawah
                const btn = document.createElement('button');
                btn.innerHTML = '?';
                btn.title = 'Panduan Login';
                Object.assign(btn.style, {
                    position: 'fixed',
                    bottom: '24px',
                    right: '24px',
                    width: '42px',
                    height: '42px',
                    backgroundColor: '#f97316',
                    color: '#ffffff',
                    border: 'none',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    fontSize: '18px',
                    fontWeight: 'bold',
                    zIndex: '9999',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    boxShadow: '0 4px 14px rgba(249, 115, 22, 0.45)',
                    transition: 'all 0.25s ease-in-out',
                    animation: 'pulseOrange 2s infinite'
                });

                btn.onmouseenter = () => {
                    btn.style.backgroundColor = '#ea580c';
                    btn.style.transform = 'scale(1.12)';
                };
                btn.onmouseleave = () => {
                    btn.style.backgroundColor = '#f97316';
                    btn.style.transform = 'scale(1)';
                };

                btn.onclick = window.jalankanTour;
                document.body.appendChild(btn);
            }
        });
    </script>

</body>
</html>
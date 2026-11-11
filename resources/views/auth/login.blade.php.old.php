<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ONION CREATIVE</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F4F6FB] min-h-screen flex items-center justify-center p-4 md:p-6 font-sans select-none">

    <!-- Container Utama Split Layout -->
    <div class="bg-white rounded-[2.5rem] border border-slate-300 shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">
        
        <!-- SISI KIRI: Banner Alur Proses dengan Overlay Text & Gradient Shadow -->
        <div class="w-full md:w-1/2 h-64 md:h-auto relative bg-slate-950 flex flex-col justify-end overflow-hidden">
            <!-- Foto Alur Proses (Full Cover) -->
            <img 
                src="/img/login.webp" 
                alt="Alur Proses Onion Creative" 
                class="absolute inset-0 w-full h-full object-cover object-center opacity-90"
                onerror="this.src='/img/onion_logo.jpg'"
            >
            
            <!-- Overlay Gradasi Hitam (Membuat Gambar Lebih Gelap & Teks Terbaca) -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/10 to-transparent"></div>
            {{-- <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent hidden md:block"></div> --}}

            <!-- Teks Informasi & Brand Overlay -->
            <div class="relative z-10 p-6 md:p-10 space-y-4 md:space-y-6 mt-auto">
                <!-- Branding Mini di Atas -->
                {{-- <div class="flex items-center gap-3">
                    <div class="w-14  bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center text-white shadow-lg shrink-0">
                        <img 
                            src="/img/onion_logo.jpg" 
                            alt="Logo" 
                            class="w-full h-full object-contain rounded-xl"
                            onerror="this.src='https://placehold.co/100x100/000000/ffffff?text=O'"
                        >
                    </div>
                    <div>
                        <h2 class="text-lg md:text-xl font-extrabold tracking-tight text-white uppercase leading-none font-sans">
                            ONION CREATIVE
                        </h2>
                        <span class="text-[9px] font-extrabold tracking-widest text-emerald-400 uppercase">
                            Workshop & Vendor
                        </span>
                    </div>
                </div> --}}

                <!-- Tagline Besar -->
                {{-- <div class="space-y-1">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-200">Workflow System</p>
                    <h3 class="text-xl md:text-2xl font-black text-emerald-400 leading-tight">
                        Manajemen Antrean <br class="hidden md:inline">& Alur Produksi
                    </h3>
                </div>

                <!-- Footer Informasi Tahun -->
                <div class="pt-4 border-t border-white/10 flex items-center justify-between text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                    <span>© 2026 ONION CREATIVE</span>
                    <span class="hidden sm:inline">v1.2 Active</span>
                </div> --}}
            </div>
        </div>

        <!-- SISI KANAN: Form Input Login -->
        <div class="w-full md:w-1/2 p-6 md:p-12 flex flex-col justify-center">
            
            <!-- Header Judul Form -->
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-slate-500 tracking-tight flex items-center gap-2">
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
                    <label class="block text-xs font-black uppercase text-slate-500 mb-1 tracking-wider">Nama Akun / Username</label>
                    <div class="relative">
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

                <!-- Input Password dengan Toggle Mata & Ubah Tipe Dinamis -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1 tracking-wider">Password</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            required 
                            placeholder="Masukkan password..."
                            class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold outline-none focus:border-black focus:bg-white focus:ring-1 focus:ring-black/10 transition-all text-black"
                        >
                        <i class="ph-bold ph-lock-key absolute left-3.5 top-3.5 text-slate-400 text-base"></i>
                        
                        <!-- Tombol Ikon Mata di Ujung Kanan Input -->
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

                <!-- Fitur 'Remember Me' & Opsi Lupa Password -->
                <div class="flex items-center justify-between pt-1 text-xs">
                    <label class="flex items-center gap-2 font-bold text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-black border-slate-300 focus:ring-black accent-black">
                        Ingat saya
                    </label>
                </div>

                <!-- Tombol Submit Form Login -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer mt-2"
                >
                    <i class="ph-bold ph-sign-in text-base text-emerald-400"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Footer Teks -->
            {{-- <div class="mt-6 pt-4 border-t border-slate-200 text-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    1 Divisi &bull; 1 Akun Tim Bersama
                </span>
            </div> --}}
        </div>

    </div>

</body>
</html>
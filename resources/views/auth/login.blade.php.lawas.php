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
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
            
            <!-- Overlay Gradasi Hitam -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/10 to-transparent"></div>
            <div class="relative z-10 p-6 md:p-10 space-y-4 md:space-y-6 mt-auto"></div>
        </div>

        <!-- SISI KANAN: Form Input Login -->
        <div x-data="{ showPassword: false }" class="w-full md:w-1/2 p-6 md:p-12 flex flex-col justify-center">
            
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
                    <label class="block text-sm font-semibold text-slate-600 mb-1 ">Nama Akun / Username</label>
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

                <!-- Input Password dengan Toggle Mata & Tipe Dinamis -->
                <div>
                    <label class="block text-sm font-semibold  text-slate-600 mb-1 ">Password</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="passwordInput"
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

                <!-- Checkbox Opsi: Show Password & Ingat Saya -->
                <div class="flex items-center justify-between pt-0.5 text-xs">
                    <!-- Checkbox Show Password Sesuai Permintaan -->
                    {{-- <label class="flex items-center gap-2 font-bold text-slate-600 cursor-pointer hover:text-black select-none">
                        <input 
                            type="checkbox" 
                            x-model="showPassword"
                            class="w-4 h-4 rounded text-black border-slate-300 focus:ring-black accent-black cursor-pointer"
                        >
                        <span>Show password</span>
                    </label> --}}

                    <!-- Checkbox Ingat Saya -->
                    <label class="flex items-center gap-2 font-bold text-slate-600 cursor-pointer hover:text-black select-none">
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

</body>
</html>
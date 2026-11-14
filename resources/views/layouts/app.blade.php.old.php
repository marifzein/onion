<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ONION CREATIVE - Manajemen Antrean & Produksi' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Alpine.js -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    {{-- driver.js tour guide --}}
    <link rel="stylesheet" href="{{ asset('css/driver.css') }}">
    <script src="{{ asset('js/driver.js.iife.js') }}"></script>
</head>
<body class="bg-[#F4F6FB] text-slate-800 antialiased h-screen w-screen flex flex-col font-sans select-none overflow-hidden">

    @php
        $user = auth()->user();
        $role = $user->role ?? 'admin';
        $canUpdateStatus = !in_array($role, ['owner', 'admin']);
    @endphp

    <!-- ==================== TOP BAR & HEADER ==================== -->
    <header class="bg-white border-b border-slate-300 px-3 md:px-6 py-2.5 flex items-center justify-between shrink-0 shadow-xs z-30">
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2.5">
                <div class="h-8 md:h-12 w-auto flex items-center justify-center text-black shrink-0">
                    <img
                        src="/img/onion_logo.jpg"
                        alt="ONION CREATIVE Logo"
                        class="h-full w-auto object-contain rounded-lg"
                        onerror="this.src='https://placehold.co/100x100/000000/ffffff?text=ONION'"
                    />
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] md:text-[11px] px-1.5 md:px-2 py-0.5 rounded-full bg-blue-500 text-white font-extrabold uppercase tracking-wider">
                            Display
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-xs font-bold text-blue-500 tracking-wide mt-0.5">
                        Manajemen Antrean & Produksi
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header: Clock & User Profile with Dropdown -->
        <div class="flex items-center gap-2 md:gap-4">
            <div class="hidden md:flex items-center gap-2 bg-slate-100 border border-slate-300 px-3 py-1.5 rounded-xl text-xs font-black text-black">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                <span class="font-mono text-sm tracking-wide">{{ now()->translatedFormat('l, d M Y') }}</span>
            </div>

            <!-- Dropdown Menu Profil -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <!-- Tombol Trigger Profil -->
                <button @click="open = !open" class="flex items-center gap-2 pl-2 border-l border-slate-300 cursor-pointer focus:outline-none group">
                    
                    <!-- Teks Nama & Role: Muncul di Desktop, dan versi ringkas/rapi di Mobile -->
                    <div class="text-right">
                        <p class="text-xs md:text-sm font-black text-black leading-tight group-hover:text-blue-600 transition truncate max-w-[110px] sm:max-w-none">{{ $user->name ?? 'Admin' }}</p>
                        <span class="text-[10px] md:text-[11px] text-slate-500 font-extrabold uppercase block leading-none mt-0.5">{{ $role }}</span>
                    </div>

                    <!-- Avatar / Inisial -->
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded-full ring-2 ring-black bg-slate-900 text-white flex items-center justify-center font-black text-xs md:text-sm shrink-0 uppercase select-none shadow-xs group-hover:bg-blue-600 transition overflow-hidden">       
                        @if(!empty($user->avatar))
                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name ?? 'Admin', 0, 1)) }}
                        @endif
                    </div>
                </button>

                <!-- Popup Menu Dropdown -->
                <div 
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50"
                    style="display: none;"
                >
                    <div class="px-4 py-1.5 mb-1">
                        <p class="text-[11px] text-slate-400 font-medium">Menu Akun</p>
                    </div>

                    <!-- Link Ganti Password -->
                    <a href="{{ route('password.change') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">
                        <i class="ph-bold ph-lock text-base text-slate-400"></i>
                        <span>Ganti Password</span>
                    </a>

                    <!-- Link Ganti Avatar -->
                    <a href="{{ route('avatar.change') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition">
                        <i class="ph-bold ph-user-circle text-base text-slate-400"></i>
                        <span>Ganti Foto Avatar</span>
                    </a>

                    <div class="border-t border-slate-100 my-1"></div>

                    <!-- Form Logout -->
                    <form id="dropdown-logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-500 hover:bg-rose-50 transition text-left cursor-pointer">
                            <i class="ph-bold ph-sign-out text-base text-rose-400"></i>
                            <span>Logout Aplikasi</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN WRAPPER ==================== -->
    <div class="flex-1 flex overflow-hidden">

        <!-- ==================== SIDE MENU PUTIH ==================== -->
        <aside class="hidden md:flex w-14 bg-white border-r border-slate-200 flex-col items-center py-4 justify-between shrink-0 z-20">
            <nav class="flex flex-col gap-3 w-full items-center">
                
                <!-- 1. Dashboard -->
                <div class="relative group flex justify-center">
                    <a href="/dashboard" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('dashboard') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-monitor text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Dashboard
                    </div>
                </div>
                
                @if(in_array($role, ['superadmin','owner','kaprod','admin']))
                <!-- 2. Monitoring Progress SO -->
                <div class="relative group flex justify-center">
                    <a href="/monitoring" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('monitoring*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-chart-line-up text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Order Summary
                    </div>
                </div>
                @endif

                <!-- 3. Input SO -->
                @if(in_array($role, ['admin', 'superadmin']))
                <div class="relative group flex justify-center">
                    <a href="/orders" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('orders*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-file-plus text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Input Sales Order
                    </div>
                </div>
                @endif

                <!-- 4. Update Status Divisi -->
                @if($canUpdateStatus)
                <div class="relative group flex justify-center">
                    <a href="/update-status" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('update-status*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-check-square-offset text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Update Status Divisi
                    </div>
                </div>
                @endif

                <!-- 5. Master Bahan -->
                @if(in_array($role, ['owner', 'kaprod', 'admin', 'superadmin']))
                <div class="relative group flex justify-center">
                    <a href="/master/fabrics" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('master/fabrics*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-t-shirt text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Master Bahan Kain
                    </div>
                </div>
                @endif

                <!-- 6. Kelola Pengguna -->
                @if(in_array($role, ['owner', 'superadmin']))
                <div class="relative group flex justify-center">
                    <a href="/users" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('users*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-users text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Kelola Akun User Divisi
                    </div>
                </div>
                @endif

                <!-- 7. Kelola Customers -->
                @if(in_array($role, ['owner', 'superadmin','admin']))
                <div class="relative group flex justify-center">
                    <a href="/customers" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('customers*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-users-three text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Kelola Customer
                    </div>
                </div>
                @endif

                <!-- Developer Module Manager -->
                @if($role === 'developer')
                <div class="relative group flex justify-center mt-2">
                    <a href="/developer/modules" class="w-9 h-9 rounded-xl flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                        <i class="ph-bold ph-gear-six text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-rose-950 text-rose-100 text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Modul Rahasia Developer
                    </div>
                </div>
                @endif

            </nav>

            <!-- Bottom Icon: Logout dengan Tooltip -->
            <div class="relative group flex justify-center pb-2">
                <button
                    type="button"
                    onclick="confirmLogout()"
                    class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                    title="Keluar Sistem"
                >
                    <i class="ph-bold ph-sign-out text-xl"></i>
                </button>
                <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-rose-600 text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                    Keluar / Logout
                </div>
                <!-- Hidden Form Logout -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- KONTEN HALAMAN -->
        <main class="flex-1 flex flex-col p-3 md:p-5 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <!-- ==================== MOBILE BOTTOM BAR ==================== -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-white border-t border-slate-300 px-2 flex items-center justify-around z-50 shadow-lg">
        
        <!-- 1. Beranda / Dashboard (Selalu ada untuk semua) -->
        <a href="/dashboard" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('dashboard') ? 'text-black font-black' : 'text-slate-400' }}">
            <i class="ph-bold ph-house text-xl"></i>
            <span>Beranda</span>
        </a>

        <!-- 2. Monitoring (Selalu ada untuk semua) -->
        {{-- <a href="/monitoring" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('monitoring*') ? 'text-black font-black' : 'text-slate-400' }}">
            <i class="ph-bold ph-monitor text-xl"></i>
            <span>Monitoring</span>
        </a> --}}

        <!-- 3. Update Status (Khusus Divisi Produksi: Bahan, Print, Jahit, QC, Finishing, Ka Prod) -->
        @if($canUpdateStatus)
            <a href="/update-status" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('update-status*') ? 'text-black font-black' : 'text-slate-400' }}">
                <i class="ph-bold ph-check-square-offset text-xl"></i>
                <span>Update Status</span>
            </a>
        @else
            <!-- Jika Admin/Owner buka lewat HP, tombol Update Status dinonaktifkan atau diarahkan ke notifikasi -->
            <button
                type="button"
                onclick="Swal.fire({
                    title: 'Akses Khusus Divisi',
                    text: 'Menu Update Status di HP dikhususkan untuk operator divisi lapangan (Bahan s/d QC).',
                    icon: 'info',
                    confirmButtonColor: '#000000'
                })"
                class="flex flex-col items-center gap-1 text-[11px] font-bold text-slate-300 opacity-60 cursor-pointer"
            >
                <i class="ph-bold ph-prohibit text-xl"></i>
                <span>Update Status</span>
            </button>
        @endif

        <!-- 4. Profil / Akun (Untuk ganti password / logout) -->
        <a href="{{ route('password.change') }}" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('password*') ? 'text-black font-black' : 'text-slate-400' }}">
            <i class="ph-bold ph-user text-xl"></i>
            <span>Profil</span>
        </a>

    </nav>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Keluar dari sistem?',
                text: 'Sesi akun divisi ini akan diakhiri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
</body>
</html>
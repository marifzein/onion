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
    <script src="https://cdn.tailwindcss.com"></script>
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
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#F4F6FB] text-slate-800 antialiased h-screen w-screen flex flex-col font-sans select-none overflow-hidden">

    @php
        $user = auth()->user();
        $role = $user->role ?? 'admin';
        $canUpdateStatus = !in_array($role, ['owner', 'admin']);
    @endphp

    <!-- ==================== TOP BAR & HEADER ( ) ==================== -->
    <header class="bg-white border-b border-slate-300 px-6 py-2.5 flex items-center justify-between shrink-0 shadow-xs z-30">
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-3">
                <div class="h-12 w-auto flex items-center justify-center text-black shrink-0">
                    <img
                        src="/img/onion_logo.jpg"
                        alt="ONION CREATIVE Logo"
                        class="h-full w-auto object-contain rounded-lg"
                        onerror="this.src='https://placehold.co/100x100/000000/ffffff?text=ONION'"
                    />
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        {{-- <h1 class="text-2xl font-black tracking-tight text-black uppercase leading-none font-sans">
                            ONION CREATIVE
                        </h1> --}}
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-500 text-white font-extrabold uppercase tracking-wider">
                            Display
                        </span>
                    </div>
                    <p class="text-xs font-bold text-blue-500 tracking-wide mt-0.5">
                        Manajemen Antrean & Produksi
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Header: Clock & User Profile ( ) -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-slate-100 border border-slate-300 px-3 py-1.5 rounded-xl text-xs font-black text-black">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                <span class="font-mono text-sm tracking-wide">{{ now()->translatedFormat('l, d M Y') }}</span>
            </div>

            <div class="flex items-center gap-2.5 pl-2 border-l border-slate-300">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-black text-black leading-none">{{ $user->name ?? 'Admin Produksi' }}</p>
                    <span class="text-[11px] text-black font-extrabold uppercase">{{ $role }}</span>
                </div>
                <div class="w-9 h-9 rounded-full ring-2 ring-black overflow-hidden bg-slate-200 shrink-0">
                    <img
                        src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80"
                        alt="Avatar"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN WRAPPER ==================== -->
    <div class="flex-1 flex overflow-hidden">

        <!-- ==================== SIDE MENU PUTIH (  + HOVER TOOLTIP) ==================== -->
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

                <!-- 3. Input SO (Admin & Dev) -->
                @if(in_array($role, ['admin', 'superadmin']))
                <div class="relative group flex justify-center">
                    <a href="/orders" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('/orders') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-file-plus text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Input Sales Order
                    </div>
                </div>
                @endif

                <!-- 4. Update Status Divisi (Selain Owner & Dev) -->
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

                <!-- 6. Kelola Pengguna / User Divisi (owner & superadmin) -->
                @if(in_array($role, ['owner', 'superadmin' ]))
                <div class="relative group flex justify-center">
                    <a href="/users" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('users*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-users text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Kelola Akun User Divisi
                    </div>
                </div>
                @endif

                <!-- 6. Kelola Customers -->
                @if(in_array($role, ['owner', 'superadmin','admin' ]))
                <div class="relative group flex justify-center">
                    <a href="/customers" class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors {{ request()->is('customers*') ? 'bg-black text-white shadow-sm' : 'text-slate-500 hover:text-black hover:bg-slate-100' }}">
                        <i class="ph-bold ph-users-three text-xl"></i>
                    </a>
                    <div class="absolute left-12 top-1 hidden group-hover:flex items-center px-2.5 py-1 bg-black text-white text-[11px] font-bold rounded-lg shadow-lg whitespace-nowrap z-50">
                        Kelola Customer
                    </div>
                </div>
                @endif

                

                <!-- 6. Developer Secret Module Manager -->
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

            <!-- Bottom Icon Setting -->
            {{-- <div class="w-8 h-8 rounded-xl text-slate-500 flex items-center justify-center">
                <i class="ph-bold ph-sliders-horizontal text-lg"></i>
            </div> --}}
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
        <a href="/dashboard" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('dashboard') ? 'text-black font-black' : 'text-slate-400' }}">
            <i class="ph-bold ph-house text-xl"></i>
            <span>Beranda</span>
        </a>

        <a href="/monitoring" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('monitoring*') ? 'text-black font-black' : 'text-slate-400' }}">
            <i class="ph-bold ph-monitor text-xl"></i>
            <span>Monitoring</span>
        </a>

        @if($canUpdateStatus)
            <a href="/update-status" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('update-status*') ? 'text-black font-black' : 'text-slate-400' }}">
                <i class="ph-bold ph-check-square-offset text-xl"></i>
                <span>Update Status</span>
            </a>
        @else
            <button
                type="button"
                onclick="Swal.fire({
                    title: 'Akses Dibatasi',
                    text: 'Role {{ strtoupper($role) }} hanya memonitor dan tidak mengisi checklist update divisi.',
                    icon: 'info',
                    confirmButtonColor: '#000000'
                })"
                class="flex flex-col items-center gap-1 text-[11px] font-bold text-slate-300 opacity-50 cursor-not-allowed"
            >
                <i class="ph-bold ph-prohibit text-xl"></i>
                <span>Update Status</span>
            </button>
        @endif

        <a href="/profile" class="flex flex-col items-center gap-1 text-[11px] font-bold {{ request()->is('profile*') ? 'text-black font-black' : 'text-slate-400' }}">
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
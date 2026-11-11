{{-- Hanya muncul di HP/Tablet (md:hidden) --}}
<div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 md:hidden flex items-center justify-around px-2 shadow-lg">
    
    <!-- Tombol Antrean Order -->
    <a href="/admin/orders" class="flex flex-col items-center justify-center text-xs text-gray-600 dark:text-gray-300 hover:text-primary-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <span>Pesanan</span>
    </a>

    <!-- Tombol Input Baru (Tombol Menonjol di Tengah) -->
    <a href="/admin/orders/create" class="flex flex-col items-center justify-center -mt-5 bg-primary-600 text-white rounded-full w-12 h-12 shadow-md">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>

    <!-- Tombol Profil / Logout -->
    <a href="/admin" class="flex flex-col items-center justify-center text-xs text-gray-600 dark:text-gray-300 hover:text-primary-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span>Dashboard</span>
    </a>
</div>

{{-- Beri padding bawah pada konten agar tidak tertutup bottom bar di HP --}}
<style>
    @media (max-width: 768px) {
        .fi-main { padding-bottom: 5rem !important; }
    }
</style>
@extends('layouts.app') <!-- Sesuaikan dengan layout utama aplikasimu -->

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header Halaman -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Ganti Password</h1>
            <p class="text-sm text-slate-500 mt-0.5">Demi keamanan, perbarui password akun Anda secara berkala</p>
        </div>
        {{-- <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-sm font-medium transition shadow-sm">
            <i class="ri-arrow-left-line"></i>
            <span>Kembali ke Dashboard</span>
        </a> --}}
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-sm flex items-center gap-3">
            <i class="ri-checkbox-circle-line text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <form action="{{ route('password.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Password Lama -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password Lama <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="ri-lock-2-line text-lg"></i>
                    </span>
                    <input :type="show ? 'text' : 'password'" name="current_password" required placeholder="Masukkan password Anda saat ini" 
                        class="w-full pl-11 pr-10 py-3 bg-slate-50/50 border @error('current_password') border-rose-300 bg-rose-50/30 @else border-slate-200 @endif rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                        <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-lg"></i>
                    </button>
                </div>
                @error('current_password')
                    <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Baru -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password Baru <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="ri-lock-password-line text-lg"></i>
                    </span>
                    <input :type="show ? 'text' : 'password'" name="password" required placeholder="Masukkan password baru (min. 6 karakter)" 
                        class="w-full pl-11 pr-10 py-3 bg-slate-50/50 border @error('password') border-rose-300 bg-rose-50/30 @else border-slate-200 @endif rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                        <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-lg"></i>
                    </button>
                </div>
                @error('password')
                    <span class="text-xs text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <!-- Konfirmasi Password Baru -->
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                        <i class="ri-lock-password-line text-lg"></i>
                    </span>
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="Ulangi password baru Anda" 
                        class="w-full pl-11 pr-10 py-3 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                        <i :class="show ? 'ri-eye-off-line' : 'ri-eye-line'" class="text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                    <i class="ri-save-line"></i>
                    <span>Simpan Password</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
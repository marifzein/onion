@extends('layouts.app', ['title' => 'ONION CREATIVE - Ganti Foto Avatar'])

@section('content')
<div class="p-4 max-w-2xl mx-auto w-full">
    <!-- Header Halaman -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-black tracking-tight">Ganti Foto Avatar</h1>
            <p class="text-xs font-bold text-slate-500 mt-0.5">Perbarui foto profil akun Anda agar mudah dikenali tim</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-black transition shadow-xs">
            <i class="ph-bold ph-arrow-left"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-black flex items-center gap-2">
            <i class="ph-bold ph-check-circle text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold space-y-1">
            <div class="flex items-center gap-1.5 font-black">
                <i class="ph-bold ph-warning-circle text-base shrink-0"></i>
                <span>Gagal memperbarui avatar:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-300 overflow-hidden p-6" x-data="avatarManager()">
        <form action="{{ route('avatar.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Preview & Upload Area -->
            <div class="flex flex-col sm:flex-row items-center gap-6 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <div class="w-24 h-24 rounded-full ring-4 ring-slate-200 bg-slate-900 text-white flex items-center justify-center font-black text-2xl shrink-0 overflow-hidden shadow-md">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!previewUrl">
                        <span>{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                    </template>
                </div>

                <div class="flex-1 w-full text-center sm:text-left space-y-2">
                    <label class="block font-black uppercase text-slate-700 text-xs">Pilih Foto Baru</label>
                    <input
                        type="file"
                        name="avatar"
                        accept="image/png, image/jpeg, image/jpg"
                        @change="showPreview($event)"
                        required
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-black file:text-white hover:file:bg-slate-800 cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400 font-bold">Format yang didukung: JPG, JPEG, PNG. Ukuran maksimal 2MB.</p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-black transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <i class="ph-bold ph-floppy-disk text-sm text-emerald-400"></i>
                    <span>Simpan Avatar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function avatarManager() {
    return {
        previewUrl: '{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : null }}',
        showPreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        }
    }
}
</script>
@endsection
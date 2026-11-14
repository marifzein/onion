@extends('layouts.app', ['title' => 'ONION CREATIVE - Manajemen User Divisi'])

@section('content')
<!-- Driver.js Assets (CSS & JS Lokal) -->
<link rel="stylesheet" href="{{ asset('css/driver.css') }}">
<script src="{{ asset('js/driver.js.iife.js') }}"></script>

<style>
    /* Animasi Berdenyut Tombol Panduan Oranye */
    @keyframes pulseOrange {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.65); }
        70% { transform: scale(1.08); box-shadow: 0 0 0 14px rgba(249, 115, 22, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }
    .btn-pulse-orange {
        animation: pulseOrange 2s infinite;
    }
    .btn-pulse-orange:hover {
        animation: none;
        transform: scale(1.1);
    }

    /* Styling Popover Driver.js ala TataKas */
    .driver-popover .driver-popover-next-btn,
    .driver-popover .driver-popover-done-btn {
        background-color: #10b981 !important;
        color: #ffffff !important;
        border: none !important;
        text-shadow: none !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
    }
    .driver-popover .driver-popover-next-btn:hover,
    .driver-popover .driver-popover-done-btn:hover {
        background-color: #059669 !important;
    }
    .driver-popover .driver-popover-prev-btn {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        padding: 6px 14px !important;
        border-radius: 8px !important;
    }
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

<div x-data="userManager()" class="max-w-7xl w-full mx-auto space-y-4">

    <!-- Top Action & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div id="tour-user-header">
            <h1 class="text-2xl font-black text-black tracking-tight">Akun Pengguna & Divisi</h1>
            <p class="text-xs font-bold text-slate-500">Kelola 1 akun tim kerja per divisi produksi (Bahan, Print, Jahit, QC, Finishing)</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Search Form -->
            <form id="tour-user-search" method="GET" action="{{ route('users.index') }}" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari user / role..."
                    class="w-48 sm:w-64 pl-8 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold outline-none focus:border-black"
                />
                <i class="ph-bold ph-magnifying-glass absolute left-2.5 top-2.5 text-slate-400"></i>
            </form>

            <!-- Tombol Tambah -->
            <button
                id="tour-btn-add-user"
                type="button"
                @click="openCreateModal()"
                class="px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0"
            >
                <i class="ph-bold ph-plus text-sm text-emerald-400"></i>
                <span>Tambah User</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi Sukses / Error / Validation -->
    @if(session('success'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-black flex items-center justify-between">
            <span class="flex items-center gap-1.5"><i class="ph-bold ph-check-circle text-base"></i> {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-black flex items-center justify-between">
            <span class="flex items-center gap-1.5"><i class="ph-bold ph-warning-circle text-base"></i> {{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold space-y-1">
            <div class="flex items-center gap-1.5 font-black">
                <i class="ph-bold ph-warning-circle text-base shrink-0"></i>
                <span>Gagal menyimpan data user:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel User -->
    <div id="tour-user-table" class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left text-xs">
            <thead class="bg-slate-50/80 border-b border-slate-300 text-slate-600 font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-3 text-center w-10">NO</th>
                    <th id="tour-col-name" class="py-3 px-3">NAMA AKUN</th>
                    <th id="tour-col-email" class="py-3 px-3">EMAIL LOGIN</th>
                    <th id="tour-col-role" class="py-3 px-3">DIVISI / ROLE</th>
                    <th id="tour-col-branch" class="py-3 px-3">CABANG</th>
                    <th id="tour-col-status" class="py-3 px-3 text-center">STATUS</th>
                    <th id="tour-col-action" class="py-3 px-3 text-center w-20">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($users as $idx => $u)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="py-3 px-3 text-center font-black text-slate-400">
                        {{ $users->firstItem() + $idx }}
                    </td>
                    <td class="py-3 px-3">
                        <p class="font-black text-black text-sm">{{ $u->name }}</p>
                    </td>
                    <td class="py-3 px-3 font-mono font-bold text-slate-600">
                        {{ $u->email }}
                    </td>
                    <td class="py-3 px-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black uppercase border 
                            @if($u->role === 'owner') bg-amber-100 text-amber-900 border-amber-300 
                            @elseif(in_array($u->role, ['superadmin', 'admin'])) bg-fuchsia-100 text-fuchsia-800 border-fuchsia-200
                            @elseif($u->role === 'kaprod') bg-blue-100 text-blue-800 border-blue-200
                            @elseif($u->role === 'bahan') bg-cyan-100 text-cyan-800 border-cyan-200
                            @elseif($u->role === 'print') bg-orange-100 text-orange-800 border-orange-200
                            @elseif($u->role === 'jahit') bg-sky-100 text-sky-800 border-sky-200
                            @elseif($u->role === 'qc') bg-emerald-100 text-emerald-800 border-emerald-200
                            @elseif($u->role === 'finishing') bg-lime-100 text-lime-800 border-lime-200
                            @else bg-slate-100 text-slate-700 border-slate-300 @endif">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td class="py-3 px-3 font-bold text-slate-700">
                        {{ $u->cabang->nama_cabang ?? 'Pusat' }}
                    </td>
                    <td class="py-3 px-3 text-center">
                        @if($u->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button
                                type="button"
                                @click="openEditModal({{ json_encode($u) }})"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-700 hover:text-black transition-colors cursor-pointer"
                                title="Edit Akun User"
                            >
                                <i class="ph-bold ph-pencil-simple text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 font-bold">
                        Tidak ada data user.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="p-3 border-t border-slate-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- MODAL FORM USER (CREATE / EDIT) -->
    <div
        x-show="modalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="modalOpen = false"
    >
        <div @click.outside="modalOpen = false" class="bg-white rounded-3xl border border-slate-300 shadow-2xl w-full max-w-md overflow-hidden p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <i class="ph-bold ph-user-gear text-indigo-600 text-xl"></i>
                    <h3 class="text-sm font-black text-black" x-text="isEdit ? 'Edit Akun Pengguna' : 'Tambah Akun Divisi Baru'"></h3>
                </div>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-black cursor-pointer">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <form :action="formUrl" method="POST" @submit="handleSubmit($event)" class="mt-4 space-y-3.5 text-xs">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div id="tour-modal-name">
                    <label class="block font-black uppercase text-slate-700 mb-1">Nama Pengguna / Divisi <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        name="name"
                        x-model="formData.name"
                        required
                        placeholder="Contoh: Divisi Printing Gresik"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                    />
                </div>

                <div id="tour-modal-email">
                    <label class="block font-black uppercase text-slate-700 mb-1">Email Login <span class="text-rose-500">*</span></label>
                    <input
                        type="email"
                        name="email"
                        x-model="formData.email"
                        required
                        placeholder="contoh: print@onion.id"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                    />
                </div>

                <div id="tour-modal-role-branch" class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Role / Divisi <span class="text-rose-500">*</span></label>
                        <select
                            name="role"
                            x-model="formData.role"
                            required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50 cursor-pointer"
                        >
                            @foreach($allowedRoles as $roleOption)
                                <option value="{{ $roleOption }}">{{ $roleOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Cabang</label>
                        <select
                            name="cabang_id"
                            x-model="formData.cabang_id"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50 cursor-pointer"
                        >
                            @foreach($cabangs as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Input Password & Konfirmasi Password Berdampingan -->
                <div id="tour-modal-password" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">
                            Password <span x-show="!isEdit" class="text-rose-500">*</span>
                            <span x-show="isEdit" class="text-[10px] text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            x-model="formData.password"
                            :required="!isEdit"
                            :minlength="(!isEdit || formData.password.length > 0) ? 6 : null"
                            placeholder="Min. 6 karakter"
                            class="w-full px-3 py-2 rounded-xl border outline-none font-bold text-sm bg-slate-50/50"
                            :class="passwordTooShort ? 'border-rose-500 focus:border-rose-600' : 'border-slate-300 focus:border-black'"
                        />
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">
                            Ulangi Password <span x-show="!isEdit" class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            x-model="formData.password_confirmation"
                            :required="!isEdit || formData.password.length > 0"
                            :minlength="(!isEdit || formData.password.length > 0) ? 6 : null"
                            placeholder="Ketik ulang password"
                            class="w-full px-3 py-2 rounded-xl border outline-none font-bold text-sm bg-slate-50/50"
                            :class="passwordMismatch ? 'border-rose-500 focus:border-rose-600' : 'border-slate-300 focus:border-black'"
                        />
                    </div>
                </div>

                <!-- Pesan Peringatan Validasi Password -->
                <div class="space-y-1 -mt-1">
                    <p x-show="passwordTooShort" x-cloak class="text-[11px] font-bold text-rose-600 flex items-center gap-1">
                        <i class="ph-bold ph-warning-circle text-xs"></i>
                        Password minimal harus 6 karakter! (Saat ini: <span x-text="formData.password.length"></span> karakter)
                    </p>
                    <p x-show="passwordMismatch" x-cloak class="text-[11px] font-bold text-rose-600 flex items-center gap-1">
                        <i class="ph-bold ph-warning-circle text-xs"></i>
                        Konfirmasi password tidak cocok!
                    </p>
                </div>

                <div id="tour-modal-active" class="flex items-center gap-2 pt-1">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        x-model="formData.is_active"
                        class="w-4 h-4 rounded text-black border-slate-300 cursor-pointer"
                    />
                    <label for="is_active" class="font-bold text-slate-700 cursor-pointer">Akun Aktif (Bisa Login)</label>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-200">
                    <button type="button" @click="modalOpen = false" class="px-3.5 py-1.5 rounded-xl bg-slate-100 font-bold text-slate-600 cursor-pointer">
                        Batal
                    </button>
                    <button 
                        id="tour-modal-submit"
                        type="submit" 
                        :disabled="hasFormError"
                        :class="hasFormError ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-slate-800'"
                        class="px-5 py-1.5 rounded-xl bg-black text-white font-black shadow-xs transition-all"
                    >
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Akun'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form Delete -->
    <form id="deleteUserForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<!-- TOMBOL BANTUAN "?" MELAYANG ORANYE BERDENYUT DI POJOK KANAN BAWAH -->
<button 
    type="button" 
    onclick="jalankanTourUserIndex()" 
    title="Panduan Manajemen User"
    class="btn-pulse-orange fixed bottom-6 right-6 z-[9999] w-11 h-11 rounded-full bg-orange-500 hover:bg-orange-600 text-white font-black text-lg shadow-lg flex items-center justify-center cursor-pointer transition-colors"
>
    ?
</button>

<script>
function jalankanTourUserIndex() {
    const driverFactory = window.driver?.js?.driver || window.driver;
    if (!driverFactory) {
        alert('Panduan sedang memuat, silakan klik sekali lagi.');
        return;
    }

    const steps = [
        { 
            element: '#tour-user-header',
            popover: { 
                title: 'Manajemen Akun Pengguna', 
                description: 'Halaman ini digunakan untuk mengelola akun login tim kerja di setiap divisi produksi (Bahan, Print, Jahit, QC, Finishing).', 
                side: "bottom", 
                align: 'start' 
            } 
        },
        { 
            element: '#tour-user-search',
            popover: { 
                title: 'Pencarian User', 
                description: 'Cari akun staf atau divisi tertentu berdasarkan nama, email, atau role.', 
                side: "bottom", 
                align: 'start' 
            } 
        },
        { 
            element: '#tour-btn-add-user',
            popover: { 
                title: 'Tambah Akun Baru', 
                description: 'Klik tombol ini untuk mendaftarkan akun divisi baru. (Catatan: Jika modal terbuka, panduan akan otomatis menyoroti bagian form di dalamnya).', 
                side: "bottom", 
                align: 'end' 
            } 
        },
        { 
            element: '#tour-user-table',
            popover: { 
                title: 'Daftar Akun & Hak Akses', 
                description: 'Menampilkan nama akun, email login, divisi/role, cabang penugasan, serta tombol edit untuk memperbarui data.', 
                side: "top", 
                align: 'start' 
            } 
        }
    ];

    const driverObj = driverFactory({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(0, 0, 0, 0.75)',
        nextBtnText: 'Lanjut &rarr;',
        prevBtnText: '&larr; Kembali',
        doneBtnText: 'Selesai',
        steps: steps
    });

    driverObj.drive();
}

document.addEventListener('DOMContentLoaded', function () {
    const hasSeenTour = localStorage.getItem('onion_user_index_tour_seen');
    if (!hasSeenTour) {
        setTimeout(() => {
            jalankanTourUserIndex();
            localStorage.setItem('onion_user_index_tour_seen', 'true');
        }, 600);
    }
});

function userManager() {
    return {
        modalOpen: false,
        isEdit: false,
        formUrl: '{{ route("users.store") }}',
        formData: {
            name: '',
            email: '',
            role: 'bahan',
            cabang_id: '{{ $cabangs->first()->id ?? 1 }}',
            password: '',
            password_confirmation: '',
            is_active: true,
        },

        get passwordTooShort() {
            if (!this.formData.password) return false;
            return this.formData.password.length < 6;
        },

        get passwordMismatch() {
            if (!this.formData.password && !this.formData.password_confirmation) return false;
            return this.formData.password !== this.formData.password_confirmation;
        },

        get hasFormError() {
            return this.passwordTooShort || this.passwordMismatch;
        },

        handleSubmit(e) {
            if (this.hasFormError) {
                e.preventDefault();
            }
        },

        openCreateModal() {
            this.isEdit = false;
            this.formUrl = '{{ route("users.store") }}';
            this.formData = {
                name: '',
                email: '',
                role: 'bahan',
                cabang_id: '{{ $cabangs->first()->id ?? 1 }}',
                password: '',
                password_confirmation: '',
                is_active: true,
            };
            this.modalOpen = true;
        },

        openEditModal(user) {
            this.isEdit = true;
            this.formUrl = `/users/${user.id}`;
            this.formData = {
                name: user.name,
                email: user.email,
                role: user.role,
                cabang_id: user.cabang_id,
                password: '',
                password_confirmation: '',
                is_active: Boolean(user.is_active),
            };
            this.modalOpen = true;
        },

        deleteUser(id, name) {
            Swal.fire({
                title: 'Hapus akun ini?',
                text: `Akun "${name}" akan dihapus permanen dari sistem.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    const form = document.getElementById('deleteUserForm');
                    form.action = `/users/${id}`;
                    form.submit();
                }
            });
        }
    };
}
</script>
@endsection
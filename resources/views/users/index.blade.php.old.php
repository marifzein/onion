@extends('layouts.app', ['title' => 'ONION CREATIVE - Manajemen User Divisi'])

@section('content')
<div x-data="userManager()" class="max-w-7xl w-full mx-auto space-y-4">

    <!-- Top Action & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-black tracking-tight">Akun Pengguna & Divisi</h1>
            <p class="text-xs font-bold text-slate-500">Kelola 1 akun tim kerja per divisi produksi (Bahan, Print, Jahit, QC, Finishing)</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Search Form -->
            <form method="GET" action="{{ route('users.index') }}" class="relative">
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
                type="button"
                @click="openCreateModal()"
                class="px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer shrink-0"
            >
                <i class="ph-bold ph-plus text-sm text-emerald-400"></i>
                <span>Tambah User</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
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

    <!-- Tabel User -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left text-xs">
            <thead class="bg-slate-50/80 border-b border-slate-300 text-slate-600 font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-3 text-center w-10">NO</th>
                    <th class="py-3 px-3">NAMA AKUN</th>
                    <th class="py-3 px-3">EMAIL LOGIN</th>
                    <th class="py-3 px-3">DIVISI / ROLE</th>
                    <th class="py-3 px-3">CABANG</th>
                    <th class="py-3 px-3 text-center">STATUS</th>
                    <th class="py-3 px-3 text-center w-20">AKSI</th>
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
                            {{-- <button
                                type="button"
                                @click="deleteUser({{ $u->id }}, '{{ $u->name }}')"
                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 transition-colors cursor-pointer"
                                title="Hapus User"
                            >
                                <i class="ph-bold ph-trash text-sm"></i>
                            </button> --}}
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
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-black">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <form :action="formUrl" method="POST" class="mt-4 space-y-3.5 text-xs">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
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

                <div>
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

                <div class="grid grid-cols-2 gap-3">
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

                {{-- <div>
                    <label class="block font-black uppercase text-slate-700 mb-1">
                        Password <span x-show="!isEdit" class="text-rose-500">*</span>
                        <span x-show="isEdit" class="text-[10px] text-slate-400 font-normal lowercase">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        :required="!isEdit"
                        placeholder="Minimal 6 karakter"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                    />
                </div> --}}
                <!-- Input Password & Konfirmasi Password Berdampingan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            placeholder="Min. 6 karakter"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
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
                            placeholder="Ketik ulang password"
                            class="w-full px-3 py-2 rounded-xl border outline-none font-bold text-sm bg-slate-50/50"
                            :class="passwordMismatch ? 'border-rose-500 focus:border-rose-600' : 'border-slate-300 focus:border-black'"
                        />
                    </div>
                </div>

                <!-- Pesan Peringatan Jika Password Tidak Cocok -->
                <p x-show="passwordMismatch" x-cloak class="text-[11px] font-bold text-rose-600 flex items-center gap-1 -mt-2">
                    <i class="ph-bold ph-warning-circle text-xs"></i>
                    Konfirmasi password tidak cocok!
                </p>

                <div class="flex items-center gap-2 pt-1">
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
                    <button type="button" @click="modalOpen = false" class="px-3.5 py-1.5 rounded-xl bg-slate-100 font-bold text-slate-600">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="passwordMismatch"
                        :class="passwordMismatch ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
                        class="px-5 py-1.5 rounded-xl bg-black hover:bg-slate-800 text-white font-black shadow-xs"
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

<script>
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

        // Cek ketidakcocokan password secara reaktif
        get passwordMismatch() {
            if (!this.formData.password && !this.formData.password_confirmation) return false;
            return this.formData.password !== this.formData.password_confirmation;
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

function userManager_lawas() {
    return {
        modalOpen: false,
        isEdit: false,
        formUrl: '{{ route("users.store") }}',
        formData: {
            name: '',
            email: '',
            role: 'bahan',
            cabang_id: '{{ $cabangs->first()->id ?? 1 }}',
            is_active: true,
        },

        openCreateModal() {
            this.isEdit = false;
            this.formUrl = '{{ route("users.store") }}';
            this.formData = {
                name: '',
                email: '',
                role: 'bahan',
                cabang_id: '{{ $cabangs->first()->id ?? 1 }}',
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
@extends('layouts.app', ['title' => 'ONION CREATIVE - Master Customer'])

@section('content')
<div class="max-w-7xl w-full mx-auto space-y-5">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-black tracking-tight">Master Customer</h1>
            <p class="text-xs font-bold text-slate-500">Database kontak pelanggan & instansi pemesan</p>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('customers.index') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama, no telp, alamat..."
                    class="pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-black outline-none focus:border-black shadow-2xs"
                />
                <i class="ph-bold ph-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
            </form>

            <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black flex items-center gap-2 shadow-xs transition-all">
                <i class="ph-bold ph-plus text-emerald-400 text-sm"></i>
                <span>Tambah Customer</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
        <i class="ph-bold ph-check-circle text-base text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead class="bg-slate-100 border-b border-slate-300 text-black text-xs font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-4 w-12 text-center">No</th>
                    <th class="py-3 px-4">Nama Customer / Instansi</th>
                    <th class="py-3 px-4">PIC</th>
                    <th class="py-3 px-4 w-44">No. Telepon / WA</th>
                    <th class="py-3 px-4">Alamat</th>
                    <th class="py-3 px-4 w-24 text-center">Status</th>
                    <th class="py-3 px-4 w-24 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm font-medium">
                @forelse($customers as $idx => $c)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 text-center font-bold text-slate-500">
                        {{ $customers->firstItem() + $idx }}
                    </td>
                    <td class="py-3 px-4 font-black text-black">
                        {{ $c->name }}
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-black text-black leading-tight">{{ $c->name }}</p>
                        @if($c->pic_name)
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1 mt-0.5">
                                <i class="ph-bold ph-identification-badge text-indigo-600"></i> PIC: {{ $c->pic_name }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-xs font-mono font-bold text-indigo-700">
                        {{ $c->phone ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-xs text-slate-600 font-semibold truncate max-w-xs">
                        {{ $c->address ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($c->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-600">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('customers.edit', $c->id) }}" class="p-1.5 text-slate-600 hover:text-black hover:bg-slate-100 rounded-lg" title="Edit">
                                <i class="ph-bold ph-pencil-simple text-base"></i>
                            </a>
                            {{-- <form action="{{ route('customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg cursor-pointer" title="Hapus">
                                    <i class="ph-bold ph-trash text-base"></i>
                                </button>
                            </form> --}}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-xs font-bold text-slate-400">
                        Belum ada database customer. Silakan klik tombol "Tambah Customer".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
        <div class="p-3 border-t border-slate-200">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
@extends('layouts.app', ['title' => 'Edit Customer'])

@section('content')
<div class="max-w-2xl w-full mx-auto space-y-5">
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div>
            <h1 class="text-xl font-black text-black tracking-tight">Edit Customer</h1>
            <p class="text-xs font-bold text-slate-500">Perbarui informasi pelanggan {{ $customer->name }}</p>
        </div>
        <a href="{{ route('customers.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black transition-colors">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-300 shadow-xs p-6">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                    Nama Lengkap Customer / Instansi <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $customer->name) }}"
                    required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
                @error('name') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                    Nama PIC / Kontak Person (Opsional)
                </label>
                <input
                    type="text"
                    name="pic_name"
                    value="{{ old('pic_name', $customer->pic_name ?? '') }}"
                    placeholder="Contoh: Bpk. Hendra (Divisi Promosi)"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                    No. Telepon / WhatsApp (Opsional)
                </label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $customer->phone) }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                />
            </div>

            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                    Alamat Lengkap (Opsional)
                </label>
                <textarea
                    name="address"
                    rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50/50"
                >{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-black focus:ring-0">
                <label for="is_active" class="text-xs font-black text-slate-800 cursor-pointer">Status Aktif</label>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end gap-2">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-md">Update Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
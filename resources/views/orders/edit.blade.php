@extends('layouts.app', ['title' => 'ONION CREATIVE - Edit Sales Order'])

@section('content')
<div
    x-data="orderEditForm()"
    class="max-w-4xl w-full mx-auto p-2 md:p-4 flex flex-col justify-center pb-12"
>
    <div class="bg-white rounded-3xl border border-slate-300 shadow-xs p-6 md:p-8">
        <!-- Header Form -->
        <div class="border-b border-slate-200 pb-4 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-black tracking-tight">Edit Sales Order</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                    Perbarui informasi pesanan untuk <strong class="text-indigo-600" x-text="form.no_so"></strong>
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">No. Registrasi SO</span>
                    <span class="font-mono text-base font-black text-indigo-700" x-text="form.no_so"></span>
                </div>
                <!-- Tombol Kembali ke Daftar SO -->
                <a href="{{ route('orders.index') }}" class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors shadow-xs" title="Kembali">
                    <i class="ph-bold ph-arrow-left text-lg"></i>
                </a>
            </div>
        </div>

        <form @submit.prevent="submitUpdate()" class="space-y-5">
            <!-- Cabang -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                <span class="text-xs font-black uppercase text-slate-700 flex items-center gap-1.5">
                    <i class="ph-bold ph-storefront text-indigo-600 text-base"></i> Cabang Produksi
                </span>
                <select x-model="form.cabang_id" disabled class="px-3 py-1.5 bg-slate-200 border border-slate-300 rounded-xl font-black text-xs text-black outline-none cursor-not-allowed">
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Customer & Judul Order -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative" @click.outside="customerDropdownOpen = false">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Nama Customer / Instansi <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.customer_name"
                        @focus="searchCustomer(); customerDropdownOpen = true"
                        @input.debounce.250ms="searchCustomer(); customerDropdownOpen = true"
                        placeholder="Ketik nama customer..."
                        required
                        autocomplete="off"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                    />
                    <div
                        x-show="customerDropdownOpen && customerItems.length > 0"
                        x-cloak
                        class="absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-2xl shadow-xl max-h-56 overflow-y-auto z-50 divide-y divide-slate-100"
                    >
                        <template x-for="c in customerItems" :key="c.id">
                            <div
                                @click="selectCustomerItem(c)"
                                class="px-4 py-2.5 cursor-pointer text-xs hover:bg-indigo-50 font-black"
                                x-text="c.pic_name ? c.name + ' (PIC: ' + c.pic_name + ')' : c.name"
                            ></div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">
                        Judul Order / Event <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="form.order_title"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50/50"
                    />
                </div>
            </div>

            <!-- Qty & Satuan -->
            <div class="p-4 bg-indigo-50/40 rounded-2xl border border-indigo-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-900 mb-1.5">Jumlah / Qty Total *</label>
                        <input type="number" min="1" x-model.number="form.qty" required class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-300 font-black text-lg bg-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-900 mb-1.5">Satuan *</label>
                        <select x-model="form.satuan" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-300 font-black text-base bg-white">
                            <option value="pcs">pcs</option>
                            <option value="stel">stel</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Lampiran Breakdown Size -->
            <div class="p-4 bg-cyan-50/40 rounded-2xl border border-cyan-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black uppercase text-cyan-950 flex items-center gap-1.5">
                        <i class="ph-bold ph-ruler text-cyan-700"></i> Lampiran Breakdown Size (Excel / Screenshot)
                    </label>
                    <button type="button" @click="$refs.sizeFileInput.click()" class="px-2.5 py-1 bg-white hover:bg-cyan-100 border border-cyan-300 text-cyan-800 rounded-lg text-[11px] font-black flex items-center gap-1.5 cursor-pointer">
                        <i class="ph-bold ph-folder-open text-cyan-700"></i>
                        <span>Upload File Gambar</span>
                    </button>
                </div>

                <div
                    @paste.window="handleSizePaste($event)"
                    tabindex="0"
                    class="relative border-2 border-dashed rounded-xl p-4 transition-all flex flex-col items-center justify-center text-center focus:outline-none focus:border-indigo-600 bg-white"
                    :class="sizePreview ? 'border-emerald-400 bg-emerald-50/20' : 'border-cyan-300'"
                >
                    <input type="file" x-ref="sizeFileInput" @change="handleSizeFileInput($event)" accept="image/*" class="hidden" />

                    <template x-if="!sizePreview">
                        <div class="py-3 flex flex-col items-center select-none pointer-events-none">
                            <i class="ph-bold ph-clipboard-text text-2xl text-cyan-700 mb-1.5"></i>
                            <p class="text-xs font-black text-slate-800">Klik area ini lalu <span class="text-indigo-600 underline">Ctrl + V</span> untuk ganti gambar size</p>
                        </div>
                    </template>

                    <template x-if="sizePreview">
                        <div class="w-full flex items-center justify-between gap-4" @click.stop>
                            <div class="flex items-center gap-3">
                                <img :src="sizePreview" class="w-14 h-14 object-cover rounded-xl border border-slate-300" />
                                <span class="text-xs font-black text-emerald-600 uppercase">✓ Breakdown Size Terlampir</span>
                            </div>
                            <button type="button" @click="removeSizePhoto()" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg cursor-pointer">
                                <i class="ph-bold ph-trash text-base"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Bahan & Deadline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative" @click.outside="fabricDropdownOpen = false">
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Bahan / Fabric *</label>
                    <input
                        type="text"
                        x-model="form.fabric_type"
                        @focus="searchFabric(); fabricDropdownOpen = true"
                        @input.debounce.250ms="searchFabric(); fabricDropdownOpen = true"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-sm bg-slate-50/50"
                    />
                    <div x-show="fabricDropdownOpen && fabricItems.length > 0" x-cloak class="absolute left-0 right-0 mt-1 bg-white border border-slate-300 rounded-2xl shadow-xl max-h-56 overflow-y-auto z-50">
                        <template x-for="item in fabricItems" :key="item.id">
                            <div @click="selectFabricItem(item)" class="px-4 py-2.5 cursor-pointer text-xs hover:bg-indigo-50 font-black" x-text="item.name"></div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Target Deadline *</label>
                    <input type="date" x-model="form.target_deadline" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-black text-sm bg-slate-50/50 text-rose-600" />
                </div>
            </div>

            <!-- Tipe Pesanan -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Tipe Pesanan</label>
                <input type="text" x-model="form.order_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-sm bg-slate-50/50" />
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-black uppercase text-slate-700 mb-1.5">Deskripsi & Instruksi</label>
                <textarea x-model="form.notes" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs bg-slate-50/50"></textarea>
            </div>

            <!-- Foto SPK Fisik -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-300">
                <label class="block text-xs font-black uppercase text-slate-900 mb-2 flex items-center gap-1.5">
                    <i class="ph-bold ph-camera text-indigo-600"></i> Foto Formulir SO Fisik
                </label>
                <div @click="$refs.fileInput.click()" class="relative border-2 border-dashed rounded-xl p-4 flex flex-col items-center justify-center text-center cursor-pointer bg-white">
                    <input type="file" x-ref="fileInput" @change="handleFileInput($event)" accept="image/*" class="hidden" />
                    <template x-if="!spkPreview">
                        <p class="text-xs font-black text-slate-800">Klik untuk ganti foto SPK fisik</p>
                    </template>
                    <template x-if="spkPreview">
                        <div class="w-full flex items-center justify-between gap-4" @click.stop>
                            <img :src="spkPreview" class="w-12 h-12 object-cover rounded-xl border" />
                            <span class="text-xs font-black text-emerald-600">✓ SPK Terlampir</span>
                            <button type="button" @click="removePhoto()" class="p-1 text-rose-600"><i class="ph-bold ph-trash"></i></button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                    <i class="ph-bold ph-shield-check text-emerald-600"></i> Perubahan langsung sinkron
                </span>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="px-6 py-2.5 rounded-xl bg-red-800 hover:bg-slate-800 text-white font-black text-xs shadow-md cursor-pointer flex items-center gap-2"
                    >
                        <i class="ph-bold ph-arrow-arc-left text-base"></i>
                        <a href="{{ route('orders.index') }}" >
                            Cancel
                        </a>
                    </button>
                    <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 rounded-xl bg-black hover:bg-slate-800 text-white font-black text-xs shadow-md cursor-pointer">
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function orderEditForm() {
    return {
        isSubmitting: false,
        spkPreview: "{{ $order->spk_photo_path ? asset('storage/' . $order->spk_photo_path) : null }}",
        sizePreview: "{{ $order->size_photo_path ? asset('storage/' . $order->size_photo_path) : null }}",

        customerDropdownOpen: false,
        customerItems: [],
        fabricDropdownOpen: false,
        fabricItems: [],

        form: {
            cabang_id: "{{ $order->cabang_id }}",
            customer_id: "{{ $order->customer_id }}",
            no_so: "{{ $order->no_so }}",
            customer_name: "{{ $order->customer->name ?? $order->customer_name }}",
            order_title: "{{ $order->order_title }}",
            qty: {{ $order->qty }},
            satuan: "{{ $order->satuan }}",
            fabric_type: "{{ $order->fabric_type }}",
            order_type: "{{ $order->order_type }}",
            target_deadline: "{{ $order->target_deadline }}",
            notes: "{{ $order->notes }}",
            spk_photo_base64: null,
            size_photo_base64: null,
        },

        handleSizePaste(e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let item of items) {
                if (item.type.includes('image')) {
                    this.compressSize(item.getAsFile());
                    break;
                }
            }
        },

        handleSizeFileInput(e) {
            const file = e.target.files[0];
            if (file) this.compressSize(file);
        },

        compressSize(file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const max = 1400;
                    let w = img.width, h = img.height;
                    if (w > h && w > max) { h = (h * max) / w; w = max; }
                    else if (h > max) { w = (w * max) / h; h = max; }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    const webpData = canvas.toDataURL('image/webp', 0.8);
                    this.sizePreview = webpData;
                    this.form.size_photo_base64 = webpData;
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeSizePhoto() {
            this.sizePreview = null;
            this.form.size_photo_base64 = null;
        },

        handleFileInput(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.spkPreview = event.target.result;
                    this.form.spk_photo_base64 = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removePhoto() {
            this.spkPreview = null;
            this.form.spk_photo_base64 = null;
        },

        searchCustomer() {
            fetch(`/api/customers/search?q=${encodeURIComponent(this.form.customer_name)}`)
                .then(res => res.json())
                .then(data => { this.customerItems = data; });
        },

        selectCustomerItem(item) {
            this.form.customer_id = item.id;
            this.form.customer_name = item.name;
            this.customerDropdownOpen = false;
        },

        searchFabric() {
            fetch(`/api/fabrics/search?q=${encodeURIComponent(this.form.fabric_type || '')}`)
                .then(res => res.json())
                .then(data => { this.fabricItems = data; });
        },

        selectFabricItem(item) {
            this.form.fabric_type = item.name;
            this.fabricDropdownOpen = false;
        },

        submitUpdate() {
            this.isSubmitting = true;
            fetch("{{ route('orders.update', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    ...this.form,
                    _method: 'PUT'
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSubmitting = false;
                if (data.status === 'success') {
                    Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', confirmButtonColor: '#000000' }).then(() => {
                        window.location.href = "{{ route('orders.index') }}";
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Validasi gagal', 'error');
                }
            })
            .catch(() => {
                this.isSubmitting = false;
                Swal.fire('Error', 'Gagal menghubungi server', 'error');
            });
        }
    };
}
</script>
@endsection
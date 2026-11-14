@extends('layouts.app', ['title' => 'ONION CREATIVE - Daftar Sales Order'])

@section('content')
<style>
    /* Custom Checkbox Touch-Friendly */
    .qc-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 1.6rem;
        height: 1.6rem;
        border: 2px solid #94a3b8;
        border-radius: 0.5rem;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-grid;
        place-content: center;
        background-color: #ffffff;
    }
    .qc-checkbox:hover:not(:disabled) {
        border-color: #00bf63;
        box-shadow: 0 0 0 3px rgba(0, 191, 99, 0.15);
    }
    .qc-checkbox:checked {
        background-color: #00bf63;
        border-color: #00bf63;
    }
    .qc-checkbox:checked::before {
        content: "";
        width: 0.6rem;
        height: 0.9rem;
        border: solid white;
        border-width: 0 3px 3px 0;
        transform: rotate(45deg) translate(-1px, -1px);
    }
    .qc-checkbox:disabled {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>

<div x-data="orderIndexManager()" class="max-w-7xl w-full mx-auto space-y-4">
    
    <!-- Header Halaman -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-black tracking-tight">Daftar Sales Order</h1>
            <p class="text-xs font-bold text-slate-500">Seluruh riwayat, progres antrean, dan serah terima Sales Order (SO)</p>
        </div>
        
        <!-- Tombol Input SO Baru -->
        <a href="{{ route('orders.create') }}" class="px-4 py-2 bg-black hover:bg-slate-800 text-white rounded-xl text-xs font-black flex items-center gap-2 shadow-xs transition-all cursor-pointer">
            <i class="ph-bold ph-plus text-base text-emerald-400"></i>
            <span>Input SO Baru</span>
        </a>
    </div>

    <!-- Tabel Daftar SO -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead class="bg-slate-100 border-b border-slate-300 text-black text-xs font-black uppercase tracking-wider">
                <tr>
                    <th class="py-3 px-3 text-center w-12">No</th>
                    <th class="py-3 px-3">No. SO & Customer</th>
                    <th class="py-3 px-3">Bahan & Qty</th>
                    <th class="py-3 px-3">Deadline (Out)</th>
                    <th class="py-3 px-3 text-center">Status</th>
                    <th class="py-3 px-3 text-center w-28">Aksi Selesai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm font-medium">
                @forelse($orders as $idx => $order)
                @php
                    $isDone = ($order->current_status === 'selesai');
                    $canClose = ($order->current_status === 'qc' || $isDone);
                    $custName = $order->customer->name ?? $order->customer_name;
                @endphp
                <tr class="transition-colors {{ $isDone ? 'bg-slate-50/70 opacity-70 hover:opacity-100' : 'hover:bg-slate-50' }}">
                    <td class="py-3 px-3 text-center font-bold text-slate-500">
                        {{ $orders->firstItem() + $idx }}
                    </td>
                    <td class="py-3 px-3">
                        <p class="font-black text-black leading-tight {{ $isDone ? 'line-through text-slate-500' : '' }}">
                            {{ $custName }}
                            @if(optional($order->customer)->pic_name)
                                <span class="text-xs font-semibold text-slate-500">(PIC: {{ $order->customer->pic_name }})</span>
                            @endif
                        </p>
                        <span class="text-xs font-mono font-bold text-slate-500">{{ $order->no_so }}</span>
                        <span class="text-xs text-slate-400">&bull; {{ $order->order_title }}</span>
                    </td>
                    <td class="py-3 px-3">
                        <p class="text-xs font-bold text-slate-800">{{ $order->fabric_type ?? '-' }}</p>
                        <p class="text-xs font-black text-indigo-700 leading-tight">
                            {{ $order->qty }} {{ strtoupper($order->satuan) }}
                        </p>
                    </td>
                    <td class="py-3 px-3">
                        <span class="text-xs font-black {{ $isDone ? 'text-slate-400 line-through' : 'text-rose-600' }}">
                            {{ \Carbon\Carbon::parse($order->target_deadline)->translatedFormat('d M Y') }}
                        </span>
                    </td>
                    <td class="py-3 px-3 text-center">
                        @if($isDone)
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                SELESAI
                            </span>
                        @elseif($order->current_status === 'qc')
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                SIAP DIAMBIL (QC OK)
                            </span>
                        @else
                            <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-300">
                                {{ strtoupper($order->current_status) }}
                            </span>
                        @endif
                    </td>

                    <!-- Kolom Checkbox Selesai (Closed) -->
                    <td class="py-3 px-3 text-center">
                        <div class="flex items-center justify-center">
                            <input
                                type="checkbox"
                                class="qc-checkbox"
                                {{ $isDone ? 'checked' : '' }}
                                {{ !$canClose ? 'disabled' : '' }}
                                @click.prevent="
                                    if (!{{ $canClose ? 'true' : 'false' }}) return;
                                    handleToggleComplete(
                                        {{ $order->id }}, 
                                        '{{ $order->no_so }}', 
                                        '{{ addslashes($custName) }}', 
                                        '{{ addslashes($order->order_title) }}', 
                                        '{{ $order->qty }} {{ strtoupper($order->satuan) }}', 
                                        {{ $isDone ? 'true' : 'false' }}
                                    )
                                "
                                title="{{ !$canClose ? 'Pesanan masih berjalan di antrean produksi (belum lolos QC)' : ($isDone ? 'Klik untuk membatalkan status selesai' : 'Klik untuk menutup order (barang sudah diambil)') }}"
                            />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-xs font-bold text-slate-400">
                        Belum ada data Sales Order. Silakan klik tombol "Input SO Baru".
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
        <div class="p-3 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function orderIndexManager() {
    return {
        handleToggleComplete(orderId, noSo, customer, eventTitle, qtyText, isCurrentlyDone) {
            // Skenario 1: Menandai Selesai (Barang Diambil)
            if (!isCurrentlyDone) {
                Swal.fire({
                    title: 'Serah Terima Pesanan?',
                    html: `
                        <div class="text-left text-xs bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-1.5 mt-2 text-slate-700">
                            <div><span class="text-slate-400 font-bold">Nomor SO:</span> <strong class="text-black font-mono">${noSo}</strong></div>
                            <div><span class="text-slate-400 font-bold">Customer:</span> <strong class="text-black">${customer}</strong></div>
                            <div><span class="text-slate-400 font-bold">Pesanan/Event:</span> <strong class="text-indigo-700">${eventTitle}</strong></div>
                            <div><span class="text-slate-400 font-bold">Jumlah Qty:</span> <strong class="text-emerald-700">${qtyText}</strong></div>
                        </div>
                        <p class="text-xs text-slate-600 font-bold mt-3 text-center">
                            Apakah pesanan di atas <u>sudah diambil</u> oleh pemesan?
                        </p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#00bf63',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Sudah Diambil!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.sendRequest(orderId, true);
                    }
                });
            } 
            // Skenario 2: Rollback / Batal Selesai
            else {
                Swal.fire({
                    title: 'Batalkan Status Selesai?',
                    text: `Pesanan ${noSo} (${customer}) akan dikembalikan ke status antrean QC.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Batalkan Selesai!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.sendRequest(orderId, false);
                    }
                });
            }
        },

        sendRequest(orderId, isComplete) {
            fetch(`/orders/${orderId}/toggle-complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ is_complete: isComplete })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan sistem', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal menghubungi server database', 'error');
            });
        }
    };
}
</script>
@endsection
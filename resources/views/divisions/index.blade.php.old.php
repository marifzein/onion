@extends('layouts.app', ['title' => 'ONION CREATIVE - ' . $config['name']])

@section('content')
<style>
    .qc-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 1.75rem;
        height: 1.75rem;
        border: 2.5px solid #94a3b8;
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
        width: 0.65rem;
        height: 1rem;
        border: solid white;
        border-width: 0 3.5px 3.5px 0;
        transform: rotate(45deg) translate(-1px, -1px);
    }
    /* Style saat checkbox terkunci */
    .qc-checkbox:disabled {
        background-color: #e2e8f0;
        border-color: #cbd5e1;
        cursor: not-allowed;
        opacity: 0.6;
    }
    @keyframes slowMarquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee-slow {
        display: inline-block;
        white-space: nowrap;
        animation: slowMarquee 45s linear infinite;
    }
</style>

@php
    $stageFlow = ['kaprod', 'bahan', 'print', 'jahit', 'finishing', 'qc', 'selesai'];
    $currentDivIndex = array_search($activeDivision, $stageFlow);
@endphp

<div x-data="divisionManager()" class="h-full w-full flex flex-col justify-between overflow-hidden">
    
    <!-- SUB-BAR / HEADER DIVISI -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-2 shrink-0">
        <div class="flex items-center gap-3">
            <span class="text-xs px-3 py-1 rounded-full text-white font-black uppercase tracking-wider {{ $config['badge_bg'] }} shadow-xs flex items-center gap-1.5">
                <i class="ph-bold ph-shield-check"></i> {{ $config['name'] }}
            </span>
            <span class="text-xs font-bold text-slate-500 hidden md:inline">
                {{ $config['sub'] }}
            </span>

            @if(in_array($user->role, ['admin', 'superadmin']))
                <select 
                    onchange="window.location.href='/update-status?div=' + this.value"
                    class="ml-2 px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-xs font-black text-black outline-none focus:border-black cursor-pointer shadow-xs"
                >
                    <option value="kaprod" {{ $activeDivision == 'kaprod' ? 'selected' : '' }}>Ka Prod</option>
                    <option value="bahan" {{ $activeDivision == 'bahan' ? 'selected' : '' }}>Divisi Bahan</option>
                    <option value="print" {{ $activeDivision == 'print' ? 'selected' : '' }}>Divisi Print</option>
                    <option value="jahit" {{ $activeDivision == 'jahit' ? 'selected' : '' }}>Divisi Jahit</option>
                    <option value="finishing" {{ $activeDivision == 'finishing' ? 'selected' : '' }}>Divisi Finishing</option>
                    <option value="qc" {{ $activeDivision == 'qc' ? 'selected' : '' }}>Divisi QC</option>
                </select>
            @endif
        </div>

        @php
            $totalCount = $orders->count();
            $doneCount = $orders->filter(function($o) use ($stageFlow, $currentDivIndex) {
                $orderIdx = array_search($o->current_status, $stageFlow);
                return $orderIdx !== false && $orderIdx > $currentDivIndex;
            })->count();
            $pendingCount = $totalCount - $doneCount;
        @endphp
        <div class="flex items-center gap-2 bg-white border border-slate-300 px-3 py-1 rounded-xl text-xs font-bold text-slate-700 shadow-xs">
            <span>Antrean: <strong class="text-black">{{ $totalCount }}</strong></span>
            <span class="text-slate-300">|</span>
            <span class="text-emerald-700">Selesai: <strong>{{ $doneCount }}</strong></span>
            <span class="text-slate-300">|</span>
            <span class="text-amber-700">Menunggu: <strong>{{ $pendingCount }}</strong></span>
        </div>
    </div>

    <!-- TABEL ORDERAN -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs flex-1 flex flex-col overflow-hidden">
        <div class="px-5 py-2 bg-slate-50 border-b border-slate-200 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <p class="text-xs font-bold text-slate-600">
                    Beri centang saat pesanan <strong class="text-black">selesai pada {{ $config['name'] }}</strong> untuk meneruskan ke divisi berikutnya.
                </p>
            </div>
            <div class="text-[11px] font-semibold text-slate-400">Rantai Produksi Berurutan (Strict Flow)</div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <table class="w-full border-collapse table-fixed text-xs">
                <thead class="bg-slate-100 border-b border-slate-300 sticky top-0 z-10">
                    <tr class="text-black text-xs md:text-sm font-black uppercase tracking-wider text-left">
                        <th class="py-2.5 px-3 w-[4%] text-center">No</th>
                        <th class="py-2.5 px-3 w-[25%]">No. SO & Customer</th>
                        <th class="py-2.5 px-3 w-[20%]">Fabric, Size & Qty</th>
                        <th class="py-2.5 px-3 w-[14%]">Order Out</th>
                        <th class="py-2.5 px-3 w-[13%] text-center">Tahap Sebelumnya</th>
                        <th class="py-2.5 px-3 w-[12%] text-center">Status</th>
                        <th class="py-2.5 px-3 w-[7%] text-center">Aksi Cek</th>
                        <th class="py-2.5 px-2 w-[5%] text-center">Edit</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($orders as $idx => $order)
                    @php
                        $orderStageIdx = array_search($order->current_status, $stageFlow);
                        if ($orderStageIdx === false) $orderStageIdx = 0;

                        // Status logika per divisi
                        $isPast = $orderStageIdx > $currentDivIndex;       // Sudah selesai di divisi ini
                        $isCurrent = $orderStageIdx === $currentDivIndex;  // Sedang giliran divisi ini
                        $isWaiting = $orderStageIdx < $currentDivIndex;    // Masih menunggu tahap sebelumnya

                        // Syarat bisa dicentang / diuncheck
                        $canCheck = $isCurrent;
                        $canUncheck = ($orderStageIdx === $currentDivIndex + 1); // hanya bisa batal jika divisi berikutnya belum selesai
                        $canAction = $canCheck || $canUncheck;

                        $progress = $order->stationProgress->firstWhere('division', $activeDivision);
                        $isUrgent = \Carbon\Carbon::parse($order->target_deadline)->isPast() || \Carbon\Carbon::parse($order->target_deadline)->isToday();
                    @endphp
                    <tr class="transition-colors {{ $isPast ? 'bg-emerald-50/30' : ($isWaiting ? 'bg-slate-50/50 opacity-60' : 'hover:bg-slate-50/80') }}">
                        <!-- No -->
                        <td class="py-2.5 px-3 text-center font-black text-black text-sm">
                            {{ $idx + 1 }}
                        </td>

                        <!-- No. SO & Customer -->
                        <td class="py-2.5 px-3">
                            <div class="truncate">
                                <p class="text-sm font-black text-black leading-tight">{{ $order->customer->name ?? $order->customer_name }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] font-mono font-bold text-slate-600">{{ $order->no_so }}</span>
                                    <span class="text-[11px] text-slate-500 font-semibold">&bull; {{ $order->order_type ?? 'Atasan Fullprint' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Fabric & Qty -->
                        <td class="py-2.5 px-3">
                            <div class="truncate">
                                <p class="text-xs font-black text-black">{{ $order->fabric_type ?? '-' }}</p>
                                <p class="text-xs font-black text-indigo-700 leading-tight">
                                    TOTAL: {{ $order->qty }} {{ strtoupper($order->satuan) }}
                                    @if($progress?->completed_qty)
                                        <span class="text-emerald-700 font-bold">({{ $progress->completed_qty }} ok)</span>
                                    @endif
                                </p>
                            </div>
                        </td>

                        <!-- Order Out -->
                        <td class="py-2.5 px-3">
                            <p class="text-sm font-black leading-tight {{ $isUrgent ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ \Carbon\Carbon::parse($order->target_deadline)->translatedFormat('d M Y') }}
                            </p>
                            <span class="text-[10px] font-black uppercase {{ $isUrgent ? 'text-rose-600' : 'text-slate-500' }}">
                                {{ \Carbon\Carbon::parse($order->target_deadline)->diffForHumans() }}
                            </span>
                        </td>

                        <!-- Tahap Sebelumnya -->
                        <td class="py-2.5 px-3 text-center">
                            @if($isWaiting)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="ph-bold ph-hourglass text-xs"></i> Belum Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-lime-100 text-lime-800 border border-lime-300 shadow-2xs">
                                    <i class="ph-bold ph-check-circle text-lime-600"></i> {{ $config['prev_label'] }}
                                </span>
                            @endif
                        </td>

                        <!-- Status Divisi Ini -->
                        <td class="py-2.5 px-3 text-center">
                            @if($isPast)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs">
                                    <i class="ph-bold ph-seal-check text-emerald-600 text-sm"></i> {{ $config['next_label'] }}
                                </span>
                            @elseif($isCurrent)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                    <i class="ph-bold ph-clock text-amber-600"></i> Belum selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-400 border border-slate-200">
                                    <i class="ph-bold ph-lock text-xs"></i> Dalam proses
                                </span>
                            @endif
                        </td>

                        <!-- Kolom Aksi Checkbox (Dikunci Jika Belum Gilirannya) -->
                        <td class="py-2.5 px-3 text-center">
                            <div class="flex items-center justify-center">
                                <input
                                    type="checkbox"
                                    class="qc-checkbox"
                                    {{ $isPast ? 'checked' : '' }}
                                    {{ !$canAction ? 'disabled' : '' }}
                                    @click.prevent="
                                        if (!{{ $canAction ? 'true' : 'false' }}) return;
                                        handleToggle({{ $order->id }}, '{{ $order->no_so }}', '{{ addslashes($order->customer->name ?? $order->customer_name) }}', {{ $isPast ? 'true' : 'false' }})
                                    "
                                    title="{{ !$canAction ? ($isWaiting ? 'Menunggu tahap sebelumnya selesai' : 'Sudah diproses divisi berikutnya') : ($isPast ? 'Klik untuk membatalkan (rollback)' : 'Klik untuk approve status selesai') }}"
                                />
                            </div>
                        </td>

                        <!-- Modal Edit -->
                        <td class="py-2.5 px-2 text-center">
                            <button
                                type="button"
                                @click="openEditModal({{ $order->id }}, '{{ $order->no_so }}', {{ $order->qty }}, {{ $progress?->completed_qty ?? 0 }}, '{{ addslashes($progress?->issue_note ?? '') }}')"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-600 hover:text-black transition-colors cursor-pointer mx-auto"
                                title="Edit progres qty & catatan kendala"
                            >
                                <i class="ph-bold ph-pencil-simple text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400 font-bold">
                            Tidak ada antrean pesanan untuk {{ $config['name'] }} saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RUNNING TEXT FOOTER -->
    <footer class="bg-black text-white h-10 px-5 flex items-center overflow-hidden border-t border-slate-800 shrink-0 select-none rounded-xl mt-2">
        <div class="flex items-center w-full">
            <div class="flex items-center gap-1.5 {{ $config['badge_bg'] }} text-white font-black text-xs uppercase tracking-wider px-2.5 py-0.5 rounded-md shrink-0 mr-3 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                <span>{{ $config['sop_title'] }}</span>
            </div>

            <div class="overflow-hidden relative w-full text-xs font-bold tracking-wide text-white">
                <div class="animate-marquee-slow inline-block whitespace-nowrap">
                    <span>{{ $config['sop_text'] }}</span>
                    <span class="mx-7 text-slate-500 font-black">&bull;</span>
                    <span class="text-amber-400 font-black">Tahap divisi bersifat sekuensial (tidak dapat melompati divisi sebelumnya).</span>
                    <span class="mx-7 text-slate-500 font-black">&bull;</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL EDIT PROGRES -->
    <div
        x-show="modalEditOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="modalEditOpen = false"
    >
        <div @click.outside="modalEditOpen = false" class="bg-white rounded-3xl border border-slate-300 shadow-2xl w-full max-w-sm overflow-hidden p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-sm font-black text-black">Catatan & Progres {{ $config['name'] }}</h3>
                    <p class="text-[11px] font-bold text-slate-400 font-mono" x-text="activeOrderNo"></p>
                </div>
                <button type="button" @click="modalEditOpen = false" class="text-slate-400 hover:text-black">
                    <i class="ph-bold ph-x text-base"></i>
                </button>
            </div>

            <form @submit.prevent="saveProgress()" class="mt-4 space-y-3.5 text-xs">
                <div>
                    <label class="block font-black uppercase text-slate-700 mb-1">
                        Qty Selesai Dikerjakan (Opsional)
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            type="number"
                            min="0"
                            :max="activeTotalQty"
                            x-model.number="formEdit.completed_qty"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-sm bg-slate-50"
                        />
                        <span class="text-slate-400 font-bold" x-text="'/ ' + activeTotalQty"></span>
                    </div>
                </div>

                <div>
                    <label class="block font-black uppercase text-slate-700 mb-1">
                        Catatan Kendala / Revisi (Opsional)
                    </label>
                    <textarea
                        x-model="formEdit.issue_note"
                        rows="3"
                        placeholder="Tulis kendala jika ada..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-black outline-none font-bold text-xs bg-slate-50"
                    ></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" @click="modalEditOpen = false" class="px-3.5 py-1.5 rounded-xl bg-slate-100 font-bold text-slate-600">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSaving" class="px-4 py-1.5 rounded-xl bg-black hover:bg-slate-800 text-white font-black shadow-xs cursor-pointer">
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Progres'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function divisionManager() {
    return {
        activeDivision: '{{ $activeDivision }}',
        modalEditOpen: false,
        activeOrderId: null,
        activeOrderNo: '',
        activeTotalQty: 0,
        isSaving: false,

        formEdit: {
            division: '{{ $activeDivision }}',
            completed_qty: 0,
            issue_note: '',
        },

        handleToggle(orderId, noSo, customerName, isCurrentlyCleared) {
            const title = isCurrentlyCleared 
                ? 'Update status menjadi belum selesai?' 
                : 'Update status ' + this.activeDivision.toUpperCase() + ' menjadi selesai?';
            
            const htmlText = isCurrentlyCleared
                ? `<span class="text-sm text-slate-600">Pesanan <b>${noSo}</b> (${customerName}) akan dikembalikan ke status antrean ${this.activeDivision.toUpperCase()}.</span>`
                : `<span class="text-sm text-slate-600">Pastikan pekerjaan order <b>${noSo}</b> (${customerName}) sudah selesai dan sesuai SOP.</span>`;

            const confirmBtnText = isCurrentlyCleared ? 'Ya, Batalkan!' : 'Ya, Selesai!';
            const confirmColor = isCurrentlyCleared ? '#e11d48' : '#00bf63';

            Swal.fire({
                title: title,
                html: htmlText,
                icon: isCurrentlyCleared ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/divisions/${orderId}/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            division: this.activeDivision,
                            is_cleared: !isCurrentlyCleared
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: isCurrentlyCleared ? 'Status Dibatalkan!' : 'Berhasil!',
                                text: data.message,
                                icon: isCurrentlyCleared ? 'info' : 'success',
                                confirmButtonColor: confirmColor,
                                timer: 1500,
                                showConfirmButton: false,
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Aksi Ditolak!',
                                text: data.message || 'Urutan tahap tidak sesuai.',
                                icon: 'error',
                                confirmButtonColor: '#e11d48',
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Gagal memproses ke database server', 'error');
                    });
                }
            });
        },

        openEditModal(id, noSo, totalQty, completedQty, note) {
            this.activeOrderId = id;
            this.activeOrderNo = noSo;
            this.activeTotalQty = totalQty;
            this.formEdit.completed_qty = completedQty || totalQty;
            this.formEdit.issue_note = note || '';
            this.modalEditOpen = true;
        },

        saveProgress() {
            this.isSaving = true;
            fetch(`/divisions/${this.activeOrderId}/update-progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.formEdit)
            })
            .then(res => res.json())
            .then(data => {
                this.isSaving = false;
                if (data.status === 'success') {
                    this.modalEditOpen = false;
                    Swal.fire({
                        title: 'Tersimpan!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal menyimpan catatan', 'error');
                }
            })
            .catch(() => {
                this.isSaving = false;
                Swal.fire('Error', 'Gagal menghubungi server database', 'error');
            });
        }
    };
}
</script>
@endsection
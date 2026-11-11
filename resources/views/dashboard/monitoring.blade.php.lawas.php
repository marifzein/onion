@extends('layouts.app', ['title' => 'ONION CREATIVE - Monitor Progress Produksi'])

@section('content')
<style>
    /* Animasi Ticker Running Text pelan */
    @keyframes slowMarquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee-slow {
        display: inline-block;
        white-space: nowrap;
        animation: slowMarquee 50s linear infinite;
    }
    .animate-marquee-slow:hover {
        animation-play-state: paused;
    }
</style>

<div x-data="monitoringApp()" class="h-full w-full flex flex-col justify-between overflow-hidden select-none">
    
    <!-- TABEL UTAMA ANTREAN & MONITORING STEPPER -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-xs flex-1 flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto">
            <table class="w-full border-collapse table-fixed text-xs">
                <thead class="bg-slate-100 border-b border-slate-300 sticky top-0 z-10">
                    <tr class="text-black text-xs md:text-sm font-black uppercase tracking-wider text-left">
                        <th class="py-2.5 px-2 w-[4%] text-center">No</th>
                        <th class="py-2.5 px-2 w-[20%]">No. SO & Customer</th>
                        <th class="py-2.5 px-2 w-[14%]">Fabric & Qty</th>
                        <th class="py-2.5 px-2 w-[12%]">Order Out</th>
                        <th class="py-2.5 px-3 w-[50%] text-center">Progress Alur Produksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 font-medium">
                    <template x-for="(order, idx) in orderList" :key="order.id">
                        <tr
                            @click="openDetail(order)"
                            class="cursor-pointer transition-colors group"
                            :class="order.is_closed ? 'bg-slate-50/70 opacity-60 hover:opacity-100 hover:bg-slate-100' : 'hover:bg-indigo-50/40'"
                        >
                            <!-- No -->
                            <td
                                class="py-2 px-2 text-center font-black text-base"
                                :class="order.is_closed ? 'text-slate-500' : 'text-black'"
                                x-text="idx + 1"
                            ></td>

                            <!-- SO & Customer -->
                            <td class="py-2 px-2">
                                <div class="truncate">
                                    <p
                                        class="text-sm font-black leading-tight flex items-center gap-1.5"
                                        :class="order.is_closed ? 'text-slate-600 line-through' : 'text-black'"
                                    >
                                        <span x-text="order.customer"></span>
                                        <i class="ph-bold ph-arrow-square-out text-slate-400 group-hover:text-indigo-600 text-xs transition-colors"></i>
                                    </p>
                                    <span class="text-[11px] font-mono font-bold text-slate-600" x-text="order.so"></span>
                                    <span class="text-[11px] text-slate-500 font-bold" x-text="'• ' + order.type"></span>
                                </div>
                            </td>

                            <!-- Fabric & Qty -->
                            <td class="py-2 px-2">
                                <div class="truncate">
                                    <p class="text-xs font-black text-black" x-text="order.fabric"></p>
                                    <p class="text-xs font-black text-indigo-700 leading-tight" x-text="'TOTAL: ' + order.total_qty + ' PCS'"></p>
                                </div>
                            </td>

                            <!-- Deadline -->
                            <td class="py-2 px-2">
                                <p
                                    class="text-sm font-black leading-tight"
                                    :class="order.is_urgent ? 'text-rose-600' : (order.is_closed ? 'text-slate-500 line-through' : 'text-black')"
                                    x-text="order.order_out"
                                ></p>
                                <span
                                    class="text-[10px] font-black uppercase"
                                    :class="order.is_urgent ? 'text-rose-600' : 'text-slate-500'"
                                    x-text="order.deadline_note"
                                ></span>
                            </td>

                            <!-- Stepper Horizontal (7 Tahap) -->
                            <td class="py-2 px-3">
                                <div class="flex items-center justify-between w-full px-1">
                                    
                                    <!-- 1. Admin -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full text-white flex items-center justify-center font-bold shadow-xs"
                                            :class="order.step_active === 1 && !order.is_closed ? 'ring-4 ring-fuchsia-200' : ''"
                                            style="background-color: #cb6ce6"
                                        >
                                            <i class="ph-bold ph-check text-xs"></i>
                                        </div>
                                        <span class="text-[9px] font-black mt-0.5 uppercase tracking-tight text-black">Admin</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-slate-300 mx-1 mb-2.5"></div>

                                    <!-- 2. Ka Prod -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                            :class="order.step_active >= 2 ? 'text-white shadow-xs' : 'bg-slate-200 text-transparent'"
                                            :style="order.step_active >= 2 ? 'background-color: #5170ff;' : ''"
                                        >
                                            <i class="ph-bold ph-check text-xs" x-show="order.step_active >= 2"></i>
                                        </div>
                                        <span class="text-[9px] mt-0.5 uppercase tracking-tight" :class="order.step_active >= 2 ? 'font-black text-black' : 'font-bold text-slate-400'">Ka Prod</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-slate-300 mx-1 mb-2.5"></div>

                                    <!-- 3. Bahan -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                            :class="order.step_active >= 3 ? 'text-white shadow-xs' : 'bg-slate-200 text-transparent'"
                                            :style="order.step_active >= 3 ? 'background-color: #0cc0df;' : ''"
                                        >
                                            <i class="ph-bold ph-check text-xs" x-show="order.step_active >= 3"></i>
                                        </div>
                                        <span class="text-[9px] mt-0.5 uppercase tracking-tight" :class="order.step_active >= 3 ? 'font-black text-black' : 'font-bold text-slate-400'">Bahan</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-slate-300 mx-1 mb-2.5"></div>

                                    <!-- 4. Printing -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                            :class="order.step_active >= 4 ? 'text-white shadow-xs' : 'bg-slate-200 text-transparent'"
                                            :style="order.step_active >= 4 ? 'background-color: #ff914d;' : ''"
                                        >
                                            <i class="ph-bold ph-check text-xs" x-show="order.step_active >= 4"></i>
                                        </div>
                                        <span class="text-[9px] mt-0.5 uppercase tracking-tight" :class="order.step_active >= 4 ? 'font-black text-black' : 'font-bold text-slate-400'">Print</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-slate-300 mx-1 mb-2.5"></div>

                                    <!-- 5. Jahit -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                            :class="order.step_active >= 5 ? 'text-slate-900 shadow-xs' : 'bg-slate-200 text-transparent'"
                                            :style="order.step_active >= 5 ? 'background-color: #5ce7ff;' : ''"
                                        >
                                            <i class="ph-bold ph-check text-xs" x-show="order.step_active >= 5"></i>
                                        </div>
                                        <span class="text-[9px] mt-0.5 uppercase tracking-tight" :class="order.step_active >= 5 ? 'font-black text-black' : 'font-bold text-slate-400'">Jahit</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-slate-300 mx-1 mb-2.5"></div>

                                    <!-- 6. Finishing -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                            :class="order.step_active >= 6 ? 'text-white shadow-xs' : 'bg-slate-200 text-transparent'"
                                            :style="order.step_active >= 6 ? 'background-color: #74cc00;' : ''"
                                        >
                                            <i class="ph-bold ph-check text-xs" x-show="order.step_active >= 6"></i>
                                        </div>
                                        <span class="text-[9px] mt-0.5 uppercase tracking-tight" :class="order.step_active >= 6 ? 'font-black text-black' : 'font-bold text-slate-400'">Finishing</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-slate-300 mx-1 mb-2.5"></div>

                                    <!-- 7. QC / Selesai -->
                                    <div class="w-10 shrink-0 flex flex-col items-center">
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                            :class="order.step_active >= 7 ? 'text-white shadow-xs' : 'bg-slate-200 text-transparent'"
                                            :style="order.step_active >= 7 ? 'background-color: #00bf63;' : ''"
                                        >
                                            <i class="ph-bold ph-check text-xs" x-show="order.step_active >= 7"></i>
                                        </div>
                                        <span
                                            class="text-[9px] mt-0.5 uppercase tracking-tight"
                                            :class="order.step_active >= 7 ? 'font-black text-black' : 'font-bold text-slate-400'"
                                            x-text="order.is_closed ? 'SELESAI' : 'QC'"
                                        ></span>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RUNNING TEXT FOOTER PERSIS PROTOTIPE -->
    <footer class="bg-black text-white h-10 px-5 flex items-center overflow-hidden border-t border-slate-800 shrink-0 select-none rounded-xl mt-2">
        <div class="flex items-center w-full">
            <div class="flex items-center gap-1.5 bg-rose-600 text-white font-black text-xs uppercase tracking-wider px-2.5 py-0.5 rounded-md shrink-0 mr-3 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                <span>Prioritas Hari Ini</span>
            </div>

            <div class="overflow-hidden relative w-full text-xs font-bold tracking-wide text-white">
                <div class="animate-marquee-slow inline-block whitespace-nowrap">
                    <template x-for="item in urgentList" :key="item.id">
                        <span>
                            <span class="text-amber-400 font-black text-xs" x-text="item.customer"></span>
                            - <span x-text="item.total_qty + ' pcs ' + item.fabric"></span>
                            (Posisi: <strong class="text-emerald-400" x-text="item.current_div"></strong>)
                            <span class="mx-6 text-slate-500 font-black">&bull;</span>
                        </span>
                    </template>
                    <span class="text-slate-300">Klik baris orderan untuk membuka dialog rincian lengkap SPK & lembar formulir fisik.</span>
                    <span class="mx-6 text-slate-500 font-black">&bull;</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL POPUP DETAIL SPK -->
    <div
        x-show="selectedOrder"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        @keydown.escape.window="selectedOrder = null"
    >
        <div
            @click.outside="selectedOrder = null"
            class="bg-white rounded-3xl border border-slate-300 shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white">
                        <i class="ph-bold ph-receipt text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black tracking-tight" x-text="selectedOrder?.customer"></h3>
                            <span
                                class="text-[10px] font-black px-2 py-0.5 rounded-full uppercase"
                                :style="'background-color:' + selectedOrder?.current_div_color + '; color:#fff;'"
                                x-text="selectedOrder?.current_div"
                            ></span>
                        </div>
                        <p class="text-xs text-slate-400 font-mono" x-text="selectedOrder?.so + ' • Masuk: ' + selectedOrder?.order_in"></p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="selectedOrder = null"
                    class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-lg cursor-pointer"
                >
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto space-y-4 text-slate-800 text-xs">
                
                <!-- Box Progress Realtime -->
                <div class="bg-indigo-50/60 border border-indigo-200 rounded-2xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full animate-ping" :style="'background-color:' + selectedOrder?.current_div_color"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-slate-700">
                                Posisi Divisi Saat Ini: <strong class="text-indigo-950" x-text="selectedOrder?.current_div"></strong>
                            </span>
                        </div>
                        <span class="text-xs font-black text-indigo-700 font-mono" x-text="'Progres Fisik: ' + selectedOrder?.progress_pcs"></span>
                    </div>
                    <p class="text-xs font-bold text-slate-700 mb-2.5" x-text="selectedOrder?.current_job"></p>
                    
                    <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-500"
                            :style="'width:' + selectedOrder?.progress_percent + '%; background-color:' + selectedOrder?.current_div_color"
                        ></div>
                    </div>
                </div>

                <!-- 2 Kolom Spesifikasi & Foto Form SPK -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Rincian Spesifikasi -->
                    <div class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                            <i class="ph-bold ph-info"></i> Rincian Spesifikasi Order
                        </h4>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between border-b border-slate-200 pb-1">
                                <span class="text-slate-500">Tipe Pesanan:</span>
                                <span class="font-black text-black" x-text="selectedOrder?.type"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-1">
                                <span class="text-slate-500">Bahan / Fabric:</span>
                                <span class="font-black text-black" x-text="selectedOrder?.fabric"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-1">
                                <span class="text-slate-500">Tanggal Masuk (In):</span>
                                <span class="font-bold text-slate-700" x-text="selectedOrder?.order_in"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200 pb-1">
                                <span class="text-slate-500">Target Selesai (Out):</span>
                                <span class="font-black text-rose-600" x-text="selectedOrder?.order_out"></span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span class="text-slate-600 font-bold">Total Jumlah:</span>
                                <span class="font-black text-indigo-700 text-sm" x-text="selectedOrder?.total_qty + ' PCS'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Lembar Form SPK Fisik -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-2 flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i class="ph-bold ph-image"></i> Lembar Form SPK Fisik</span>
                                <span class="text-[10px] text-indigo-600 font-bold lowercase">klik foto untuk perbesar</span>
                            </h4>
                            <div
                                @click="if (selectedOrder?.spk_image) zoomImage = true"
                                class="w-full h-44 bg-slate-200 rounded-xl overflow-hidden border border-slate-300 cursor-zoom-in relative group flex items-center justify-center"
                            >
                                <template x-if="selectedOrder?.spk_image">
                                    <img
                                        :src="selectedOrder?.spk_image"
                                        alt="Foto SPK"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    />
                                </template>
                                <template x-if="!selectedOrder?.spk_image">
                                    <div class="text-center p-4 text-slate-400">
                                        <i class="ph-bold ph-image-broken text-3xl mb-1"></i>
                                        <p class="text-xs font-bold">Tidak ada lampiran foto fisik</p>
                                    </div>
                                </template>
                                <div
                                    x-show="selectedOrder?.spk_image"
                                    class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-black"
                                >
                                    <i class="ph-bold ph-magnifying-glass-plus text-lg mr-1"></i> Perbesar Foto
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Catatan Spesifikasi Khusus -->
                <div class="bg-amber-50/70 border border-amber-200 p-4 rounded-2xl">
                    <h4 class="text-xs font-black uppercase tracking-wider text-amber-800 mb-1 flex items-center gap-1.5">
                        <i class="ph-bold ph-warning-circle"></i> Catatan Spesifikasi Khusus:
                    </h4>
                    <p class="text-xs font-bold text-slate-800 leading-relaxed" x-text="selectedOrder?.description"></p>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold">
                    <i class="ph-bold ph-info text-indigo-600"></i>
                    <span>Data tersinkronisasi otomatis dengan checklist divisi operator.</span>
                </div>
                <button
                    type="button"
                    @click="selectedOrder = null"
                    class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-black rounded-xl transition-colors cursor-pointer"
                >
                    Tutup Dialog
                </button>
            </div>
        </div>

        <!-- Zoom Modal Foto SPK Layar Penuh -->
        <div
            x-show="zoomImage"
            x-cloak
            class="fixed inset-0 z-60 bg-black/85 flex items-center justify-center p-4 cursor-zoom-out"
            @click="zoomImage = false"
        >
            <div class="max-w-4xl max-h-[92vh] bg-white p-2 rounded-2xl overflow-hidden shadow-2xl relative">
                <img
                    :src="selectedOrder?.spk_image"
                    alt="Foto SPK Layar Penuh"
                    class="w-full h-full max-h-[88vh] object-contain"
                />
                <button
                    type="button"
                    @click.stop="zoomImage = false"
                    class="absolute top-4 right-4 bg-black/70 hover:bg-black text-white w-9 h-9 rounded-full flex items-center justify-center font-black"
                >
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
        </div>

    </div>

</div>

<script>
function monitoringApp() {
    return {
        selectedOrder: null,
        zoomImage: false,
        orderList: @json($orders),

        get urgentList() {
            const urgents = this.orderList.filter(o => o.is_urgent);
            return urgents.length > 0 ? urgents : this.orderList.slice(0, 4);
        },

        openDetail(order) {
            this.selectedOrder = order;
        }
    };
}
</script>
@endsection
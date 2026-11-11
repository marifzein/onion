<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\OrderStationProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['cabang', 'customer'])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    // Tampilkan Form Input Sales Order
    public function create()
    {
        $cabangs = Cabang::where('is_active', true)->get();
        
        // Auto generate No SO default
        $noSo = 'SO-' . date('Ym') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        return view('orders.create', compact('cabangs', 'noSo'));
    }

    // Simpan Data Order ke Database
    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi login telah berakhir, silakan login kembali.',
            ], 401);
        }

        $validated = $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'no_so'           => 'required|string|unique:orders,no_so',
            'customer_name'   => 'required|string|max:255',
            'order_title'     => 'required|string|max:255',
            'qty'             => 'required|integer|min:1',
            'satuan'          => 'required|in:pcs,stel',
            'fabric_type'     => 'required|string|max:255',
            'order_type'      => 'nullable|string|max:255',
            'target_deadline' => 'required|date',
            'notes'           => 'nullable|string',
            'spk_photo_base64'=> 'nullable|string',
        ]);

        $spkPath = null;

        // Proses kompresi biner WebP / Base64 dari HP atau Paste WA
        if (!empty($validated['spk_photo_base64']) && str_starts_with($validated['spk_photo_base64'], 'data:image')) {
            $imageParts = explode(';base64,', $validated['spk_photo_base64']);
            $imageBase64 = base64_decode($imageParts[1]);

            $fileName = 'spk_documents/' . Str::slug($validated['no_so']) . '-' . time() . '.webp';
            Storage::disk('public')->put($fileName, $imageBase64);
            $spkPath = $fileName;
        }

        try {
            $order = DB::transaction(function () use ($validated, $spkPath, $user) {
                // 1. Simpan Header Order
                $newOrder = Order::create([
                    'no_so'           => $validated['no_so'],
                    'cabang_id'       => $user->cabang_id,
                    'customer_id'     => $validated['customer_id'],
                    'customer_name'   => $validated['customer_name'],
                    'order_title'     => $validated['order_title'],
                    'qty'             => $validated['qty'],
                    'satuan'          => $validated['satuan'],
                    'fabric_type'     => $validated['fabric_type'],
                    'order_type'      => $validated['order_type'] ?? 'Atasan Fullprint',
                    'target_deadline' => $validated['target_deadline'],
                    'spk_photo_path'  => $spkPath,
                    'notes'           => $validated['notes'] ?? null,
                    'current_status'  => 'kaprod',
                    'created_by'      => $user->id,
                ]);

                // 2. Simpan Riwayat Log Pertama
                OrderStatusLog::create([
                    'order_id'    => $newOrder->id,
                    'cabang_id'   => $user->cabang_id,
                    'from_status' => 'draft_admin',
                    'to_status'   => 'kaprod',
                    'user_id'     => $user->id,
                    'action_type' => 'forward',
                    'note'        => 'Sales order baru diinput oleh ' . $user->name,
                ]);

                // 3. Inisialisasi Antrean Divisi Produksi
                $divisions = ['bahan', 'print', 'jahit', 'qc', 'finishing'];
                foreach ($divisions as $div) {
                    OrderStationProgress::create([
                        'order_id'   => $newOrder->id,
                        'cabang_id'  => $user->cabang_id,
                        'division'   => $div,
                        'is_cleared' => 0,
                    ]);
                }

                return $newOrder;
            });

            return response()->json([
                'status'  => 'success',
                'message' => "Order {$order->no_so} berhasil disimpan ke database!",
                'order'   => $order,
            ]);

        } catch (\Throwable $e) {
            if ($spkPath && Storage::disk('public')->exists($spkPath)) {
                Storage::disk('public')->delete($spkPath);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Transaksi database gagal: ' . $e->getMessage(),
            ], 500);
        }
    }
}
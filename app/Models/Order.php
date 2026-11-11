<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'no_so',
        'cabang_id',
        'customer_id',
        'customer_name',
        'order_title',
        'qty',
        'satuan',
        'fabric_type',
        'order_type',
        'target_deadline',
        'spk_photo_path',
        'notes',
        'current_status',
        'created_by',
    ];

    public const STAGES = [
        'kaprod'    => 1,
        'bahan'     => 2,
        'print'     => 3,
        'jahit'     => 4,
        'qc'        => 5,
        'finishing' => 6,
        'selesai'   => 7,   
    ];

    // acessor laravel, getStatusInfoAttribute camel case, buang 'get' dan 'Attribute' yg tengah jadi snake case status_info
    public function getStatusInfoAttribute(): array
    {
        return match ($this->current_status) {
            'kaprod' => [
                'label'       => 'Ka Prod',
                'bg'          => 'bg-blue-100 text-blue-800 border-blue-300',
                'dot'         => 'bg-blue-500',
                'next_label'  => 'ACC & Kirim ke Bahan',
                'next_status' => 'bahan',
            ],
            'bahan' => [
                'label'       => 'Divisi Bahan',
                'bg'          => 'bg-cyan-100 text-cyan-800 border-cyan-300',
                'dot'         => 'bg-cyan-500',
                'next_label'  => 'Bahan Selesai -> Kirim ke Print',
                'next_status' => 'print',
            ],
            'print' => [
                'label'       => 'Divisi Print',
                'bg'          => 'bg-amber-100 text-amber-800 border-amber-300',
                'dot'         => 'bg-amber-500',
                'next_label'  => 'Print Selesai -> Kirim ke Jahit',
                'next_status' => 'jahit',
            ],
            'jahit' => [
                'label'       => 'Divisi Jahit',
                'bg'          => 'bg-sky-100 text-sky-800 border-sky-300',
                'dot'         => 'bg-sky-500',
                'next_label'  => 'Jahit Selesai -> Kirim ke QC',
                'next_status' => 'qc',
            ],
            'qc' => [
                'label'       => 'Divisi QC',
                'bg'          => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'dot'         => 'bg-emerald-500',
                'next_label'  => 'Lolos QC -> Kirim ke Finishing',
                'next_status' => 'finishing',
            ],
            'finishing' => [
                'label'       => 'Divisi Finishing',
                'bg'          => 'bg-lime-100 text-lime-800 border-lime-300',
                'dot'         => 'bg-lime-500',
                'next_label'  => 'Finishing Selesai -> Siap Diambil',
                'next_status' => 'selesai',
            ],
            'selesai' => [
                'label'       => 'Order Selesai',
                'bg'          => 'bg-slate-100 text-slate-600 border-slate-300',
                'dot'         => 'bg-slate-400',
                'next_label'  => null,
                'next_status' => null,
            ],
            default => [
                'label'       => strtoupper($this->current_status),
                'bg'          => 'bg-slate-100 text-slate-800 border-slate-300',
                'dot'         => 'bg-slate-400',
                'next_label'  => null,
                'next_status' => null,
            ],
        };
    }


    public function getCurrentStepLevelAttribute(): int
    {
        return self::STAGES[$this->current_status] ?? 1;
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stationProgress(): HasMany
    {
        return $this->hasMany(OrderStationProgress::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->latest();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
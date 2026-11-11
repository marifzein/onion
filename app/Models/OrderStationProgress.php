<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStationProgress extends Model
{
    use HasFactory;

    protected $table = 'order_station_progress';

    protected $fillable = [
        'order_id',
        'cabang_id',
        'division',
        'user_id',
        'completed_qty',
        'current_activity',
        'issue_note',
        'is_cleared',
        'finished_at',
    ];

    protected $casts = [
        'is_cleared'   => 'boolean',
        'finished_at'  => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
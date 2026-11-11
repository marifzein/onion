<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cabang_id',
        'from_status',
        'to_status',
        'user_id',
        'action_type',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
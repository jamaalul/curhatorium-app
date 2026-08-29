<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakeOrder extends Model
{
    use HasFactory;

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    protected $fillable = [
        'order_ref',
        'user_id',
        'orderable_type',
        'orderable_id',
        'quantity',
        'unit_price',
        'gross_amount',
        'status',
        'expired_at',
    ];
}

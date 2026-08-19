<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_transaction_id',
        'gross_amount',
        'currency',
        'payment_type',
        'transaction_status',
        'qris_url',
        'payment_gateway_response',
        'transaction_time',
        'expired_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'payment_gateway_response' => 'json',
        'transaction_time' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isSettled(): bool
    {
        return $this->transaction_status === 'settlement';
    }

    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }
}

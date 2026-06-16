<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderable()
    {
        return $this->morphTo();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

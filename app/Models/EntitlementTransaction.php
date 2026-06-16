<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntitlementTransaction extends Model
{
    protected $fillable = [
        'user_entitlement_id',
        'user_id',
        'benefit',
        'amount_delta',
        'amount_after',
        'source_type',
    ];

    protected $casts = [
        'benefit' => EntitlementTypeEnum::class,
        'source_type' => EntitlementTransactionSourceTypeEnum::class,
    ];

    public function userEntitlement(): BelongsTo
    {
        return $this->belongsTo(UserEntitlement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

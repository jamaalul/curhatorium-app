<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    use HasFactory;

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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->isPending() && $this->expired_at?->isPast());
    }

    /**
     * Generate a unique order reference like "ORD-20260616-A3F8B".
     */
    public static function generateOrderRef(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        return "ORD-{$date}-{$random}";
    }
}

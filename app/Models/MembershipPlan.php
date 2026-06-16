<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MembershipPlan extends Model
{
    protected $fillable = [
        'name',
        'price_idr',
        'billing_cycle',
        'ai_window_hours',
    ];

    public function getPriceInIDR(): string
    {
        return "Rp " . number_format($this->price_idr, 2, ",", ".");
    }

    public function planBenefits(): HasMany
    {
        return $this->hasMany(PlanBenefit::class);
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }
}

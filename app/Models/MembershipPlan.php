<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function planBenefit(): HasMany
    {
        return $this->hasMany(PlanBenefit::class);
    }
}

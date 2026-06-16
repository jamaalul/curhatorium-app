<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = [
        'name',
        'price_idr',
        'billing_cycle',
    ];

    public function getPriceInIDR(): string
    {
        return "Rp " . number_format($this->price_idr, 2, ",", ".");
    }
}

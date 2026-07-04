<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcommerceLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
        'ecommerce_name',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

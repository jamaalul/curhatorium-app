<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'ecommerce_url',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('order_number');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductMedia::class)
            ->ofMany(
                ['order_number' => 'MIN', 'id' => 'MIN'],
                function (Builder $query): void {
                    $query->where('media_type', 'image');
                },
            );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

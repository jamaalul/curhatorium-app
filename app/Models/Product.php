<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'product_category_id',
        'slug',
        'description',
        'price',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Product $product): void {
            $product->media()->get()->each->delete();
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('order_number');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function ecommerceLinks(): HasMany
    {
        return $this->hasMany(EcommerceLink::class);
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

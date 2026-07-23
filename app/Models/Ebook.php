<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ebook extends Model
{
    use HasFactory;

    protected $fillable = [
        'ebook_category_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'file_url',
        'price',
        'page_count',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'page_count' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EbookCategory::class, 'ebook_category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(EbookComment::class);
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn (): string => $this->title);
    }
}

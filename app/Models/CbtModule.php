<?php

namespace App\Models;

use App\ProgressStatus;
use Database\Factories\CbtModuleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CbtModule extends Model
{
    /** @use HasFactory<CbtModuleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'is_published',
        'published_at',
    ];

    protected $attributes = [
        'is_published' => false,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order_number');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(UserCbtModule::class);
    }

    public function moduleProgresses(): HasMany
    {
        return $this->hasMany(UserModuleProgress::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->entitlements()
            ->whereBelongsTo($user)
            ->active()
            ->exists();
    }

    public function progressPercentage(User $user): float
    {
        $totalChapters = $this->chapters()->count();

        if ($totalChapters === 0) {
            return 0.0;
        }

        $completedChapters = UserChapterProgress::query()
            ->whereBelongsTo($user)
            ->whereIn('chapter_id', $this->chapters()->select('id'))
            ->where('status', ProgressStatus::Completed->value)
            ->count();

        return round(($completedChapters / $totalChapters) * 100, 2);
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn (): string => $this->title);
    }
}

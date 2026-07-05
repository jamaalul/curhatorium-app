<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductMedia extends Model
{
    use HasFactory;

    private const STORAGE_DISK = 'public';

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'media_type',
        'media_url',
        'order_number',
    ];

    protected function casts(): array
    {
        return [
            'order_number' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (ProductMedia $productMedia): void {
            self::deleteStoredFile($productMedia->media_url);
        });

        static::updated(function (ProductMedia $productMedia): void {
            if (! array_key_exists('media_url', $productMedia->getChanges())) {
                return;
            }

            self::deleteStoredFile($productMedia->getPrevious()['media_url'] ?? null);
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function publicUrl(): string
    {
        if (self::isRemoteUrl($this->media_url)) {
            return $this->media_url;
        }

        return Storage::disk(self::STORAGE_DISK)->url($this->media_url);
    }

    private static function deleteStoredFile(?string $path): void
    {
        if (blank($path) || self::isRemoteUrl($path)) {
            return;
        }

        Storage::disk(self::STORAGE_DISK)->delete($path);
    }

    private static function isRemoteUrl(?string $path): bool
    {
        return Str::startsWith((string) $path, ['http://', 'https://']);
    }
}

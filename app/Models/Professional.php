<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Professional extends Model
{
    protected $fillable = [
        'name', 'title', 'avatar', 'specialties', 'availability', 'availabilityText', 'type', 'rating',
        'whatsapp_number', 'bank_account_number', 'bank_name'
    ];

    protected $casts = [
        'specialties' => 'array',
    ];

    protected static function booted()
    {
        parent::booted();
        static::deleting(function ($professional) {
            if ($professional->avatar && Storage::disk('public')->exists($professional->avatar)) {
                Storage::disk('public')->delete($professional->avatar);
            }
        });
        static::updating(function ($professional) {
            if ($professional->isDirty('avatar')) {
                $original = $professional->getOriginal('avatar');
                if ($original && Storage::disk('public')->exists($original)) {
                    Storage::disk('public')->delete($original);
                }
            }
        });
    }
}

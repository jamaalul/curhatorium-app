<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Professional extends Authenticatable
{
    protected $fillable = [
        'name', 'password', 'title', 'avatar', 'specialties', 'type', 'rating',
        'whatsapp_number', 'bank_account_number', 'bank_name',
        'availability', 'availabilityText',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'specialties' => 'array',
        'password' => 'hashed',
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

    /**
     * Check if professional has an active session
     */
    public function hasActiveSession()
    {
        return $this->consultations()
            ->whereIn('status', ['active', 'pending'])
            ->where('start', '>', Carbon::now('Asia/Jakarta')->subMinutes(5))
            ->exists();
    }

    /**
     * Relationship with consultations
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Relationship with schedule slots
     */
    public function scheduleSlots()
    {
        return $this->hasMany(ProfessionalScheduleSlot::class);
    }

    /**
     * Get all of the professional's consultation messages.
     */
    public function consultationMessages()
    {
        return $this->morphMany(ConsultationMessage::class, 'sender');
    }
}

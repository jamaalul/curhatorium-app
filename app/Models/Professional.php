<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

    /**
     * Check if professional has an active session
     */
    public function hasActiveSession()
    {
        return $this->chatSessions()
            ->whereIn('status', ['active', 'pending'])
            ->where('start', '>', Carbon::now('Asia/Jakarta')->subMinutes(5))
            ->exists();
    }

    /**
     * Get the effective availability status (considers active sessions)
     */
    public function getEffectiveAvailability()
    {
        if ($this->availability === 'offline') {
            return 'offline';
        }

        if ($this->hasActiveSession()) {
            return 'busy';
        }

        return 'online';
    }

    /**
     * Get the effective availability text
     */
    public function getEffectiveAvailabilityText()
    {
        $status = $this->getEffectiveAvailability();
        
        switch ($status) {
            case 'offline':
                return 'Offline';
            case 'busy':
                return 'Sedang dalam sesi';
            case 'online':
                return 'Tersedia';
            default:
                return 'Tidak diketahui';
        }
    }

    /**
     * Relationship with chat sessions
     */
    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }
}

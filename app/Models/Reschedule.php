<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reschedule extends Model
{
    protected $fillable = [
        'consultation_id',
        'original_slot_id',
        'status',
        'token',
        'expires_at',
        'client_response_at',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'client_response_at' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function originalSlot()
    {
        return $this->belongsTo(ProfessionalScheduleSlot::class, 'original_slot_id');
    }

    public function rescheduleSlots()
    {
        return $this->hasMany(RescheduleSlot::class);
    }
}

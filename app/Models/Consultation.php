<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    protected $table = 'consultations';

    protected $fillable = [
        'professional_schedule_slot_id',
        'user_id',
        'professional_id',
        'room',
        'consultation_type',
        'no_wa',
        'status',
        'start',
        'end',
        'pending_end',
        'jitsi_room',
        'facilitator_status',
        'client_status',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'pending_end' => 'datetime',
    ];

    public function professionalScheduleSlot(): BelongsTo
    {
        return $this->belongsTo(ProfessionalScheduleSlot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConsultationMessage::class);
    }
}

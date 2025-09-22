<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'consultations';
    protected $fillable = [
        'professional_schedule_slot_id',
        'room',
        'consultation_type',
        'no_wa',
    ];

    public function professionalScheduleSlot()
    {
        return $this->belongsTo(ProfessionalScheduleSlot::class);
    }
}

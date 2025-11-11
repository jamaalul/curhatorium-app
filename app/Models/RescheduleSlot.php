<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RescheduleSlot extends Model
{
    protected $table = 'reschedule_slots';

    protected $fillable = [
        'reschedule_id',
        'professional_schedule_slot_id',
        'is_selected',
    ];

    public function reschedule()
    {
        return $this->belongsTo(Reschedule::class);
    }

    public function slot()
    {
        return $this->belongsTo(ProfessionalScheduleSlot::class, 'professional_schedule_slot_id');
    }
}

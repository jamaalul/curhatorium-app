<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalScheduleSlot extends Model
{
    protected $fillable = [
        'professional_id',
        'slot_start_time',
        'slot_end_time',
        'status',
        'booked_by_user_id',
    ];

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }
}

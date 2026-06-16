<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ConsultationMessage extends Model
{
    protected $fillable = [
        'consultation_id',
        'sender_id',
        'sender_type',
        'message',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }
}

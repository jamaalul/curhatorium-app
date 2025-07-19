<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentalHealthTestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_score',
        'emotional_score',
        'social_score',
        'psychological_score',
        'category',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
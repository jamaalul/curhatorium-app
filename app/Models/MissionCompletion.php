<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mission_id',
        'reflection',
        'feeling',
        'completed_at',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
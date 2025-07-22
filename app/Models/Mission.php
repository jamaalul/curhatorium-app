<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MissionCompletion;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'points',
        'difficulty',
    ];

    public function completions()
    {
        return $this->hasMany(MissionCompletion::class);
    }
} 
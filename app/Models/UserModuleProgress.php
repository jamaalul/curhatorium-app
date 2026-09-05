<?php

namespace App\Models;

use App\ProgressStatus;
use Database\Factories\UserModuleProgressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModuleProgress extends Model
{
    /** @use HasFactory<UserModuleProgressFactory> */
    use HasFactory;

    protected $table = 'user_module_progresses';

    protected $fillable = [
        'user_id',
        'cbt_module_id',
        'status',
        'started_at',
        'last_accessed_at',
        'completed_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => ProgressStatus::class,
            'started_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CbtModule::class, 'cbt_module_id');
    }

    public function markCompleted(): void
    {
        $this->forceFill([
            'status' => ProgressStatus::Completed,
            'started_at' => $this->started_at ?? now(),
            'last_accessed_at' => now(),
            'completed_at' => $this->completed_at ?? now(),
        ])->save();
    }

    public function markInProgress(): void
    {
        $this->forceFill([
            'status' => ProgressStatus::InProgress,
            'started_at' => $this->started_at ?? now(),
            'last_accessed_at' => now(),
            'completed_at' => null,
        ])->save();
    }
}

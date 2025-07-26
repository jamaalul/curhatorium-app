<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserTicket extends Model
{
    // For 'unlimited' tickets, limit_value and remaining_value are null. For others, these are numeric.
    protected $fillable = [
        'user_id', 'ticket_type', 'limit_type', 'limit_value', 'remaining_value', 'expires_at',
    ];

    /**
     * Check if the ticket should be deleted (has 0 limit_value or is expired)
     */
    public function shouldBeDeleted(): bool
    {
        // Check if ticket has 0 limit_value (not null)
        if ($this->limit_value === 0) {
            return true;
        }

        // Check if ticket is expired
        if ($this->expires_at && $this->expires_at < Carbon::now()) {
            return true;
        }

        return false;
    }

    /**
     * Clean up tickets that should be deleted after consumption
     */
    public static function cleanupAfterConsumption()
    {
        $deletedCount = self::where('limit_value', 0)
            ->orWhere('expires_at', '<', Carbon::now())
            ->delete();
        return $deletedCount;
    }
}

<?php

namespace App\Services;

use App\Models\Professional;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    /**
     * Generate schedule slots for a professional.
     *
     * @param Professional $professional
     * @param array $daysOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param string $startDate
     * @param string $endDate
     * @param int $slotDuration in minutes
     * @return void
     */
    public function generateSlots(
        Professional $professional,
        array $daysOfWeek,
        string $startTime,
        string $endTime,
        string $startDate,
        string $endDate,
        int $slotDuration = 60
    ): void
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $slots = [];

        foreach ($period as $date) {
            if (in_array($date->dayOfWeek, $daysOfWeek)) {
                $startDateTime = $date->copy()->setTimeFromTimeString($startTime);
                $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

                while ($startDateTime < $endDateTime) {
                    $slotEndTime = $startDateTime->copy()->addMinutes($slotDuration);
                    if ($slotEndTime > $endDateTime) {
                        break;
                    }

                    $slots[] = [
                        'professional_id' => $professional->id,
                        'slot_start_time' => $startDateTime->toDateTimeString(),
                        'slot_end_time' => $slotEndTime->toDateTimeString(),
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $startDateTime->addMinutes($slotDuration);
                }
            }
        }

        if (!empty($slots)) {
            DB::table('professional_schedule_slots')->insert($slots);
        }
    }
}
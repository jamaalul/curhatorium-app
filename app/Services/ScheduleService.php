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
     * @param  int  $slotDuration  in minutes
     */
    public function generateSlots(
        Professional $professional,
        array $daysOfWeek,
        string $startTime,
        string $endTime,
        string $startDate,
        string $endDate,
        string $conflictResolution = 'skip',
        int $slotDuration = 60
    ): array {
        $period = CarbonPeriod::create($startDate, $endDate);
        $slots = [];
        $skippedCount = 0;
        $deletedSlots = [];

        $existingSlots = DB::table('professional_schedule_slots')
            ->where('professional_id', $professional->id)
            ->whereDate('slot_start_time', '>=', $startDate)
            ->whereDate('slot_end_time', '<=', $endDate)
            ->get();

        foreach ($period as $date) {
            if (in_array($date->dayOfWeek, $daysOfWeek)) {
                $startDateTime = $date->copy()->setTimeFromTimeString($startTime);
                $endDateTime = $date->copy()->setTimeFromTimeString($endTime);

                while ($startDateTime < $endDateTime) {
                    $slotEndTime = $startDateTime->copy()->addMinutes($slotDuration);
                    if ($slotEndTime > $endDateTime) {
                        break;
                    }

                    $conflictingSlots = $existingSlots->filter(function ($existingSlot) use ($startDateTime, $slotEndTime) {
                        $existingStart = Carbon::parse($existingSlot->slot_start_time);
                        $existingEnd = Carbon::parse($existingSlot->slot_end_time);

                        return $startDateTime < $existingEnd && $slotEndTime > $existingStart;
                    });

                    $canCreate = true;

                    if ($conflictingSlots->isNotEmpty()) {
                        if ($conflictResolution === 'overwrite') {
                            // Check if all conflicting slots are available
                            $allAvailable = $conflictingSlots->every(function ($slot) {
                                return $slot->status === 'available';
                            });

                            if ($allAvailable) {
                                foreach ($conflictingSlots as $slot) {
                                    $deletedSlots[] = $slot->id;
                                }
                            } else {
                                $canCreate = false;
                            }
                        } else {
                            $canCreate = false;
                        }
                    }

                    if ($canCreate) {
                        $slots[] = [
                            'professional_id' => $professional->id,
                            'slot_start_time' => $startDateTime->toDateTimeString(),
                            'slot_end_time' => $slotEndTime->toDateTimeString(),
                            'status' => 'available',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        $skippedCount++;
                    }

                    $startDateTime->addMinutes($slotDuration);
                }
            }
        }

        if (! empty($deletedSlots)) {
            DB::table('professional_schedule_slots')->whereIn('id', array_unique($deletedSlots))->delete();
        }

        if (! empty($slots)) {
            DB::table('professional_schedule_slots')->insert($slots);
        }

        return [
            'created' => count($slots),
            'skipped' => $skippedCount,
        ];
    }
}

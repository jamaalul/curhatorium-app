<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Models\User;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class ConsultationReminderTest extends TestCase
{
    use RefreshDatabase;

    private function createProfessional(string $name = 'Dr. Budi', string $phone = '628111000001'): Professional
    {
        return Professional::create([
            'name' => $name,
            'title' => 'Psikolog',
            'specialties' => ['anxiety'],
            'availability' => 'online',
            'availabilityText' => 'Available',
            'type' => 'psychiatrist',
            'avatar' => 'avatars/test.png',
            'whatsapp_number' => $phone,
            'password' => 'password',
        ]);
    }

    private function createUser(): User
    {
        return User::factory()->create([
            'name' => 'Andi Santoso',
        ]);
    }

    private function createPendingSlot(Professional $professional, User $user, Carbon $startTime): ProfessionalScheduleSlot
    {
        $slot = ProfessionalScheduleSlot::create([
            'professional_id' => $professional->id,
            'slot_start_time' => $startTime,
            'slot_end_time' => $startTime->copy()->addHour(),
            'status' => 'pending_confirmation',
            'booked_by_user_id' => $user->id,
        ]);

        Consultation::create([
            'professional_schedule_slot_id' => $slot->id,
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'room' => 'sharetalk_test_room',
            'consultation_type' => 'Chat w/ Psikolog',
            'no_wa' => '628222000002',
            'status' => 'waiting',
            'start' => $startTime,
            'end' => $startTime->copy()->addHour(),
        ]);

        return $slot;
    }

    public function test_accepting_booking_sends_scheduled_reminders_to_both_parties(): void
    {
        $professional = $this->createProfessional();
        $user = $this->createUser();
        $sessionStart = Carbon::now()->addDays(2)->setTime(10, 0, 0);
        $slot = $this->createPendingSlot($professional, $user, $sessionStart);

        $expectedReminderTimestamp = $sessionStart->copy()->subHour()->timestamp;

        $this->mock(FonnteService::class, function (MockInterface $mock) use ($professional, $expectedReminderTimestamp) {
            // Immediate confirmation message to client
            $mock->shouldReceive('sendWhatsApp')
                ->once()
                ->with('628222000002', \Mockery::type('string'));

            // Scheduled reminder to professional — contains client name + professional dashboard URL
            $mock->shouldReceive('sendScheduledWhatsApp')
                ->once()
                ->withArgs(function (string $phone, string $message, int $timestamp) use ($professional, $expectedReminderTimestamp) {
                    return $phone === $professional->whatsapp_number
                        && str_contains($message, 'Andi Santoso')
                        && str_contains($message, '/professional/dashboard')
                        && $timestamp === $expectedReminderTimestamp;
                })
                ->andReturn(true);

            // Scheduled reminder to client — contains professional name + client dashboard URL
            $mock->shouldReceive('sendScheduledWhatsApp')
                ->once()
                ->withArgs(function (string $phone, string $message, int $timestamp) use ($expectedReminderTimestamp) {
                    return $phone === '628222000002'
                        && str_contains($message, 'Dr. Budi')
                        && str_contains($message, '/dashboard')
                        && $timestamp === $expectedReminderTimestamp;
                })
                ->andReturn(true);
        });

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.booking.accept', $slot));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('booked', $slot->fresh()->status);
    }

    public function test_reminder_timestamp_is_exactly_one_hour_before_session(): void
    {
        $professional = $this->createProfessional();
        $user = $this->createUser();
        $sessionStart = Carbon::now()->addDays(3)->setTime(14, 0, 0);
        $slot = $this->createPendingSlot($professional, $user, $sessionStart);

        $expectedTimestamp = $sessionStart->copy()->subHour()->timestamp;

        $this->mock(FonnteService::class, function (MockInterface $mock) use ($expectedTimestamp) {
            $mock->shouldReceive('sendWhatsApp')->once();

            $mock->shouldReceive('sendScheduledWhatsApp')
                ->twice()
                ->withArgs(function (string $phone, string $message, int $timestamp) use ($expectedTimestamp) {
                    return $timestamp === $expectedTimestamp;
                })
                ->andReturn(true);
        });

        $this->actingAs($professional, 'professional')
            ->post(route('professional.booking.accept', $slot));
    }

    public function test_already_booked_slot_does_not_send_reminders(): void
    {
        $professional = $this->createProfessional();
        $user = $this->createUser();
        $sessionStart = Carbon::now()->addDays(2)->setTime(10, 0, 0);
        $slot = $this->createPendingSlot($professional, $user, $sessionStart);

        $slot->status = 'booked';
        $slot->save();

        $this->mock(FonnteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendWhatsApp')->never();
            $mock->shouldReceive('sendScheduledWhatsApp')->never();
        });

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.booking.accept', $slot));

        $response->assertSessionHas('error');
    }

    public function test_professional_cannot_accept_another_professionals_slot(): void
    {
        $professional = $this->createProfessional();
        $otherProfessional = $this->createProfessional('Dr. Other', '628333000003');

        $user = $this->createUser();
        $sessionStart = Carbon::now()->addDays(2)->setTime(10, 0, 0);
        $slot = $this->createPendingSlot($professional, $user, $sessionStart);

        $response = $this->actingAs($otherProfessional, 'professional')
            ->post(route('professional.booking.accept', $slot));

        $response->assertForbidden();
    }
}

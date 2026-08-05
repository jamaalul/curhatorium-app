<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationHistoryTest extends TestCase
{
    use RefreshDatabase;

    private function createProfessional(): Professional
    {
        return Professional::create([
            'name' => 'Dr. Test',
            'title' => 'Psikolog',
            'avatar' => 'avatars/test.png',
            'specialties' => ['anxiety', 'depression'],
            'availability' => 'online',
            'availabilityText' => 'Available',
            'type' => 'psychiatrist',
            'password' => 'password',
        ]);
    }

    private function createCompletedSlot(Professional $professional, User $user, string $startTime): ProfessionalScheduleSlot
    {
        $slot = ProfessionalScheduleSlot::create([
            'professional_id' => $professional->id,
            'slot_start_time' => $startTime,
            'slot_end_time' => date('Y-m-d H:i:s', strtotime($startTime.' +1 hour')),
            'status' => 'completed',
            'booked_by_user_id' => $user->id,
        ]);

        Consultation::create([
            'professional_schedule_slot_id' => $slot->id,
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'room' => 'room-'.$slot->id,
            'consultation_type' => 'Chat w/ Psikolog',
            'no_wa' => '08123456789',
            'status' => 'completed',
            'start' => $startTime,
            'end' => date('Y-m-d H:i:s', strtotime($startTime.' +1 hour')),
        ]);

        return $slot;
    }

    public function test_unauthenticated_user_cannot_access_consultation_history(): void
    {
        $response = $this->get(route('professional.consultation-history'));

        $response->assertRedirect(route('professional.login'));
    }

    public function test_dashboard_shows_consultation_history_in_sidebar(): void
    {
        $professional = $this->createProfessional();
        $user = User::factory()->create();

        $this->createCompletedSlot($professional, $user, '2026-07-10 10:00:00');

        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat sesi');
        $response->assertSee($user->name);
    }

    public function test_sidebar_shows_maximum_six_history_items(): void
    {
        $professional = $this->createProfessional();
        $user = User::factory()->create();

        // Create 8 completed slots
        for ($i = 1; $i <= 8; $i++) {
            $this->createCompletedSlot($professional, $user, "2026-07-{$i} 10:00:00");
        }

        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('consultationHistory', function ($history) {
            return $history->count() === 6;
        });
    }

    public function test_sidebar_hides_history_when_no_completed_consultations(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Riwayat sesi');
    }

    public function test_consultation_history_page_shows_paginated_results(): void
    {
        $professional = $this->createProfessional();
        $user = User::factory()->create();

        // Create 12 completed slots to trigger pagination (10 per page)
        for ($i = 1; $i <= 12; $i++) {
            $date = str_pad($i, 2, '0', STR_PAD_LEFT);
            $this->createCompletedSlot($professional, $user, "2026-07-{$date} 10:00:00");
        }

        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.consultation-history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Konsultasi');
        $response->assertViewHas('paginatedHistory', function ($paginator) {
            return $paginator->count() === 10 && $paginator->total() === 12;
        });
    }

    public function test_consultation_history_page_shows_empty_state(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.consultation-history'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada riwayat konsultasi');
    }

    public function test_consultation_history_only_shows_completed_slots(): void
    {
        $professional = $this->createProfessional();
        $user = User::factory()->create();

        // Create a completed slot
        $this->createCompletedSlot($professional, $user, '2026-07-10 10:00:00');

        // Create a booked (non-completed) slot
        $bookedSlot = ProfessionalScheduleSlot::create([
            'professional_id' => $professional->id,
            'slot_start_time' => '2026-07-11 10:00:00',
            'slot_end_time' => '2026-07-11 11:00:00',
            'status' => 'booked',
            'booked_by_user_id' => $user->id,
        ]);

        Consultation::create([
            'professional_schedule_slot_id' => $bookedSlot->id,
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'room' => 'room-booked-'.$bookedSlot->id,
            'consultation_type' => 'Chat w/ Psikolog',
            'no_wa' => '08123456789',
            'status' => 'active',
            'start' => '2026-07-11 10:00:00',
            'end' => '2026-07-11 11:00:00',
        ]);

        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.consultation-history'));

        $response->assertStatus(200);
        $response->assertViewHas('paginatedHistory', function ($paginator) {
            return $paginator->total() === 1;
        });
    }
}

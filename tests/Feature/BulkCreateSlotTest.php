<?php

namespace Tests\Feature;

use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkCreateSlotTest extends TestCase
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

    public function test_unauthenticated_professional_cannot_create_slots(): void
    {
        $response = $this->post(route('professional.set-availability'), [
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(6)->format('Y-m-d'),
            'days' => ['senin', 'selasa'],
            'start_time' => '16:00',
            'end_time' => '21:00',
        ]);

        $response->assertRedirect(route('professional.login'));
    }

    public function test_professional_can_bulk_create_slots(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.set-availability'), [
                'start_date' => '2026-07-06',
                'end_date' => '2026-07-12',
                'days' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat'],
                'start_time' => '16:00',
                'end_time' => '21:00',
            ]);

        $response->assertRedirect(route('professional.dashboard'));
        $response->assertSessionHas('success');

        $slotCount = $professional->scheduleSlots()->count();
        $this->assertGreaterThan(0, $slotCount);
    }

    public function test_slot_creation_validates_required_fields(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.set-availability'), []);

        $response->assertSessionHasErrors([
            'start_date',
            'end_date',
            'days',
            'start_time',
            'end_time',
        ]);
    }

    public function test_slot_creation_validates_end_date_after_start_date(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.set-availability'), [
                'start_date' => '2026-07-12',
                'end_date' => '2026-07-06',
                'days' => ['senin'],
                'start_time' => '16:00',
                'end_time' => '21:00',
            ]);

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_slot_creation_validates_end_time_after_start_time(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.set-availability'), [
                'start_date' => '2026-07-06',
                'end_date' => '2026-07-12',
                'days' => ['senin'],
                'start_time' => '21:00',
                'end_time' => '16:00',
            ]);

        $response->assertSessionHasErrors(['end_time']);
    }

    public function test_slot_creation_requires_at_least_one_day(): void
    {
        $professional = $this->createProfessional();

        $response = $this->actingAs($professional, 'professional')
            ->post(route('professional.set-availability'), [
                'start_date' => '2026-07-06',
                'end_date' => '2026-07-12',
                'days' => [],
                'start_time' => '16:00',
                'end_time' => '21:00',
            ]);

        $response->assertSessionHasErrors(['days']);
    }

    public function test_day_names_are_mapped_to_correct_integers(): void
    {
        $professional = $this->createProfessional();

        // 2026-07-06 is a Monday, 2026-07-12 is a Sunday
        $this->actingAs($professional, 'professional')
            ->post(route('professional.set-availability'), [
                'start_date' => '2026-07-06',
                'end_date' => '2026-07-12',
                'days' => ['senin'],
                'start_time' => '09:00',
                'end_time' => '10:00',
            ]);

        // Only Monday (2026-07-06) should have a slot
        $slots = $professional->scheduleSlots()->get();
        $this->assertCount(1, $slots);
        $this->assertEquals('2026-07-06 09:00:00', $slots->first()->slot_start_time);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardBirthDateTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_follows_existing_dashboard_auth_middleware(): void
    {
        $this->get(route('dashboard'))->assertRedirect('/login');
    }

    public function test_user_without_birth_date_receives_false_boolean(): void
    {
        $this->withoutVite();

        $user = User::factory()->create(['birth_date' => null]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertFalse($response->viewData('hasBirthDate'));
    }

    public function test_user_with_birth_date_receives_true_boolean(): void
    {
        $this->withoutVite();

        $user = User::factory()->create(['birth_date' => '1995-06-12']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertTrue($response->viewData('hasBirthDate'));
    }

    public function test_dashboard_still_renders(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('main.main');
    }
}

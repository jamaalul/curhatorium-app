<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BirthDateUserModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_birth_date_can_be_mass_assigned(): void
    {
        $user = User::factory()->create();

        $user->update(['birth_date' => '1998-04-21']);

        $this->assertSame('1998-04-21', $user->refresh()->birth_date?->toDateString());
    }

    public function test_birth_date_is_cast_to_a_carbon_date(): void
    {
        $user = User::factory()->create(['birth_date' => '2001-12-09']);

        $this->assertInstanceOf(Carbon::class, $user->birth_date);
        $this->assertSame('2001-12-09', $user->birth_date->toDateString());
    }

    public function test_birth_date_remains_nullable(): void
    {
        $user = User::factory()->create(['birth_date' => null]);

        $this->assertNull($user->birth_date);
    }
}

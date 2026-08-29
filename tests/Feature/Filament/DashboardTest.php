<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Dashboard;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs(User::factory()->create([
            'is_admin' => true,
        ]));
    }

    public function test_dashboard_can_render_with_default_month_and_year_filters(): void
    {
        Livewire::test(Dashboard::class)
            ->assertSuccessful()
            ->assertFormSet([
                'month' => (string) now()->month,
                'year' => (string) now()->year,
            ], 'filtersForm');
    }
}

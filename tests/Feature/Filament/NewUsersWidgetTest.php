<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\NewUsersWidget;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewUsersWidgetTest extends TestCase
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

    public function test_new_users_widget_can_render_with_increase(): void
    {
        // 5 users created in the last 30 days
        User::factory()->count(5)->create([
            'created_at' => now()->subDays(5),
        ]);

        // 2 users created in the previous 30-60 day period
        User::factory()->count(2)->create([
            'created_at' => now()->subDays(40),
        ]);

        Livewire::test(NewUsersWidget::class)
            ->assertSuccessful()
            ->assertSee('New Users (Last 30 Days)')
            ->assertSee('increase vs last period');
    }

    public function test_new_users_widget_can_render_with_decrease(): void
    {
        // 1 user created in the last 30 days
        User::factory()->count(1)->create([
            'created_at' => now()->subDays(5),
        ]);

        // 5 users created in the previous 30-60 day period
        User::factory()->count(5)->create([
            'created_at' => now()->subDays(40),
        ]);

        Livewire::test(NewUsersWidget::class)
            ->assertSuccessful()
            ->assertSee('New Users (Last 30 Days)')
            ->assertSee('decrease vs last period');
    }

    public function test_new_users_widget_column_span_configuration(): void
    {
        $widget = new NewUsersWidget;

        $this->assertEquals([
            'default' => 'full',
            'md' => 1,
            'lg' => 1,
        ], $widget->getColumnSpan());
    }
}

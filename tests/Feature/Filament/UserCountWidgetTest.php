<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\UserCountWidget;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserCountWidgetTest extends TestCase
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

    public function test_user_count_widget_can_render(): void
    {
        User::factory()->count(5)->create();

        Livewire::test(UserCountWidget::class)
            ->assertSuccessful()
            ->assertSee('Total Users')
            ->assertSee((string) User::count());
    }

    public function test_user_count_widget_column_span_configuration(): void
    {
        $widget = new UserCountWidget;

        $this->assertEquals([
            'default' => 'full',
            'md' => 1,
            'lg' => 1,
        ], $widget->getColumnSpan());
    }
}

<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\EClassOverview;
use App\Filament\Resources\CbtModuleResource;
use App\Filament\Widgets\EClassStats;
use App\Models\CbtModule;
use App\Models\Certificate;
use App\Models\User;
use App\Models\UserCbtModule;
use App\Models\UserModuleProgress;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EClassOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_e_class_overview_page(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get(EClassOverview::getUrl())
            ->assertOk()
            ->assertSee('Ringkasan E-Class')
            ->assertSee('Kelola Modul E-Class')
            ->assertSee(CbtModuleResource::getUrl('index'))
            ->assertSeeLivewire(EClassStats::class);
    }

    public function test_non_admin_cannot_access_admin_panel_page(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]));

        $this->get(EClassOverview::getUrl())->assertForbidden();
    }

    public function test_e_class_statistics_are_correct(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $modules = CbtModule::factory()->published()->count(2)->create();
        $modules->push(CbtModule::factory()->create());
        $firstOwner = User::factory()->create();
        $secondOwner = User::factory()->create();
        UserCbtModule::factory()->for($firstOwner)->for($modules[0], 'module')->create();
        UserCbtModule::factory()->for($secondOwner)->for($modules[1], 'module')->create();
        UserCbtModule::factory()->revoked()->for($firstOwner)->for($modules[2], 'module')->create();
        $completedUser = User::factory()->create();
        UserModuleProgress::factory()->completed()->for($completedUser)->for($modules[0], 'module')->create();
        UserModuleProgress::factory()->completed()->for($completedUser)->for($modules[1], 'module')->create();
        UserModuleProgress::factory()->completed()->for($secondOwner)->for($modules[2], 'module')->create();
        Certificate::factory()->for($firstOwner)->for($modules[0], 'module')->create();
        Certificate::factory()->for($secondOwner)->for($modules[1], 'module')->create();

        Livewire::test(EClassStats::class)
            ->assertSuccessful()
            ->assertSee('Total Module')
            ->assertSee('3')
            ->assertSee('Module Published')
            ->assertSee('2')
            ->assertSee('Entitlement Aktif')
            ->assertSee('User Menyelesaikan Module')
            ->assertSee('Total Sertifikat');
    }

    public function test_navigation_group_is_e_class(): void
    {
        $this->assertSame('E-Class', EClassOverview::getNavigationGroup());
        $this->assertSame('Ringkasan E-Class', EClassOverview::getNavigationLabel());
    }
}

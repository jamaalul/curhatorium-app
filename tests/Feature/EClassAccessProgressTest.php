<?php

namespace Tests\Feature;

use App\Models\CbtModule;
use App\Models\Chapter;
use App\Models\User;
use App\Models\UserCbtModule;
use App\Models\UserChapterProgress;
use App\Models\UserModuleProgress;
use App\ProgressStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EClassAccessProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_library_only_contains_active_entitlements_owned_by_logged_in_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $owned = CbtModule::factory()->published()->create(['title' => 'Module Saya']);
        $revoked = CbtModule::factory()->published()->create(['title' => 'Module Dicabut']);
        $other = CbtModule::factory()->published()->create(['title' => 'Module Orang Lain']);
        UserCbtModule::factory()->for($user)->for($owned, 'module')->create();
        UserCbtModule::factory()->revoked()->for($user)->for($revoked, 'module')->create();
        UserCbtModule::factory()->for($otherUser)->for($other, 'module')->create();

        $this->actingAs($user)->get(route('e-class.library'))
            ->assertOk()
            ->assertSee('Module Saya')
            ->assertDontSee('Module Dicabut')
            ->assertDontSee('Module Orang Lain');
    }

    public function test_chapter_access_requires_active_ownership_and_matching_module(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        $otherModule = CbtModule::factory()->published()->create();
        $chapter = Chapter::factory()->for($module, 'module')->create();
        $otherChapter = Chapter::factory()->for($otherModule, 'module')->create();
        UserCbtModule::factory()->for($owner)->for($module, 'module')->create();

        $this->get(route('e-class.chapters.show', [$module, $chapter]))->assertRedirect(route('login'));
        $this->actingAs($otherUser)->get(route('e-class.chapters.show', [$module, $chapter]))->assertForbidden();
        $this->actingAs($owner)->get(route('e-class.chapters.show', [$module, $otherChapter]))->assertNotFound();

        UserCbtModule::query()->whereBelongsTo($owner)->update(['revoked_at' => now()]);
        $this->actingAs($owner)->get(route('e-class.chapters.show', [$module, $chapter]))->assertForbidden();
    }

    public function test_soft_deleted_chapter_cannot_be_opened(): void
    {
        [$user, $module] = $this->ownedModule();
        $chapter = Chapter::factory()->for($module, 'module')->create();
        $chapter->delete();

        $this->actingAs($user)
            ->get('/e-class/'.$module->slug.'/chapters/'.$chapter->getKey())
            ->assertNotFound();
    }

    public function test_opening_and_completing_reading_chapters_synchronizes_progress(): void
    {
        [$user, $module] = $this->ownedModule();
        $first = Chapter::factory()->for($module, 'module')->create(['order_number' => 1]);
        $second = Chapter::factory()->for($module, 'module')->create(['order_number' => 2]);

        $this->actingAs($user)->get(route('e-class.chapters.show', [$module, $first]))->assertOk();

        $this->assertDatabaseHas('user_module_progresses', [
            'user_id' => $user->getKey(),
            'cbt_module_id' => $module->getKey(),
            'status' => ProgressStatus::InProgress->value,
        ]);
        $this->assertDatabaseHas('user_chapter_progresses', [
            'user_id' => $user->getKey(),
            'chapter_id' => $first->getKey(),
            'status' => ProgressStatus::InProgress->value,
        ]);

        $this->actingAs($user)->post(route('e-class.chapters.complete', [$module, $first]))->assertRedirect();
        $this->assertSame(ProgressStatus::InProgress, UserModuleProgress::query()->sole()->status);
        $this->assertNotNull(UserChapterProgress::query()->whereBelongsTo($first, 'chapter')->sole()->completed_at);

        $this->actingAs($user)->post(route('e-class.chapters.complete', [$module, $second]))->assertRedirect();
        $this->actingAs($user)->post(route('e-class.chapters.complete', [$module, $second]))->assertRedirect();

        $moduleProgress = UserModuleProgress::query()->sole();
        $this->assertSame(ProgressStatus::Completed, $moduleProgress->status);
        $this->assertNotNull($moduleProgress->completed_at);
        $this->assertSame(2, UserChapterProgress::query()->count());
    }

    public function test_private_video_is_only_retrieved_after_authorization(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('e-class/video.mp4', 'video-content');
        [$owner, $module] = $this->ownedModule();
        $chapter = Chapter::factory()->video()->for($module, 'module')->create([
            'video_url' => 'e-class/video.mp4',
        ]);

        $otherUser = User::factory()->create();
        $this->actingAs($otherUser)->get(route('e-class.chapters.video', [$module, $chapter]))->assertForbidden();
        $this->actingAs($owner)->get(route('e-class.chapters.show', [$module, $chapter]))
            ->assertOk()
            ->assertSee(route('e-class.chapters.video', [$module, $chapter]))
            ->assertDontSee('e-class/video.mp4');
        $this->actingAs($owner)->get(route('e-class.chapters.video', [$module, $chapter]))
            ->assertOk()
            ->assertStreamedContent('video-content');
    }

    /** @return array{User, CbtModule} */
    private function ownedModule(): array
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        UserCbtModule::factory()->for($user)->for($module, 'module')->create();

        return [$user, $module];
    }
}

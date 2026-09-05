<?php

namespace Tests\Feature;

use App\ChapterType;
use App\Models\CbtModule;
use App\Models\Certificate;
use App\Models\Chapter;
use App\Models\Order;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;
use App\Models\UserCbtModule;
use App\Models\UserChapterProgress;
use App\Models\UserModuleProgress;
use App\ProgressStatus;
use App\QuizQuestionType;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CbtModuleModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cbt_models_and_user_relationships_are_connected(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->create();
        $chapter = Chapter::factory()->for($module, 'module')->create();
        $order = Order::factory()->for($user)->create([
            'orderable_type' => CbtModule::class,
            'orderable_id' => $module->getKey(),
        ]);
        $entitlement = UserCbtModule::factory()->for($user)->for($module, 'module')->create([
            'source_order_id' => $order->getKey(),
        ]);
        $moduleProgress = UserModuleProgress::factory()->for($user)->for($module, 'module')->create();
        $chapterProgress = UserChapterProgress::factory()->for($user)->for($chapter)->create();
        $attempt = QuizAttempt::factory()->for($user)->for($chapter)->create();
        $certificate = Certificate::factory()->for($user)->for($module, 'module')->create();

        $this->assertTrue($module->chapters->first()->is($chapter));
        $this->assertTrue($chapter->module->is($module));
        $this->assertTrue($module->entitlements->first()->is($entitlement));
        $this->assertTrue($module->moduleProgresses->first()->is($moduleProgress));
        $this->assertTrue($module->certificates->first()->is($certificate));
        $this->assertTrue($entitlement->user->is($user));
        $this->assertTrue($entitlement->module->is($module));
        $this->assertTrue($entitlement->sourceOrder->is($order));
        $this->assertTrue($moduleProgress->user->is($user));
        $this->assertTrue($moduleProgress->module->is($module));
        $this->assertTrue($chapterProgress->chapter->is($chapter));
        $this->assertTrue($attempt->chapter->is($chapter));
        $this->assertTrue($certificate->module->is($module));
        $this->assertTrue($user->cbtModuleEntitlements->first()->is($entitlement));
        $this->assertTrue($user->moduleProgresses->first()->is($moduleProgress));
        $this->assertTrue($user->chapterProgresses->first()->is($chapterProgress));
        $this->assertTrue($user->quizAttempts->first()->is($attempt));
        $this->assertTrue($user->certificates->first()->is($certificate));
    }

    public function test_content_models_use_soft_deletes(): void
    {
        $module = CbtModule::factory()->create();
        $chapter = Chapter::factory()->for($module, 'module')->quiz()->create();
        $question = QuizQuestion::factory()->for($chapter)->create();
        $option = QuizQuestionOption::factory()->for($question, 'question')->create();

        $option->delete();
        $question->delete();
        $chapter->delete();
        $module->delete();

        $this->assertSoftDeleted($option);
        $this->assertSoftDeleted($question);
        $this->assertSoftDeleted($chapter);
        $this->assertSoftDeleted($module);
    }

    public function test_enum_casts_and_answer_keys_are_protected(): void
    {
        $chapter = Chapter::factory()->quiz()->create();
        $question = QuizQuestion::factory()->for($chapter)->shortAnswer()->create([
            'accepted_answer' => 'Mindfulness',
        ]);
        $multipleChoice = QuizQuestion::factory()->for($chapter)->create(['order_number' => 2]);
        $option = QuizQuestionOption::factory()->for($multipleChoice, 'question')->correct()->create();

        $this->assertSame(ChapterType::Quiz, $chapter->type);
        $this->assertSame(QuizQuestionType::ShortAnswer, $question->type);
        $this->assertTrue($option->is_correct);
        $this->assertArrayNotHasKey('accepted_answer', $question->toArray());
        $this->assertArrayNotHasKey('is_correct', $option->toArray());
    }

    public function test_published_scope_route_binding_and_name_accessor_work(): void
    {
        $published = CbtModule::factory()->published()->create([
            'title' => 'Kelas Mindfulness',
            'slug' => 'kelas-mindfulness',
        ]);
        $draft = CbtModule::factory()->create();

        $modules = CbtModule::query()->published()->get();

        $this->assertTrue($modules->contains($published));
        $this->assertFalse($modules->contains($draft));
        $this->assertSame('slug', $published->getRouteKeyName());
        $this->assertTrue($published->resolveRouteBinding('kelas-mindfulness')->is($published));
        $this->assertSame('Kelas Mindfulness', $published->name);
    }

    public function test_module_has_polymorphic_orders(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->create();
        $order = Order::factory()->for($user)->create([
            'orderable_type' => CbtModule::class,
            'orderable_id' => $module->getKey(),
        ]);

        $this->assertTrue($module->orders->first()->is($order));
        $this->assertTrue($order->orderable->is($module));
    }

    public function test_only_active_entitlement_grants_module_ownership(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->create();
        $entitlement = UserCbtModule::factory()->for($user)->for($module, 'module')->create();

        $this->assertTrue($module->isOwnedBy($user));
        $this->assertTrue(UserCbtModule::query()->active()->get()->contains($entitlement));

        $entitlement->update(['revoked_at' => now()]);

        $this->assertFalse($module->isOwnedBy($user));
        $this->assertFalse(UserCbtModule::query()->active()->get()->contains($entitlement));
    }

    public function test_chapters_questions_and_options_are_ordered(): void
    {
        $module = CbtModule::factory()->create();
        $chapterThree = Chapter::factory()->for($module, 'module')->quiz()->create(['order_number' => 3]);
        $chapterOne = Chapter::factory()->for($module, 'module')->quiz()->create(['order_number' => 1]);
        $chapterTwo = Chapter::factory()->for($module, 'module')->quiz()->create(['order_number' => 2]);
        $questionTwo = QuizQuestion::factory()->for($chapterOne)->create(['order_number' => 2]);
        $questionOne = QuizQuestion::factory()->for($chapterOne)->create(['order_number' => 1]);
        $optionTwo = QuizQuestionOption::factory()->for($questionOne, 'question')->create(['order_number' => 2]);
        $optionOne = QuizQuestionOption::factory()->for($questionOne, 'question')->create(['order_number' => 1]);

        $this->assertEquals([$chapterOne->id, $chapterTwo->id, $chapterThree->id], $module->chapters()->pluck('id')->all());
        $this->assertEquals([$questionOne->id, $questionTwo->id], $chapterOne->questions()->pluck('id')->all());
        $this->assertEquals([$optionOne->id, $optionTwo->id], $questionOne->options()->pluck('id')->all());
    }

    public function test_progress_percentage_uses_only_active_chapters(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->create();
        $completedChapter = Chapter::factory()->for($module, 'module')->create(['order_number' => 1]);
        $inProgressChapter = Chapter::factory()->for($module, 'module')->create(['order_number' => 2]);
        $deletedChapter = Chapter::factory()->for($module, 'module')->create(['order_number' => 3]);
        UserChapterProgress::factory()->for($user)->for($completedChapter)->completed()->create();
        UserChapterProgress::factory()->for($user)->for($inProgressChapter)->create();
        $deletedChapter->delete();

        $this->assertSame(50.0, $module->progressPercentage($user));
        $this->assertSame(0.0, CbtModule::factory()->create()->progressPercentage($user));
    }

    public function test_progress_helpers_keep_status_and_completed_timestamp_consistent(): void
    {
        $moduleProgress = UserModuleProgress::factory()->create();
        $chapterProgress = UserChapterProgress::factory()->create();

        $moduleProgress->markCompleted();
        $chapterProgress->markCompleted();

        $this->assertSame(ProgressStatus::Completed, $moduleProgress->status);
        $this->assertNotNull($moduleProgress->completed_at);
        $this->assertSame(ProgressStatus::Completed, $chapterProgress->status);
        $this->assertNotNull($chapterProgress->completed_at);

        $moduleProgress->markInProgress();
        $chapterProgress->markInProgress();

        $this->assertSame(ProgressStatus::InProgress, $moduleProgress->status);
        $this->assertNull($moduleProgress->completed_at);
        $this->assertSame(ProgressStatus::InProgress, $chapterProgress->status);
        $this->assertNull($chapterProgress->completed_at);
    }

    public function test_quiz_attempts_and_answers_are_connected(): void
    {
        $user = User::factory()->create();
        $chapter = Chapter::factory()->quiz()->create();
        $question = QuizQuestion::factory()->for($chapter)->create();
        $option = QuizQuestionOption::factory()->for($question, 'question')->correct()->create();
        $attempt = QuizAttempt::factory()->for($user)->for($chapter)->create();
        $answer = QuizAnswer::factory()->for($attempt, 'attempt')->for($question, 'question')->create([
            'selected_option_id' => $option->getKey(),
            'is_correct' => true,
            'awarded_points' => 1,
        ]);

        $this->assertTrue($attempt->user->is($user));
        $this->assertTrue($attempt->answers->first()->is($answer));
        $this->assertTrue($answer->attempt->is($attempt));
        $this->assertTrue($answer->question->is($question));
        $this->assertTrue($answer->selectedOption->is($option));
        $this->assertTrue($question->answers->first()->is($answer));
        $this->assertTrue($option->answers->first()->is($answer));
    }

    public function test_certificate_is_unique_per_user_and_module(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->create();
        $certificate = Certificate::factory()->for($user)->for($module, 'module')->create();

        $this->assertTrue($certificate->user->is($user));
        $this->assertTrue($certificate->isOwnedBy($user));
        $this->assertFalse($certificate->isOwnedBy(User::factory()->create()));

        $this->expectException(QueryException::class);

        Certificate::factory()->for($user)->for($module, 'module')->create();
    }
}

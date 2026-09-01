<?php

namespace Tests\Feature;

use App\Models\CbtModule;
use App\Models\Chapter;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;
use App\Models\UserCbtModule;
use App\Models\UserChapterProgress;
use App\ProgressStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_quiz_page_does_not_expose_answer_keys(): void
    {
        [$user, $module, $chapter] = $this->ownedQuiz();
        [$question, $correct] = $this->multipleChoiceQuestion($chapter);
        QuizQuestion::factory()->shortAnswer()->for($chapter)->create([
            'accepted_answer' => 'KUNCI SANGAT RAHASIA',
            'order_number' => 2,
        ]);
        QuizAttempt::factory()->for($user)->for($chapter)->create();

        $this->actingAs($user)
            ->get(route('e-class.chapters.show', [$module, $chapter]))
            ->assertOk()
            ->assertSee($question->question)
            ->assertSee($correct->option_text)
            ->assertDontSee('KUNCI SANGAT RAHASIA')
            ->assertDontSee('is_correct');
    }

    public function test_starting_quiz_creates_incrementing_attempts_without_overwriting_history(): void
    {
        [$user, $module, $chapter] = $this->ownedQuiz();

        $this->actingAs($user)
            ->post(route('e-class.quiz-attempts.start', [$module, $chapter]))
            ->assertRedirect(route('e-class.chapters.show', [$module, $chapter]));

        QuizAttempt::query()->sole()->update(['submitted_at' => now()]);

        $this->actingAs($user)
            ->post(route('e-class.quiz-attempts.start', [$module, $chapter]))
            ->assertRedirect();

        $this->assertSame([1, 2], QuizAttempt::query()->orderBy('attempt_number')->pluck('attempt_number')->all());
    }

    public function test_multiple_choice_and_normalized_short_answer_are_graded_server_side(): void
    {
        [$user, $module, $chapter] = $this->ownedQuiz();
        [$multipleChoice, $correctOption] = $this->multipleChoiceQuestion($chapter, 2.5);
        $shortAnswer = QuizQuestion::factory()->shortAnswer()->for($chapter)->create([
            'accepted_answer' => 'Ruang Aman',
            'points' => 3,
            'order_number' => 2,
        ]);
        $attempt = QuizAttempt::factory()->for($user)->for($chapter)->create();

        $response = $this->actingAs($user)->post(route('e-class.quiz-attempts.submit', $attempt), [
            'answers' => [
                [
                    'question_id' => $multipleChoice->getKey(),
                    'selected_option_id' => $correctOption->getKey(),
                ],
                [
                    'question_id' => $shortAnswer->getKey(),
                    'answer_text' => '  RUANG    aman ',
                ],
            ],
            'score' => 999,
        ]);

        $response->assertRedirect(route('e-class.quiz-attempts.show', $attempt));
        $attempt->refresh();

        $this->assertSame('5.50', $attempt->score);
        $this->assertSame('5.50', $attempt->max_score);
        $this->assertNotNull($attempt->submitted_at);
        $this->assertSame(2, $attempt->answers()->count());
        $this->assertTrue($attempt->answers()->get()->every(fn ($answer): bool => $answer->is_correct));
        $this->assertSame(ProgressStatus::Completed, UserChapterProgress::query()->sole()->status);
    }

    public function test_selected_option_must_belong_to_submitted_question(): void
    {
        [$user, , $chapter] = $this->ownedQuiz();
        [$question] = $this->multipleChoiceQuestion($chapter);
        $otherQuestion = QuizQuestion::factory()->for($chapter)->create(['order_number' => 2]);
        $otherOption = QuizQuestionOption::factory()->correct()->for($otherQuestion, 'question')->create();
        $attempt = QuizAttempt::factory()->for($user)->for($chapter)->create();

        $this->actingAs($user)->post(route('e-class.quiz-attempts.submit', $attempt), [
            'answers' => [
                ['question_id' => $question->getKey(), 'selected_option_id' => $otherOption->getKey()],
                ['question_id' => $otherQuestion->getKey(), 'selected_option_id' => $otherOption->getKey()],
            ],
        ])->assertUnprocessable();

        $this->assertNull($attempt->fresh()->submitted_at);
        $this->assertDatabaseCount('quiz_answers', 0);
    }

    public function test_attempt_from_another_user_and_duplicate_submission_are_rejected(): void
    {
        [$owner, $module, $chapter] = $this->ownedQuiz();
        [$question, $correctOption] = $this->multipleChoiceQuestion($chapter);
        $attempt = QuizAttempt::factory()->for($owner)->for($chapter)->create();
        $otherUser = User::factory()->create();
        UserCbtModule::factory()->for($otherUser)->for($module, 'module')->create();
        $payload = [
            'answers' => [
                ['question_id' => $question->getKey(), 'selected_option_id' => $correctOption->getKey()],
            ],
        ];

        $this->actingAs($otherUser)
            ->post(route('e-class.quiz-attempts.submit', $attempt), $payload)
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('e-class.quiz-attempts.submit', $attempt), $payload)
            ->assertRedirect();
        $this->actingAs($owner)
            ->post(route('e-class.quiz-attempts.submit', $attempt), $payload)
            ->assertConflict();

        $this->assertSame(1, $attempt->answers()->count());
    }

    public function test_retake_creates_new_answers_without_changing_previous_attempt(): void
    {
        [$user, $module, $chapter] = $this->ownedQuiz();
        [$question, $correctOption] = $this->multipleChoiceQuestion($chapter);
        $wrongOption = QuizQuestionOption::factory()->for($question, 'question')->create(['order_number' => 2]);

        $firstAttempt = QuizAttempt::factory()->for($user)->for($chapter)->create(['attempt_number' => 1]);
        $this->actingAs($user)->post(route('e-class.quiz-attempts.submit', $firstAttempt), [
            'answers' => [
                ['question_id' => $question->getKey(), 'selected_option_id' => $wrongOption->getKey()],
            ],
        ])->assertRedirect();

        $this->actingAs($user)->post(route('e-class.quiz-attempts.start', [$module, $chapter]))->assertRedirect();
        $secondAttempt = QuizAttempt::query()->where('attempt_number', 2)->sole();
        $this->actingAs($user)->post(route('e-class.quiz-attempts.submit', $secondAttempt), [
            'answers' => [
                ['question_id' => $question->getKey(), 'selected_option_id' => $correctOption->getKey()],
            ],
        ])->assertRedirect();

        $this->assertSame('0.00', $firstAttempt->fresh()->score);
        $this->assertSame('1.00', $secondAttempt->fresh()->score);
        $this->assertSame(2, QuizAttempt::query()->count());
        $this->assertSame(2, $question->answers()->count());
    }

    /** @return array{User, CbtModule, Chapter} */
    private function ownedQuiz(): array
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        $chapter = Chapter::factory()->quiz()->for($module, 'module')->create();
        UserCbtModule::factory()->for($user)->for($module, 'module')->create();

        return [$user, $module, $chapter];
    }

    /** @return array{QuizQuestion, QuizQuestionOption} */
    private function multipleChoiceQuestion(Chapter $chapter, float $points = 1): array
    {
        $question = QuizQuestion::factory()->for($chapter)->create(['points' => $points]);
        $correctOption = QuizQuestionOption::factory()->correct()->for($question, 'question')->create();

        return [$question, $correctOption];
    }
}

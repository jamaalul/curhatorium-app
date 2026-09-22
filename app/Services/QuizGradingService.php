<?php

namespace App\Services;

use App\ChapterType;
use App\Models\Chapter;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use App\QuizQuestionType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuizGradingService
{
    public function __construct(private EClassProgressService $progress) {}

    public function start(User $user, Chapter $chapter): QuizAttempt
    {
        abort_unless($chapter->type === ChapterType::Quiz, 404);

        return DB::transaction(function () use ($user, $chapter): QuizAttempt {
            Chapter::query()->whereKey($chapter->getKey())->lockForUpdate()->firstOrFail();

            $attemptNumber = ((int) QuizAttempt::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($chapter)
                ->max('attempt_number')) + 1;

            return QuizAttempt::query()->create([
                'user_id' => $user->getKey(),
                'chapter_id' => $chapter->getKey(),
                'attempt_number' => $attemptNumber,
                'score' => 0,
                'max_score' => 0,
                'started_at' => now(),
            ]);
        });
    }

    /** @param list<array{question_id: int, selected_option_id?: int|null, answer_text?: string|null}> $submittedAnswers */
    public function grade(User $user, QuizAttempt $attempt, array $submittedAnswers): QuizAttempt
    {
        return DB::transaction(function () use ($user, $attempt, $submittedAnswers): QuizAttempt {
            $lockedAttempt = QuizAttempt::query()
                ->with('chapter.module')
                ->whereKey($attempt->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedAttempt->user_id === $user->getKey(), 403);
            abort_if($lockedAttempt->submitted_at !== null, 409, 'Quiz attempt sudah disubmit.');

            $questions = $lockedAttempt->chapter->questions()->with('options')->get();
            $answers = collect($submittedAnswers)->keyBy('question_id');

            $this->assertSubmittedQuestionsAreValid($questions, $answers);

            $score = 0.0;
            $maxScore = (float) $questions->sum(fn (QuizQuestion $question): float => (float) $question->points);

            foreach ($questions as $question) {
                $answerData = $answers->get($question->getKey());
                $answer = $this->gradeQuestion($lockedAttempt, $question, $answerData);
                $score += (float) $answer->awarded_points;
            }

            $lockedAttempt->update([
                'score' => $score,
                'max_score' => $maxScore,
                'submitted_at' => now(),
            ]);

            $this->progress->completeChapter($user, $lockedAttempt->chapter->module, $lockedAttempt->chapter);

            return $lockedAttempt->fresh(['answers.question', 'answers.selectedOption']);
        });
    }

    /**
     * @param  Collection<int, QuizQuestion>  $questions
     * @param  Collection<int, array{question_id: int, selected_option_id?: int|null, answer_text?: string|null}>  $answers
     */
    private function assertSubmittedQuestionsAreValid(Collection $questions, Collection $answers): void
    {
        $questionIds = $questions->modelKeys();
        $submittedQuestionIds = $answers->keys()->map(fn (mixed $id): int => (int) $id)->values()->all();

        sort($questionIds);
        sort($submittedQuestionIds);

        abort_unless($questionIds === $submittedQuestionIds, 422, 'Jawaban harus berasal dari seluruh pertanyaan quiz.');
    }

    /** @param array{question_id: int, selected_option_id?: int|null, answer_text?: string|null} $answerData */
    private function gradeQuestion(QuizAttempt $attempt, QuizQuestion $question, array $answerData): QuizAnswer
    {
        if ($question->type === QuizQuestionType::MultipleChoice) {
            $selectedOptionId = $answerData['selected_option_id'] ?? null;
            $selectedOption = $question->options->firstWhere('id', $selectedOptionId);

            abort_unless($selectedOptionId !== null && $selectedOption !== null, 422, 'Pilihan jawaban tidak valid.');
            abort_unless(blank($answerData['answer_text'] ?? null), 422, 'Multiple choice tidak menerima jawaban teks.');

            $isCorrect = $selectedOption->is_correct;

            return $attempt->answers()->create([
                'quiz_question_id' => $question->getKey(),
                'selected_option_id' => $selectedOption->getKey(),
                'answer_text' => null,
                'is_correct' => $isCorrect,
                'awarded_points' => $isCorrect ? $question->points : 0,
            ]);
        }

        $answerText = $answerData['answer_text'] ?? null;
        abort_unless(is_string($answerText) && filled($answerText), 422, 'Jawaban singkat wajib diisi.');
        abort_unless(($answerData['selected_option_id'] ?? null) === null, 422, 'Jawaban singkat tidak menerima pilihan.');
        abort_unless(filled($question->accepted_answer), 422, 'Kunci jawaban singkat belum tersedia.');

        $isCorrect = $this->normalize($answerText) === $this->normalize((string) $question->accepted_answer);

        return $attempt->answers()->create([
            'quiz_question_id' => $question->getKey(),
            'selected_option_id' => null,
            'answer_text' => Str::of($answerText)->trim()->toString(),
            'is_correct' => $isCorrect,
            'awarded_points' => $isCorrect ? $question->points : 0,
        ]);
    }

    private function normalize(string $answer): string
    {
        return Str::of($answer)->trim()->squish()->lower()->toString();
    }
}

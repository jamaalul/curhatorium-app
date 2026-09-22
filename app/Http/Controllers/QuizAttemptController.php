<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuizAttemptRequest;
use App\Models\CbtModule;
use App\Models\Chapter;
use App\Models\QuizAttempt;
use App\Services\EClassProgressService;
use App\Services\QuizGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizAttemptController extends Controller
{
    public function start(
        Request $request,
        CbtModule $module,
        Chapter $chapter,
        QuizGradingService $grading,
        EClassProgressService $progress,
    ): RedirectResponse {
        $this->authorizeChapter($request, $module, $chapter);
        $progress->touch($request->user(), $module, $chapter);
        $grading->start($request->user(), $chapter);

        return redirect()->route('e-class.chapters.show', [$module, $chapter]);
    }

    public function submit(
        SubmitQuizAttemptRequest $request,
        QuizAttempt $attempt,
        QuizGradingService $grading,
    ): RedirectResponse {
        $attempt->loadMissing('chapter.module');
        abort_unless($attempt->chapter !== null && $attempt->chapter->module !== null, 404);
        abort_unless($attempt->chapter->module->is_published, 404);
        abort_unless($attempt->chapter->module->isOwnedBy($request->user()), 403);

        $grading->grade($request->user(), $attempt, $request->validated('answers'));

        return redirect()->route('e-class.quiz-attempts.show', $attempt);
    }

    public function show(Request $request, QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === $request->user()->getKey(), 403);
        abort_unless($attempt->submitted_at !== null, 404);

        $attempt->load(['chapter.module', 'answers.question', 'answers.selectedOption']);

        return view('e-class.quiz-result', compact('attempt'));
    }

    private function authorizeChapter(Request $request, CbtModule $module, Chapter $chapter): void
    {
        abort_unless($module->is_published, 404);
        abort_unless($chapter->cbt_module_id === $module->getKey(), 404);
        abort_unless($module->isOwnedBy($request->user()), 403);
    }
}

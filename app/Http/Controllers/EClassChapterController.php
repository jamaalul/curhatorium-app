<?php

namespace App\Http\Controllers;

use App\ChapterType;
use App\Models\CbtModule;
use App\Models\Chapter;
use App\Services\EClassProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EClassChapterController extends Controller
{
    public function show(
        Request $request,
        CbtModule $module,
        Chapter $chapter,
        EClassProgressService $progress,
    ): View {
        $this->authorizeAccess($request, $module, $chapter);
        $progress->touch($request->user(), $module, $chapter);

        if ($chapter->type === ChapterType::Reading) {
            return view('e-class.chapters.reading', compact('module', 'chapter'));
        }

        if ($chapter->type === ChapterType::Video) {
            $videoUrl = Str::startsWith((string) $chapter->video_url, ['http://', 'https://'])
                ? $chapter->video_url
                : route('e-class.chapters.video', [$module, $chapter]);

            return view('e-class.chapters.video', compact('module', 'chapter', 'videoUrl'));
        }

        $questions = $chapter->questions()
            ->select(['id', 'chapter_id', 'question', 'type', 'points', 'order_number'])
            ->with([
                'options' => fn ($query) => $query->select([
                    'id',
                    'quiz_question_id',
                    'option_text',
                    'order_number',
                ]),
            ])
            ->get();
        $attempt = $chapter->quizAttempts()
            ->whereBelongsTo($request->user())
            ->whereNull('submitted_at')
            ->latest('attempt_number')
            ->first();

        return view('e-class.chapters.quiz', compact('module', 'chapter', 'questions', 'attempt'));
    }

    public function complete(
        Request $request,
        CbtModule $module,
        Chapter $chapter,
        EClassProgressService $progress,
    ): RedirectResponse {
        $this->authorizeAccess($request, $module, $chapter);
        abort_if($chapter->type === ChapterType::Quiz, 422, 'Quiz selesai setelah attempt disubmit.');

        $progress->completeChapter($request->user(), $module, $chapter);

        return redirect()
            ->route('e-class.chapters.show', [$module, $chapter])
            ->with('success', 'Chapter berhasil diselesaikan.');
    }

    public function video(Request $request, CbtModule $module, Chapter $chapter): StreamedResponse
    {
        $this->authorizeAccess($request, $module, $chapter);
        abort_unless($chapter->type === ChapterType::Video, 404);
        abort_if(Str::startsWith((string) $chapter->video_url, ['http://', 'https://']), 404);
        abort_unless(Storage::disk('private')->exists((string) $chapter->video_url), 404);

        return Storage::disk('private')->response((string) $chapter->video_url, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeAccess(Request $request, CbtModule $module, Chapter $chapter): void
    {
        abort_unless($module->is_published, 404);
        abort_unless($chapter->cbt_module_id === $module->getKey(), 404);
        abort_unless($module->isOwnedBy($request->user()), 403);
    }
}

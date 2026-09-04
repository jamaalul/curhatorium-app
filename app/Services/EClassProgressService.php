<?php

namespace App\Services;

use App\Models\CbtModule;
use App\Models\Chapter;
use App\Models\User;
use App\Models\UserChapterProgress;
use App\Models\UserModuleProgress;
use App\ProgressStatus;
use Illuminate\Support\Facades\DB;

class EClassProgressService
{
    /** @return array{module: UserModuleProgress, chapter: UserChapterProgress} */
    public function touch(User $user, CbtModule $module, Chapter $chapter): array
    {
        return DB::transaction(function () use ($user, $module, $chapter): array {
            $now = now();
            $moduleProgress = UserModuleProgress::query()->firstOrNew([
                'user_id' => $user->getKey(),
                'cbt_module_id' => $module->getKey(),
            ]);
            $moduleProgress->status ??= ProgressStatus::InProgress;
            $moduleProgress->started_at ??= $now;
            $moduleProgress->last_accessed_at = $now;
            $moduleProgress->save();

            $chapterProgress = UserChapterProgress::query()->firstOrNew([
                'user_id' => $user->getKey(),
                'chapter_id' => $chapter->getKey(),
            ]);
            $chapterProgress->status ??= ProgressStatus::InProgress;
            $chapterProgress->started_at ??= $now;
            $chapterProgress->last_accessed_at = $now;
            $chapterProgress->save();

            return ['module' => $moduleProgress, 'chapter' => $chapterProgress];
        });
    }

    public function completeChapter(User $user, CbtModule $module, Chapter $chapter): UserModuleProgress
    {
        return DB::transaction(function () use ($user, $module, $chapter): UserModuleProgress {
            $progress = $this->touch($user, $module, $chapter);
            $progress['chapter']->markCompleted();

            $chapterIds = $module->chapters()->select('id');
            $totalChapters = $module->chapters()->count();
            $completedChapters = UserChapterProgress::query()
                ->whereBelongsTo($user)
                ->whereIn('chapter_id', $chapterIds)
                ->where('status', ProgressStatus::Completed->value)
                ->count();

            if ($totalChapters > 0 && $completedChapters === $totalChapters) {
                $progress['module']->markCompleted();
            } else {
                $progress['module']->markInProgress();
            }

            return $progress['module'];
        });
    }
}

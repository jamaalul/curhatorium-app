<?php

namespace App\Services;

use App\Models\CbtModule;
use App\Models\Certificate;
use App\Models\User;
use App\Models\UserChapterProgress;
use App\Models\UserModuleProgress;
use App\ProgressStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CertificateService
{
    public function claim(User $user, CbtModule $module): Certificate
    {
        abort_unless($module->is_published, 404);
        abort_unless($module->isOwnedBy($user), 403);

        $certificate = DB::transaction(function () use ($user, $module): Certificate {
            $lockedModule = CbtModule::query()->whereKey($module->getKey())->lockForUpdate()->firstOrFail();
            $chapterIds = $lockedModule->chapters()->pluck('id');

            abort_if($chapterIds->isEmpty(), 422, 'Module tanpa chapter belum dapat disertifikasi.');

            $completedChapters = UserChapterProgress::query()
                ->whereBelongsTo($user)
                ->whereIn('chapter_id', $chapterIds)
                ->where('status', ProgressStatus::Completed->value)
                ->count();
            $moduleCompleted = UserModuleProgress::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($lockedModule, 'module')
                ->where('status', ProgressStatus::Completed->value)
                ->whereNotNull('completed_at')
                ->exists();

            abort_unless($completedChapters === $chapterIds->count() && $moduleCompleted, 422, 'Module belum selesai.');

            return Certificate::query()->firstOrCreate(
                [
                    'user_id' => $user->getKey(),
                    'cbt_module_id' => $lockedModule->getKey(),
                ],
                [
                    'certificate_number' => $this->generateNumber(),
                    'issued_at' => now(),
                ],
            );
        });

        return $this->ensurePdf($certificate);
    }

    public function ensurePdf(Certificate $certificate): Certificate
    {
        if ($certificate->pdf_path && Storage::disk('private')->exists($certificate->pdf_path)) {
            return $certificate;
        }

        $certificate->loadMissing(['user', 'module']);
        $path = 'certificates/'.$certificate->certificate_number.'.pdf';
        $contents = Pdf::loadView('e-class.certificates.pdf', compact('certificate'))->output();

        if (! Storage::disk('private')->put($path, $contents)) {
            throw new RuntimeException('Gagal menyimpan PDF sertifikat.');
        }

        $certificate->update(['pdf_path' => $path]);

        return $certificate->refresh();
    }

    private function generateNumber(): string
    {
        do {
            $number = 'CRH-'.Str::upper((string) Str::ulid());
        } while (Certificate::query()->where('certificate_number', $number)->exists());

        return $number;
    }
}

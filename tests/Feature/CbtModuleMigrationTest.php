<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CbtModuleMigrationTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<string, list<string>> */
    private const TABLE_COLUMNS = [
        'cbt_modules' => ['id', 'title', 'slug', 'description', 'price', 'is_published', 'published_at', 'created_at', 'updated_at', 'deleted_at'],
        'chapters' => ['id', 'cbt_module_id', 'title', 'type', 'text_content', 'video_url', 'order_number', 'created_at', 'updated_at', 'deleted_at'],
        'quiz_questions' => ['id', 'chapter_id', 'question', 'type', 'accepted_answer', 'points', 'order_number', 'created_at', 'updated_at', 'deleted_at'],
        'quiz_question_options' => ['id', 'quiz_question_id', 'option_text', 'is_correct', 'order_number', 'created_at', 'updated_at', 'deleted_at'],
        'user_cbt_modules' => ['id', 'user_id', 'cbt_module_id', 'source_order_id', 'granted_at', 'revoked_at', 'created_at', 'updated_at'],
        'user_module_progresses' => ['id', 'user_id', 'cbt_module_id', 'status', 'started_at', 'last_accessed_at', 'completed_at', 'created_at', 'updated_at'],
        'user_chapter_progresses' => ['id', 'user_id', 'chapter_id', 'status', 'started_at', 'last_accessed_at', 'completed_at', 'created_at', 'updated_at'],
        'quiz_attempts' => ['id', 'user_id', 'chapter_id', 'attempt_number', 'score', 'max_score', 'started_at', 'submitted_at', 'created_at', 'updated_at'],
        'quiz_answers' => ['id', 'quiz_attempt_id', 'quiz_question_id', 'selected_option_id', 'answer_text', 'is_correct', 'awarded_points', 'created_at', 'updated_at'],
        'certificates' => ['id', 'certificate_number', 'user_id', 'cbt_module_id', 'issued_at', 'pdf_path', 'created_at', 'updated_at'],
    ];

    public function test_all_cbt_tables_columns_and_indexes_are_created(): void
    {
        foreach (self::TABLE_COLUMNS as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table));
            $this->assertTrue(Schema::hasColumns($table, $columns));
        }

        $entitlementIndexes = collect(Schema::getIndexes('user_cbt_modules'))->pluck('name');
        $moduleProgressIndexes = collect(Schema::getIndexes('user_module_progresses'))->pluck('name');

        $this->assertContains('idx_user_cbt_modules_source_order', $entitlementIndexes);
        $this->assertContains('idx_user_module_last_accessed', $moduleProgressIndexes);
    }

    public function test_chapter_type_rejects_an_unknown_value(): void
    {
        $moduleId = $this->createModule();

        $this->expectException(QueryException::class);

        DB::table('chapters')->insert([
            'cbt_module_id' => $moduleId,
            'title' => 'Audio Chapter',
            'type' => 'audio',
            'order_number' => 1,
        ]);
    }

    public function test_question_type_rejects_an_unknown_value(): void
    {
        $chapterId = $this->createChapter($this->createModule());

        $this->expectException(QueryException::class);

        DB::table('quiz_questions')->insert([
            'chapter_id' => $chapterId,
            'question' => 'Invalid type?',
            'type' => 'essay',
            'order_number' => 1,
        ]);
    }

    public function test_progress_status_rejects_an_unknown_value(): void
    {
        $user = User::factory()->create();

        $this->expectException(QueryException::class);

        DB::table('user_module_progresses')->insert([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $this->createModule(),
            'status' => 'paused',
            'started_at' => now(),
            'last_accessed_at' => now(),
        ]);
    }

    public function test_nullable_fields_and_default_values_match_the_cdm(): void
    {
        $user = User::factory()->create();
        $moduleId = $this->createModule();
        $chapterId = $this->createChapter($moduleId, ['text_content' => null, 'video_url' => null]);
        $questionId = $this->createQuestion($chapterId, ['accepted_answer' => null]);
        $optionId = $this->createOption($questionId);

        $entitlementId = DB::table('user_cbt_modules')->insertGetId([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'source_order_id' => null,
            'granted_at' => now(),
            'revoked_at' => null,
        ]);
        $moduleProgressId = DB::table('user_module_progresses')->insertGetId([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_accessed_at' => now(),
            'completed_at' => null,
        ]);
        $chapterProgressId = DB::table('user_chapter_progresses')->insertGetId([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_accessed_at' => now(),
            'completed_at' => null,
        ]);
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'attempt_number' => 1,
            'started_at' => now(),
            'submitted_at' => null,
        ]);
        $answerId = DB::table('quiz_answers')->insertGetId([
            'quiz_attempt_id' => $attemptId,
            'quiz_question_id' => $questionId,
            'selected_option_id' => null,
            'answer_text' => null,
            'is_correct' => null,
        ]);
        $certificateId = DB::table('certificates')->insertGetId([
            'certificate_number' => 'CERT-NULLABLE-001',
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'issued_at' => now(),
            'pdf_path' => null,
        ]);

        $module = DB::table('cbt_modules')->find($moduleId);
        $chapter = DB::table('chapters')->find($chapterId);
        $question = DB::table('quiz_questions')->find($questionId);
        $option = DB::table('quiz_question_options')->find($optionId);
        $attempt = DB::table('quiz_attempts')->find($attemptId);
        $answer = DB::table('quiz_answers')->find($answerId);

        $this->assertFalse((bool) $module->is_published);
        $this->assertNull($module->published_at);
        $this->assertNull($chapter->text_content);
        $this->assertNull($chapter->video_url);
        $this->assertNull($question->accepted_answer);
        $this->assertSame(1.0, (float) $question->points);
        $this->assertFalse((bool) $option->is_correct);
        $this->assertNull(DB::table('user_cbt_modules')->find($entitlementId)->source_order_id);
        $this->assertNull(DB::table('user_cbt_modules')->find($entitlementId)->revoked_at);
        $this->assertNull(DB::table('user_module_progresses')->find($moduleProgressId)->completed_at);
        $this->assertNull(DB::table('user_chapter_progresses')->find($chapterProgressId)->completed_at);
        $this->assertSame(0.0, (float) $attempt->score);
        $this->assertSame(0.0, (float) $attempt->max_score);
        $this->assertNull($attempt->submitted_at);
        $this->assertNull($answer->selected_option_id);
        $this->assertNull($answer->answer_text);
        $this->assertNull($answer->is_correct);
        $this->assertSame(0.0, (float) $answer->awarded_points);
        $this->assertNull(DB::table('certificates')->find($certificateId)->pdf_path);
    }

    public function test_unique_constraints_prevent_duplicate_cdm_records(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $moduleId = $this->createModule();
        $chapterId = $this->createChapter($moduleId);
        $questionId = $this->createQuestion($chapterId);
        $optionId = $this->createOption($questionId);

        DB::table('user_cbt_modules')->insert([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'granted_at' => now(),
        ]);
        DB::table('user_module_progresses')->insert([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_accessed_at' => now(),
        ]);
        DB::table('user_chapter_progresses')->insert([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_accessed_at' => now(),
        ]);
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'attempt_number' => 1,
            'started_at' => now(),
        ]);
        DB::table('quiz_answers')->insert([
            'quiz_attempt_id' => $attemptId,
            'quiz_question_id' => $questionId,
            'selected_option_id' => $optionId,
        ]);
        DB::table('certificates')->insert([
            'certificate_number' => 'CERT-UNIQUE-001',
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'issued_at' => now(),
        ]);

        $this->assertUniqueConstraint(fn (): bool => DB::table('cbt_modules')->insert([
            'title' => 'Duplicate Slug',
            'slug' => 'module-migration-test',
            'description' => 'Duplicate.',
            'price' => 10000,
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('chapters')->insert([
            'cbt_module_id' => $moduleId,
            'title' => 'Duplicate Order',
            'type' => 'reading',
            'order_number' => 1,
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('quiz_questions')->insert([
            'chapter_id' => $chapterId,
            'question' => 'Duplicate order?',
            'type' => 'multiple_choice',
            'order_number' => 1,
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('quiz_question_options')->insert([
            'quiz_question_id' => $questionId,
            'option_text' => 'Duplicate order',
            'order_number' => 1,
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('user_cbt_modules')->insert([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'granted_at' => now(),
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('user_module_progresses')->insert([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_accessed_at' => now(),
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('user_chapter_progresses')->insert([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_accessed_at' => now(),
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('quiz_attempts')->insert([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'attempt_number' => 1,
            'started_at' => now(),
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('quiz_answers')->insert([
            'quiz_attempt_id' => $attemptId,
            'quiz_question_id' => $questionId,
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('certificates')->insert([
            'certificate_number' => 'CERT-UNIQUE-001',
            'user_id' => $otherUser->getKey(),
            'cbt_module_id' => $moduleId,
            'issued_at' => now(),
        ]));
        $this->assertUniqueConstraint(fn (): bool => DB::table('certificates')->insert([
            'certificate_number' => 'CERT-UNIQUE-002',
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'issued_at' => now(),
        ]));
    }

    public function test_nullable_foreign_keys_are_set_to_null_when_sources_are_deleted(): void
    {
        $user = User::factory()->create();
        $moduleId = $this->createModule();
        $chapterId = $this->createChapter($moduleId);
        $questionId = $this->createQuestion($chapterId);
        $optionId = $this->createOption($questionId);
        $order = Order::factory()->for($user)->create();
        $entitlementId = DB::table('user_cbt_modules')->insertGetId([
            'user_id' => $user->getKey(),
            'cbt_module_id' => $moduleId,
            'source_order_id' => $order->getKey(),
            'granted_at' => now(),
        ]);
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'user_id' => $user->getKey(),
            'chapter_id' => $chapterId,
            'attempt_number' => 1,
            'started_at' => now(),
        ]);
        $answerId = DB::table('quiz_answers')->insertGetId([
            'quiz_attempt_id' => $attemptId,
            'quiz_question_id' => $questionId,
            'selected_option_id' => $optionId,
        ]);

        $order->delete();
        DB::table('quiz_question_options')->where('id', $optionId)->delete();

        $this->assertNull(DB::table('user_cbt_modules')->find($entitlementId)->source_order_id);
        $this->assertNull(DB::table('quiz_answers')->find($answerId)->selected_option_id);
    }

    public function test_user_owned_transactional_rows_cascade_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $moduleId = $this->createModule();
        $chapterId = $this->createChapter($moduleId);
        $questionId = $this->createQuestion($chapterId);
        $optionId = $this->createOption($questionId);

        DB::table('user_cbt_modules')->insert(['user_id' => $user->getKey(), 'cbt_module_id' => $moduleId, 'granted_at' => now()]);
        DB::table('user_module_progresses')->insert(['user_id' => $user->getKey(), 'cbt_module_id' => $moduleId, 'status' => 'in_progress', 'started_at' => now(), 'last_accessed_at' => now()]);
        DB::table('user_chapter_progresses')->insert(['user_id' => $user->getKey(), 'chapter_id' => $chapterId, 'status' => 'in_progress', 'started_at' => now(), 'last_accessed_at' => now()]);
        $attemptId = DB::table('quiz_attempts')->insertGetId(['user_id' => $user->getKey(), 'chapter_id' => $chapterId, 'attempt_number' => 1, 'started_at' => now()]);
        DB::table('quiz_answers')->insert(['quiz_attempt_id' => $attemptId, 'quiz_question_id' => $questionId, 'selected_option_id' => $optionId]);
        DB::table('certificates')->insert(['certificate_number' => 'CERT-CASCADE-001', 'user_id' => $user->getKey(), 'cbt_module_id' => $moduleId, 'issued_at' => now()]);

        $user->delete();

        foreach (['user_cbt_modules', 'user_module_progresses', 'user_chapter_progresses', 'quiz_attempts', 'quiz_answers', 'certificates'] as $table) {
            $this->assertSame(0, DB::table($table)->count());
        }
    }

    public function test_cbt_migrations_roll_back_in_reverse_order_and_can_be_reapplied(): void
    {
        $migrations = array_map(
            static fn (string $file): object => require $file,
            $this->migrationFiles(),
        );

        Schema::disableForeignKeyConstraints();

        foreach (array_reverse($migrations) as $migration) {
            $migration->down();
        }

        foreach (array_keys(self::TABLE_COLUMNS) as $table) {
            $this->assertFalse(Schema::hasTable($table));
        }

        foreach ($migrations as $migration) {
            $migration->up();
        }

        Schema::enableForeignKeyConstraints();

        foreach (array_keys(self::TABLE_COLUMNS) as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }
    }

    private function createModule(): int
    {
        return DB::table('cbt_modules')->insertGetId([
            'title' => 'Module Migration Test',
            'slug' => 'module-migration-test',
            'description' => 'Module used by migration tests.',
            'price' => 125000,
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function createChapter(int $moduleId, array $attributes = []): int
    {
        return DB::table('chapters')->insertGetId(array_merge([
            'cbt_module_id' => $moduleId,
            'title' => 'Quiz Chapter',
            'type' => 'quiz',
            'order_number' => 1,
        ], $attributes));
    }

    /** @param array<string, mixed> $attributes */
    private function createQuestion(int $chapterId, array $attributes = []): int
    {
        return DB::table('quiz_questions')->insertGetId(array_merge([
            'chapter_id' => $chapterId,
            'question' => 'What is the answer?',
            'type' => 'multiple_choice',
            'order_number' => 1,
        ], $attributes));
    }

    private function createOption(int $questionId): int
    {
        return DB::table('quiz_question_options')->insertGetId([
            'quiz_question_id' => $questionId,
            'option_text' => 'Correct answer',
            'order_number' => 1,
        ]);
    }

    private function assertUniqueConstraint(Closure $insert): void
    {
        try {
            $insert();
            $this->fail('The database accepted a duplicate row.');
        } catch (QueryException) {
            $this->addToAssertionCount(1);
        }
    }

    /** @return list<string> */
    private function migrationFiles(): array
    {
        return [
            database_path('migrations/2026_09_02_055828_create_cbt_modules_table.php'),
            database_path('migrations/2026_09_02_055829_create_chapters_table.php'),
            database_path('migrations/2026_09_02_055830_create_quiz_questions_table.php'),
            database_path('migrations/2026_09_02_055831_create_quiz_question_options_table.php'),
            database_path('migrations/2026_09_02_055833_create_user_cbt_modules_table.php'),
            database_path('migrations/2026_09_02_055834_create_user_module_progresses_table.php'),
            database_path('migrations/2026_09_02_055836_create_user_chapter_progresses_table.php'),
            database_path('migrations/2026_09_02_055837_create_quiz_attempts_table.php'),
            database_path('migrations/2026_09_02_055838_create_quiz_answers_table.php'),
            database_path('migrations/2026_09_02_055840_create_certificates_table.php'),
        ];
    }
}

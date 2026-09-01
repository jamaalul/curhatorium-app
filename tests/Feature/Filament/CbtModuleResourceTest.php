<?php

namespace Tests\Feature\Filament;

use App\ChapterType;
use App\Filament\Resources\CbtModuleResource;
use App\Filament\Resources\CbtModuleResource\Pages\CreateCbtModule;
use App\Filament\Resources\CbtModuleResource\Pages\EditCbtModule;
use App\Filament\Resources\CbtModuleResource\Pages\ListCbtModules;
use App\Filament\Resources\CbtModuleResource\RelationManagers\ChaptersRelationManager;
use App\Models\CbtModule;
use App\Models\Chapter;
use App\Models\User;
use App\QuizQuestionType;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class CbtModuleResourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::factory()->create(['is_admin' => true]));
    }

    public function test_resource_pages_can_render(): void
    {
        $module = CbtModule::factory()->create();

        $this->get(CbtModuleResource::getUrl('index'))->assertSuccessful();
        $this->get(CbtModuleResource::getUrl('create'))->assertSuccessful();
        $this->get(CbtModuleResource::getUrl('edit', ['record' => $module]))->assertSuccessful();
    }

    public function test_admin_can_create_and_edit_module_with_consistent_published_date(): void
    {
        Livewire::test(CreateCbtModule::class)
            ->fillForm([
                'title' => 'Kelas Regulasi Emosi',
                'slug' => 'kelas-regulasi-emosi',
                'description' => 'Materi regulasi emosi.',
                'price' => 150000,
                'is_published' => true,
                'published_at' => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        $module = CbtModule::query()->where('slug', 'kelas-regulasi-emosi')->sole();
        $this->assertTrue($module->is_published);
        $this->assertNotNull($module->published_at);

        Livewire::test(EditCbtModule::class, ['record' => $module->getRouteKey()])
            ->fillForm([
                'title' => 'Kelas Regulasi Emosi Baru',
                'slug' => 'kelas-regulasi-emosi-baru',
                'description' => 'Materi yang telah diperbarui.',
                'price' => 175000,
                'is_published' => false,
                'published_at' => $module->published_at,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $module->refresh();
        $this->assertSame('Kelas Regulasi Emosi Baru', $module->title);
        $this->assertSame('175000.00', $module->price);
        $this->assertFalse($module->is_published);
        $this->assertNull($module->published_at);
    }

    public function test_slug_is_generated_from_title_until_manually_changed_and_must_be_unique(): void
    {
        Livewire::test(CreateCbtModule::class)
            ->set('data.title', 'Kelas Kesadaran Diri')
            ->assertFormSet(['slug' => 'kelas-kesadaran-diri'])
            ->set('data.slug', 'slug-kustom')
            ->set('data.title', 'Judul Baru')
            ->assertFormSet(['slug' => 'slug-kustom']);

        CbtModule::factory()->create(['slug' => 'slug-sama']);

        Livewire::test(CreateCbtModule::class)
            ->fillForm([
                'title' => 'Module Baru',
                'slug' => 'slug-sama',
                'description' => 'Deskripsi module.',
                'price' => 10000,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_module_table_can_search_filter_soft_delete_and_restore(): void
    {
        $published = CbtModule::factory()->published()->create([
            'title' => 'Module Published',
            'slug' => 'module-published',
        ]);
        $draft = CbtModule::factory()->create([
            'title' => 'Module Draft',
            'slug' => 'slug-draft-dicari',
        ]);

        Livewire::test(ListCbtModules::class)
            ->searchTable('Module Published')
            ->assertCanSeeTableRecords([$published])
            ->assertCanNotSeeTableRecords([$draft]);

        Livewire::test(ListCbtModules::class)
            ->searchTable('slug-draft-dicari')
            ->assertCanSeeTableRecords([$draft])
            ->assertCanNotSeeTableRecords([$published]);

        Livewire::test(ListCbtModules::class)
            ->filterTable('is_published', true)
            ->assertCanSeeTableRecords([$published])
            ->assertCanNotSeeTableRecords([$draft]);

        Livewire::test(ListCbtModules::class)
            ->callTableAction('delete', $draft)
            ->assertNotified();
        $this->assertSoftDeleted($draft);
        $draft->refresh();

        Livewire::test(ListCbtModules::class)
            ->filterTable('trashed', 'onlyTrashed')
            ->assertCanSeeTableRecords([$draft])
            ->callTableAction('restore', $draft)
            ->assertNotified();

        $this->assertNull($draft->fresh()->deleted_at);
    }

    public function test_admin_can_create_reading_and_video_chapters_with_conditional_validation(): void
    {
        $module = CbtModule::factory()->create();

        $this->chapterManager($module)
            ->callTableAction('create', data: [
                'title' => 'Materi Bacaan',
                'type' => ChapterType::Reading->value,
                'text_content' => '<p>Konten bacaan.</p>',
                'order_number' => 1,
            ])
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $this->chapterManager($module)
            ->callTableAction('create', data: [
                'title' => 'Materi Video',
                'type' => ChapterType::Video->value,
                'video_url' => 'https://example.com/video.mp4',
                'order_number' => 2,
            ])
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $reading = Chapter::query()->where('order_number', 1)->sole();
        $video = Chapter::query()->where('order_number', 2)->sole();
        $this->assertNotNull($reading->text_content);
        $this->assertNull($reading->video_url);
        $this->assertNotNull($video->video_url);
        $this->assertNull($video->text_content);

        $this->chapterManager($module)
            ->callTableAction('create', data: [
                'title' => 'Reading Invalid',
                'type' => ChapterType::Reading->value,
                'text_content' => null,
                'order_number' => 3,
            ])
            ->assertHasTableActionErrors();

        $this->chapterManager($module)
            ->callTableAction('create', data: [
                'title' => 'Video Invalid',
                'type' => ChapterType::Video->value,
                'video_url' => 'bukan-url',
                'order_number' => 3,
            ])
            ->assertHasTableActionErrors();
    }

    public function test_chapter_order_is_unique_per_module_and_can_be_reordered_safely(): void
    {
        $module = CbtModule::factory()->create();
        $first = Chapter::factory()->for($module, 'module')->create(['title' => 'Satu', 'order_number' => 1]);
        $second = Chapter::factory()->for($module, 'module')->create(['title' => 'Dua', 'order_number' => 2]);
        $third = Chapter::factory()->for($module, 'module')->create(['title' => 'Tiga', 'order_number' => 3]);

        $this->chapterManager($module)
            ->callTableAction('create', data: [
                'title' => 'Duplikat',
                'type' => ChapterType::Reading->value,
                'text_content' => 'Konten.',
                'order_number' => 1,
            ])
            ->assertHasTableActionErrors(['order_number' => 'unique']);

        $this->chapterManager($module)
            ->call('reorderTable', [$third->getKey(), $first->getKey(), $second->getKey()]);

        $this->assertSame(
            [$third->getKey(), $first->getKey(), $second->getKey()],
            $module->chapters()->pluck('id')->all(),
        );
        $this->assertSame([1, 2, 3], $module->chapters()->pluck('order_number')->all());
    }

    public function test_admin_can_create_quiz_with_multiple_choice_options_and_short_answer(): void
    {
        $module = CbtModule::factory()->create();

        $this->chapterManager($module)
            ->callTableAction('create', data: $this->validQuizData())
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $chapter = Chapter::query()->sole();
        $questions = $chapter->questions()->get();
        $this->assertSame(ChapterType::Quiz, $chapter->type);
        $this->assertCount(2, $questions);
        $this->assertSame(QuizQuestionType::MultipleChoice, $questions[0]->type);
        $this->assertNull($questions[0]->accepted_answer);
        $this->assertSame(2, $questions[0]->options()->count());
        $this->assertSame(1, $questions[0]->options()->where('is_correct', true)->count());
        $this->assertSame(QuizQuestionType::ShortAnswer, $questions[1]->type);
        $this->assertSame('Bernapas', $questions[1]->accepted_answer);
        $this->assertSame(0, $questions[1]->options()->count());
    }

    public function test_quiz_question_and_option_validation_rejects_invalid_nested_data(): void
    {
        $module = CbtModule::factory()->create();
        $withoutCorrectOption = $this->validQuizData();
        $withoutCorrectOption['questions'][0]['options'][0]['is_correct'] = false;

        $this->chapterManager($module)
            ->callTableAction('create', data: $withoutCorrectOption)
            ->assertHasTableActionErrors();

        $withoutAcceptedAnswer = $this->validQuizData();
        $withoutAcceptedAnswer['questions'][1]['accepted_answer'] = null;
        $this->chapterManager($module)
            ->callTableAction('create', data: $withoutAcceptedAnswer)
            ->assertHasTableActionErrors();

        $duplicateQuestionOrder = $this->validQuizData();
        $duplicateQuestionOrder['questions'][1]['order_number'] = 1;
        $this->chapterManager($module)
            ->callTableAction('create', data: $duplicateQuestionOrder)
            ->assertHasTableActionErrors();

        $duplicateOptionOrder = $this->validQuizData();
        $duplicateOptionOrder['questions'][0]['options'][1]['order_number'] = 1;
        $this->chapterManager($module)
            ->callTableAction('create', data: $duplicateOptionOrder)
            ->assertHasTableActionErrors();

        $this->assertDatabaseCount('chapters', 0);
    }

    public function test_questions_are_only_saved_for_quiz_and_options_only_for_multiple_choice(): void
    {
        $module = CbtModule::factory()->create();
        $readingData = $this->validQuizData();
        $readingData['title'] = 'Reading dengan data quiz';
        $readingData['type'] = ChapterType::Reading->value;
        $readingData['text_content'] = 'Konten reading.';

        $this->chapterManager($module)
            ->callTableAction('create', data: $readingData)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseCount('quiz_questions', 0);
    }

    public function test_chapter_question_and_option_are_soft_deleted_through_filament(): void
    {
        $module = CbtModule::factory()->create();
        $this->chapterManager($module)->callTableAction('create', data: $this->validQuizData());
        $chapter = Chapter::query()->sole();
        $question = $chapter->questions()->where('type', QuizQuestionType::MultipleChoice->value)->sole();
        $option = $question->options()->reorder()->orderByDesc('order_number')->firstOrFail();

        $manager = $this->chapterManager($module)->mountTableAction('edit', $chapter);
        $data = $manager->get('mountedTableActionsData.0');
        $questionKey = array_key_first($data['questions']);
        $optionKey = array_key_last($data['questions'][$questionKey]['options']);
        unset($data['questions'][$questionKey]['options'][$optionKey]);
        $manager->set('mountedTableActionsData.0', $data)
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();
        $this->assertSoftDeleted($option);

        $manager = $this->chapterManager($module)->mountTableAction('edit', $chapter);
        $data = $manager->get('mountedTableActionsData.0');
        $questionKey = array_key_first($data['questions']);
        unset($data['questions'][$questionKey]);
        $manager->set('mountedTableActionsData.0', $data)
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();
        $this->assertSoftDeleted($question);

        $this->chapterManager($module)
            ->callTableAction('delete', $chapter)
            ->assertNotified();
        $this->assertSoftDeleted($chapter);
        $chapter->refresh();

        $this->chapterManager($module)
            ->filterTable('trashed', 'onlyTrashed')
            ->callTableAction('restore', $chapter)
            ->assertNotified();
        $this->assertNull($chapter->fresh()->deleted_at);
    }

    private function chapterManager(CbtModule $module): Testable
    {
        return Livewire::test(ChaptersRelationManager::class, [
            'ownerRecord' => $module,
            'pageClass' => EditCbtModule::class,
        ]);
    }

    /** @return array<string, mixed> */
    private function validQuizData(): array
    {
        return [
            'title' => 'Quiz Kesadaran Diri',
            'type' => ChapterType::Quiz->value,
            'order_number' => 1,
            'questions' => [
                [
                    'question' => 'Apa respons yang paling tepat?',
                    'type' => QuizQuestionType::MultipleChoice->value,
                    'accepted_answer' => 'Tidak boleh tersimpan',
                    'points' => 2,
                    'order_number' => 1,
                    'options' => [
                        [
                            'option_text' => 'Berhenti dan bernapas',
                            'is_correct' => true,
                            'order_number' => 1,
                        ],
                        [
                            'option_text' => 'Mengabaikan perasaan',
                            'is_correct' => false,
                            'order_number' => 2,
                        ],
                    ],
                ],
                [
                    'question' => 'Sebutkan satu teknik regulasi.',
                    'type' => QuizQuestionType::ShortAnswer->value,
                    'accepted_answer' => 'Bernapas',
                    'points' => 3,
                    'order_number' => 2,
                    'options' => [],
                ],
            ],
        ];
    }
}

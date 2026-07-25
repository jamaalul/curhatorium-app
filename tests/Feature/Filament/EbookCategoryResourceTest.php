<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\EbookCategoryResource;
use App\Filament\Resources\EbookCategoryResource\Pages\CreateEbookCategory;
use App\Filament\Resources\EbookCategoryResource\Pages\EditEbookCategory;
use App\Filament\Resources\EbookCategoryResource\Pages\ListEbookCategories;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EbookCategoryResourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs(User::factory()->create([
            'is_admin' => true,
        ]));
    }

    public function test_ebook_category_resource_pages_can_render(): void
    {
        $category = $this->createCategory();

        $this->get(EbookCategoryResource::getUrl('index'))->assertSuccessful();
        $this->get(EbookCategoryResource::getUrl('create'))->assertSuccessful();
        $this->get(EbookCategoryResource::getUrl('edit', ['record' => $category]))->assertSuccessful();
    }

    public function test_admin_can_create_and_edit_an_ebook_category(): void
    {
        Livewire::test(CreateEbookCategory::class)
            ->set('data.name', 'Mindfulness Dasar')
            ->assertFormSet([
                'slug' => 'mindfulness-dasar',
            ])
            ->set('data.slug', 'slug-kustom')
            ->set('data.name', 'Mindfulness Lanjutan')
            ->assertFormSet([
                'slug' => 'slug-kustom',
            ]);

        Livewire::test(CreateEbookCategory::class)
            ->fillForm([
                'name' => 'Mindfulness Dasar',
                'slug' => 'mindfulness-dasar',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        $category = EbookCategory::query()->where('slug', 'mindfulness-dasar')->firstOrFail();

        Livewire::test(EditEbookCategory::class, [
            'record' => $category->getKey(),
        ])
            ->fillForm([
                'name' => 'Mindfulness Dasar Updated',
                'slug' => 'mindfulness-dasar',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertSame('Mindfulness Dasar Updated', $category->refresh()->name);
    }

    public function test_ebook_category_slug_must_be_unique(): void
    {
        $this->createCategory([
            'name' => 'Jurnal',
            'slug' => 'jurnal',
        ]);

        Livewire::test(CreateEbookCategory::class)
            ->fillForm([
                'name' => 'Jurnal Baru',
                'slug' => 'jurnal',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'slug' => 'unique',
            ]);
    }

    public function test_ebook_category_table_can_search_show_ebook_count_and_delete(): void
    {
        $category = $this->createCategory([
            'name' => 'Mindfulness',
            'slug' => 'mindfulness',
        ]);
        $otherCategory = $this->createCategory([
            'name' => 'Produktivitas',
            'slug' => 'produktivitas',
        ]);

        $this->createEbook([
            'ebook_category_id' => $category->getKey(),
            'title' => 'Panduan Mindfulness',
            'slug' => 'panduan-mindfulness',
        ]);

        Livewire::test(ListEbookCategories::class)
            ->searchTable('Mindfulness')
            ->assertCanSeeTableRecords([$category])
            ->assertCanNotSeeTableRecords([$otherCategory]);

        $this->assertSame(1, $category->ebooks()->count());

        Livewire::test(ListEbookCategories::class)
            ->callTableAction('delete', $otherCategory)
            ->assertNotified();

        $this->assertModelMissing($otherCategory);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createCategory(array $attributes = []): EbookCategory
    {
        return EbookCategory::query()->create(array_merge([
            'name' => 'Kategori Ebook',
            'slug' => 'kategori-ebook-'.EbookCategory::query()->count(),
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createEbook(array $attributes = []): Ebook
    {
        return Ebook::query()->create(array_merge([
            'title' => 'Ebook Curhatorium',
            'slug' => 'ebook-curhatorium-'.Ebook::query()->count(),
            'description' => 'Konten ebook Curhatorium.',
            'price' => 99000,
            'page_count' => 30,
            'is_published' => true,
        ], $attributes));
    }
}

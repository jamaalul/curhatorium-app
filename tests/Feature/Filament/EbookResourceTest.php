<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\EbookResource;
use App\Filament\Resources\EbookResource\Pages\CreateEbook;
use App\Filament\Resources\EbookResource\Pages\EditEbook;
use App\Filament\Resources\EbookResource\Pages\ListEbooks;
use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EbookResourceTest extends TestCase
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

    public function test_ebook_resource_pages_can_render(): void
    {
        $ebook = $this->createEbook();

        $this->get(EbookResource::getUrl('index'))->assertSuccessful();
        $this->get(EbookResource::getUrl('create'))->assertSuccessful();
        $this->get(EbookResource::getUrl('edit', ['record' => $ebook]))->assertSuccessful();
    }

    public function test_admin_can_create_and_update_an_ebook(): void
    {
        $category = $this->createCategory([
            'name' => 'Mindfulness',
            'slug' => 'mindfulness',
        ]);
        $newCategory = $this->createCategory([
            'name' => 'Produktivitas',
            'slug' => 'produktivitas',
        ]);

        Livewire::test(CreateEbook::class)
            ->set('data.title', 'Panduan Mindfulness')
            ->assertFormSet([
                'slug' => 'panduan-mindfulness',
            ])
            ->set('data.slug', 'slug-ebook-kustom')
            ->set('data.title', 'Panduan Mindfulness Baru')
            ->assertFormSet([
                'slug' => 'slug-ebook-kustom',
            ]);

        Livewire::test(CreateEbook::class)
            ->fillForm([
                'ebook_category_id' => $category->getKey(),
                'title' => 'Panduan Mindfulness',
                'slug' => 'panduan-mindfulness',
                'description' => 'Ebook untuk latihan mindfulness.',
                'price' => 75000,
                'page_count' => 42,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        $ebook = Ebook::query()->where('slug', 'panduan-mindfulness')->firstOrFail();

        $this->assertTrue($ebook->is_published);
        $this->assertTrue($ebook->category->is($category));
        $this->assertSame(42, $ebook->page_count);

        Livewire::test(EditEbook::class, [
            'record' => $ebook->getRouteKey(),
        ])
            ->fillForm([
                'ebook_category_id' => $newCategory->getKey(),
                'title' => 'Panduan Mindfulness Baru',
                'slug' => 'panduan-mindfulness-baru',
                'description' => 'Ebook edisi baru.',
                'price' => 85000,
                'page_count' => 50,
                'is_published' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $ebook->refresh();

        $this->assertSame('Panduan Mindfulness Baru', $ebook->title);
        $this->assertSame('panduan-mindfulness-baru', $ebook->slug);
        $this->assertTrue($ebook->category->is($newCategory));
        $this->assertFalse($ebook->is_published);
        $this->assertSame(50, $ebook->page_count);
    }

    public function test_ebook_slug_must_be_unique(): void
    {
        $this->createEbook([
            'title' => 'Ebook Lama',
            'slug' => 'ebook-sama',
        ]);

        Livewire::test(CreateEbook::class)
            ->fillForm([
                'title' => 'Ebook Baru',
                'slug' => 'ebook-sama',
                'price' => 50000,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'slug' => 'unique',
            ]);
    }

    public function test_ebook_table_can_search_filter_and_delete_ebooks(): void
    {
        $category = $this->createCategory([
            'name' => 'Mindfulness',
            'slug' => 'mindfulness',
        ]);
        $otherCategory = $this->createCategory([
            'name' => 'Produktivitas',
            'slug' => 'produktivitas',
        ]);
        $publishedEbook = $this->createEbook([
            'ebook_category_id' => $category->getKey(),
            'title' => 'Ebook Published',
            'slug' => 'ebook-published',
            'is_published' => true,
        ]);
        $draftEbook = $this->createEbook([
            'ebook_category_id' => $otherCategory->getKey(),
            'title' => 'Ebook Draft',
            'slug' => 'slug-draft-yang-dicari',
            'is_published' => false,
        ]);

        Livewire::test(ListEbooks::class)
            ->searchTable('Ebook Published')
            ->assertCanSeeTableRecords([$publishedEbook])
            ->assertCanNotSeeTableRecords([$draftEbook]);

        Livewire::test(ListEbooks::class)
            ->searchTable('slug-draft-yang-dicari')
            ->assertCanSeeTableRecords([$draftEbook])
            ->assertCanNotSeeTableRecords([$publishedEbook]);

        Livewire::test(ListEbooks::class)
            ->filterTable('ebook_category_id', $category->getKey())
            ->assertCanSeeTableRecords([$publishedEbook])
            ->assertCanNotSeeTableRecords([$draftEbook]);

        Livewire::test(ListEbooks::class)
            ->filterTable('is_published', true)
            ->assertCanSeeTableRecords([$publishedEbook])
            ->assertCanNotSeeTableRecords([$draftEbook]);

        Livewire::test(ListEbooks::class)
            ->filterTable('is_published', false)
            ->assertCanSeeTableRecords([$draftEbook])
            ->assertCanNotSeeTableRecords([$publishedEbook])
            ->callTableAction('delete', $draftEbook)
            ->assertNotified();

        $this->assertModelMissing($draftEbook);
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
        $categoryId = $attributes['ebook_category_id'] ?? $this->createCategory()->getKey();

        return Ebook::query()->create(array_merge([
            'ebook_category_id' => $categoryId,
            'title' => 'Ebook Curhatorium',
            'slug' => 'ebook-curhatorium-'.Ebook::query()->count(),
            'description' => 'Konten ebook Curhatorium.',
            'price' => 99000,
            'page_count' => 30,
            'is_published' => true,
        ], $attributes));
    }
}

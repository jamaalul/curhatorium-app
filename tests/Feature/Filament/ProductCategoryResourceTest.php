<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ProductCategoryResource;
use App\Filament\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use App\Filament\Resources\ProductCategoryResource\Pages\EditProductCategory;
use App\Filament\Resources\ProductCategoryResource\Pages\ListProductCategories;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCategoryResourceTest extends TestCase
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

    public function test_product_category_resource_pages_can_render(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Jurnal',
            'slug' => 'jurnal',
        ]);

        $this->get(ProductCategoryResource::getUrl('index'))->assertSuccessful();
        $this->get(ProductCategoryResource::getUrl('create'))->assertSuccessful();
        $this->get(ProductCategoryResource::getUrl('edit', ['record' => $category]))->assertSuccessful();
    }

    public function test_admin_can_create_and_edit_a_product_category(): void
    {
        Livewire::test(CreateProductCategory::class)
            ->set('data.name', 'Jurnal Digital')
            ->assertFormSet([
                'slug' => 'jurnal-digital',
            ])
            ->set('data.slug', 'slug-kategori-kustom')
            ->set('data.name', 'Jurnal Digital Baru')
            ->assertFormSet([
                'slug' => 'slug-kategori-kustom',
            ]);

        Livewire::test(CreateProductCategory::class)
            ->fillForm([
                'name' => 'Jurnal Digital',
                'slug' => 'jurnal-digital',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        $category = ProductCategory::query()->where('slug', 'jurnal-digital')->firstOrFail();

        Livewire::test(EditProductCategory::class, [
            'record' => $category->getKey(),
        ])
            ->fillForm([
                'name' => 'Jurnal Digital Updated',
                'slug' => 'jurnal-digital',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertSame('Jurnal Digital Updated', $category->refresh()->name);
    }

    public function test_product_category_table_can_search_show_product_count_and_delete(): void
    {
        $category = ProductCategory::query()->create([
            'name' => 'Worksheet',
            'slug' => 'worksheet',
        ]);
        $otherCategory = ProductCategory::query()->create([
            'name' => 'Poster',
            'slug' => 'poster',
        ]);

        Product::query()->create([
            'product_category_id' => $category->getKey(),
            'name' => 'Worksheet Mindfulness',
            'slug' => 'worksheet-mindfulness',
            'description' => 'Worksheet produk.',
            'price' => 50000,
            'is_published' => true,
        ]);

        Livewire::test(ListProductCategories::class)
            ->searchTable('Worksheet')
            ->assertCanSeeTableRecords([$category])
            ->assertCanNotSeeTableRecords([$otherCategory]);

        $this->assertSame(1, $category->products()->count());

        Livewire::test(ListProductCategories::class)
            ->callTableAction('delete', $otherCategory)
            ->assertNotified();

        $this->assertModelMissing($otherCategory);
    }
}

<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\RelationManagers\MediaRelationManager;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductResourceTest extends TestCase
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

    public function test_product_resource_pages_can_render(): void
    {
        $product = $this->createProduct();

        $this->get(ProductResource::getUrl('index'))->assertSuccessful();
        $this->get(ProductResource::getUrl('create'))->assertSuccessful();
        $this->get(ProductResource::getUrl('edit', ['record' => $product]))->assertSuccessful();
    }

    public function test_admin_can_create_and_update_a_product(): void
    {
        Livewire::test(CreateProduct::class)
            ->set('data.name', 'Jurnal Refleksi')
            ->assertFormSet([
                'slug' => 'jurnal-refleksi',
            ])
            ->set('data.slug', 'slug-kustom')
            ->set('data.name', 'Jurnal Refleksi Baru')
            ->assertFormSet([
                'slug' => 'slug-kustom',
            ]);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Jurnal Refleksi',
                'slug' => 'jurnal-refleksi',
                'description' => 'Jurnal untuk refleksi harian.',
                'price' => 75000,
                'ecommerce_url' => 'https://example.com/products/jurnal-refleksi',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        $product = Product::query()->where('slug', 'jurnal-refleksi')->firstOrFail();

        $this->assertTrue($product->is_published);

        Livewire::test(EditProduct::class, [
            'record' => $product->getRouteKey(),
        ])
            ->fillForm([
                'name' => 'Jurnal Refleksi Baru',
                'slug' => 'jurnal-refleksi-baru',
                'description' => 'Jurnal refleksi dengan edisi baru.',
                'price' => 85000,
                'ecommerce_url' => 'https://example.com/products/jurnal-refleksi-baru',
                'is_published' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $product->refresh();

        $this->assertSame('Jurnal Refleksi Baru', $product->name);
        $this->assertSame('jurnal-refleksi-baru', $product->slug);
        $this->assertFalse($product->is_published);
    }

    public function test_product_table_can_search_filter_and_delete_products(): void
    {
        $publishedProduct = $this->createProduct([
            'name' => 'Produk Published',
            'slug' => 'produk-published',
            'is_published' => true,
        ]);
        $draftProduct = $this->createProduct([
            'name' => 'Produk Draft',
            'slug' => 'slug-draft-yang-dicari',
            'is_published' => false,
        ]);

        Livewire::test(ListProducts::class)
            ->searchTable('Produk Published')
            ->assertCanSeeTableRecords([$publishedProduct])
            ->assertCanNotSeeTableRecords([$draftProduct]);

        Livewire::test(ListProducts::class)
            ->searchTable('slug-draft-yang-dicari')
            ->assertCanSeeTableRecords([$draftProduct])
            ->assertCanNotSeeTableRecords([$publishedProduct]);

        Livewire::test(ListProducts::class)
            ->filterTable('is_published', true)
            ->assertCanSeeTableRecords([$publishedProduct])
            ->assertCanNotSeeTableRecords([$draftProduct]);

        Livewire::test(ListProducts::class)
            ->filterTable('is_published', false)
            ->assertCanSeeTableRecords([$draftProduct])
            ->assertCanNotSeeTableRecords([$publishedProduct])
            ->callTableAction('delete', $draftProduct)
            ->assertNotified();

        $this->assertModelMissing($draftProduct);
    }

    public function test_media_relation_manager_lists_product_media(): void
    {
        $product = $this->createProduct();
        $media = ProductMedia::query()->create([
            'product_id' => $product->getKey(),
            'media_type' => 'image',
            'media_url' => 'https://example.com/images/product.jpg',
            'order_number' => 1,
        ]);

        Livewire::test(MediaRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->assertCanSeeTableRecords([$media])
            ->assertTableActionExists('edit')
            ->assertTableActionExists('delete');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createProduct(array $attributes = []): Product
    {
        return Product::query()->create(array_merge([
            'name' => 'Produk Marketplace',
            'slug' => 'produk-marketplace',
            'description' => 'Deskripsi produk marketplace.',
            'price' => 100000,
            'ecommerce_url' => 'https://example.com/products/produk-marketplace',
            'is_published' => false,
        ], $attributes));
    }
}

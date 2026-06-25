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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_upload_product_media_when_creating_product(): void
    {
        Storage::fake('public');

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Produk Dengan Media',
                'slug' => 'produk-dengan-media',
                'description' => 'Produk marketplace dengan foto dan video.',
                'price' => 125000,
                'ecommerce_url' => 'https://example.com/products/produk-dengan-media',
                'is_published' => true,
                'media' => [
                    [
                        'media_type' => 'image',
                        'media_url' => [UploadedFile::fake()->image('produk.jpg')],
                    ],
                    [
                        'media_type' => 'video',
                        'media_url' => [UploadedFile::fake()->create('produk.mp4', 1024, 'video/mp4')],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect();

        $product = Product::query()->where('slug', 'produk-dengan-media')->firstOrFail();
        $media = $product->media()->get();

        $this->assertCount(2, $media);
        $this->assertSame(['image', 'video'], $media->pluck('media_type')->all());
        $this->assertSame([1, 2], $media->pluck('order_number')->all());

        $media->each(function (ProductMedia $productMedia): void {
            $this->assertStringStartsWith('product-media/', $productMedia->media_url);
            Storage::disk('public')->assertExists($productMedia->media_url);
        });
    }

    public function test_media_relation_manager_can_upload_update_and_delete_product_media(): void
    {
        Storage::fake('public');

        $product = $this->createProduct();

        Livewire::test(MediaRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->callTableAction('create', data: [
                'media_type' => 'image',
                'media_url' => [UploadedFile::fake()->image('produk.jpg')],
                'order_number' => 1,
            ])
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $media = ProductMedia::query()->whereBelongsTo($product)->firstOrFail();
        $originalPath = $media->media_url;

        $this->assertStringStartsWith('product-media/', $originalPath);
        Storage::disk('public')->assertExists($originalPath);

        Livewire::test(MediaRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->mountTableAction('edit', $media)
            ->setTableActionData([
                'media_type' => 'video',
                'order_number' => 1,
            ])
            ->set('mountedTableActionsData.0.media_url', [UploadedFile::fake()->create('produk.mp4', 1024, 'video/mp4')])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors()
            ->assertNotified();

        $media->refresh();

        Storage::disk('public')->assertMissing($originalPath);
        Storage::disk('public')->assertExists($media->media_url);
        $this->assertSame('video', $media->media_type);

        $updatedPath = $media->media_url;

        Livewire::test(MediaRelationManager::class, [
            'ownerRecord' => $product,
            'pageClass' => EditProduct::class,
        ])
            ->callTableAction('delete', $media)
            ->assertNotified();

        $this->assertModelMissing($media);
        Storage::disk('public')->assertMissing($updatedPath);
    }

    public function test_deleting_product_deletes_product_media_files(): void
    {
        Storage::fake('public');

        $product = $this->createProduct();
        $mediaPaths = [
            'product-media/produk.jpg',
            'product-media/produk.mp4',
        ];

        Storage::disk('public')->put($mediaPaths[0], 'image');
        Storage::disk('public')->put($mediaPaths[1], 'video');

        $media = collect($mediaPaths)->map(
            fn (string $mediaPath, int $index): ProductMedia => ProductMedia::query()->create([
                'product_id' => $product->getKey(),
                'media_type' => $index === 0 ? 'image' : 'video',
                'media_url' => $mediaPath,
                'order_number' => $index + 1,
            ]),
        );

        $product->delete();

        $this->assertModelMissing($product);

        $media->each(fn (ProductMedia $productMedia): mixed => $this->assertModelMissing($productMedia));

        foreach ($mediaPaths as $mediaPath) {
            Storage::disk('public')->assertMissing($mediaPath);
        }
    }

    public function test_media_relation_manager_lists_product_media(): void
    {
        Storage::fake('public');

        $product = $this->createProduct();
        Storage::disk('public')->put('product-media/product.jpg', 'image');

        $media = ProductMedia::query()->create([
            'product_id' => $product->getKey(),
            'media_type' => 'image',
            'media_url' => 'product-media/product.jpg',
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

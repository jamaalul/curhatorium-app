<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ecommerce_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->enum('ecommerce_name', [
                'shopee',
                'tokopedia',
                'tiktok',
                'other'
            ]);
            $table->text('url');
            $table->timestamps();
        });

        $this->backfillFromProductEcommerceUrl();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_links');
    }

    private function backfillFromProductEcommerceUrl(): void
    {
        if (! Schema::hasColumn('products', 'ecommerce_url')) {
            return;
        }

        DB::table('products')
            ->select(['id', 'ecommerce_url'])
            ->whereNotNull('ecommerce_url')
            ->where('ecommerce_url', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                $now = now();

                $rows = $products->map(fn (object $product): array => [
                    'product_id' => $product->id,
                    'ecommerce_name' => $this->resolveEcommerceName((string) $product->ecommerce_url),
                    'url' => $product->ecommerce_url,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows === []) {
                    return;
                }

                DB::table('ecommerce_links')->insert($rows);
            });
    }

    private function resolveEcommerceName(string $url): string
    {
        $normalizedUrl = mb_strtolower($url);
        $host = parse_url($normalizedUrl, PHP_URL_HOST) ?: $normalizedUrl;

        if (str_contains($host, 'shopee')) {
            return 'shopee';
        }

        if (str_contains($host, 'tokopedia')) {
            return 'tokopedia';
        }

        return 'other';
    }
};

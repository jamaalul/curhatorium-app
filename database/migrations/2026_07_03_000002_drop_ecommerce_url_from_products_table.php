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
        $this->backfillMissingEcommerceLinks();

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('ecommerce_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ecommerce_url', 255)
                ->default('')
                ->after('price');
        });

        $this->restoreProductEcommerceUrl();
    }

    private function backfillMissingEcommerceLinks(): void
    {
        if (! Schema::hasTable('ecommerce_links') || ! Schema::hasColumn('products', 'ecommerce_url')) {
            return;
        }

        DB::table('products')
            ->select(['id', 'ecommerce_url'])
            ->whereNotNull('ecommerce_url')
            ->where('ecommerce_url', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $exists = DB::table('ecommerce_links')
                        ->where('product_id', $product->id)
                        ->where('url', $product->ecommerce_url)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('ecommerce_links')->insert([
                        'product_id' => $product->id,
                        'ecommerce_name' => $this->resolveEcommerceName((string) $product->ecommerce_url),
                        'url' => $product->ecommerce_url,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    private function restoreProductEcommerceUrl(): void
    {
        if (! Schema::hasTable('ecommerce_links')) {
            return;
        }

        DB::table('ecommerce_links')
            ->select(['product_id', 'url'])
            ->orderBy('id')
            ->get()
            ->unique('product_id')
            ->each(function (object $link): void {
                DB::table('products')
                    ->where('id', $link->product_id)
                    ->update([
                        'ecommerce_url' => mb_substr((string) $link->url, 0, 255),
                    ]);
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

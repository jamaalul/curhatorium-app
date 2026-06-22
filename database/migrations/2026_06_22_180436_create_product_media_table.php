<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id') ->constrained('products') -> cascadeOnDelete();
            $table->enum('media_type', ['image', 'video']);
            $table->string('media_url', 255);
            $table->unsignedInteger('order_number')->default(1);
            $table->unique(['product_id', 'order_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};

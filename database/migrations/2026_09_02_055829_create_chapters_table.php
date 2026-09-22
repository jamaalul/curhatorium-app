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
        Schema::create('chapters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cbt_module_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->enum('type', ['reading', 'video', 'quiz']);
            $table->longText('text_content')->nullable();
            $table->string('video_url', 2048)->nullable();
            $table->unsignedInteger('order_number');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['cbt_module_id', 'order_number'], 'uq_chapter_order_per_module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};

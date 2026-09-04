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
        Schema::create('quiz_question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained()->restrictOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('order_number');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['quiz_question_id', 'order_number'], 'uq_option_order_per_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_question_options');
    }
};

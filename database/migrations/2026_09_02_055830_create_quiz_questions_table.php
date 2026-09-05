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
        Schema::create('quiz_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->restrictOnDelete();
            $table->longText('question');
            $table->enum('type', ['multiple_choice', 'short_answer']);
            $table->text('accepted_answer')->nullable();
            $table->decimal('points', 8, 2)->default(1);
            $table->unsignedInteger('order_number');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['chapter_id', 'order_number'], 'uq_question_order_per_chapter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

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
        Schema::create('user_module_progresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cbt_module_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['in_progress', 'completed']);
            $table->timestamp('started_at');
            $table->timestamp('last_accessed_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cbt_module_id'], 'uq_user_module_progress');
            $table->index(['user_id', 'last_accessed_at'], 'idx_user_module_last_accessed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_module_progresses');
    }
};

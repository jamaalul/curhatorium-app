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
        Schema::create('user_cbt_modules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cbt_module_id')->constrained()->restrictOnDelete();
            $table->foreignId('source_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('granted_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cbt_module_id'], 'uq_user_cbt_module');
            $table->index('source_order_id', 'idx_user_cbt_modules_source_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cbt_modules');
    }
};

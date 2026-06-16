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
        Schema::create('user_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_subscription_id')->constrained();
            $table->enum('benefit', ['snt_rgr_chat', 'snt_psy_chat', 'snt_psy_vc', 'sgd', 'ai_window_token']);
            $table->integer('amount_total')->default(0);
            $table->integer('amount_used')->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->timestamp('last_reset_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_entitlements');
    }
};

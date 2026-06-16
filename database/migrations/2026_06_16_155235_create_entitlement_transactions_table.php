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
        Schema::create('entitlement_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_entitlement_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('benefit', ['snt_rgr_chat', 'snt_psy_chat', 'snt_psy_vc', 'sgd', 'ai_window_token']);
            $table->integer('amount_delta');  // negative = consume, positive = credit
            $table->integer('amount_after');
            $table->enum('source_type', [
                'feature_use',   // user consumed a ticket
                'cycle_reset',   // monthly quota refreshed
                'purchase',      // subscription purchase
                'other',         // others
            ]);
            $table->timestamp('created_at')->useCurrent();  // no updated_at — append-only

            $table->index('user_entitlement_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entitlement_transactions');
    }
};

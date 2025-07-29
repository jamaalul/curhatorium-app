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
        Schema::create('sgd_ticket_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sgd_group_id')->constrained('sgd_groups')->onDelete('cascade');
            $table->enum('ticket_source', ['calm_starter', 'paid']);
            $table->timestamp('consumed_at');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['user_id', 'consumed_at']);
            $table->index(['sgd_group_id', 'ticket_source']);
            $table->index('ticket_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgd_ticket_consumptions');
    }
};

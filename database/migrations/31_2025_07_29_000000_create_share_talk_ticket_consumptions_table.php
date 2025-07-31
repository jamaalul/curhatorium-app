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
        Schema::create('share_talk_ticket_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chat_session_id')->constrained('chat_sessions')->onDelete('cascade');
            $table->enum('ticket_source', ['calm_starter', 'paid']);
            $table->timestamp('consumed_at');
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['user_id', 'consumed_at']);
            $table->index(['chat_session_id', 'ticket_source']);
            $table->index('ticket_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_talk_ticket_consumptions');
    }
}; 
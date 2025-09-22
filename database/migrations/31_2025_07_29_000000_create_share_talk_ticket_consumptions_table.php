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

            // Indexes with shorter names to avoid MySQL limit
            $table->index(['user_id', 'consumed_at'], 'sttc_user_consumed_idx');
            $table->index(['chat_session_id', 'ticket_source'], 'sttc_session_source_idx');
            $table->index('ticket_source', 'sttc_source_idx');
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
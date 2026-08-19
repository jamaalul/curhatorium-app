<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('chatbot_chat_message_v2_s');
        Schema::dropIfExists('chatbot_chat_v2_s');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_sessions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tables are permanently dropped as part of chatbot removal.
    }
};

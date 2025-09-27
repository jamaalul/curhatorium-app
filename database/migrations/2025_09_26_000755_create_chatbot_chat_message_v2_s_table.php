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
        Schema::create('chatbot_chat_message_v2_s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chatbot_chat_v2_s')->onDelete('cascade');
            $table->text('message');
            $table->enum('role', ['user', 'assistant']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_chat_message_v2_s');
    }
};

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
        Schema::dropIfExists('message_v2s');
        Schema::dropIfExists('new_messages');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chat_sessions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversing, these are permanently dropped
    }
};

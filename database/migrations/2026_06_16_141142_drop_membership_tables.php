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
        Schema::dropIfExists('share_talk_ticket_consumptions');
        Schema::dropIfExists('sgd_ticket_consumptions');
        Schema::dropIfExists('membership_tickets');
        Schema::dropIfExists('user_tickets');
        Schema::dropIfExists('user_memberships');
        Schema::dropIfExists('memberships');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration since this is an amputation
    }
};

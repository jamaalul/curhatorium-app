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
        Schema::table('consultations', function (Blueprint $table) {
            $table->foreignId('professional_schedule_slot_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('professional_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('status')->default('waiting'); // waiting, active, completed, cancelled, pending
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->dateTime('pending_end')->nullable();
            $table->string('jitsi_room')->nullable();
            $table->string('no_wa')->nullable()->change();
            $table->string('consultation_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['professional_id']);
            $table->dropColumn(['user_id', 'professional_id', 'status', 'start', 'end', 'pending_end', 'jitsi_room']);
            $table->foreignId('professional_schedule_slot_id')->nullable(false)->change();
            $table->string('no_wa')->nullable(false)->change();
            $table->string('consultation_type')->nullable(false)->change();
        });
    }
};

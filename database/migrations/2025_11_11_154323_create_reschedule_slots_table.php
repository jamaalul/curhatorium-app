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
        Schema::create('reschedule_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reschedule_id')->constrained('reschedules')->onDelete('cascade');
            $table->foreignId('professional_schedule_slot_id')->constrained('professional_schedule_slots')->onDelete('cascade');
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reschedule_slots');
    }
};

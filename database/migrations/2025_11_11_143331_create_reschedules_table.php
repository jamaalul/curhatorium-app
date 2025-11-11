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
        Schema::create('reschedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('original_slot_id')->constrained('professional_schedule_slots')->onDelete('cascade');
            $table->string('status')->default('pending'); // e.g., pending, offered, accepted, declined, expired
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('client_response_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reschedules');
    }
};

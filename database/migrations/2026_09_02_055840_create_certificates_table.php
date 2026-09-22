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
        Schema::create('certificates', function (Blueprint $table): void {
            $table->id();
            $table->string('certificate_number')->unique('uq_certificates_number');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cbt_module_id')->constrained()->restrictOnDelete();
            $table->timestamp('issued_at');
            $table->string('pdf_path', 2048)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cbt_module_id'], 'uq_user_module_certificate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('mood')->comment('Mood rating from 1-10');
            $table->string('activity')->comment('Main activity that influenced mood');
            $table->text('explanation')->nullable()->comment('Detailed explanation of the activity');
            $table->integer('energy')->comment('Energy level from 1-10');
            $table->integer('productivity')->comment('Productivity level from 1-10');
            $table->string('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};

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
        Schema::create('sgd_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('topic');
            $table->string('meeting_address');
            $table->boolean('is_done')->default(false);
            $table->string('category');
            $table->dateTime('schedule');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sgd_groups');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mental_health_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('total_score');
            $table->integer('emotional_score');
            $table->integer('social_score');
            $table->integer('psychological_score');
            $table->string('category');
            $table->json('answers');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mental_health_test_results');
    }
}; 
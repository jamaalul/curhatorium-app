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
        Schema::table('sgd_group_user', function (Blueprint $table) {
            // Add foreign key columns if they don't exist
            if (!Schema::hasColumn('sgd_group_user', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('sgd_group_user', 'sgd_group_id')) {
                $table->foreignId('sgd_group_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('sgd_group_user', 'joined_at')) {
                $table->timestamp('joined_at')->useCurrent();
            }
            
            // Add unique constraint if it doesn't exist
            if (!Schema::hasIndex('sgd_group_user', 'sgd_group_user_user_id_sgd_group_id_unique')) {
                $table->unique(['user_id', 'sgd_group_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sgd_group_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['sgd_group_id']);
            $table->dropUnique(['user_id', 'sgd_group_id']);
            $table->dropColumn(['user_id', 'sgd_group_id', 'joined_at']);
        });
    }
}; 
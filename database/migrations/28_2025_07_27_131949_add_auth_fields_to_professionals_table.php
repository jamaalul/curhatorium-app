<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('password')->nullable()->after('whatsapp_number');
            $table->rememberToken()->after('password');
        });
        
        // Update existing records with default password
        DB::table('professionals')->whereNotNull('whatsapp_number')->update([
            'password' => Hash::make('password123'), // Default password, should be changed
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token']);
        });
    }
};

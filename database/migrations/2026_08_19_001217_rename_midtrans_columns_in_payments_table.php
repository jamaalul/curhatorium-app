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
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('midtrans_transaction_id', 'payment_transaction_id');
            $table->renameColumn('midtrans_response', 'payment_gateway_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('payment_transaction_id', 'midtrans_transaction_id');
            $table->renameColumn('payment_gateway_response', 'midtrans_response');
        });
    }
};

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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('midtrans_transaction_id', 50)->nullable()->index();
            $table->decimal('gross_amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_type', 45)->nullable();
            $table->enum('transaction_status', [
                'pending',
                'settlement',
                'deny',
                'cancel',
                'expire',
            ])->default('pending');
            $table->text('qris_url')->nullable();
            $table->json('midtrans_response');
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref', 50)->unique(); // e.g. "ORD-20240616-00123", sent to Midtrans
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('orderable');               // orderable_type + orderable_id
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);      // price snapshot at time of order
            $table->decimal('gross_amount', 15, 2);    // quantity * unit_price
            $table->enum('status', [
                'pending',
                'paid',
                'expired',
                'cancelled',
                'refunded',
            ])->default('pending');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

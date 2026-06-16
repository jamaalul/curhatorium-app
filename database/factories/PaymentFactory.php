<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'midtrans_transaction_id' => fake()->uuid(),
            'gross_amount' => fake()->randomElement([49900, 79900, 149900]),
            'currency' => 'IDR',
            'payment_type' => 'gopay',
            'transaction_status' => 'pending',
            'qris_url' => 'https://api.sandbox.veritrans.co.id/v2/gopay/'.fake()->uuid().'/qr-code',
            'midtrans_response' => ['status_code' => '201', 'status_message' => 'GO-PAY transaction is created'],
            'transaction_time' => now(),
            'expired_at' => now()->addMinutes(15),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'transaction_status' => 'pending',
        ]);
    }

    public function settled(): static
    {
        return $this->state(fn () => [
            'transaction_status' => 'settlement',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'transaction_status' => 'expire',
            'expired_at' => now()->subMinutes(1),
        ]);
    }
}

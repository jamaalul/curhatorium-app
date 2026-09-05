<?php

namespace App\Services;

use App\Models\CbtModule;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EClassPurchaseService
{
    public function __construct(private DokuService $doku) {}

    public function create(User $user, CbtModule $module): Order
    {
        abort_unless($module->is_published, 404);

        return DB::transaction(function () use ($user, $module): Order {
            $lockedModule = CbtModule::query()->whereKey($module->getKey())->lockForUpdate()->firstOrFail();

            abort_if($lockedModule->isOwnedBy($user), 409, 'Module E-Class sudah dimiliki.');

            $orderQuery = Order::query()
                ->whereBelongsTo($user)
                ->where('orderable_type', CbtModule::class)
                ->where('orderable_id', $lockedModule->getKey());

            abort_if((clone $orderQuery)->where('status', 'paid')->exists(), 409, 'Module E-Class sudah pernah dibayar.');

            $pendingOrder = (clone $orderQuery)
                ->where('status', 'pending')
                ->where('expired_at', '>', now())
                ->latest()
                ->first();

            if ($pendingOrder) {
                return $pendingOrder;
            }

            $order = Order::query()->create([
                'order_ref' => Order::generateOrderRef(),
                'user_id' => $user->getKey(),
                'orderable_type' => CbtModule::class,
                'orderable_id' => $lockedModule->getKey(),
                'quantity' => 1,
                'unit_price' => $lockedModule->price,
                'gross_amount' => $lockedModule->price,
                'status' => 'pending',
                'expired_at' => now()->addMinutes(15),
            ]);

            $charge = $this->doku->chargeQris($order);

            $order->payments()->create([
                'payment_transaction_id' => $charge['transaction_id'],
                'gross_amount' => $charge['gross_amount'],
                'currency' => 'IDR',
                'payment_type' => $charge['payment_type'],
                'transaction_status' => $charge['transaction_status'],
                'qris_url' => $charge['qr_code_url'],
                'payment_gateway_response' => $charge['raw'],
                'transaction_time' => now(),
                'expired_at' => now()->addMinutes(15),
            ]);

            return $order;
        });
    }
}

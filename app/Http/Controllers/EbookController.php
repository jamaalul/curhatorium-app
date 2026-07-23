<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EbookController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    public function index(): View
    {
        $ebooks = Ebook::query()
            ->published()
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('ebooks.index', compact('ebooks'));
    }

    public function show(Ebook $ebook): View
    {
        abort_unless($ebook->is_published, 404);

        $ebook->load([
            'category',
            'comments' => fn ($query) => $query
                ->where('is_visible', true)
                ->with('user:id,name')
                ->latest(),
        ]);

        return view('ebooks.show', compact('ebook'));
    }

    public function checkout(Ebook $ebook): RedirectResponse
    {
        abort_unless($ebook->is_published, 404);

        $user = Auth::user();

        $existingOrder = Order::query()
            ->where('user_id', $user->id)
            ->where('orderable_type', Ebook::class)
            ->where('orderable_id', $ebook->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        if ($existingOrder) {
            return redirect()->route('order.show', $existingOrder);
        }

        $order = DB::transaction(function () use ($user, $ebook) {
            $order = Order::create([
                'order_ref' => Order::generateOrderRef(),
                'user_id' => $user->id,
                'orderable_type' => Ebook::class,
                'orderable_id' => $ebook->id,
                'quantity' => 1,
                'unit_price' => $ebook->price,
                'gross_amount' => $ebook->price,
                'status' => 'pending',
                'expired_at' => now()->addMinutes(15),
            ]);

            $chargeResult = $this->midtrans->chargeQris($order);

            $order->payments()->create([
                'midtrans_transaction_id' => $chargeResult['transaction_id'],
                'gross_amount' => $chargeResult['gross_amount'],
                'currency' => 'IDR',
                'payment_type' => $chargeResult['payment_type'],
                'transaction_status' => $chargeResult['transaction_status'],
                'qris_url' => $chargeResult['qr_code_url'],
                'midtrans_response' => $chargeResult['raw'],
                'transaction_time' => now(),
                'expired_at' => now()->addMinutes(15),
            ]);

            return $order;
        });

        return redirect()->route('order.show', $order);
    }
}

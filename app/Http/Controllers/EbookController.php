<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use \Illuminate\Http\Request;

class EbookController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    public function index(Request $request): View
    {
        $categories = \App\Models\EbookCategory::orderBy('name')->get();

        $ebooks = Ebook::query()
            ->published()
            ->when($request->category, function ($query, $categorySlug) {
                $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
            })
            ->with('category')
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('ebooks.index', compact('ebooks', 'categories'));
    }

    public function show(Ebook $ebook): View
    {
        abort_unless($ebook->is_published, 404);

        $ebook->load(['category']);

        $comments = $ebook->comments()
            ->where('is_visible', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(5);

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $hasPurchased = Order::where('user_id', Auth::id())
                ->where('orderable_type', Ebook::class)
                ->where('orderable_id', $ebook->id)
                ->where('status', 'paid')
                ->exists();
            
            $hasReviewed = $ebook->comments()->where('user_id', Auth::id())->exists();
        }

        $relatedEbooks = Ebook::query()
            ->published()
            ->where('id', '!=', $ebook->id)
            ->when($ebook->ebook_category_id, function ($q) use ($ebook) {
                $q->where('ebook_category_id', $ebook->ebook_category_id);
            })
            ->latest()
            ->limit(4)
            ->get();

        return view('ebooks.show', compact('ebook', 'comments', 'hasPurchased', 'hasReviewed', 'relatedEbooks'));
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

    public function review(Request $request, Ebook $ebook): RedirectResponse
    {
        abort_unless($ebook->is_published, 404);

        $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:500'],
        ]);

        $user = Auth::user();

        $hasPurchased = Order::where('user_id', $user->id)
            ->where('orderable_type', Ebook::class)
            ->where('orderable_id', $ebook->id)
            ->where('status', 'paid')
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Anda harus membeli ebook ini terlebih dahulu untuk memberikan ulasan.');
        }

        $hasReviewed = $ebook->comments()->where('user_id', $user->id)->exists();

        if ($hasReviewed) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk ebook ini.');
        }

        $ebook->comments()->create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'is_visible' => true,
        ]);

        return back()->with('success', 'Ulasan Anda berhasil ditambahkan.');
    }
}


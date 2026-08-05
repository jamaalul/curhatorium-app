<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Models\EbookCategory;
use App\Models\EbookReadingProgress;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EbookController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
    ) {}

    public function index(Request $request): View
    {
        $categories = EbookCategory::orderBy('name', 'asc')->get();

        $ebooks = Ebook::query()
            ->published()
            ->with('category')
            ->latest()
            ->get();

        return view('ebooks.index', compact('ebooks', 'categories'));
    }

    public function library(Request $request): View
    {
        $categories = EbookCategory::query()->orderBy('name', 'asc')->get();

        $query = Order::query()
            ->where('user_id', Auth::id())
            ->where('orderable_type', Ebook::class)
            ->where('status', 'paid')
            ->with(['orderable.category', 'orderable.progress' => fn ($q) => $q->where('user_id', Auth::id())]);

        if ($request->filled('category')) {
            $query->whereHasMorph('orderable', [Ebook::class], function ($q) use ($request) {
                $q->whereHas('category', function ($q2) use ($request) {
                    $q2->where('slug', $request->category);
                });
            });
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'oldest') {
                $query->oldest();
            } elseif ($request->sort === 'title_asc') {
                $query->join('ebooks', 'orders.orderable_id', '=', 'ebooks.id')
                    ->orderBy('ebooks.title', 'asc')
                    ->select('orders.*');
            } elseif ($request->sort === 'title_desc') {
                $query->join('ebooks', 'orders.orderable_id', '=', 'ebooks.id')
                    ->orderBy('ebooks.title', 'desc')
                    ->select('orders.*');
            } else {
                $query->latest('orders.created_at');
            }
        } else {
            $query->latest('orders.created_at');
        }

        $orders = $query->paginate(12)->withQueryString();

        return view('ebooks.library', compact('orders', 'categories'));
    }

    public function show(Ebook $ebook): View
    {
        abort_unless($ebook->is_published, 404);

        $ebook->load(['category']);

        $comments = $ebook->comments()
            ->where('is_visible', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(5)
            ->onEachSide(1);

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $hasPurchased = Order::query()
                ->where('user_id', '=', Auth::id())
                ->where('orderable_type', '=', Ebook::class)
                ->where('orderable_id', '=', $ebook->id)
                ->where('status', '=', 'paid')
                ->exists();

            $hasReviewed = $ebook->comments()->where('user_id', '=', Auth::id())->exists();
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

        $hasPurchased = Order::query()
            ->where('user_id', '=', $user->id)
            ->where('orderable_type', '=', Ebook::class)
            ->where('orderable_id', '=', $ebook->id)
            ->where('status', '=', 'paid')
            ->exists();

        if (! $hasPurchased) {
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

    public function read(Ebook $ebook): View
    {
        abort_unless($ebook->isOwnedBy(Auth::user()), 403, 'You do not own this ebook.');

        $pdfUrl = URL::temporarySignedRoute(
            'ebooks.stream',
            now()->addMinutes(15),
            ['ebook' => $ebook]
        );

        $progress = EbookReadingProgress::query()
            ->where('user_id', '=', Auth::id())
            ->where('ebook_id', '=', $ebook->id)
            ->first();

        $startPage = $progress ? $progress->last_page : 1;

        return view('ebooks.read', compact('ebook', 'pdfUrl', 'startPage'));
    }

    public function stream(Ebook $ebook)
    {
        abort_unless($ebook->isOwnedBy(Auth::user()), 403, 'You do not own this ebook.');

        if (Str::startsWith($ebook->file_url, ['http://', 'https://'])) {
            try {
                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get($ebook->file_url);

                if (! $response->successful()) {
                    abort(404, 'Gagal mengambil PDF dari server luar (HTTP '.$response->status().').');
                }

                return response($response->body(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline',
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                ]);
            } catch (\Exception $e) {
                abort(500, 'Gagal terhubung ke server PDF luar: '.$e->getMessage());
            }
        }

        $path = Storage::disk('private')->path($ebook->file_url);

        if (! file_exists($path)) {
            $path = Storage::disk('public')->path($ebook->file_url);
        }

        if (! file_exists($path)) {
            if (file_exists(public_path($ebook->file_url))) {
                $path = public_path($ebook->file_url);
            } elseif (file_exists(base_path($ebook->file_url))) {
                $path = base_path($ebook->file_url);
            }
        }

        if (! file_exists($path)) {
            abort(404, 'File PDF lokal tidak ditemukan di server (Path: '.$ebook->file_url.').');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    public function refreshUrl(Ebook $ebook)
    {
        abort_unless($ebook->isOwnedBy(Auth::user()), 403, 'You do not own this ebook.');

        return response()->json([
            'url' => URL::temporarySignedRoute(
                'ebooks.stream',
                now()->addMinutes(15),
                ['ebook' => $ebook]
            ),
        ]);
    }

    public function updateProgress(Request $request, Ebook $ebook)
    {
        abort_unless($ebook->isOwnedBy(Auth::user()), 403, 'You do not own this ebook.');

        $request->validate([
            'page' => ['required', 'integer', 'min:1'],
        ]);

        EbookReadingProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'ebook_id' => $ebook->id],
            ['last_page' => $request->page]
        );

        return response()->json(['success' => true]);
    }
}

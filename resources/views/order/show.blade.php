<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Checkout — {{ $order->order_ref }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex justify-center items-center bg-zinc-100 w-screen min-h-screen p-4">
    <div class="bg-white shadow-sm p-8 border rounded-xl w-full max-w-md">

        {{-- Order Header --}}
        <h1 class="mb-1 font-semibold text-2xl text-center">Checkout</h1>
        <p class="mb-6 text-center text-sm text-zinc-500">{{ $order->order_ref }}</p>

        {{-- Order Summary --}}
        <div class="mb-6 p-4 bg-zinc-50 rounded-lg border">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-zinc-600">Item</span>
                <span class="font-medium text-sm">{{ $order->orderable->name ?? 'Order Item' }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-zinc-600">Quantity</span>
                <span class="font-medium text-sm">{{ $order->quantity }}</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t">
                <span class="font-semibold">Total</span>
                <span class="font-bold text-lg">Rp {{ number_format($order->gross_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment Status --}}
        <div id="payment-area">
            @if ($order->isPaid())
                {{-- Paid State --}}
                <div class="text-center p-6">
                    <div class="mb-3 text-5xl">✅</div>
                    <h2 class="font-semibold text-xl text-green-600 mb-1">Payment Successful</h2>
                    <p class="text-sm text-zinc-500">Your payment has been confirmed.</p>
                    <a href="{{ route('membership.index') }}"
                        class="inline-block mt-4 bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition-colors">
                        Back to Plans
                    </a>
                </div>
            @elseif ($order->isExpired() || ($latestPayment && in_array($latestPayment->transaction_status, ['expire', 'cancel', 'deny'])))
                {{-- Expired / Cancelled State --}}
                <div class="text-center p-6">
                    <div class="mb-3 text-5xl">⏰</div>
                    <h2 class="font-semibold text-xl text-red-600 mb-1">Payment Expired</h2>
                    <p class="text-sm text-zinc-500">This payment session has expired. Please create a new order.</p>
                    <a href="{{ route('membership.index') }}"
                        class="inline-block mt-4 bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition-colors">
                        Back to Plans
                    </a>
                </div>
            @elseif ($latestPayment && $latestPayment->qris_url)
                {{-- QR Code / Pending State --}}
                <div class="text-center">
                    <p class="mb-3 font-medium text-zinc-700">Scan QR code to pay</p>
                    <div class="flex justify-center mb-4">
                        <img id="qr-image" src="{{ $latestPayment->qris_url }}" alt="QRIS QR Code"
                            class="w-56 h-56 rounded-lg border">
                    </div>

                    {{-- Timer --}}
                    <div class="mb-4">
                        <p class="text-sm text-zinc-500">Expires in</p>
                        <p id="countdown-timer" class="font-mono font-bold text-xl text-orange-500"
                            data-expires-at="{{ $order->expired_at->toIso8601String() }}">
                            --:--
                        </p>
                    </div>

                    {{-- Status indicator --}}
                    <div id="status-indicator" class="flex items-center justify-center gap-2 text-sm text-zinc-500">
                        <span class="inline-block w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                        Waiting for payment...
                    </div>
                </div>
            @else
                {{-- Fallback --}}
                <div class="text-center p-6">
                    <p class="text-zinc-500">Payment information unavailable.</p>
                    <a href="{{ route('membership.index') }}"
                        class="inline-block mt-4 bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition-colors">
                        Back to Plans
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if ($order->isPending() && $latestPayment && $latestPayment->isPending())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusUrl = "{{ route('order.check-status', $order) }}";
                const timerEl = document.getElementById('countdown-timer');
                const expiresAt = new Date(timerEl.dataset.expiresAt);

                // Countdown timer
                const timerInterval = setInterval(function() {
                    const now = new Date();
                    const diff = expiresAt - now;

                    if (diff <= 0) {
                        timerEl.textContent = '00:00';
                        clearInterval(timerInterval);
                        clearInterval(pollInterval);
                        location.reload();
                        return;
                    }

                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000);
                    timerEl.textContent =
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');
                }, 1000);

                // Poll status every 5 seconds
                const pollInterval = setInterval(function() {
                    fetch(statusUrl)
                        .then(r => r.json())
                        .then(data => {
                            if (data.order_status === 'paid' || data.payment_status === 'settlement') {
                                clearInterval(timerInterval);
                                clearInterval(pollInterval);
                                location.reload();
                            } else if (['expired', 'cancelled'].includes(data.order_status) ||
                                ['expire', 'cancel', 'deny'].includes(data.payment_status)) {
                                clearInterval(timerInterval);
                                clearInterval(pollInterval);
                                location.reload();
                            }
                        })
                        .catch(() => {});
                }, 5000);
            });
        </script>
    @endif
</body>

</html>

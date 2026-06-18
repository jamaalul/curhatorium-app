@extends('layouts.app')

@section('title', 'Checkout — ' . $order->order_ref)

@section('bodyClass', 'flex justify-center items-center bg-zinc-100 p-4 w-screen min-h-screen')

@section('content')
    <div class="bg-white shadow-sm p-8 border rounded-xl w-full max-w-md">

        {{-- Order Header --}}
        <h1 class="mb-1 font-semibold text-2xl text-center">Checkout</h1>
        <p class="mb-6 text-zinc-500 text-sm text-center">{{ $order->order_ref }}</p>

        {{-- Order Summary --}}
        <div class="bg-zinc-50 mb-6 p-4 border rounded-lg">
            <div class="flex justify-between items-center mb-2">
                <span class="text-zinc-600 text-sm">Item</span>
                <span class="font-medium text-sm">{{ $order->orderable->name ?? 'Order Item' }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-zinc-600 text-sm">Quantity</span>
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
                <div class="p-6 text-center">
                    <div class="mb-3 text-5xl">✅</div>
                    <h2 class="mb-1 font-semibold text-green-600 text-xl">Payment Successful</h2>
                    <p class="text-zinc-500 text-sm">Your payment has been confirmed.</p>
                    <a href="{{ route('membership.index') }}"
                        class="inline-block bg-blue-500 hover:bg-blue-600 mt-4 px-6 py-2 rounded-md text-white transition-colors">
                        Back to Plans
                    </a>
                </div>
            @elseif ($order->isExpired() || ($latestPayment && in_array($latestPayment->transaction_status, ['expire', 'cancel', 'deny'])))
                {{-- Expired / Cancelled State --}}
                <div class="p-6 text-center">
                    <div class="mb-3 text-5xl">⏰</div>
                    <h2 class="mb-1 font-semibold text-red-600 text-xl">Payment Expired</h2>
                    <p class="text-zinc-500 text-sm">This payment session has expired. Please create a new order.</p>
                    <a href="{{ route('membership.index') }}"
                        class="inline-block bg-blue-500 hover:bg-blue-600 mt-4 px-6 py-2 rounded-md text-white transition-colors">
                        Back to Plans
                    </a>
                </div>
            @elseif ($latestPayment && $latestPayment->qris_url)
                {{-- QR Code / Pending State --}}
                <div class="text-center">
                    <p class="mb-3 font-medium text-zinc-700">Scan QR code to pay</p>
                    <div class="flex justify-center mb-4">
                        <img id="qr-image" src="{{ $latestPayment->qris_url }}" alt="QRIS QR Code"
                            class="border rounded-lg w-56 h-56">
                    </div>

                    {{-- Timer --}}
                    <div class="mb-4">
                        <p class="text-zinc-500 text-sm">Expires in</p>
                        <p id="countdown-timer" class="font-mono font-bold text-orange-500 text-xl"
                            data-expires-at="{{ $order->expired_at->toIso8601String() }}">
                            --:--
                        </p>
                    </div>

                    {{-- Status indicator --}}
                    <div id="status-indicator" class="flex justify-center items-center gap-2 text-zinc-500 text-sm">
                        <span class="inline-block bg-yellow-400 rounded-full w-2 h-2 animate-pulse"></span>
                        Waiting for payment...
                    </div>
                </div>
            @else
                {{-- Fallback --}}
                <div class="p-6 text-center">
                    <p class="text-zinc-500">Payment information unavailable.</p>
                    <a href="{{ route('membership.index') }}"
                        class="inline-block bg-blue-500 hover:bg-blue-600 mt-4 px-6 py-2 rounded-md text-white transition-colors">
                        Back to Plans
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@if ($order->isPending() && $latestPayment && $latestPayment->isPending())
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const statusUrl = "{{ route('order.check-status', $order) }}";
                const timerEl = document.getElementById('countdown-timer');
                const expiresAt = new Date(timerEl.dataset.expiresAt);

                // Countdown timer
                const timerInterval = setInterval(function () {
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
                const pollInterval = setInterval(function () {
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
                        .catch(() => { });
                }, 2000);
            });
        </script>
    @endsection
@endif
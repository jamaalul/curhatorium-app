@extends('layouts.dashboard')

@section('title', 'Checkout — ' . $order->order_ref)

@section('bodyClass', 'pt-16 w-full overflow-x-hidden')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@endsection

@section('dashboard-content')
<div class="w-full bg-gray-200 py-11 px-4 sm:px-8 flex justify-center items-center min-h-[calc(100vh-64px)]">
    @if ($order->isPaid())
        {{-- Paid State (1-Column Layout) --}}
        <div class="max-w-[480px] p-6 bg-base-50 rounded-2xl flex flex-col justify-start items-start gap-5">
            <div class="self-stretch flex flex-col justify-start items-center gap-1">
                <div class="self-stretch text-center justify-start text-zinc-900 text-4xl font-semibold font-bricolage leading-[48px]">Detail transaksi</div>
                <div class="self-stretch text-center justify-start text-text-secondary text-base font-normal font-dm leading-7">{{ $order->order_ref }}</div>
            </div>
            
            <div class="self-stretch flex flex-col justify-start items-start gap-2">
                <div class="self-stretch inline-flex justify-between items-center">
                    <div class="justify-start text-text-secondary text-xl font-medium font-dm leading-9">Item</div>
                    <div class="justify-start text-base-900 text-xl font-medium font-dm leading-9">{{ $order->orderable->name ?? 'Order Item' }}</div>
                </div>
                <div class="self-stretch inline-flex justify-between items-center">
                    <div class="justify-start text-text-secondary text-xl font-medium font-dm leading-9">Harga</div>
                    <div class="text-right justify-start text-base-900 text-xl font-medium font-dm leading-9">Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                </div>
                <div class="self-stretch inline-flex justify-between items-center">
                    <div class="justify-start text-text-secondary text-xl font-medium font-dm leading-9">Metode</div>
                    <div class="text-right justify-start text-base-900 text-xl font-medium font-dm leading-9">{{ strtoupper($latestPayment->payment_type ?? 'QRIS') }}</div>
                </div>
                <div class="self-stretch pt-3 border-t border-base-200 inline-flex justify-between items-center">
                    <div class="justify-start text-base-900 text-xl font-medium font-dm leading-9">Total</div>
                    <div class="text-right justify-start text-base-900 text-2xl font-medium font-bricolage leading-7">Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                </div>
            </div>
            
            <div class="self-stretch flex flex-col justify-start items-center gap-2 mt-4">
                <div class="size-16 bg-teal-500 rounded-full flex justify-center items-center text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <div class="justify-start text-teal-500 text-3xl font-semibold font-bricolage leading-9 text-center">Pembayaran berhasil</div>
                <div class="self-stretch text-center justify-start text-zinc-500 text-base font-medium font-dm leading-7">Pembayaranmu sudah kami terima. Selamat menikmati paket barumu!</div>
            </div>
            
            <a href="{{ route('membership.index') }}" class="self-stretch mt-4 px-2 py-4 bg-primary-500 hover:bg-primary-600 transition-colors rounded-xl flex flex-col justify-center items-center gap-4">
                <div class="text-center justify-start text-white text-lg font-medium font-dm leading-4">Kembali</div>
            </a>
        </div>
    @else
        {{-- Existing 2-Column Layout for Pending/Expired --}}
        <div class="p-6 sm:p-8 bg-base-50 rounded-2xl w-full max-w-[850px] mx-auto">
            <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
            
            {{-- Left Column: Details --}}
            <div class="w-full flex flex-col justify-start items-start gap-4">
                <div class="flex flex-col justify-start items-start gap-1 w-full">
                    <div class="text-base-900 text-3xl font-semibold font-bricolage leading-9">Detail transaksi</div>
                    <div class="text-text-tertiary text-xs font-normal font-dm leading-4">{{ $order->order_ref }}</div>
                </div>
                
                <div class="w-full flex justify-start items-start gap-3">
                    <div class="flex-1 flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-start">
                            <div class="text-text-secondary text-base font-normal font-dm leading-7">Item</div>
                            <div class="text-right text-base-900 text-base font-normal font-dm leading-7">{{ $order->orderable->name ?? 'Order Item' }}</div>
                        </div>
                        <div class="w-full flex justify-between items-start">
                            <div class="text-text-secondary text-base font-normal font-dm leading-7">Harga</div>
                            <div class="text-right text-base-900 text-base font-normal font-dm leading-7">Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                        </div>
                        <div class="w-full flex justify-between items-start">
                            <div class="text-text-secondary text-base font-normal font-dm leading-7">Metode</div>
                            <div class="text-right text-base-900 text-base font-normal font-dm leading-7">{{ strtoupper($latestPayment->payment_type ?? 'QRIS') }}</div>
                        </div>
                    </div>
                </div>
                
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="1" viewBox="0 0 368 1" fill="none" class="self-stretch my-2" preserveAspectRatio="none">
                  <path d="M0.5 0.5L367.5 0.5" stroke="#E4E4E7" stroke-linecap="round"/>
                </svg>
                
                <div class="w-full flex justify-between items-center">
                    <div class="text-base-900 text-base font-medium font-dm leading-7">Total</div>
                    <div class="text-right text-base-900 text-2xl font-medium font-bricolage leading-7">Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                </div>
            </div>

            {{-- Right Column: Dynamic Status --}}
            <div class="w-full flex flex-col justify-start items-start gap-4">
                @if ($order->isExpired() || ($latestPayment && in_array($latestPayment->transaction_status, ['expire', 'cancel', 'deny'])))
                    {{-- Expired State --}}
                    <div class="w-full h-full flex flex-col justify-center items-center gap-4 text-center py-10">
                        <div class="self-stretch flex flex-col justify-start items-center gap-4">
                            <div class="size-12 bg-orange-100 rounded-full flex justify-center items-center text-orange-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            
                            <div class="flex flex-col items-center gap-2 text-center">
                                <h3 class="text-stone-900 text-xl font-semibold font-bricolage leading-6">Pembayaran kadaluarsa</h3>
                                <p class="text-text-secondary text-sm font-medium font-dm leading-5 max-w-[320px]">Sayang sekali, batas waktu pembayaran sudah berakhir. Kamu bisa memilih paket lagi untuk melanjutkan.</p>
                            </div>
                        </div>
                        <div class="w-full mt-4 flex flex-col justify-start items-start gap-3">
                            <a href="{{ route('membership.index') }}" class="w-full px-2 py-4 bg-primary-500 hover:bg-primary-600 rounded-xl flex flex-col justify-center items-center transition-colors">
                                <span class="text-center text-white text-base font-medium font-dm leading-4">Pilih Paket Baru</span>
                            </a>
                        </div>
                    </div>
                @elseif ($latestPayment && $latestPayment->qris_url)
                    {{-- QRIS Pending State --}}
                    <div class="w-full flex flex-col justify-start items-center gap-4">
                        <div class="text-center text-base-900 text-base font-medium font-dm leading-7">Scan QR untuk bayar</div>
                        <div class="w-64 flex flex-col justify-start items-center gap-4">
                            <img src="{{ asset('assets/qris-logo 1.svg') }}" alt="QRIS Logo" class="w-40 object-contain" />
                            <div class="w-auto bg-white rounded-[9.45px] outline outline-1 outline-gray-200 flex flex-col justify-start items-start">
                                <div class="size-44 relative rounded-md overflow-hidden flex justify-center items-center bg-white">
                                    <img id="qr-image" src="{{ $latestPayment->qris_url }}" alt="QR Code" class="w-full h-full object-contain mix-blend-multiply" />
                                </div>
                            </div>
                            <div class="w-full text-center text-base-900 text-xs font-normal font-dm leading-4">NMID: IDXXXXXXXX</div>
                            <div class="w-full flex justify-center items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-teal-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                <a href="{{ $latestPayment->qris_url }}" download="QRIS-{{ $order->order_ref }}.png" target="_blank" class="text-center text-teal-500 text-base font-medium font-dm leading-7 hover:underline">Unduh QR</a>
                            </div>
                        </div>
                        
                        <div class="w-full flex flex-col justify-center items-center gap-1 mt-2">
                            <div class="w-full text-center text-text-tertiary text-base font-normal font-dm leading-7">Kadaluarsa dalam</div>
                            <div id="countdown-timer" class="w-full text-center text-yellow-500 text-3xl font-semibold font-bricolage leading-9" data-expires-at="{{ $order->expired_at->toIso8601String() }}">--:--</div>
                            <div class="w-full flex justify-center items-center gap-3">
                                <div class="size-2 bg-teal-600 rounded-full animate-pulse"></div>
                                <div class="text-teal-600 text-base font-medium font-dm leading-7">Menunggu pembayaran....</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full flex flex-col justify-start items-start gap-3 mt-4">
                        <button id="btn-check-status" onclick="checkPaymentStatus()" class="w-full px-2 py-4 bg-primary-500 hover:bg-primary-600 rounded-xl flex flex-col justify-center items-center transition-colors">
                            <span class="text-center text-white text-base font-medium font-dm leading-4">Cek status pembayaran</span>
                        </button>
                        <button onclick="openCancelModal()" class="w-full px-2 py-4 bg-gray-200 hover:bg-gray-300 rounded-xl flex flex-col justify-center items-center transition-colors">
                            <span class="text-center text-base-900 text-lg font-medium font-dm leading-4">Batalkan</span>
                        </button>
                    </div>
                @endif
            </div>
            </div>
        </div>
    @endif

{{-- Modal Overlay --}}
<div id="cancel-modal" class="fixed inset-0 z-50 hidden bg-black/50 justify-center items-center p-4">
    <div class="w-full max-w-96 p-6 bg-base-50 rounded-2xl flex flex-col justify-start items-start gap-6 shadow-xl">
        <div class="self-stretch flex flex-col justify-start items-center gap-4">
            <div class="size-12 bg-orange-100 rounded-full flex justify-center items-center text-orange-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <div class="flex flex-col items-center gap-2 text-center">
                <h3 class="text-stone-900 text-xl font-semibold font-bricolage leading-6">Batalkan pembayaran?</h3>
                <p class="text-text-secondary text-xs font-medium font-dm leading-4">Pesananmu akan dibatalkan dan kamu harus memulai ulang dari halaman paket.</p>
            </div>
        </div>
        <div class="w-full flex justify-start items-start gap-3 mt-2">
            <button onclick="closeCancelModal()" class="flex-1 py-2.5 bg-gray-200 hover:bg-gray-300 transition-colors rounded-[10px] flex justify-center items-center">
                <span class="text-black text-sm font-medium font-dm leading-5">Lanjut bayar</span>
            </button>
            <a href="{{ route('membership.index') }}" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 transition-colors rounded-[10px] flex justify-center items-center">
                <span class="text-white text-sm font-medium font-dm leading-5">Ya, batalkan</span>
            </a>
        </div>
    </div>
</div>

{{-- Pending Modal Overlay --}}
<div id="pending-modal" class="fixed inset-0 z-50 hidden bg-black/50 justify-center items-center p-4">
    <div class="w-full max-w-96 p-6 bg-base-50 rounded-2xl flex flex-col justify-start items-start gap-6 shadow-xl">
        <div class="self-stretch flex flex-col justify-start items-center gap-4">
            <div class="size-12 bg-orange-100 rounded-full flex justify-center items-center text-orange-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <div class="flex flex-col items-center gap-2 text-center">
                <h3 class="text-stone-900 text-xl font-semibold font-bricolage leading-6">Pembayaran belum diterima</h3>
                <p class="text-text-secondary text-sm font-medium font-dm leading-5 max-w-[320px]">Kami belum menerima pembayaranmu. Selesaikan pembayaran lewat QR code, lalu cek status lagi.</p>
            </div>
        </div>
        <div class="w-full mt-2 flex flex-col justify-start items-start gap-3">
            <button onclick="closePendingModal()" class="w-full py-2.5 bg-teal-500 hover:bg-teal-600 transition-colors rounded-[10px] flex justify-center items-center">
                <span class="text-white text-sm font-medium font-dm leading-5">Kembali ke QR</span>
            </button>
        </div>
    </div>
</div>

<script>
    function checkPaymentStatus() {
        const button = document.getElementById('btn-check-status');
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="text-center text-white text-base font-medium font-dm leading-4">Mengecek...</span>';
        button.disabled = true;

        fetch('{{ route('order.check-status', $order) }}')
            .then(response => response.json())
            .then(data => {
                button.innerHTML = originalText;
                button.disabled = false;
                
                if (data.order_status === 'pending') {
                    openPendingModal();
                } else {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error checking status:', error);
                button.innerHTML = originalText;
                button.disabled = false;
            });
    }

    function openPendingModal() {
        const modal = document.getElementById('pending-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closePendingModal() {
        const modal = document.getElementById('pending-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openCancelModal() {
        const modal = document.getElementById('cancel-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeCancelModal() {
        const modal = document.getElementById('cancel-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

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
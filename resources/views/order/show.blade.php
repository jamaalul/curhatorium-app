@extends('layouts.dashboard')

@section('title', 'Checkout — ' . $order->order_ref)

@section('bodyClass', 'pt-16 w-full overflow-x-hidden')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@endsection

@section('dashboard-content')
    @php
        $returnUrl = route('membership.index');
        $returnLabel = 'Kembali';
        $retryLabel = 'Pilih Paket Baru';
        $paidDescription = 'Pembayaranmu sudah kami terima. Selamat menikmati paket barumu!';
        $cancelDescription = 'Pesananmu akan dibatalkan dan kamu harus memulai ulang dari halaman paket.';

        if ($order->orderable instanceof \App\Models\Ebook) {
            $returnUrl = route('ebooks.show', $order->orderable);
            $retryLabel = 'Kembali ke Ebook';
            $paidDescription = 'Pembayaranmu sudah kami terima. Akses ebook akan diproses melalui sistem order.';
            $cancelDescription = 'Pesananmu akan dibatalkan dan kamu harus memulai ulang dari halaman ebook.';
        }
    @endphp

    <div class="flex justify-center items-center bg-gray-200 px-4 sm:px-8 py-11 w-full min-h-[calc(100vh-64px)]">
        @if ($order->isPaid())
            {{-- Paid State (1-Column Layout) --}}
            <div class="flex flex-col justify-start items-start gap-5 bg-base-50 p-6 rounded-2xl max-w-[480px]">
                <div class="flex flex-col justify-start items-center self-stretch gap-1">
                    <div
                        class="justify-start self-stretch font-bricolage font-semibold text-zinc-900 text-4xl text-center leading-[48px]">
                        Detail transaksi</div>
                    <div
                        class="justify-start self-stretch font-dm font-normal text-text-secondary text-base text-center leading-7">
                        {{ $order->order_ref }}</div>
                </div>

                <div class="flex flex-col justify-start items-start self-stretch gap-2">
                    <div class="inline-flex justify-between items-center self-stretch">
                        <div class="justify-start font-dm font-medium text-text-secondary text-xl leading-9">Item</div>
                        <div class="justify-start font-dm font-medium text-base-900 text-xl leading-9">
                            {{ $order->orderable->name ?? 'Order Item' }}</div>
                    </div>
                    <div class="inline-flex justify-between items-center self-stretch">
                        <div class="justify-start font-dm font-medium text-text-secondary text-xl leading-9">Harga</div>
                        <div class="justify-start font-dm font-medium text-base-900 text-xl text-right leading-9">
                            Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                    </div>
                    <div class="inline-flex justify-between items-center self-stretch">
                        <div class="justify-start font-dm font-medium text-text-secondary text-xl leading-9">Metode</div>
                        <div class="justify-start font-dm font-medium text-base-900 text-xl text-right leading-9">
                            {{ strtoupper($latestPayment->payment_type ?? 'QRIS') }}</div>
                    </div>
                    <div class="inline-flex justify-between items-center self-stretch pt-3 border-base-200 border-t">
                        <div class="justify-start font-dm font-medium text-base-900 text-xl leading-9">Total</div>
                        <div class="justify-start font-bricolage font-medium text-base-900 text-2xl text-right leading-7">
                            Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                    </div>
                </div>

                <div class="flex flex-col justify-start items-center self-stretch gap-2 mt-4">
                    <div class="flex justify-center items-center bg-teal-500 rounded-full size-16 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                            stroke="currentColor" class="size-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div class="justify-start font-bricolage font-semibold text-teal-500 text-3xl text-center leading-9">
                        Pembayaran berhasil</div>
                    <div class="justify-start self-stretch font-dm font-medium text-zinc-500 text-base text-center leading-7">
                        {{ $paidDescription }}</div>
                </div>

                <a href="{{ $returnUrl }}"
                    class="flex flex-col justify-center items-center self-stretch gap-4 bg-primary-500 hover:bg-primary-600 mt-4 px-2 py-4 rounded-xl transition-colors">
                    <div class="justify-start font-dm font-medium text-white text-lg text-center leading-4">{{ $returnLabel }}
                    </div>
                </a>
            </div>
        @else
            {{-- Existing 2-Column Layout for Pending/Expired --}}
            <div class="bg-base-50 mx-auto p-6 sm:p-8 rounded-2xl w-full max-w-[850px]">
                <div class="gap-8 md:gap-12 grid grid-cols-1 md:grid-cols-2 w-full">

                    {{-- Left Column: Details --}}
                    <div class="flex flex-col justify-start items-start gap-4 w-full">
                        <div class="flex flex-col justify-start items-start gap-1 w-full">
                            <div class="font-bricolage font-semibold text-base-900 text-3xl leading-9">Detail transaksi</div>
                            <div class="font-dm font-normal text-text-tertiary text-xs leading-4">{{ $order->order_ref }}</div>
                        </div>

                        <div class="flex justify-start items-start gap-3 w-full">
                            <div class="flex flex-col flex-1 justify-start items-start gap-2">
                                <div class="flex justify-between items-start w-full">
                                    <div class="font-dm font-normal text-text-secondary text-base leading-7">Item</div>
                                    <div class="font-dm font-normal text-base text-base-900 text-right leading-7">
                                        {{ $order->orderable->name ?? 'Order Item' }}</div>
                                </div>
                                <div class="flex justify-between items-start w-full">
                                    <div class="font-dm font-normal text-text-secondary text-base leading-7">Harga</div>
                                    <div class="font-dm font-normal text-base text-base-900 text-right leading-7">
                                        Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                                </div>
                                <div class="flex justify-between items-start w-full">
                                    <div class="font-dm font-normal text-text-secondary text-base leading-7">Metode</div>
                                    <div class="font-dm font-normal text-base text-base-900 text-right leading-7">
                                        {{ strtoupper($latestPayment->payment_type ?? 'QRIS') }}</div>
                                </div>
                            </div>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="1" viewBox="0 0 368 1" fill="none"
                            class="self-stretch my-2" preserveAspectRatio="none">
                            <path d="M0.5 0.5L367.5 0.5" stroke="#E4E4E7" stroke-linecap="round" />
                        </svg>

                        <div class="flex justify-between items-center w-full">
                            <div class="font-dm font-medium text-base text-base-900 leading-7">Total</div>
                            <div class="font-bricolage font-medium text-base-900 text-2xl text-right leading-7">
                                Rp{{ number_format($order->gross_amount, 2, ',', '.') }}</div>
                        </div>
                    </div>

                    {{-- Right Column: Dynamic Status --}}
                    <div class="flex flex-col justify-start items-start gap-4 w-full">
                        @if ($order->isExpired() || ($latestPayment && in_array($latestPayment->transaction_status, ['expire', 'cancel', 'deny'])))
                            {{-- Expired State --}}
                            <div class="flex flex-col justify-center items-center gap-4 py-10 w-full h-full text-center">
                                <div class="flex flex-col justify-start items-center self-stretch gap-4">
                                    <div
                                        class="flex justify-center items-center bg-orange-100 rounded-full size-12 text-orange-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>

                                    <div class="flex flex-col items-center gap-2 text-center">
                                        <h3 class="font-bricolage font-semibold text-stone-900 text-xl leading-6">Pembayaran
                                            kadaluarsa</h3>
                                        <p class="max-w-[320px] font-dm font-medium text-text-secondary text-sm leading-5">Sayang
                                            sekali, batas waktu pembayaran sudah berakhir. Kamu bisa memilih paket lagi untuk
                                            melanjutkan.</p>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-start items-start gap-3 mt-4 w-full">
                                    <a href="{{ $returnUrl }}"
                                        class="flex flex-col justify-center items-center bg-primary-500 hover:bg-primary-600 px-2 py-4 rounded-xl w-full transition-colors">
                                        <span
                                            class="font-dm font-medium text-white text-base text-center leading-4">{{ $retryLabel }}</span>
                                    </a>
                                </div>
                            </div>
                        @elseif ($latestPayment && $latestPayment->qris_url)
                            @php
                                $isQrUrl = \Illuminate\Support\Str::startsWith($latestPayment->qris_url, ['http://', 'https://', 'data:image/']);
                            @endphp
                            {{-- QRIS Pending State --}}
                            <div class="flex flex-col justify-start items-center gap-4 w-full">
                                <div class="font-dm font-medium text-base text-base-900 text-center leading-7">Scan QR untuk bayar
                                </div>
                                <div class="flex flex-col justify-start items-center gap-4 w-64">
                                    <img src="{{ asset('assets/qris-logo 1.svg') }}" alt="QRIS Logo" class="w-40 object-contain" />
                                    <div
                                        class="flex flex-col justify-start items-start bg-white rounded-[9.45px] outline outline-1 outline-gray-200 w-auto">
                                        <div
                                            class="relative flex justify-center items-center bg-white p-2 rounded-md size-44 overflow-hidden">
                                            <div id="qr-container" class="flex justify-center items-center"></div>
                                            <img id="qr-image" src="{{ $isQrUrl ? $latestPayment->qris_url : '' }}" alt="QR Code"
                                                class="w-full h-full object-contain mix-blend-multiply {{ $isQrUrl ? '' : 'hidden' }}" />
                                        </div>
                                    </div>
                                    <div class="w-full font-dm font-normal text-xs text-base-900 text-center leading-4">NMID: ID2026572732561</div>
                                    <div class="flex justify-center items-center gap-2 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-6 text-teal-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        <a id="qr-download-link" href="{{ $isQrUrl ? $latestPayment->qris_url : '#' }}"
                                            download="QRIS-{{ $order->order_ref }}.png" target="_blank"
                                            class="font-dm font-medium text-teal-500 text-base text-center hover:underline leading-7">Unduh
                                            QR</a>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-center items-center gap-1 mt-2 w-full">
                                    <div class="w-full font-dm font-normal text-text-tertiary text-base text-center leading-7">
                                        Kadaluarsa dalam</div>
                                    <div id="countdown-timer"
                                        class="w-full font-bricolage font-semibold text-yellow-500 text-3xl text-center leading-9"
                                        data-expires-at="{{ $order->expired_at->toIso8601String() }}">--:--</div>
                                    <div class="flex justify-center items-center gap-3 w-full">
                                        <div class="bg-teal-600 rounded-full size-2 animate-pulse"></div>
                                        <div class="font-dm font-medium text-teal-600 text-base leading-7">Menunggu pembayaran....
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col justify-start items-start gap-3 mt-4 w-full">
                                <button id="btn-check-status" onclick="checkPaymentStatus()"
                                    class="flex flex-col justify-center items-center bg-primary-500 hover:bg-primary-600 px-2 py-4 rounded-xl w-full transition-colors">
                                    <span class="font-dm font-medium text-white text-base text-center leading-4">Cek status
                                        pembayaran</span>
                                </button>
                                <button onclick="openCancelModal()"
                                    class="flex flex-col justify-center items-center bg-gray-200 hover:bg-gray-300 px-2 py-4 rounded-xl w-full transition-colors">
                                    <span class="font-dm font-medium text-base-900 text-lg text-center leading-4">Batalkan</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Overlay --}}
        <div id="cancel-modal" class="hidden z-50 fixed inset-0 justify-center items-center bg-black/50 p-4">
            <div class="flex flex-col justify-start items-start gap-6 bg-base-50 shadow-xl p-6 rounded-2xl w-full max-w-96">
                <div class="flex flex-col justify-start items-center self-stretch gap-4">
                    <div class="flex justify-center items-center bg-orange-100 rounded-full size-12 text-orange-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <div class="flex flex-col items-center gap-2 text-center">
                        <h3 class="font-bricolage font-semibold text-stone-900 text-xl leading-6">Batalkan pembayaran?</h3>
                        <p class="font-dm font-medium text-text-secondary text-xs leading-4">{{ $cancelDescription }}</p>
                    </div>
                </div>
                <div class="flex justify-start items-start gap-3 mt-2 w-full">
                    <button onclick="closeCancelModal()"
                        class="flex flex-1 justify-center items-center bg-gray-200 hover:bg-gray-300 py-2.5 rounded-[10px] transition-colors">
                        <span class="font-dm font-medium text-black text-sm leading-5">Lanjut bayar</span>
                    </button>
                    <a href="{{ $returnUrl }}"
                        class="flex flex-1 justify-center items-center bg-red-500 hover:bg-red-600 py-2.5 rounded-[10px] transition-colors">
                        <span class="font-dm font-medium text-white text-sm leading-5">Ya, batalkan</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Pending Modal Overlay --}}
        <div id="pending-modal" class="hidden z-50 fixed inset-0 justify-center items-center bg-black/50 p-4">
            <div class="flex flex-col justify-start items-start gap-6 bg-base-50 shadow-xl p-6 rounded-2xl w-full max-w-96">
                <div class="flex flex-col justify-start items-center self-stretch gap-4">
                    <div class="flex justify-center items-center bg-orange-100 rounded-full size-12 text-orange-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <div class="flex flex-col items-center gap-2 text-center">
                        <h3 class="font-bricolage font-semibold text-stone-900 text-xl leading-6">Pembayaran belum diterima
                        </h3>
                        <p class="max-w-[320px] font-dm font-medium text-text-secondary text-sm leading-5">Kami belum
                            menerima pembayaranmu. Selesaikan pembayaran lewat QR code, lalu cek status lagi.</p>
                    </div>
                </div>
                <div class="flex flex-col justify-start items-start gap-3 mt-2 w-full">
                    <button onclick="closePendingModal()"
                        class="flex justify-center items-center bg-teal-500 hover:bg-teal-600 py-2.5 rounded-[10px] w-full transition-colors">
                        <span class="font-dm font-medium text-white text-sm leading-5">Kembali ke QR</span>
                    </button>
                </div>
            </div>
        </div>

        <script>
            function checkPaymentStatus() {
                const button = document.getElementById('btn-check-status');
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="font-dm font-medium text-white text-base text-center leading-4">Mengecek...</span>';
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

            document.addEventListener('DOMContentLoaded', function () {
                const qrString = @json($latestPayment->qris_url ?? null);
                const qrContainer = document.getElementById('qr-container');
                const qrImage = document.getElementById('qr-image');
                const downloadLink = document.getElementById('qr-download-link');

                if (qrString) {
                    if (qrString.startsWith('http://') || qrString.startsWith('https://')) {
                        if (qrImage) {
                            qrImage.src = qrString;
                            qrImage.classList.remove('hidden');
                        }
                        if (downloadLink) {
                            downloadLink.href = qrString;
                        }
                    } else if (typeof QRCode !== 'undefined' && qrContainer) {
                        new QRCode(qrContainer, {
                            text: qrString,
                            width: 160,
                            height: 160,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        setTimeout(() => {
                            const canvas = qrContainer.querySelector('canvas');
                            const img = qrContainer.querySelector('img');
                            if (canvas && downloadLink) {
                                downloadLink.href = canvas.toDataURL('image/png');
                            } else if (img && downloadLink) {
                                downloadLink.href = img.src;
                            }
                        }, 300);
                    }
                }
            });
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
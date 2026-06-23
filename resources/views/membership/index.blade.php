@extends('layouts.dashboard')

@section('title', 'Pilih Paket | Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden')

@section('head')
    <meta name="description"
        content="Pilih paket membership Curhatorium yang sesuai dengan kebutuhanmu dan mulai perjalanan kesehatan mentalmu.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .plan-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .plan-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -10px rgba(72, 166, 166, 0.25);
        }
    </style>
@endsection

@section('dashboard-content')
    <section class="w-full max-w-full px-6 sm:px-12 md:px-28 py-11 bg-gray-200 flex flex-col justify-start items-start gap-2 overflow-hidden">
        <div class="w-full max-w-[1200px] mx-auto flex flex-col justify-start items-center gap-10">

            {{-- Header Section --}}
            <div class="flex flex-col justify-start items-center gap-5">
                <div class="inline-flex justify-start items-center gap-3">
                    {{-- Icon --}}
                    <img src="{{ asset('assets/emoji/11.svg') }}" alt="Curhatorium Logo" class="w-12 h-12"/>
                    <h2 class="text-base-900 text-2xl font-medium font-bricolage leading-7">Berlangganan</h2>
                </div>
                <div class="flex flex-col justify-start items-center gap-2">
                    <h1 class="text-base-900 text-3xl sm:text-4xl font-semibold font-bricolage leading-[48px]">Pilih paketmu</h1>
                    <p class="max-w-[672px] text-center text-text-secondary text-lg sm:text-xl md:text-2xl font-medium font-bricolage leading-7">Temukan paket yang paling pas buat kamu</p>
                </div>
            </div>

            {{-- Pricing Cards --}}
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                @foreach ($plans as $plan)
                    @php
                        $isFree = $plan->price_idr == 0;
                        $isCalm = $plan->name === 'Calm';
                        $isPeaceful = $plan->name === 'Peaceful';
                        $mentAiLabel = match ($plan->name) {
                            'Free' => 'Batas dasar',
                            'Calm' => 'Batas lebih luas',
                            default => 'Batas lebih bebas',
                        };
                        $tagline = match ($plan->name) {
                            'Free' => 'Fitur dasar siap menemanimu kapan saja.',
                            'Calm' => 'Lebih banyak ruang buat cerita dan eksplorasi.',
                            default => 'Akses penuh ke semua yang kamu butuhkan.',
                        };
                        $includes = match ($plan->name) {
                            'Free' => 'Paket Free mencakup:',
                            'Calm' => 'Paket Calm mencakup:',
                            default => 'Paket Peaceful mencakup:',
                        };
                    @endphp

                    @if ($isCalm)
                        {{-- Calm card with recommendation wrapper --}}
                        <div class="plan-card w-full min-w-0 sm:col-span-2 lg:col-span-1 p-2 bg-secondary-300 rounded-2xl flex flex-col items-center gap-2 overflow-hidden order-2 sm:order-3 lg:order-2">
                            <div class="text-center text-base-900 text-2xl font-medium font-bricolage leading-7">Rekomendasi tepat</div>
                            <div class="w-full flex-1 p-6 bg-base-50 rounded-xl flex flex-col items-start gap-8">
                                {{-- Plan Name --}}
                                <div class="w-full flex flex-col justify-start items-start gap-5">
                                    <div class="w-full inline-flex justify-start items-center gap-2">
                                        <img src="{{ asset('assets/Calm.svg') }}" alt="Calm" class="w-9 h-9"/>
                                        <span class="text-base-900 text-2xl xl:text-3xl font-semibold font-bricolage leading-9">{{ $plan->name }}</span>
                                    </div>
                                    <p class="w-full text-base-900 text-xl xl:text-2xl font-medium font-bricolage leading-7">{{ $tagline }}</p>
                                </div>

                                {{-- Price --}}
                                <div class="w-full flex flex-col justify-start items-start gap-5">
                                    <div class="w-full inline-flex justify-start items-end gap-1">
                                        <span class="text-base-900 text-3xl xl:text-4xl font-semibold font-bricolage leading-[48px]">{{ $plan->getPriceInIDR() }}</span>
                                        <span class="text-text-secondary text-xl xl:text-2xl font-medium font-dm leading-7">/Bulan</span>
                                    </div>
                                    <form action="{{ route('order.create', $plan) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" id="purchase-{{ $plan->id }}" class="w-full px-2 py-4 bg-primary-500 hover:bg-primary-600 rounded-xl inline-flex justify-center items-center gap-2 transition-colors duration-200 cursor-pointer">
                                            <span class="text-base-50 text-lg font-medium font-dm leading-4">Mulai sekarang</span>
                                        </button>
                                    </form>
                                </div>

                                {{-- Benefits --}}
                                <div class="w-full flex flex-col justify-start items-start gap-1 mb-auto">
                                    <p class="w-full text-base-900 text-xl font-medium font-dm leading-9">{{ $includes }}</p>

                                    @include('membership._benefit-row', ['label' => 'Tes Kesejahteraan Mental', 'value' => 'check'])
                                    @include('membership._benefit-row', ['label' => 'Mood Tracker', 'value' => 'check'])
                                    @include('membership._benefit-row', ['label' => 'Daily Missions', 'value' => 'check'])
                                    @include('membership._benefit-row', ['label' => 'Deep Cards', 'value' => 'check'])

                                    @foreach ($plan->planBenefits as $benefit)
                                        @if ($benefit->amount >= 1 && $benefit->benefit !== 'ai_window_token')
                                            @include('membership._benefit-row', [
                                                'label' => ucwords(str_replace('_', ' ', $benefit->benefit)),
                                                'value' => (string) $benefit->amount,
                                            ])
                                        @endif
                                    @endforeach

                                    @include('membership._benefit-row', ['label' => 'Ment-AI', 'value' => $mentAiLabel])
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Free / Peaceful card --}}
                        <div class="plan-card w-full min-w-0 lg:mt-8 p-6 bg-base-50 rounded-2xl flex flex-col items-start gap-8 overflow-hidden {{ $isFree ? 'order-1' : 'order-3 sm:order-2 lg:order-3' }}">
                            {{-- Plan Name --}}
                            <div class="w-full flex flex-col justify-start items-start gap-5">
                                <div class="w-full inline-flex justify-start items-center gap-2">
                                    @if ($isFree)
                                        <img src="{{ asset('assets/Free.svg') }}" alt="Free" class="w-9 h-9"/>
                                    @else
                                        <img src="{{ asset('assets/Peaceful.svg') }}" alt="Peaceful" class="w-9 h-9"/>
                                    @endif
                                    <span class="text-base-900 text-2xl xl:text-3xl font-semibold font-bricolage leading-9">{{ $plan->name }}</span>
                                </div>
                                <p class="w-full text-base-900 text-xl xl:text-2xl font-medium font-bricolage leading-7">{{ $tagline }}</p>
                            </div>

                            {{-- Price --}}
                            <div class="w-full flex flex-col justify-start items-start gap-5">
                                <div class="w-full inline-flex justify-start items-end gap-2">
                                    @if ($isFree)
                                        <span class="text-base-900 text-4xl font-semibold font-bricolage leading-[48px]">Gratis</span>
                                    @else
                                        <span class="text-base-900 text-3xl xl:text-4xl font-semibold font-bricolage leading-[48px]">{{ $plan->getPriceInIDR() }}</span>
                                    @endif
                                    <span class="text-text-secondary text-xl xl:text-2xl font-medium font-dm leading-7">/Bulan</span>
                                </div>

                                @if ($isFree)
                                    <button disabled class="w-full px-2 py-4 bg-base-100 rounded-xl inline-flex justify-center items-center gap-2 cursor-not-allowed opacity-80">
                                        <span class="text-base-900 text-lg font-medium font-dm leading-4">Paket kamu saat ini</span>
                                    </button>
                                @else
                                    <form action="{{ route('order.create', $plan) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" id="purchase-{{ $plan->id }}" class="w-full px-2 py-4 bg-primary-500 hover:bg-primary-600 rounded-xl inline-flex justify-center items-center gap-2 transition-colors duration-200 cursor-pointer">
                                            <span class="text-base-50 text-lg font-medium font-dm leading-4">Mulai sekarang</span>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Benefits --}}
                            <div class="w-full flex flex-col justify-start items-start gap-1 mb-auto">
                                <p class="w-full text-base-900 text-xl font-medium font-dm leading-9">{{ $includes }}</p>

                                @include('membership._benefit-row', ['label' => 'Tes Kesejahteraan Mental', 'value' => 'check'])
                                @include('membership._benefit-row', ['label' => 'Mood Tracker', 'value' => 'check'])
                                @include('membership._benefit-row', ['label' => 'Daily Missions', 'value' => 'check'])
                                @include('membership._benefit-row', ['label' => 'Deep Cards', 'value' => 'check'])

                                @foreach ($plan->planBenefits as $benefit)
                                    @if ($benefit->amount >= 1 && $benefit->benefit !== 'ai_window_token')
                                        @include('membership._benefit-row', [
                                            'label' => ucwords(str_replace('_', ' ', $benefit->benefit)),
                                            'value' => (string) $benefit->amount,
                                        ])
                                    @endif
                                @endforeach

                                @include('membership._benefit-row', ['label' => 'Ment-AI', 'value' => $mentAiLabel])
                            </div>
                            
                            @if ($isFree)
                                <div class="mb-auto"></div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
<x-layout title="Pilih Paket | Curhatorium" bodyClass="pt-16 w-full overflow-x-hidden">
    <x-slot:head>
        <meta name="description" content="Pilih paket membership Curhatorium yang sesuai dengan kebutuhanmu dan mulai perjalanan kesehatan mentalmu.">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .plan-card {
                transition: transform 0.25s ease, box-shadow 0.25s ease;
            }

            .plan-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 20px 40px -10px rgba(72, 166, 166, 0.25);
            }

            .plan-card.featured {
                border: 2px solid #48A6A6;
            }

            .plan-card.featured::before {
                content: 'Terpopuler';
                position: absolute;
                top: -1px;
                left: 50%;
                transform: translateX(-50%);
                background: #48A6A6;
                color: white;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 4px 18px;
                border-radius: 0 0 10px 10px;
                letter-spacing: 0.05em;
            }

            .benefit-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #f0ede8;
                font-size: 0.9rem;
                color: #555;
            }

            .benefit-row:last-of-type {
                border-bottom: none;
            }

            .benefit-value {
                font-weight: 600;
                color: #222222;
                white-space: nowrap;
            }

            .purchase-btn {
                display: block;
                width: 100%;
                padding: 12px 0;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.95rem;
                text-align: center;
                cursor: pointer;
                border: none;
                transition: background 0.2s ease, box-shadow 0.2s ease;
            }

            .purchase-btn-primary {
                background: #48A6A6;
                color: white;
            }

            .purchase-btn-primary:hover {
                background: #357979;
                box-shadow: 0 4px 14px rgba(72, 166, 166, 0.4);
            }

            .purchase-btn-outline {
                background: transparent;
                color: #48A6A6;
                border: 2px solid #48A6A6;
            }

            .purchase-btn-outline:hover {
                background: #f0fafa;
            }

            .badge-free {
                background: #f3f4f6;
                color: #6b7280;
            }

            .badge-calm {
                background: linear-gradient(135deg, #d8efef, #b5dede);
                color: #2a7070;
            }

            .badge-serene {
                background: linear-gradient(135deg, #48A6A6, #357979);
                color: white;
            }
        </style>
    </x-slot:head>

    @include('components.navbar')

    {{-- Hero Section --}}
    <section
        class="relative flex flex-col justify-center items-center gap-3 bg-cover shadow-inner px-4 py-14 w-full h-fit"
        style="background-image: url('{{ asset('images/background.webp') }}');">
        <span class="inline-block bg-[#48A6A6]/15 text-[#2a7070] text-sm font-semibold px-4 py-1 rounded-full mb-1">
            Membership
        </span>
        <h1 class="font-bold text-[#222222] text-3xl md:text-5xl text-center leading-tight">
            Pilih Paket yang Tepat<br class="hidden md:block"> Untukmu
        </h1>
        <p class="text-[#444444] text-base text-center max-w-lg mt-1">
            Mulai gratis, atau upgrade untuk pengalaman kesehatan mental yang lebih lengkap dan personal.
        </p>
    </section>

    {{-- Pricing Section --}}
    <section class="bg-stone-200 w-full px-4 py-12">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                @foreach ($plans as $index => $plan)
                    @php
                        $isFeatured = $plan->name === 'Calm';
                        $badgeClass = match($plan->name) {
                            'Free'   => 'badge-free',
                            'Calm'   => 'badge-calm',
                            default  => 'badge-serene',
                        };
                        $mentAiLabel = match($plan->name) {
                            'Free'   => 'Base limit',
                            'Calm'   => 'Extended limit',
                            default  => 'Longer limit',
                        };
                    @endphp
                    <div class="plan-card relative bg-white rounded-2xl shadow-md p-7 flex flex-col gap-0 {{ $isFeatured ? 'featured mt-0 md:-mt-4' : '' }}">

                        {{-- Plan Header --}}
                        <div class="mb-5">
                            <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full mb-3 {{ $badgeClass }}">
                                {{ $plan->name }}
                            </span>
                            <div class="flex items-end gap-1 mb-1">
                                @if ($plan->price_idr > 0)
                                    <span class="text-3xl font-bold text-[#222222]">{{ $plan->getPriceInIDR() }}</span>
                                    <span class="text-gray-400 text-sm mb-1">/bulan</span>
                                @else
                                    <span class="text-3xl font-bold text-[#222222]">Gratis</span>
                                @endif
                            </div>
                            <p class="text-gray-500 text-sm">
                                @if ($plan->name === 'Free')
                                    Mulai perjalananmu tanpa biaya
                                @elseif ($plan->name === 'Calm')
                                    Lebih banyak akses, lebih tenang
                                @else
                                    Dukungan penuh tanpa batas
                                @endif
                            </p>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-stone-100 mb-4"></div>

                        {{-- Benefits --}}
                        <div class="flex flex-col flex-1 mb-6">
                            <div class="benefit-row">
                                <span>MHC-SF</span>
                                <span class="benefit-value">&#8734;</span>
                            </div>
                            <div class="benefit-row">
                                <span>Mood Tracker</span>
                                <span class="benefit-value">&#8734;</span>
                            </div>
                            <div class="benefit-row">
                                <span>Daily Missions</span>
                                <span class="benefit-value">&#8734;</span>
                            </div>
                            <div class="benefit-row">
                                <span>Deep Cards</span>
                                <span class="benefit-value">&#8734;</span>
                            </div>
                            @foreach ($plan->planBenefits as $benefit)
                                @if ($benefit->amount >= 1 && $benefit->benefit !== 'ai_window_token')
                                    <div class="benefit-row">
                                        <span>{{ ucwords(str_replace('_', ' ', $benefit->benefit)) }}</span>
                                        <span class="benefit-value">{{ $benefit->amount }}</span>
                                    </div>
                                @endif
                            @endforeach
                            <div class="benefit-row">
                                <span>Ment-AI</span>
                                <span class="benefit-value">{{ $mentAiLabel }}</span>
                            </div>
                        </div>

                        {{-- CTA --}}
                        @if ($plan->price_idr > 0)
                            <form action="{{ route('order.create', $plan) }}" method="POST">
                                @csrf
                                <button type="submit" id="purchase-{{ $plan->id }}" class="purchase-btn purchase-btn-primary">
                                    Upgrade ke {{ $plan->name }}
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="purchase-btn purchase-btn-outline opacity-60 cursor-default">
                                Paket Saat Ini
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Trust note --}}
            <p class="text-center text-sm text-gray-500 mt-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 inline-block mr-1 text-[#48A6A6]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                Pembayaran aman. Batalkan kapan saja.
            </p>
        </div>
    </section>

    @include('components.footer')
</x-layout>
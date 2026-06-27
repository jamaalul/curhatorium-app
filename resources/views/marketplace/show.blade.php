@extends('layouts.dashboard')

@section('title', $product->name . ' | Marketplace Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-white')

@section('head')
    <meta name="description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Thumbnail scrollbar */
        .thumb-list::-webkit-scrollbar {
            height: 4px;
        }

        .thumb-list::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 9999px;
        }

        /* Fade-in for main media */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.97);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .media-fade {
            animation: fadeIn 0.25s ease;
        }

        /* Thumbnail active ring */
        .thumb-item.active {
            outline: 2px solid #111827;
            outline-offset: 2px;
        }
    </style>
@endsection

@section('dashboard-content')

    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-gray-200 border-b">
        <div class="flex items-center gap-2 mx-auto px-4 sm:px-6 lg:px-8 py-3 max-w-7xl text-gray-500 text-xs">
            <a href="{{ route('marketplace.index') }}" class="hover:text-gray-900 transition-colors">Marketplace</a>
            <svg class="flex-shrink-0 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-medium text-gray-900 truncate">{{ $product->name }}</span>
        </div>
    </div>

    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16 max-w-7xl">
        <div class="flex lg:flex-row flex-col gap-10 lg:gap-16">

            {{-- ── LEFT: Media Gallery ── --}}
            <div class="flex flex-col gap-4 w-full lg:w-1/2">

                {{-- Main display --}}
                <div id="main-media-container"
                    class="relative flex justify-center items-center bg-[#f8f8f8] rounded-sm aspect-square overflow-hidden">

                    @php $allMedia = $product->media; @endphp

                    @if($allMedia->isEmpty())
                        {{-- Placeholder --}}
                        <div class="flex flex-col items-center gap-2 text-gray-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm">No media available</span>
                        </div>
                    @else
                        {{-- First media item shown by default --}}
                        @php $first = $allMedia->first(); @endphp
                        @if($first->media_type === 'video')
                            <video id="main-media" src="{{ $first->publicUrl() }}" controls
                                class="w-full h-full object-contain media-fade">
                            </video>
                        @else
                            <img id="main-media" src="{{ $first->publicUrl() }}" alt="{{ $product->name }}"
                                class="w-[82%] h-[82%] object-contain media-fade mix-blend-multiply">
                        @endif
                    @endif
                </div>

                {{-- Thumbnail strip --}}
                @if($allMedia->count() > 1)
                    <div class="flex gap-3 pb-1 overflow-x-auto thumb-list">
                        @foreach($allMedia as $i => $media)
                            <button type="button" data-src="{{ $media->publicUrl() }}" data-type="{{ $media->media_type }}"
                                class="thumb-item flex-shrink-0 w-20 h-20 bg-[#f8f8f8] overflow-hidden flex items-center justify-center rounded-sm border border-transparent hover:border-gray-400 transition-all {{ $i === 0 ? 'active' : '' }}"
                                onclick="switchMedia(this)">
                                @if($media->media_type === 'video')
                                    <div class="relative flex justify-center items-center w-full h-full text-gray-400">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </div>
                                @else
                                    <img src="{{ $media->publicUrl() }}" alt="{{ $product->name }} {{ $i + 1 }}"
                                        class="p-1 w-full h-full object-contain mix-blend-multiply">
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── RIGHT: Product Info ── --}}
            <div class="flex flex-col gap-6 w-full lg:w-1/2">

                {{-- Name & Price --}}
                <div>
                    <h1 class="mb-3 font-bold text-gray-900 text-2xl sm:text-3xl leading-snug">
                        {{ $product->name }}
                    </h1>
                    <p class="font-bold text-gray-900 text-2xl">
                        Rp{{ number_format($product->price, 0, ',', '.') }}
                    </p>
                </div>

                <div class="border-gray-200 border-t"></div>

                {{-- Description --}}
                @if($product->description)
                    <div class="max-w-none text-gray-700 leading-relaxed prose prose-sm">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif

                <div class="border-gray-200 border-t"></div>

                {{-- Buy / External Links --}}
                <div class="flex flex-col gap-3">
                    <p class="font-semibold text-gray-900 text-sm uppercase tracking-wider">Beli di</p>
                    @if($product->ecommerce_url)
                        <a href="{{ $product->tokopedia_url }}" target="_blank" rel="noopener noreferrer"
                            class="flex justify-center items-center gap-3 bg-green-500 hover:bg-green-600 px-6 py-3.5 rounded-sm font-semibold text-white text-sm transition-all duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 40 40" fill="none">
                                <circle cx="20" cy="20" r="20" fill="#42B549" />
                                <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="white"
                                    font-size="18" font-weight="bold">T</text>
                            </svg>
                            Tokopedia
                        </a>
                        <a href="{{ $product->shopee_url }}" target="_blank" rel="noopener noreferrer"
                            class="flex justify-center items-center gap-3 bg-[#ee4d2d] hover:bg-[#d9441f] px-6 py-3.5 rounded-sm font-semibold text-white text-sm transition-all duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 40 40" fill="none">
                                <circle cx="20" cy="20" r="20" fill="#EE4D2D" />
                                <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="white"
                                    font-size="18" font-weight="bold">S</text>
                            </svg>
                            Shopee
                        </a>
                        <a href="{{ $product->tiktok_url }}" target="_blank" rel="noopener noreferrer"
                            class="flex justify-center items-center gap-3 bg-black hover:bg-gray-800 px-6 py-3.5 rounded-sm font-semibold text-white text-sm transition-all duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="white">
                                <path
                                    d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06Z" />
                            </svg>
                            TikTok Shop
                        </a>
                    @else
                        <p class="text-gray-500 text-sm italic">Link pembelian belum tersedia.</p>
                    @endif
                </div>

                {{-- Social share / back --}}
                <div class="flex items-center gap-4 mt-auto pt-4">
                    <a href="{{ route('marketplace.index') }}"
                        class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-900 text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Marketplace
                    </a>
                </div>
            </div>

        </div>

        {{-- ── Related / More Products ── --}}
        @php
            $related = \App\Models\Product::select(['id', 'name', 'slug', 'price'])
                ->with(['primaryImage' => fn($q) => $q->select(['product_media.id', 'product_media.product_id', 'product_media.media_url'])])
                ->where('is_published', true)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        @endphp

        @if($related->isNotEmpty())
            <div class="mt-16 pt-12 border-gray-200 border-t">
                <h2 class="mb-8 font-bold text-gray-900 text-xl">Produk Lainnya</h2>
                <div class="gap-x-6 gap-y-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($related as $rel)
                        <div class="group flex flex-col">
                            <a href="{{ route('marketplace.detail', $rel->slug) }}"
                                class="block relative flex justify-center items-center bg-[#f8f8f8] mb-3 aspect-square overflow-hidden">
                                @if($rel->primaryImage)
                                    <img src="{{ $rel->primaryImage->publicUrl() }}" alt="{{ $rel->name }}"
                                        class="w-[80%] h-[80%] object-contain group-hover:scale-105 transition-transform duration-300 mix-blend-multiply">
                                @else
                                    <div class="text-gray-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            <h3 class="mb-1 font-bold text-gray-900 text-sm leading-snug">
                                <a href="{{ route('marketplace.detail', $rel->slug) }}" class="hover:underline">{{ $rel->name }}</a>
                            </h3>
                            <p class="text-gray-700 text-sm">Rp{{ number_format($rel->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script>
        /**
         * Switch the main media display when a thumbnail is clicked.
         * Supports both image and video media types.
         */
        function switchMedia(thumb) {
            const src = thumb.dataset.src;
            const type = thumb.dataset.type;
            const container = document.getElementById('main-media-container');

            // Remove old media element
            const old = document.getElementById('main-media');
            if (old) { old.remove(); }

            let el;
            if (type === 'video') {
                el = document.createElement('video');
                el.src = src;
                el.controls = true;
                el.className = 'media-fade w-full h-full object-contain';
            } else {
                el = document.createElement('img');
                el.src = src;
                el.alt = '{{ addslashes($product->name) }}';
                el.className = 'media-fade w-[82%] h-[82%] object-contain mix-blend-multiply';
            }
            el.id = 'main-media';
            container.appendChild(el);

            // Update active thumbnail
            document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }
    </script>
@endsection
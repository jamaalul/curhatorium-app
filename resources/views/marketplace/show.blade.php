@extends('layouts.dashboard')

@section('title', $product->name . ' | Marketplace Curhatorium')

@section('bodyClass', 'pt-16 w-full bg-[#F4F4F5]')

@section('head')
    <meta name="description" content="{{ Str::limit(strip_tags($product->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Geist:wght@100..900&family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F4F4F5 !important;
        }

        /* Fade animation for main media switcher */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.98);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .media-fade {
            animation: fadeIn 0.2s ease-out;
        }

        /* Active thumbnail ring */
        .thumb-item.active {
            border-color: #00BBA7 !important;
            box-shadow: 0 0 0 2px rgba(0, 187, 167, 0.25) !important;
        }

        /* Product Card Media Banner Aspect Ratio (Matching Figma Card Component 264:160) */
        .marketplace-card-banner {
            width: 100% !important;
            aspect-ratio: 264 / 160 !important;
            position: relative !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            background: #F4F4F5 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        /* Hero Layout Default (Mobile Stacked - Compact, no empty gap) */
        .hero-row-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 20px !important;
            width: 100% !important;
        }

        .hero-right-column {
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            gap: 20px !important;
            height: auto !important;
            min-height: 0 !important;
            width: 100% !important;
        }

        .hero-action-container {
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
            width: 100% !important;
            margin-top: 8px !important;
            padding-top: 8px !important;
            z-index: 50 !important;
        }

        /* Product Title Responsive Heading */
        .product-title-heading {
            color: #141414 !important;
            font-size: 28px !important;
            font-weight: 600 !important;
            font-family: 'Bricolage Grotesque', sans-serif !important;
            line-height: 36px !important;
            letter-spacing: -0.015em !important;
            margin: 0 !important;
        }

        /* Produk Lainnya Grid: 2 Columns on Tablet & Mobile (< 1024px), 4 Columns on Large Desktop (>= 1024px) */
        .related-products-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 16px !important;
            width: 100% !important;
        }

        /* Tablet & Desktop Layout (>= 768px) */
        @media (min-width: 768px) {
            .hero-row-container {
                flex-direction: row !important;
                gap: 28px !important;
                align-items: flex-start !important;
                justify-content: space-between !important;
            }
            .hero-left-column {
                flex: 1 1 48% !important;
                min-width: 0 !important;
                max-width: 576px !important;
            }
            .hero-right-column {
                flex: 1 1 48% !important;
                min-width: 0 !important;
                max-width: 588px !important;
                min-height: 576px !important;
                justify-content: space-between !important;
                gap: 0 !important;
            }
            .hero-action-container {
                margin-top: auto !important;
                padding-top: 24px !important;
            }
            .product-title-heading {
                font-size: 40px !important;
                line-height: 48px !important;
            }
        }

        @media (min-width: 1024px) {
            .hero-row-container {
                gap: 36px !important;
            }
            .related-products-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 20px !important;
            }
        }

        /* Platform Selection Modal Hover Effect */
        .platform-modal-item:hover {
            background-color: #F4F4F5 !important;
        }
    </style>
@endsection

@section('dashboard-content')
    <div class="w-full bg-[#F4F4F5] min-h-screen py-6 sm:py-10" style="background-color: #F4F4F5; position: relative;">
        
        <!-- Main Max-Width Container (1200px matching Figma Node 854:1771) -->
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-10 sm:gap-16" style="max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 16px; padding-right: 16px; display: flex; flex-direction: column; gap: 40px;">
            
            <!-- HERO SECTION (Side-by-Side on Tablet/Desktop, Stacked on Mobile) -->
            <div class="hero-row-container">

                <!-- LEFT COLUMN: Media Gallery (576px wide matching Figma Node 601:3570) -->
                <div class="hero-left-column" style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
                    
                    <!-- Main Image / Video Banner (Figma Node 856:1782 - 576x576px aspect 1:1 with rounded-2xl) -->
                    <div id="main-media-container"
                         class="relative w-full aspect-square bg-[#F4F4F5] rounded-2xl overflow-hidden border border-[#E4E4E7] flex items-center justify-center"
                         style="border-radius: 16px; background: #F4F4F5; border: 1px solid #E4E4E7; position: relative; aspect-ratio: 1 / 1; width: 100%; max-height: 576px;">
                        
                        @php $allMedia = $product->media; @endphp

                        <!-- Terpopuler Badge (Figma Node 856:1791 - top 17.6px, left 17.6px) -->
                        @if($product->is_popular ?? true)
                            <div style="position: absolute; top: 12px; left: 12px; padding: 4px 10px; background-color: #FEFCE8; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; z-index: 10;">
                                <img src="{{ asset('images/marketplace/popular_badge_icon.svg') }}" alt="Popular Icon" style="width: 14px; height: 14px; flex-shrink: 0;" />
                                <span style="color: #D08700; font-size: 14px; font-family: 'DM Sans', sans-serif; font-weight: 500; line-height: 20px; white-space: nowrap;">Terpopuler</span>
                            </div>
                        @endif

                        @if($allMedia->isEmpty())
                            <!-- Fallback image -->
                            <img id="main-media" 
                                 src="{{ $product->primaryImage ? $product->primaryImage->publicUrl() : 'https://placehold.co/576x576' }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover media-fade"
                                 style="width: 100%; height: 100%; object-fit: cover;" />
                        @else
                            @php $first = $allMedia->first(); @endphp
                            @if($first->media_type === 'video')
                                <video id="main-media" src="{{ $first->publicUrl() }}" controls class="w-full h-full object-cover media-fade" style="width: 100%; height: 100%; object-fit: cover;"></video>
                            @else
                                <img id="main-media" src="{{ $first->publicUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover media-fade" style="width: 100%; height: 100%; object-fit: cover;" />
                            @endif
                        @endif
                    </div>

                    <!-- Thumbnails Row (Figma Node 601:3577 - 81.23x80px rounded-lg) -->
                    @if($allMedia->count() > 1)
                        <div class="flex gap-3 overflow-x-auto pb-1" style="display: flex; gap: 12px; width: 100%;">
                            @foreach($allMedia as $i => $media)
                                <button type="button" 
                                        data-src="{{ $media->publicUrl() }}" 
                                        data-type="{{ $media->media_type }}"
                                        class="thumb-item flex-shrink-0 bg-[#F4F4F5] overflow-hidden flex items-center justify-center rounded-lg border border-[#E4E4E7] cursor-pointer transition-all {{ $i === 0 ? 'active' : '' }}"
                                        style="width: 81.23px; height: 80px; border-radius: 8px; border: 1px solid #E4E4E7; padding: 0; background: #F4F4F5;"
                                        onclick="switchMedia(this)">
                                    @if($media->media_type === 'video')
                                        <div class="flex items-center justify-center w-full h-full text-[#71717A]">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </div>
                                    @else
                                        <img src="{{ $media->publicUrl() }}" alt="{{ $product->name }} thumbnail {{ $i + 1 }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- RIGHT COLUMN: Product Details -->
                <div class="hero-right-column">
                    
                    <!-- Top & Middle Content Container (Figma Node 850:1768 - Gap 20px) -->
                    <div style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
                        
                        <!-- Header Group: Category, Title, Price with Bottom Divider (Figma Node 601:3585) -->
                        <div style="padding-bottom: 20px; border-bottom: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 10px; width: 100%;">
                            <!-- Category Badge -->
                            <span style="color: #71717A; font-size: 12px; font-family: 'DM Sans', sans-serif; text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ strtoupper($product->category?->name ?? 'TECH') }}
                            </span>

                            <!-- Product Name (Responsive Font Size: 28px on mobile, 40px on desktop) -->
                            <h1 class="product-title-heading">
                                {{ $product->name }}
                            </h1>

                            <!-- Price (Heading 5: 24px / 28px / -0.005em) -->
                            <div style="color: #141414; font-size: 22px; font-weight: 500; font-family: 'Bricolage Grotesque', sans-serif; line-height: 28px; letter-spacing: -0.005em;">
                                Rp{{ number_format($product->price < 1000 ? $product->price * 1000 : $product->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Description Group with Expand / Truncate Toggle -->
                        <div x-data="{ expanded: false }" style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                            <h3 style="color: #141414; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; line-height: 24px; margin: 0;">
                                Deskripsi
                            </h3>
                            
                            <!-- Description Text with 3-line Clamp Toggle -->
                            <div style="position: relative; width: 100%;">
                                <div :style="expanded ? 'max-height: none;' : 'display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; max-height: 84px;'"
                                     style="color: #71717A; font-size: 15px; font-family: 'DM Sans', sans-serif; line-height: 28px; transition: all 0.3s ease;">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>

                            <!-- "Baca selengkapnya" Toggle Button (Only if description > 100 chars) -->
                            @if(strlen(strip_tags($product->description)) > 100)
                                <button @click="expanded = !expanded"
                                        type="button"
                                        style="color: #00BBA7; font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif; background: none; border: none; padding: 0; cursor: pointer; text-align: left; margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                    <span x-text="expanded ? 'Sembunyikan' : 'Baca selengkapnya'">Baca selengkapnya</span>
                                    <svg style="width: 14px; height: 14px; transition: transform 0.2s;" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                    </div>

                    <!-- Bottom Action Group: Buy Button & Anchored Popover Overlay -->
                    <div x-data="{ buyModalOpen: false }" class="hero-action-container">
                        
                        @if($product->ecommerceLinks->count() > 1)
                            <!-- Backdrop Overlay for clicking outside / dimming backdrop -->
                            <div x-show="buyModalOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 @click="buyModalOpen = false"
                                 style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 40; background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
                                 x-cloak>
                            </div>

                            <!-- Popover Card Box Anchored Directly ABOVE the Buy Button -->
                            <div x-show="buyModalOpen"
                                 @click.away="buyModalOpen = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-3"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-3"
                                 style="position: absolute; bottom: 100%; left: 0; right: 0; margin-bottom: 12px; z-index: 50; background: #FFFFFF; border-radius: 20px; border: 1px solid #E4E4E7; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.18); overflow: hidden; width: 100%; box-sizing: border-box;"
                                 x-cloak>
                                
                                <!-- Popover Header -->
                                <div style="padding: 16px 20px 12px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #F4F4F5;">
                                    <span style="color: #71717A; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 400; line-height: 20px;">
                                        Pilih platform pembelian
                                    </span>
                                    <button @click="buyModalOpen = false" 
                                            style="width: 28px; height: 28px; border-radius: 9999px; background: #F4F4F5; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; color: #71717A; transition: background 0.15s ease;"
                                            title="Tutup">
                                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Platform Option List -->
                                <div style="display: flex; flex-direction: column; width: 100%;">
                                    @foreach($product->ecommerceLinks as $index => $link)
                                        @php
                                            $eName = strtolower($link->ecommerce_name);
                                            $name = match($eName) {
                                                'tokopedia' => 'Tokopedia',
                                                'shopee'    => 'Shopee',
                                                'tiktok', 'tiktok shop', 'tiktokshop' => 'TikTok Shop',
                                                default     => ucfirst($link->ecommerce_name),
                                            };
                                            
                                            $bgColor = match($eName) {
                                                'tokopedia' => '#42B549',
                                                'shopee'    => '#EE4D2D',
                                                'tiktok', 'tiktok shop', 'tiktokshop' => '#18181B',
                                                default     => '#00BBA7',
                                            };
                                        @endphp

                                        <a href="{{ $link->url }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           @click="buyModalOpen = false"
                                           class="platform-modal-item"
                                           style="display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; text-decoration: none; border-bottom: {{ $index === $product->ecommerceLinks->count() - 1 ? 'none' : '1px solid #F4F4F5' }}; transition: background 0.15s ease; width: 100%; box-sizing: border-box;">
                                            
                                            <!-- Left Group: Brand Icon + Platform Name + Price -->
                                            <div style="display: flex; align-items: center; gap: 14px;">
                                                <!-- Platform Icon Box -->
                                                <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $bgColor }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                                                    @if($eName === 'tokopedia')
                                                        <svg style="width: 22px; height: 22px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                        </svg>
                                                    @elseif($eName === 'shopee')
                                                        <svg style="width: 22px; height: 22px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                        </svg>
                                                    @elseif(in_array($eName, ['tiktok', 'tiktok shop', 'tiktokshop']))
                                                        <svg style="width: 18px; height: 18px; fill: #FFFFFF;" viewBox="0 0 24 24">
                                                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06Z" />
                                                        </svg>
                                                    @else
                                                        <svg style="width: 22px; height: 22px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                        </svg>
                                                    @endif
                                                </div>

                                                <!-- Text Details -->
                                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                                    <span style="color: #141414; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500; line-height: 20px;">
                                                        {{ $name }}
                                                    </span>
                                                    <span style="color: #A1A1AA; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 400; line-height: 18px;">
                                                        Rp{{ number_format($product->price < 1000 ? $product->price * 1000 : $product->price, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Right External Link Icon -->
                                            <div style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #141414;">
                                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                            </div>

                            <!-- Multi Links -> Toggle Popover -->
                            <button @click="buyModalOpen = !buyModalOpen"
                                    style="width: 100%; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; padding: 16px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; border: none; cursor: pointer; transition: background 0.15s ease; position: relative; z-index: 45;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <svg style="width: 24px; height: 24px; color: #FFFFFF; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span style="color: #FFFFFF; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Beli sekarang</span>
                                </div>
                                <svg style="width: 16px; height: 16px; color: #FFFFFF; flex-shrink: 0; transition: transform 0.2s;" :class="buyModalOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        @elseif($product->ecommerceLinks->count() === 1)
                            <!-- Condition B: Product has EXACTLY 1 link -> Direct link to platform -->
                            @php $singleLink = $product->ecommerceLinks->first(); @endphp
                            <a href="{{ $singleLink->url }}" target="_blank" rel="noopener noreferrer"
                               style="width: 100%; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; padding: 16px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; text-decoration: none; box-sizing: border-box; transition: background 0.15s ease;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <svg style="width: 24px; height: 24px; color: #FFFFFF; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span style="color: #FFFFFF; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Beli sekarang</span>
                                </div>
                                <svg style="width: 16px; height: 16px; color: #FFFFFF; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @else
                            <!-- Condition C: No link available -->
                            <button disabled style="width: 100%; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; padding: 16px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; border: none; cursor: not-allowed; opacity: 0.85;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <svg style="width: 24px; height: 24px; color: #FFFFFF; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span style="color: #FFFFFF; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Beli sekarang</span>
                                </div>
                                <svg style="width: 16px; height: 16px; color: #FFFFFF; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        @endif

                        <!-- Disclaimer text matching Figma Node 601:3657 -->
                        <p style="color: #A1A1AA; font-size: 12px; font-family: 'DM Sans', sans-serif; line-height: 16px; text-align: center; margin: 0;">
                            Kamu akan diarahkan ke platform penjual pilihan
                        </p>
                    </div>

                </div>

            </div>

            <!-- Bottom Section: Produk Lainnya (Figma Node 601:3658 - 4 Columns on Desktop >= 1024px, 2 Columns on Tablet/Mobile < 1024px) -->
            @php
                $related = \App\Models\Product::select(['id', 'name', 'slug', 'price', 'description', 'product_category_id'])
                    ->with([
                        'category',
                        'primaryImage' => fn($q) => $q->select(['product_media.id', 'product_media.product_id', 'product_media.media_url'])
                    ])
                    ->where('is_published', true)
                    ->where('id', '!=', $product->id)
                    ->limit(4)
                    ->get();
            @endphp

            @if($related->isNotEmpty())
                <div style="padding-top: 32px; border-top: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 20px; width: 100%;">
                    
                    <!-- Section Title -->
                    <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 24px; font-weight: 500; line-height: 28px; letter-spacing: -0.005em; color: #141414; margin: 0;">
                        Produk Lainnya
                    </h2>

                    <!-- Cards Grid (4 Columns on Large Desktop >= 1024px, 2 Columns on Tablet & Mobile < 1024px) -->
                    <div class="related-products-grid">
                        @foreach($related as $index => $rel)
                            <a href="{{ route('marketplace.detail', $rel->slug) }}" 
                               class="group bg-white rounded-2xl p-3 pb-4 border border-[#E4E4E7] shadow-xs flex flex-col justify-between gap-3 transition-all duration-300 hover:shadow-md hover:no-underline cursor-pointer block"
                               style="display: flex; flex-direction: column; justify-content: space-between; gap: 12px; background: #FFF; border-radius: 16px; border: 1px solid #E4E4E7; padding: 12px 12px 16px; box-sizing: border-box; width: 100%; text-decoration: none;">
                                
                                <!-- Card Media Banner -->
                                <div class="marketplace-card-banner">
                                    <img src="{{ $rel->primaryImage ? $rel->primaryImage->publicUrl() : 'https://placehold.co/264x160' }}" 
                                         alt="{{ $rel->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" 
                                         class="group-hover:scale-105" />
                                    
                                    @if($rel->is_popular ?? ($index === 0 || $index === 1))
                                        <div style="position: absolute; top: 8px; left: 8px; padding: 4px 6px; background-color: #FEF9C2; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; z-index: 10;">
                                            <img src="{{ asset('images/marketplace/popular_badge_icon.svg') }}" alt="Popular Icon" style="width: 16px; height: 16px; flex-shrink: 0;" />
                                            <span style="color: #A65F00; font-size: 12px; font-family: 'DM Sans', sans-serif; font-weight: 400; line-height: 16px; white-space: nowrap;">Terpopuler</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Info -->
                                <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                                    <!-- Category Badge -->
                                    <span style="color: #71717A; font-size: 12px; font-family: 'DM Sans', sans-serif; text-transform: uppercase; letter-spacing: 0.05em;">
                                        {{ strtoupper($rel->category?->name ?? 'TECH') }}
                                    </span>
                                    
                                    <!-- Title & Description Container with Bottom Divider -->
                                    <div style="padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 4px; width: 100%;">
                                        <h3 style="color: #141414; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; line-height: 28px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $rel->name }}
                                        </h3>
                                        <p style="color: #71717A; font-size: 15px; font-family: 'DM Sans', sans-serif; line-height: 28px; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 56px;">
                                            {{ Str::limit(strip_tags($rel->description), 120) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Card Footer (Price) -->
                                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 4px; width: 100%;">
                                    <span style="color: #171717; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">
                                        Rp{{ number_format($rel->price < 1000 ? $rel->price * 1000 : $rel->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

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
                el.className = 'media-fade w-full h-full object-cover';
            } else {
                el = document.createElement('img');
                el.src = src;
                el.alt = '{{ addslashes($product->name) }}';
                el.className = 'media-fade w-full h-full object-cover';
            }
            el.id = 'main-media';
            container.appendChild(el);

            // Update active thumbnail
            document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }
    </script>
@endsection
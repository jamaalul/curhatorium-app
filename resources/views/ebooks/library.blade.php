@extends('layouts.dashboard')

@section('title', 'Ebook Saya | Curhatorium')

@section('bodyClass', 'pt-16 w-full bg-[#F4F4F5]')

@section('head')
    <meta name="description" content="Koleksi Ebook Saya - Curhatorium">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F4F4F5 !important;
        }

        /* Library Page Header Title & Subtitle */
        .library-header-title {
            font-family: 'Bricolage Grotesque', sans-serif !important;
            font-size: 28px !important;
            line-height: 36px !important;
            font-weight: 600 !important;
            letter-spacing: -0.015em !important;
            color: #18181B !important;
            margin: 0 !important;
        }

        @media (min-width: 640px) {
            .library-header-title {
                font-size: 36px !important;
                line-height: 44px !important;
            }
        }

        .library-header-subtitle {
            font-family: 'DM Sans', sans-serif !important;
            font-size: 14px !important;
            line-height: 20px !important;
            font-weight: 500 !important;
            color: #71717A !important;
            margin: 0 !important;
        }

        @media (min-width: 640px) {
            .library-header-subtitle {
                font-size: 16px !important;
                line-height: 24px !important;
            }
        }

        /* Library Card Aspect & Banner Specs */
        .library-card-banner {
            width: 100% !important;
            height: 208px !important;
            position: relative !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            background: #F4F4F5 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        /* Filter Pill Button */
        .library-filter-pill {
            padding: 10px 20px !important;
            border-radius: 9999px !important;
            font-family: 'DM Sans', sans-serif !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
            text-decoration: none !important;
        }

        .library-filter-pill.active {
            background-color: #00BBA7 !important;
            color: #FFFFFF !important;
        }

        .library-filter-pill.inactive {
            background-color: #FFFFFF !important;
            color: #71717A !important;
        }

        .library-filter-pill.inactive:hover {
            background-color: #E4E4E7 !important;
            color: #18181B !important;
        }

        /* Explicit Grid Rule: 4 columns desktop, 2 columns tablet, 1 column mobile */
        .library-cards-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 24px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        @media (max-width: 1023px) {
            .library-cards-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 16px !important;
            }
        }

        @media (max-width: 639px) {
            .library-cards-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
                gap: 16px !important;
            }
        }

        /* Responsive Toolbar: 1 Row Space Between on Wide Screens, Stacks on Narrow */
        .library-toolbar-container {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 16px !important;
            width: 100% !important;
        }

        .library-toolbar-pills {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
            order: 1 !important;
        }

        .library-toolbar-search {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            background: #FFFFFF !important;
            border-radius: 9999px !important;
            border: 1px solid #E4E4E7 !important;
            padding: 10px 20px !important;
            width: 100% !important;
            max-width: 320px !important;
            box-sizing: border-box !important;
            order: 2 !important;
        }

        @media (max-width: 767px) {
            .library-toolbar-container {
                flex-direction: column !important;
            }
            .library-toolbar-search {
                order: 1 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .library-toolbar-pills {
                order: 2 !important;
                width: 100% !important;
            }
        }

        /* Pagination Explicit CSS Rules (Matching Ebook & Marketplace Index) */
        .marketplace-pagination-container {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 16px !important;
            margin-top: 40px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .marketplace-pagination-btn {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            min-height: 36px !important;
            max-width: 36px !important;
            max-height: 36px !important;
            border-radius: 9999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1px solid #E4E4E7 !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }
        .marketplace-pagination-numbers {
            display: inline-flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 16px !important;
            flex-shrink: 0 !important;
        }
        .marketplace-pagination-num-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-family: 'DM Sans', sans-serif !important;
            font-size: 16px !important;
            font-weight: 500 !important;
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
@endsection

@php
    $ordersData = $orders->map(function($order) {
        $ebook = $order->orderable;
        if (!$ebook) return null;

        $coverUrl = null;
        if ($ebook->cover_image) {
            if (str_starts_with($ebook->cover_image, 'http://') || str_starts_with($ebook->cover_image, 'https://')) {
                $coverUrl = $ebook->cover_image;
            } else {
                $coverUrl = \Illuminate\Support\Facades\Storage::url($ebook->cover_image);
            }
        }

        $prog = $ebook->progress->first();
        $lastPage = $prog ? (int)$prog->last_page : 0;
        $totalPages = $ebook->page_count ? (int)$ebook->page_count : 1;
        if ($lastPage > $totalPages) $lastPage = $totalPages;

        $percent = $totalPages > 0 ? min(100, round(($lastPage / $totalPages) * 100)) : 0;

        $status = 'unread'; // 'unread', 'reading', 'completed'
        if ($lastPage >= $totalPages && $totalPages > 0) {
            $status = 'completed';
        } elseif ($lastPage > 0) {
            $status = 'reading';
        }

        return [
            'order_id' => $order->id,
            'ebook_id' => $ebook->id,
            'title' => $ebook->title,
            'slug' => $ebook->slug,
            'category_name' => strtoupper($ebook->category?->name ?? 'REFLEKSI'),
            'cover_url' => $coverUrl,
            'last_page' => $lastPage,
            'total_pages' => $totalPages,
            'percent' => $percent,
            'status' => $status,
            'purchased_at' => $order->created_at ? $order->created_at->format('d M Y') : '',
            'read_url' => route('ebooks.read', $ebook),
            'show_url' => route('ebooks.show', $ebook),
        ];
    })->filter()->values();
@endphp

@section('dashboard-content')
    <!-- Main Max-Width Wrapper (1200px) -->
    <div x-data="libraryComponent(@js($ordersData))" class="w-full bg-[#F4F4F5] min-h-screen py-6 sm:py-10" style="background-color: #F4F4F5; position: relative;">
        
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8 sm:gap-10" style="max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 16px; padding-right: 16px; display: flex; flex-direction: column; gap: 32px;">
            
            <!-- Page Header Section -->
            <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                <h1 class="library-header-title">
                    Ebook Curhatorium
                </h1>
                <p class="library-header-subtitle">
                    Pilih bacaan digital untuk mendukung proses refleksi dan pengembangan diri.
                </p>
            </div>

            <!-- Sticky Search & Status Filter Toolbar ONLY -->
            <template x-if="items.length > 0">
                <div style="position: sticky; top: 64px; z-index: 30; background-color: #F4F4F5; padding-top: 12px; padding-bottom: 16px; border-bottom: 1px solid #E4E4E7; width: 100%;">
                    
                    <div class="library-toolbar-container">
                        
                        <!-- Status Filter Pills (Left on Wide Screen, Bottom on Small Screen) -->
                        <div class="library-toolbar-pills">
                            <button @click="statusTab = 'all'; currentPage = 1;" 
                                    class="library-filter-pill" 
                                    :class="statusTab === 'all' ? 'active' : 'inactive'">
                                Semua
                            </button>
                            <button @click="statusTab = 'reading'; currentPage = 1;" 
                                    class="library-filter-pill" 
                                    :class="statusTab === 'reading' ? 'active' : 'inactive'">
                                Sedang dibaca
                            </button>
                            <button @click="statusTab = 'completed'; currentPage = 1;" 
                                    class="library-filter-pill" 
                                    :class="statusTab === 'completed' ? 'active' : 'inactive'">
                                Selesai
                            </button>
                        </div>

                        <!-- Search Input Container (Right on Wide Screen, Top Full-Width on Small Screen) -->
                        <div class="library-toolbar-search">
                            <svg style="width: 20px; height: 20px; color: #A1A1AA; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" 
                                   x-model="searchQuery" 
                                   @input="currentPage = 1"
                                   placeholder="Cari di koleksimu..." 
                                   style="border: none; background: transparent; font-family: 'DM Sans', sans-serif; font-size: 15px; color: #18181B; width: 100%; outline: none; padding: 0;" />
                            <button x-show="searchQuery.length > 0" 
                                    @click="searchQuery = ''; currentPage = 1;" 
                                    type="button" 
                                    style="background: none; border: none; padding: 0; color: #A1A1AA; cursor: pointer; display: flex; align-items: center;">
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                    </div>

                </div>
            </template>

            <!-- Library Cards Grid (Explicit 4 Cols Desktop, 2 Cols Tablet, 1 Col Mobile) -->
            <template x-if="filteredItems.length > 0">
                <div class="library-cards-grid">
                    
                    <template x-for="item in paginatedItems" :key="item.order_id">
                        <div class="bg-white rounded-xl p-3 border border-[#E4E4E7] shadow-xs flex flex-col justify-between gap-3 transition-all duration-300 hover:shadow-md box-border w-full"
                             style="display: flex; flex-direction: column; justify-content: space-between; gap: 12px; background: #FFFFFF; border-radius: 12px; border: 1px solid #E4E4E7; padding: 12px; box-sizing: border-box; width: 100%;">
                            
                            <!-- Ebook Cover Banner Container -->
                            <div class="library-card-banner">
                                <template x-if="item.cover_url">
                                    <img :src="item.cover_url" :alt="item.title" style="width: 100%; height: 100%; object-fit: contain; padding: 8px; box-sizing: border-box;" />
                                </template>
                                <template x-if="!item.cover_url">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; color: #A1A1AA;">
                                        <svg style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                </template>

                                <!-- Status Badges (Top Left of Image) -->
                                <!-- Variant 1: Selesai -->
                                <template x-if="item.status === 'completed'">
                                    <div style="position: absolute; top: 8px; left: 8px; padding: 4px 8px; background-color: #DCFCE7; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px; z-index: 10;">
                                        <svg style="width: 12px; height: 12px; color: #00C950; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span style="color: #00C950; font-size: 11px; font-family: 'DM Sans', sans-serif; font-weight: 500; line-height: 14px; white-space: nowrap;">Selesai</span>
                                    </div>
                                </template>

                                <!-- Variant 2: Sedang dibaca -->
                                <template x-if="item.status === 'reading'">
                                    <div style="position: absolute; top: 8px; left: 8px; padding: 4px 8px; background-color: #CBFBF1; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px; z-index: 10;">
                                        <svg style="width: 12px; height: 12px; color: #00BBA7; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                        </svg>
                                        <span style="color: #00BBA7; font-size: 11px; font-family: 'DM Sans', sans-serif; font-weight: 500; line-height: 14px; white-space: nowrap;">Sedang dibaca</span>
                                    </div>
                                </template>

                                <!-- Variant 3: Belum dibaca -->
                                <template x-if="item.status === 'unread'">
                                    <div style="position: absolute; top: 8px; left: 8px; padding: 4px 8px; background-color: #E5E7EB; border-radius: 9999px; display: inline-flex; align-items: center; gap: 4px; z-index: 10;">
                                        <span style="color: #030712; font-size: 11px; font-family: 'DM Sans', sans-serif; font-weight: 500; line-height: 14px; white-space: nowrap;">Belum dibaca</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Card Body Content -->
                            <div style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
                                <!-- Info Titles -->
                                <div style="display: flex; flex-direction: column; gap: 4px; width: 100%;">
                                    <span style="color: #71717A; font-size: 12px; font-family: 'DM Sans', sans-serif; font-weight: 400; text-transform: uppercase; letter-spacing: 0.05em;" x-text="item.category_name"></span>
                                    <h3 style="color: #111827; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; line-height: 24px; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 48px;" x-text="item.title"></h3>
                                </div>

                                <!-- Progress Bar & Metadata Block -->
                                <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                                    <!-- Progress Track Line -->
                                    <div style="width: 100%; height: 8px; background-color: #E4E4E7; border-radius: 9999px; overflow: hidden; position: relative;">
                                        <div style="height: 100%; background-color: #00BBA7; border-radius: 9999px; transition: width 0.4s ease;" 
                                             :style="{ width: (item.percent || 0) + '%' }"></div>
                                    </div>

                                    <!-- Meta Info Row -->
                                    <div style="display: flex; align-items: center; gap: 6px; color: #71717A; font-family: 'DM Sans', sans-serif; font-size: 13px; line-height: 18px; font-weight: 500;">
                                        <span x-text="item.last_page + '/' + item.total_pages + ' halaman'"></span>
                                        <span style="color: #A1A1AA; margin: 0 2px;">•</span>
                                        <span x-text="'dibeli ' + item.purchased_at"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Action Button -->
                            <div style="width: 100%; margin-top: 4px;">
                                <a :href="item.read_url" 
                                   style="display: flex; justify-content: center; align-items: center; width: 100%; background: #FFFFFF; border: 1px solid #00BBA7; color: #00BBA7; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500; padding: 10px; border-radius: 8px; text-decoration: none; transition: all 0.15s ease; box-sizing: border-box;"
                                   class="hover:bg-[#F0FDF4] hover:no-underline">
                                    <span x-text="item.status === 'completed' ? 'Baca ulang' : (item.status === 'reading' ? 'Lanjut baca' : 'Mulai baca')"></span>
                                </a>
                            </div>

                        </div>
                    </template>

                </div>
            </template>

            <!-- Condition 1: No Ebooks In Library At All (items.length === 0) -->
            <template x-if="items.length === 0">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; padding: 48px 0 80px; text-align: center; box-sizing: border-box;">
                    <img src="{{ asset('images/marketplace/empty_products_illustration.svg') }}" alt="Koleksi Ebook Kosong" style="width: 215px; height: 223px; object-fit: contain; margin-bottom: 32px;" />
                    
                    <div style="display: flex; flex-direction: column; gap: 12px; align-items: center; justify-content: center; max-width: 480px;">
                        <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 32px; font-weight: 600; line-height: 36px; letter-spacing: -0.01em; color: #18181B; margin: 0;">
                            Koleksi ebook belum tersedia
                        </h2>
                        <p style="font-family: 'DM Sans', sans-serif; font-size: 20px; font-weight: 500; line-height: 36px; color: #71717A; margin: 0;">
                            Kamu belum memiliki ebook. Jelajahi katalog kami untuk menemukan bacaan favoritmu!
                        </p>
                        <a href="{{ route('ebooks.index') }}" 
                           style="background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; padding: 14px 32px; border-radius: 9999px; text-decoration: none; margin-top: 16px; transition: background 0.15s ease;"
                           class="hover:bg-[#009B8A] hover:no-underline">
                            Jelajahi Ebook
                        </a>
                    </div>
                </div>
            </template>

            <!-- Condition 2: Filter/Search Returns 0 Results (items.length > 0 && filteredItems.length === 0) -->
            <template x-if="items.length > 0 && filteredItems.length === 0">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; padding: 48px 0 80px; text-align: center; box-sizing: border-box;">
                    <img src="{{ asset('images/marketplace/empty_products_illustration.svg') }}" alt="Ebook Tidak Ditemukan" style="width: 215px; height: 223px; object-fit: contain; margin-bottom: 32px;" />
                    
                    <div style="display: flex; flex-direction: column; gap: 12px; align-items: center; justify-content: center; max-width: 480px;">
                        <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 32px; font-weight: 600; line-height: 36px; letter-spacing: -0.01em; color: #18181B; margin: 0;">
                            Ebook tidak ditemukan
                        </h2>
                        <p style="font-family: 'DM Sans', sans-serif; font-size: 20px; font-weight: 500; line-height: 36px; color: #71717A; margin: 0;">
                            Tidak ada ebook yang sesuai dengan filter atau kata kunci pencarianmu.
                        </p>
                        <button @click="statusTab = 'all'; searchQuery = ''; currentPage = 1;" 
                                type="button"
                                style="background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 500; padding: 14px 32px; border-radius: 9999px; border: none; cursor: pointer; margin-top: 16px; transition: background 0.15s ease;"
                                class="hover:bg-[#009B8A]">
                            Atur Ulang Filter
                        </button>
                    </div>
                </div>
            </template>

            <!-- Pagination Bar (Matching Ebook Index & Marketplace Index) -->
            <div class="marketplace-pagination-container" x-show="filteredItems.length > 0 && totalPages > 1" x-cloak>
                <!-- Prev Button -->
                <button @click="if(currentPage > 1) { currentPage--; window.scrollTo({top: 0, behavior: 'smooth'}); }" 
                        :disabled="currentPage === 1"
                        class="marketplace-pagination-btn"
                        :style="currentPage === 1 ? 'background: #FAFAFA !important; color: #A1A1AA !important; cursor: not-allowed !important; opacity: 0.6 !important;' : 'background: #FFFFFF !important; color: #000000 !important;'">
                    <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Page Numbers -->
                <div class="marketplace-pagination-numbers">
                    <template x-for="p in getPageRange()" :key="p">
                        <button @click="if(p !== '...') { currentPage = p; window.scrollTo({top: 0, behavior: 'smooth'}); }"
                                class="marketplace-pagination-num-btn"
                                :style="currentPage === p ? 'color: #00BBA7 !important; text-decoration: underline !important; text-underline-offset: 4px !important; font-weight: 600 !important;' : (p === '...' ? 'color: #71717A !important; cursor: default !important;' : 'color: #71717A !important;')"
                                :disabled="p === '...'">
                            <span x-text="p"></span>
                        </button>
                    </template>
                </div>

                <!-- Next Button -->
                <button @click="if(currentPage < totalPages) { currentPage++; window.scrollTo({top: 0, behavior: 'smooth'}); }" 
                        :disabled="currentPage === totalPages"
                        class="marketplace-pagination-btn"
                        :style="currentPage === totalPages ? 'background: #FAFAFA !important; color: #A1A1AA !important; cursor: not-allowed !important; opacity: 0.6 !important;' : 'background: #FFFFFF !important; color: #000000 !important;'">
                    <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

        </div>

    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('libraryComponent', (initialItems) => ({
            items: initialItems,
            statusTab: 'all',
            searchQuery: '',
            currentPage: 1,
            itemsPerPage: 12,

            get filteredItems() {
                let res = [...this.items];

                if (this.statusTab !== 'all') {
                    res = res.filter(item => item.status === this.statusTab);
                }

                if (this.searchQuery.trim().length > 0) {
                    const q = this.searchQuery.toLowerCase().trim();
                    res = res.filter(item => 
                        item.title.toLowerCase().includes(q) || 
                        item.category_name.toLowerCase().includes(q)
                    );
                }

                return res;
            },

            get paginatedItems() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredItems.slice(start, start + this.itemsPerPage);
            },

            get totalPages() {
                return Math.ceil(this.filteredItems.length / this.itemsPerPage) || 1;
            },

            getPageRange() {
                let range = [];
                let total = this.totalPages;
                let current = this.currentPage;
                
                if (total <= 6) {
                    for (let i = 1; i <= total; i++) range.push(i);
                } else {
                    if (current <= 4) {
                        range = [1, 2, 3, 4, '...', total];
                    } else if (current >= total - 3) {
                        range = [1, '...', total - 3, total - 2, total - 1, total];
                    } else {
                        range = [1, '...', current - 1, current, current + 1, '...', total];
                    }
                }
                return range;
            }
        }));
    });
</script>
@endsection
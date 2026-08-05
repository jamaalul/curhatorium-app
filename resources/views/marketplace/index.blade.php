@extends('layouts.dashboard')

@section('title', 'Marketplace | Curhatorium')

@section('bodyClass', 'pt-16 w-full bg-[#F4F4F5]')

@section('head')
    <meta name="description" content="Curhatorium Marketplace">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Geist:wght@100..900&family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F4F4F5 !important;
        }

        .marketplace-main-layout {
            display: flex;
            flex-direction: column;
            gap: 24px;
            align-items: flex-start;
            width: 100%;
            box-sizing: border-box;
        }

        /* Large Desktop Views (>= 1024px): Show 282px Sticky Sidebar + 3 Product Columns */
        @media (min-width: 1024px) {
            .marketplace-main-layout {
                flex-direction: row !important;
                align-items: flex-start !important;
            }
            .marketplace-sidebar-container {
                display: block !important;
                width: 282px !important;
                min-width: 282px !important;
                max-width: 282px !important;
                flex: 0 0 282px !important;
                position: sticky !important;
                top: 84px !important;
                align-self: flex-start !important;
                max-height: calc(100vh - 100px) !important;
                overflow-y: auto !important;
                z-index: 30 !important;
            }
            .marketplace-products-container {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                width: 100% !important;
            }
            .marketplace-products-grid {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 24px !important;
                width: 100% !important;
            }
        }

        /* Tablet / Medium Desktop Views (768px - 1023px): 240px Sticky Sidebar + 2 Spacious Product Columns */
        @media (min-width: 768px) and (max-width: 1023px) {
            .marketplace-main-layout {
                flex-direction: row !important;
                gap: 20px !important;
                align-items: flex-start !important;
            }
            .marketplace-sidebar-container {
                display: block !important;
                width: 240px !important;
                min-width: 240px !important;
                max-width: 240px !important;
                flex: 0 0 240px !important;
                position: sticky !important;
                top: 84px !important;
                align-self: flex-start !important;
                max-height: calc(100vh - 100px) !important;
                overflow-y: auto !important;
                z-index: 30 !important;
            }
            .marketplace-products-container {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                width: 100% !important;
            }
            .marketplace-products-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 16px !important;
                width: 100% !important;
            }
        }

        /* Mobile Views (< 768px): Hide Sidebar, Use Filter Drawer, 2 Product Columns */
        @media (max-width: 767px) {
            .marketplace-sidebar-container {
                display: none !important;
            }
            .marketplace-products-container {
                width: 100% !important;
            }
            .marketplace-products-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 12px !important;
                width: 100% !important;
            }
        }

        /* Silky Smooth iOS Bottom Sheet Animations */
        .sheet-slide-enter-active {
            transition: transform 380ms cubic-bezier(0.32, 0.72, 0, 1) !important;
            will-change: transform !important;
        }
        .sheet-slide-leave-active {
            transition: transform 280ms cubic-bezier(0.32, 0.72, 0, 1) !important;
            will-change: transform !important;
        }
        .sheet-slide-enter-start {
            transform: translateY(100%) !important;
        }
        .sheet-slide-enter-end {
            transform: translateY(0) !important;
        }
        .sheet-slide-leave-start {
            transform: translateY(0) !important;
        }
        .sheet-slide-leave-end {
            transform: translateY(100%) !important;
        }

        .backdrop-fade-enter-active {
            transition: opacity 350ms ease-out !important;
        }
        .backdrop-fade-leave-active {
            transition: opacity 250ms ease-in !important;
        }
        .backdrop-fade-enter-start {
            opacity: 0 !important;
        }
        .backdrop-fade-enter-end {
            opacity: 1 !important;
        }
        .backdrop-fade-leave-start {
            opacity: 1 !important;
        }
        .backdrop-fade-leave-end {
            opacity: 0 !important;
        }

        .handle-bar-area:hover .handle-bar-pill {
            background-color: #A1A1AA !important;
        }

        /* Active Filter Pills Spacing */
        .active-pills-wrapper {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 10px 8px !important;
            margin-top: 10px !important;
            margin-bottom: 4px !important;
            padding-top: 10px !important;
            padding-bottom: 16px !important;
            border-bottom: 1px solid #E4E4E7 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .active-filter-pill-item {
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            color: #18181B !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            font-family: 'DM Sans', sans-serif !important;
            cursor: pointer !important;
            background: #F4F4F5 !important;
            padding: 6px 14px !important;
            border-radius: 9999px !important;
            gap: 6px !important;
            border: 1px solid #E4E4E7 !important;
            margin-right: 6px !important;
            margin-bottom: 6px !important;
            box-sizing: border-box !important;
        }
    </style>
@endsection

@php
    $productsData = $products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'slug' => $p->slug,
        'description' => Str::limit(strip_tags($p->description), 120),
        'price' => (float) $p->price,
        'category_name' => strtoupper($p->category?->name ?? 'TECH'),
        'category_slug' => $p->category?->slug ?? 'tech',
        'image_url' => $p->primaryImage ? $p->primaryImage->publicUrl() : 'https://placehold.co/264x198',
    ])->values();

    $categoriesData = $categories->map(fn($c) => [
        'name' => $c->name,
        'slug' => $c->slug
    ])->values();
@endphp

@section('dashboard-content')
    <!-- Alpine.js State Wrapper (Root Level) -->
    <div x-data="marketplaceComponent(@js($productsData), @js($categoriesData))" class="w-full bg-[#F4F4F5] min-h-screen py-10" style="background-color: #F4F4F5; position: relative;">
        
        <!-- Main Max-Width Container (Matching Figma Node 977:4132 - width 1200px, gap 64px) -->
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-16 md:gap-20" style="max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 24px; padding-right: 24px; display: flex; flex-direction: column; gap: 64px;">
            
            <!-- Header Section Partial -->
            @include('marketplace.partials.header')

            <!-- Condition 1: Products Exist -> Show Sidebar & Products Grid -->
            <template x-if="products.length > 0">
                <div class="marketplace-main-layout">
                    <!-- Filters Sidebar Partial (Desktop & Tablet >= 768px) -->
                    @include('marketplace.partials.sidebar')

                    <!-- Products Grid & Toolbar Partial -->
                    @include('marketplace.partials.products')
                </div>
            </template>

            <!-- Condition 2: No Products Exist At All -> Show Full Empty State (Figma Node 812:912) -->
            <template x-if="products.length === 0">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; padding: 48px 0 80px; text-align: center; box-sizing: border-box;">
                    <!-- Illustration (215x223px) -->
                    <img src="{{ asset('images/marketplace/empty_products_illustration.svg') }}" alt="Produk Belum Tersedia" style="width: 215px; height: 223px; object-fit: contain; margin-bottom: 32px;" />
                    
                    <!-- Text Frame (Gap 12px) -->
                    <div style="display: flex; flex-direction: column; gap: 12px; align-items: center; justify-content: center; max-width: 480px;">
                        <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 32px; font-weight: 600; line-height: 36px; letter-spacing: -0.01em; color: #18181B; margin: 0;">
                            Produk belum tersedia
                        </h2>
                        <p style="font-family: 'DM Sans', sans-serif; font-size: 20px; font-weight: 500; line-height: 36px; color: #71717A; margin: 0;">
                            Segera hadir, pantengin terus ya!
                        </p>
                    </div>
                </div>
            </template>
            
        </div>

        <!-- ROOT LEVEL MOBILE FILTER BOTTOM SHEET MODAL (Strictly hidden on Desktop/Tablet >= 768px) -->
        <template x-teleport="body">
            <div x-show="mobileFilterOpen" 
                 class="md:hidden"
                 style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999 !important; display: flex !important; flex-direction: column !important; justify-content: flex-end !important;"
                 x-cloak>
                
                <!-- Backdrop Overlay with Smooth Fade (Clicking outside/above closes modal) -->
                <div x-show="mobileFilterOpen"
                     x-transition:enter="backdrop-fade-enter-active"
                     x-transition:enter-start="backdrop-fade-enter-start"
                     x-transition:enter-end="backdrop-fade-enter-end"
                     x-transition:leave="backdrop-fade-leave-active"
                     x-transition:leave-start="backdrop-fade-leave-start"
                     x-transition:leave-end="backdrop-fade-leave-end"
                     @click="mobileFilterOpen = false"
                     style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(0, 0, 0, 0.5) !important; backdrop-filter: blur(4px) !important; -webkit-backdrop-filter: blur(4px) !important;">
                </div>

                <!-- Sheet Drawer Content -->
                <div x-show="mobileFilterOpen"
                     x-transition:enter="sheet-slide-enter-active"
                     x-transition:enter-start="sheet-slide-enter-start"
                     x-transition:enter-end="sheet-slide-enter-end"
                     x-transition:leave="sheet-slide-leave-active"
                     x-transition:leave-start="sheet-slide-leave-start"
                     x-transition:leave-end="sheet-slide-leave-end"
                     style="position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; background: #FFFFFF !important; border-top-left-radius: 24px !important; border-top-right-radius: 24px !important; border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important; padding: 16px 20px 24px !important; max-height: 85vh !important; overflow-y: auto !important; display: flex !important; flex-direction: column !important; gap: 16px !important; z-index: 1000000 !important; box-shadow: 0 -10px 30px rgba(0,0,0,0.25) !important; width: 100% !important; box-sizing: border-box !important;">
                    
                    <!-- Handle Drag Pill Area (Clicking here closes / slides modal down) -->
                    <div @click="mobileFilterOpen = false" 
                         class="handle-bar-area"
                         style="width: 100%; display: flex; justify-content: center; align-items: center; padding: 0 0 10px; cursor: pointer; user-select: none;" 
                         title="Tutup Filter">
                        <div class="handle-bar-pill" style="width: 40px; height: 5px; background: #D4D4D8; border-radius: 9999px; transition: background 0.15s ease;"></div>
                    </div>

                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; width: 100%;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="{{ asset('images/marketplace/filter_icon.svg') }}" alt="Filter" style="width: 22px; height: 22px;" />
                            <span style="font-family: 'DM Sans', sans-serif; font-size: 18px; font-weight: 600; color: #000000;">Filters</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <button @click="clearAll()" style="color: #00BBA7; font-size: 15px; font-weight: 500; font-family: 'DM Sans', sans-serif; text-decoration: underline; background: none; border: none; cursor: pointer;">
                                Clear all
                            </button>
                            <button @click="mobileFilterOpen = false" style="width: 30px; height: 30px; border-radius: 9999px; background: #F4F4F5; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; color: #71717A;">
                                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Active Filter Pills Container -->
                    <div x-show="selectedCategories.length > 0 || selectedPriceRanges.length > 0" 
                         class="active-pills-wrapper">
                        
                        <!-- Category Pills -->
                        <template x-for="catSlug in selectedCategories" :key="catSlug">
                            <button @click="toggleCategory(catSlug)" 
                                    class="active-filter-pill-item">
                                <span x-text="getCategoryName(catSlug)"></span>
                                <span style="color: #00BBA7; font-weight: 600;">✕</span>
                            </button>
                        </template>

                        <!-- Price Range Pills -->
                        <template x-for="range in selectedPriceRanges" :key="range">
                            <button @click="togglePriceRange(range)" 
                                    class="active-filter-pill-item">
                                <span x-text="getPriceRangeLabel(range)"></span>
                                <span style="color: #00BBA7; font-weight: 600;">✕</span>
                            </button>
                        </template>
                    </div>

                    <!-- Category Section -->
                    <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
                        <span style="color: #000000; font-size: 16px; font-weight: 600; font-family: 'DM Sans', sans-serif;">Kategori</span>
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                            <template x-for="category in categories" :key="category.slug">
                                <button @click="toggleCategory(category.slug)" 
                                        class="custom-checkbox-btn"
                                        style="display: flex; align-items: center; width: 100%; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                                    <div class="custom-checkbox-box" :class="{ 'checked': selectedCategories.includes(category.slug) }">
                                        <svg x-show="selectedCategories.includes(category.slug)" style="width: 14px; height: 14px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span style="color: #000000; font-size: 15px; font-family: 'DM Sans', sans-serif;" x-text="category.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Price Section -->
                    <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
                        <span style="color: #000000; font-size: 16px; font-weight: 600; font-family: 'DM Sans', sans-serif;">Harga</span>
                        <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                            <button @click="togglePriceRange('under-100')" class="custom-checkbox-btn" style="display: flex; align-items: center; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                                <div class="custom-checkbox-box" :class="{ 'checked': selectedPriceRanges.includes('under-100') }">
                                    <svg x-show="selectedPriceRanges.includes('under-100')" style="width: 14px; height: 14px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span style="color: #000000; font-size: 15px; font-family: 'DM Sans', sans-serif;">&lt; Rp.100.000</span>
                            </button>
                            <button @click="togglePriceRange('100-200')" class="custom-checkbox-btn" style="display: flex; align-items: center; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                                <div class="custom-checkbox-box" :class="{ 'checked': selectedPriceRanges.includes('100-200') }">
                                    <svg x-show="selectedPriceRanges.includes('100-200')" style="width: 14px; height: 14px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span style="color: #000000; font-size: 15px; font-family: 'DM Sans', sans-serif;">Rp100.000 - RP 200.000</span>
                            </button>
                            <button @click="togglePriceRange('above-250')" class="custom-checkbox-btn" style="display: flex; align-items: center; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                                <div class="custom-checkbox-box" :class="{ 'checked': selectedPriceRanges.includes('above-250') }">
                                    <svg x-show="selectedPriceRanges.includes('above-250')" style="width: 14px; height: 14px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span style="color: #000000; font-size: 15px; font-family: 'DM Sans', sans-serif;">&gt; Rp250.000</span>
                            </button>
                        </div>
                    </div>

                    <!-- Apply Button -->
                    <button @click="mobileFilterOpen = false" 
                            style="width: 100%; background: #00BBA7; color: #FFFFFF; font-family: 'DM Sans', sans-serif; font-size: 16px; font-weight: 600; padding: 14px; border-radius: 9999px; border: none; cursor: pointer; margin-top: 8px; transition: background 0.15s ease;">
                        Terapkan Filter (<span x-text="filteredProducts.length"></span> Produk)
                    </button>
                </div>
            </div>
        </template>

    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('marketplaceComponent', (initialProducts, initialCategories) => ({
            products: initialProducts.map((p, index) => ({
                ...p,
                is_popular: p.id % 4 === 0 || index === 0 || index === 1 || index === 3
            })),
            categories: initialCategories,
            
            // State Filter & Sorting
            selectedCategories: [],
            selectedPriceRanges: [],
            sortBy: 'latest',
            mobileFilterOpen: false,
            
            // Pagination state
            currentPage: 1,
            itemsPerPage: 9,
            
            // Helper pencarian nama kategori
            getCategoryName(slug) {
                const cat = this.categories.find(c => c.slug === slug);
                return cat ? cat.name : slug;
            },
            
            // Helper label rentang harga
            getPriceRangeLabel(range) {
                if (range === 'under-100') return '< Rp.100.000';
                if (range === '100-200') return 'Rp100.000 - RP 200.000';
                if (range === 'above-250') return '> Rp250.000';
                return range;
            },
            
            // Normalisasi harga ke nilai ribuan
            getNormalizedPrice(price) {
                let p = Number(price);
                if (p < 1000) {
                    p = p * 1000;
                }
                return p;
            },

            // Format rupiah
            formatPrice(price) {
                const p = this.getNormalizedPrice(price);
                return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(p);
            },
            
            // Logika Filtering & Sorting
            get filteredProducts() {
                let result = [...this.products];
                
                // Filter Kategori
                if (this.selectedCategories.length > 0) {
                    result = result.filter(p => this.selectedCategories.includes(p.category_slug));
                }
                
                // Filter Rentang Harga
                if (this.selectedPriceRanges.length > 0) {
                    result = result.filter(p => {
                        const price = this.getNormalizedPrice(p.price);
                        
                        return this.selectedPriceRanges.some(range => {
                            if (range === 'under-100') return price < 100000;
                            if (range === '100-200') return price >= 100000 && price <= 200000;
                            if (range === 'above-250') return price > 250000;
                            return false;
                        });
                    });
                }
                
                // Sorting
                if (this.sortBy === 'latest') {
                    result.sort((a, b) => b.id - a.id);
                } else if (this.sortBy === 'low-to-high') {
                    result.sort((a, b) => this.getNormalizedPrice(a.price) - this.getNormalizedPrice(b.price));
                } else if (this.sortBy === 'high-to-low') {
                    result.sort((a, b) => this.getNormalizedPrice(b.price) - this.getNormalizedPrice(a.price));
                }
                
                return result;
            },
            
            // Produk terpaginasi
            get paginatedProducts() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredProducts.slice(start, start + this.itemsPerPage);
            },
            
            // Total Halaman
            get totalPages() {
                return Math.ceil(this.filteredProducts.length / this.itemsPerPage) || 1;
            },
            
            // Actions
            toggleCategory(slug) {
                if (this.selectedCategories.includes(slug)) {
                    this.selectedCategories = this.selectedCategories.filter(s => s !== slug);
                } else {
                    this.selectedCategories.push(slug);
                }
                this.currentPage = 1;
            },
            
            togglePriceRange(range) {
                if (this.selectedPriceRanges.includes(range)) {
                    this.selectedPriceRanges = this.selectedPriceRanges.filter(r => r !== range);
                } else {
                    this.selectedPriceRanges.push(range);
                }
                this.currentPage = 1;
            },
            
            clearAll() {
                this.selectedCategories = [];
                this.selectedPriceRanges = [];
                this.currentPage = 1;
            },
            
            // Dynamic page range logic matching Figma Node 828:2309
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
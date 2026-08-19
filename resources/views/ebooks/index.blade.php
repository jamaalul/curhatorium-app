@extends('layouts.dashboard')

@section('title', 'Ebook | Curhatorium')

@section('bodyClass', 'pt-16 w-full bg-[#F4F4F5]')

@section('head')
    <meta name="description" content="Katalog ebook Curhatorium">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Geist:wght@100..900&family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { background-color: #F4F4F5 !important; }
        .marketplace-main-layout { display: flex; flex-direction: column; gap: 24px; align-items: flex-start; width: 100%; box-sizing: border-box; }
        @media (min-width: 1024px) {
            .marketplace-main-layout { flex-direction: row !important; align-items: flex-start !important; }
            .marketplace-sidebar-container { display: block !important; width: 282px !important; min-width: 282px !important; max-width: 282px !important; flex: 0 0 282px !important; position: sticky !important; top: 84px !important; align-self: flex-start !important; max-height: calc(100vh - 100px) !important; overflow-y: auto !important; z-index: 30 !important; }
            .marketplace-products-container { flex: 1 1 0% !important; min-width: 0 !important; width: 100% !important; }
            .marketplace-products-grid { display: grid !important; grid-template-columns: repeat(3, minmax(0, 1fr)) !important; gap: 24px !important; width: 100% !important; }
        }
        @media (min-width: 768px) and (max-width: 1023px) {
            .marketplace-main-layout { flex-direction: row !important; gap: 20px !important; align-items: flex-start !important; }
            .marketplace-sidebar-container { display: block !important; width: 240px !important; min-width: 240px !important; max-width: 240px !important; flex: 0 0 240px !important; position: sticky !important; top: 84px !important; align-self: flex-start !important; max-height: calc(100vh - 100px) !important; overflow-y: auto !important; z-index: 30 !important; }
            .marketplace-products-container { flex: 1 1 0% !important; min-width: 0 !important; width: 100% !important; }
            .marketplace-products-grid { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 16px !important; width: 100% !important; }
        }
        @media (max-width: 767px) {
            .marketplace-sidebar-container { display: none !important; }
            .marketplace-products-container { width: 100% !important; }
            .marketplace-products-grid { display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 12px !important; width: 100% !important; }
        }
        .sheet-slide-enter-active { transition: transform 380ms cubic-bezier(0.32, 0.72, 0, 1) !important; will-change: transform !important; }
        .sheet-slide-leave-active { transition: transform 280ms cubic-bezier(0.32, 0.72, 0, 1) !important; will-change: transform !important; }
        .sheet-slide-enter-start { transform: translateY(100%) !important; }
        .sheet-slide-enter-end { transform: translateY(0) !important; }
        .sheet-slide-leave-start { transform: translateY(0) !important; }
        .sheet-slide-leave-end { transform: translateY(100%) !important; }
        .backdrop-fade-enter-active { transition: opacity 350ms ease-out !important; }
        .backdrop-fade-leave-active { transition: opacity 250ms ease-in !important; }
        .backdrop-fade-enter-start { opacity: 0 !important; }
        .backdrop-fade-enter-end { opacity: 1 !important; }
        .backdrop-fade-leave-start { opacity: 1 !important; }
        .backdrop-fade-leave-end { opacity: 0 !important; }
        .handle-bar-area:hover .handle-bar-pill { background-color: #A1A1AA !important; }
        .active-pills-wrapper { display: flex !important; flex-wrap: wrap !important; gap: 10px 8px !important; margin-top: 10px !important; margin-bottom: 4px !important; padding-top: 10px !important; padding-bottom: 16px !important; border-bottom: 1px solid #E4E4E7 !important; width: 100% !important; box-sizing: border-box !important; }
        .active-filter-pill-item { display: inline-flex !important; justify-content: center !important; align-items: center !important; color: #18181B !important; font-size: 13px !important; font-weight: 500 !important; font-family: 'DM Sans', sans-serif !important; cursor: pointer !important; background: #F4F4F5 !important; padding: 6px 14px !important; border-radius: 9999px !important; gap: 6px !important; border: 1px solid #E4E4E7 !important; margin-right: 6px !important; margin-bottom: 6px !important; box-sizing: border-box !important; }
        .ebook-card-banner { width: 100% !important; aspect-ratio: 3 / 4 !important; position: relative !important; border-radius: 8px !important; overflow: hidden !important; background: #F4F4F5 !important; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0 !important; }
        .mobile-filter-trigger-btn { width: 36px !important; height: 36px !important; border-radius: 9999px !important; background: #FFFFFF !important; border: 1px solid #E4E4E7 !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; flex-shrink: 0 !important; padding: 0 !important; margin: 0 !important; }
        @media (min-width: 768px) { .mobile-filter-trigger-btn { display: none !important; } }
    </style>
@endsection

@php
    $ebooksData = $ebooks->map(function($e, $index) {
        $imageUrl = 'https://placehold.co/300x400';
        if ($e->cover_image) {
            if (str_starts_with($e->cover_image, 'http://') || str_starts_with($e->cover_image, 'https://')) {
                $imageUrl = $e->cover_image;
            } else {
                $imageUrl = \Illuminate\Support\Facades\Storage::url($e->cover_image);
            }
        }

        return [
            'id' => $e->id,
            'name' => $e->title,
            'slug' => $e->slug,
            'description' => Str::limit(strip_tags($e->description), 120),
            'price' => (float) $e->price,
            'category_name' => strtoupper($e->category?->name ?? 'REFLEKSI'),
            'category_slug' => $e->category?->slug ?? 'refleksi',
            'image_url' => $imageUrl,
        ];
    })->values();

    $categoriesData = $categories->map(fn($c) => [
        'name' => $c->name,
        'slug' => $c->slug
    ])->values();
@endphp

@section('dashboard-content')
    <!-- Alpine.js State Wrapper (Root Level) -->
    <div x-data="ebooksComponent(@js($ebooksData), @js($categoriesData))" class="w-full bg-[#F4F4F5] min-h-screen py-6 sm:py-10" style="background-color: #F4F4F5; position: relative;">
        
        <!-- Main Max-Width Container -->
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-10 sm:gap-16" style="max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 16px; padding-right: 16px; display: flex; flex-direction: column; gap: 40px;">
            
            <!-- Ebook Header Section Partial -->
            @include('ebooks.partials.header')

            <!-- Condition 1: Ebooks Exist -> Show Sidebar & Ebooks Grid -->
            <template x-if="products.length > 0">
                <div class="marketplace-main-layout">
                    <!-- Filters Sidebar Partial (Desktop & Tablet >= 768px) -->
                    @include('ebooks.partials.sidebar')

                    <!-- Ebooks Grid & Toolbar Partial -->
                    @include('ebooks.partials.ebooks')
                </div>
            </template>

            <!-- Condition 2: No Ebooks Exist At All -> Show Full Empty State -->
            <template x-if="products.length === 0">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; padding: 48px 0 80px; text-align: center; box-sizing: border-box;">
                    <img src="{{ asset('images/marketplace/empty_products_illustration.svg') }}" alt="Ebook Belum Tersedia" style="width: 215px; height: 223px; object-fit: contain; margin-bottom: 32px;" />
                    
                    <div style="display: flex; flex-direction: column; gap: 12px; align-items: center; justify-content: center; max-width: 480px;">
                        <h2 style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 32px; font-weight: 600; line-height: 36px; letter-spacing: -0.01em; color: #18181B; margin: 0;">
                            Ebook belum tersedia
                        </h2>
                        <p style="font-family: 'DM Sans', sans-serif; font-size: 20px; font-weight: 500; line-height: 36px; color: #71717A; margin: 0;">
                            Segera hadir, pantengin terus ya!
                        </p>
                    </div>
                </div>
            </template>

        </div>

        <!-- Mobile Filter Bottom Sheet Partial -->
        @include('ebooks.partials.mobile-filter')

    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ebooksComponent', (initialEbooks, initialCategories) => ({
            products: initialEbooks,
            categories: initialCategories,
            
            selectedCategories: [],
            selectedPriceRanges: [],
            sortBy: 'latest',
            mobileFilterOpen: false,
            
            currentPage: 1,
            itemsPerPage: 9,
            
            getCategoryName(slug) {
                const cat = this.categories.find(c => c.slug === slug);
                return cat ? cat.name : slug;
            },
            
            getPriceRangeLabel(range) {
                if (range === 'under-100') return '< Rp.100.000';
                if (range === '100-200') return 'Rp100.000 - RP 200.000';
                if (range === 'above-250') return '> Rp250.000';
                return range;
            },
            
            getNormalizedPrice(price) {
                let p = Number(price);
                if (p < 1000) {
                    p = p * 1000;
                }
                return p;
            },

            formatPrice(price) {
                const p = this.getNormalizedPrice(price);
                return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(p);
            },
            
            get filteredProducts() {
                let result = [...this.products];
                
                if (this.selectedCategories.length > 0) {
                    result = result.filter(p => this.selectedCategories.includes(p.category_slug));
                }
                
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
                
                if (this.sortBy === 'latest') {
                    result.sort((a, b) => b.id - a.id);
                } else if (this.sortBy === 'low-to-high') {
                    result.sort((a, b) => this.getNormalizedPrice(a.price) - this.getNormalizedPrice(b.price));
                } else if (this.sortBy === 'high-to-low') {
                    result.sort((a, b) => this.getNormalizedPrice(b.price) - this.getNormalizedPrice(a.price));
                }
                
                return result;
            },
            
            get paginatedProducts() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredProducts.slice(start, start + this.itemsPerPage);
            },
            
            get totalPages() {
                return Math.ceil(this.filteredProducts.length / this.itemsPerPage) || 1;
            },
            
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

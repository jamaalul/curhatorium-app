<!-- Product Grid & Toolbar -->
<div class="marketplace-products-container flex-1 w-full min-w-0" style="flex: 1 1 0%; min-width: 0; width: 100%;">
    
    <!-- Catalog Header / Sorting & Mobile Filter Toolbar -->
    <div style="padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; position: relative; z-index: 40; width: 100%;">
        <div style="color: #000000; font-size: 20px; font-weight: 500; font-family: 'DM Sans', sans-serif; line-height: 36px;">
            <span x-text="filteredProducts.length"></span> Produk
        </div>
        
        <div style="display: flex; align-items: center; gap: 8px;">
            <!-- Mobile Filter Button (Round White Circle - Strictly ONLY visible on Mobile < 768px) -->
            <button @click="mobileFilterOpen = true" 
                    class="mobile-filter-trigger-btn"
                    aria-label="Filter">
                <img src="{{ asset('images/marketplace/filter_icon.svg') }}" alt="Filter" style="width: 18px; height: 18px; flex-shrink: 0;" />
            </button>

            <!-- Sort Dropdown Pill -->
            <div x-data="{ open: false }" style="position: relative; z-index: 40;">
                <button @click="open = !open" 
                        style="background: #FFF; border: 1px solid #E4E4E7; display: flex; align-items: center; border-radius: 9999px; padding: 6px 16px; gap: 8px; cursor: pointer; user-select: none;">
                    <span style="color: #737373; font-size: 15px; font-family: 'DM Sans', sans-serif;">Sortir:</span>
                    <span style="color: #141414; font-size: 15px; font-weight: 500; font-family: 'DM Sans', sans-serif;" x-text="sortBy === 'latest' ? 'Terbaru' : (sortBy === 'low-to-high' ? 'Harga Terendah' : 'Harga Tertinggi')">Terbaru</span>
                    <svg style="width: 16px; height: 16px; color: #141414; transition: transform 0.2s;" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <!-- Dropdown Menu Box (Matching Figma Node 610:1818 Overlay Component) -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     style="position: absolute; right: 0; top: 100%; margin-top: 8px; z-index: 50; border: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 4px; padding: 6px; background: #FFFFFF; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); border-radius: 12px; min-width: 200px; box-sizing: border-box;"
                     x-cloak>
                    <style>
                        .sort-dropdown-item {
                            display: flex !important;
                            flex-direction: row !important;
                            align-items: center !important;
                            justify-content: space-between !important;
                            width: 100% !important;
                            padding: 8px 12px !important;
                            border-radius: 8px !important;
                            border: none !important;
                            background: #FFFFFF !important;
                            color: #1E1E1E !important;
                            font-family: 'DM Sans', sans-serif !important;
                            font-size: 15px !important;
                            line-height: 20px !important;
                            cursor: pointer !important;
                            text-align: left !important;
                            box-sizing: border-box !important;
                            transition: background-color 0.15s ease !important;
                            white-space: nowrap !important;
                        }
                        .sort-dropdown-item:hover {
                            background-color: #F4F4F5 !important;
                        }
                        .sort-dropdown-item.active {
                            background-color: #F4F4F5 !important;
                            font-weight: 500 !important;
                            color: #000000 !important;
                        }

                        /* Card Media Banner Explicit Desktop Aspect Ratio (264/160) */
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

                        /* Pagination Explicit CSS Rules */
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
                            line-height: 28px !important;
                            background: none !important;
                            border: none !important;
                            cursor: pointer !important;
                            padding: 0 !important;
                            margin: 0 !important;
                            white-space: nowrap !important;
                        }

                        /* Mobile Filter Trigger Button Class with Media Query */
                        .mobile-filter-trigger-btn {
                            width: 36px !important;
                            height: 36px !important;
                            border-radius: 9999px !important;
                            background: #FFFFFF !important;
                            border: 1px solid #E4E4E7 !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            cursor: pointer !important;
                            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
                            flex-shrink: 0 !important;
                            padding: 0 !important;
                            margin: 0 !important;
                        }

                        @media (min-width: 768px) {
                            .mobile-filter-trigger-btn {
                                display: none !important;
                            }
                        }
                    </style>

                    <!-- Option 1: Terbaru -->
                    <button @click="sortBy = 'latest'; open = false; currentPage = 1" 
                            class="sort-dropdown-item"
                            :class="{ 'active': sortBy === 'latest' }">
                        <span>Terbaru</span>
                        <svg x-show="sortBy === 'latest'" style="width: 18px; height: 18px; color: #00BBA7; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>

                    <!-- Option 2: Harga Terendah -->
                    <button @click="sortBy = 'low-to-high'; open = false; currentPage = 1" 
                            class="sort-dropdown-item"
                            :class="{ 'active': sortBy === 'low-to-high' }">
                        <span>Harga Terendah</span>
                        <svg x-show="sortBy === 'low-to-high'" style="width: 18px; height: 18px; color: #00BBA7; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>

                    <!-- Option 3: Harga Tertinggi -->
                    <button @click="sortBy = 'high-to-low'; open = false; currentPage = 1" 
                            class="sort-dropdown-item"
                            :class="{ 'active': sortBy === 'high-to-low' }">
                        <span>Harga Tertinggi</span>
                        <svg x-show="sortBy === 'high-to-low'" style="width: 18px; height: 18px; color: #00BBA7; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid (2 cols on Mobile/Tablet, 3 cols on Large Desktop) -->
    <div class="marketplace-products-grid grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6" style="width: 100%;">
        
        <!-- Loop paginated products -->
        <template x-for="product in paginatedProducts" :key="product.id">
            <a :href="'/marketplace/' + product.slug" 
               class="group bg-white rounded-2xl p-3 pb-4 border border-[#E4E4E7] shadow-xs flex flex-col justify-between gap-3 transition-all duration-300 hover:shadow-md hover:no-underline cursor-pointer"
               style="display: flex; flex-direction: column; justify-content: space-between; gap: 12px; background: #FFF; border-radius: 16px; border: 1px solid #E4E4E7; padding: 12px 12px 16px; box-sizing: border-box; width: 100%; text-decoration: none;">
                
                <!-- Card Media Banner (Preserving Desktop Aspect Ratio 264/160 across all views) -->
                <div class="marketplace-card-banner">
                    <img :src="product.image_url" :alt="product.name" style="width: 100%; height: 100%; object-fit: cover;" />
                </div>

                <!-- Card Info -->
                <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                    <!-- Category Badge -->
                    <span style="color: #71717A; font-size: 12px; font-family: 'DM Sans', sans-serif; text-transform: uppercase; letter-spacing: 0.05em;" x-text="product.category_name"></span>
                    
                    <!-- Title & Description Container with Bottom Divider -->
                    <div style="padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; display: flex; flex-direction: column; gap: 4px; width: 100%;">
                        <h3 style="color: #141414; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; line-height: 28px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="product.name"></h3>
                        <p style="color: #71717A; font-size: 15px; font-family: 'DM Sans', sans-serif; line-height: 28px; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 56px;" x-text="product.description"></p>
                    </div>
                </div>

                <!-- Card Footer (Price) -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 4px; width: 100%;">
                    <span style="color: #171717; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;" x-text="'Rp' + formatPrice(product.price)"></span>
                </div>
            </a>
        </template>

        <!-- Empty State -->
        <template x-if="filteredProducts.length === 0">
            <div style="grid-column: 1 / -1; padding: 64px 0; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #FFF; border-radius: 16px; border: 1px dashed #D4D4D8; width: 100%;">
                <svg style="width: 64px; height: 64px; color: #D4D4D8; margin-bottom: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p style="color: #27272A; font-weight: 500; font-family: 'DM Sans', sans-serif; font-size: 18px; margin: 0;">Tidak ada produk ditemukan</p>
                <p style="color: #71717A; font-family: 'DM Sans', sans-serif; font-size: 14px; margin-top: 4px;">Coba atur ulang beberapa filter kategori atau rentang harga.</p>
            </div>
        </template>
    </div>

    <!-- Pagination Bar (Strict Horizontal Row Alignment Matching Figma Node 828:2309 Spec) -->
    <div class="marketplace-pagination-container" x-show="filteredProducts.length > 0 && totalPages > 1" x-cloak>
        <!-- Prev Button (Figma Node 863:3907: 36x36px round button) -->
        <button @click="if(currentPage > 1) { currentPage--; window.scrollTo({top: 0, behavior: 'smooth'}); }" 
                :disabled="currentPage === 1"
                class="marketplace-pagination-btn"
                :style="currentPage === 1 ? 'background: #FAFAFA !important; color: #A1A1AA !important; cursor: not-allowed !important; opacity: 0.6 !important;' : 'background: #FFFFFF !important; color: #000000 !important;'">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Page Numbers Container (Horizontal Flex Row) -->
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

        <!-- Next Button (Figma Node 828:2319: 36x36px round button) -->
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

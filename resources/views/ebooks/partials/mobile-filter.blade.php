<!-- ROOT LEVEL MOBILE FILTER BOTTOM SHEET MODAL -->
<template x-teleport="body">
    <div x-show="mobileFilterOpen" 
         class="md:hidden"
         style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999 !important; display: flex !important; flex-direction: column !important; justify-content: flex-end !important;"
         x-cloak>
        
        <!-- Backdrop Overlay -->
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
            
            <!-- Handle Drag Pill Area -->
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
                Terapkan Filter (<span x-text="filteredProducts.length"></span> Ebook)
            </button>
        </div>
    </div>
</template>

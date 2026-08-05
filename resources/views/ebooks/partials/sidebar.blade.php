<!-- Filters Sidebar Partial (Desktop & Tablet >= 768px) -->
<aside class="marketplace-sidebar-container w-full md:w-[282px] flex-shrink-0" style="flex: 0 0 282px; width: 282px; min-width: 282px; max-width: 282px;">
    <style>
        .custom-checkbox-box {
            width: 20px !important;
            height: 20px !important;
            min-width: 20px !important;
            min-height: 20px !important;
            max-width: 20px !important;
            max-height: 20px !important;
            border-radius: 4px !important;
            border: 2px solid #71717A !important;
            background-color: #FFFFFF !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            box-sizing: border-box !important;
            transition: all 0.15s ease-in-out !important;
        }
        .custom-checkbox-box.checked {
            border-color: #00BBA7 !important;
            background-color: #00BBA7 !important;
        }
        .custom-checkbox-btn:hover .custom-checkbox-box:not(.checked) {
            border-color: #18181B !important;
            background-color: #F4F4F5 !important;
        }
        .active-filter-pill:hover {
            background-color: #E4E4E7 !important;
        }
    </style>

    <div class="bg-white border border-[#E4E4E7] rounded-lg p-3 flex flex-col gap-4 shadow-xs w-full" style="background: #FFFFFF; border: 1px solid #E4E4E7; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 16px; width: 100%; box-sizing: border-box;">
        
        <!-- Filters Header -->
        <div class="pb-3 border-b border-[#E4E4E7] flex justify-between items-center w-full" style="padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 12px;">
                <img src="{{ asset('images/marketplace/filter_icon.svg') }}" alt="Filter Icon" style="width: 24px; height: 24px; flex-shrink: 0; object-fit: contain;" />
                <span style="color: #000000; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Filters</span>
            </div>
            <button @click="clearAll()" 
                    style="color: #00BBA7; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif; text-decoration: underline; background: none; border: none; cursor: pointer;">
                Clear all
            </button>
        </div>

        <!-- Active Filter Pills Container -->
        <div x-show="selectedCategories.length > 0 || selectedPriceRanges.length > 0" 
             style="display: flex; flex-wrap: wrap; gap: 8px 8px; padding-bottom: 12px; border-bottom: 1px solid #E4E4E7; width: 100%; box-sizing: border-box;"
             x-cloak>
            
            <!-- Category Pills -->
            <template x-for="catSlug in selectedCategories" :key="catSlug">
                <button @click="toggleCategory(catSlug)" 
                        class="active-filter-pill"
                        style="display: inline-flex; justify-content: center; align-items: center; color: #18181B; font-size: 13px; font-weight: 500; font-family: 'DM Sans', sans-serif; cursor: pointer; background: #F4F4F5; padding: 6px 14px; border-radius: 9999px; gap: 6px; border: 1px solid #E4E4E7; margin-right: 4px; margin-bottom: 4px; transition: all 0.15s ease;">
                    <span x-text="getCategoryName(catSlug)"></span>
                    <span style="width: 14px; height: 14px; display: flex; align-items: center; justify-content: center; color: #00BBA7;">
                        <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                </button>
            </template>

            <!-- Price Range Pills -->
            <template x-for="range in selectedPriceRanges" :key="range">
                <button @click="togglePriceRange(range)" 
                        class="active-filter-pill"
                        style="display: inline-flex; justify-content: center; align-items: center; color: #18181B; font-size: 13px; font-weight: 500; font-family: 'DM Sans', sans-serif; cursor: pointer; background: #F4F4F5; padding: 6px 14px; border-radius: 9999px; gap: 6px; border: 1px solid #E4E4E7; margin-right: 4px; margin-bottom: 4px; transition: all 0.15s ease;">
                    <span x-text="getPriceRangeLabel(range)"></span>
                    <span style="width: 14px; height: 14px; display: flex; align-items: center; justify-content: center; color: #00BBA7;">
                        <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </span>
                </button>
            </template>
        </div>

        <!-- Category Checkboxes Section -->
        <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
            <span style="color: #000000; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Kategori</span>
            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                <template x-for="category in categories" :key="category.slug">
                    <button @click="toggleCategory(category.slug)" 
                            class="custom-checkbox-btn"
                            style="display: flex; align-items: center; width: 100%; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                        <!-- Custom Checkbox Box -->
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

        <!-- Price Checkboxes Section -->
        <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
            <span style="color: #000000; font-size: 16px; font-weight: 500; font-family: 'DM Sans', sans-serif;">Harga</span>
            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                <!-- Range 1: < Rp.100.000 -->
                <button @click="togglePriceRange('under-100')" class="custom-checkbox-btn" style="display: flex; align-items: center; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                    <div class="custom-checkbox-box" :class="{ 'checked': selectedPriceRanges.includes('under-100') }">
                        <svg x-show="selectedPriceRanges.includes('under-100')" style="width: 14px; height: 14px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span style="color: #000000; font-size: 15px; font-family: 'DM Sans', sans-serif;">&lt; Rp.100.000</span>
                </button>

                <!-- Range 2: Rp100.000 - RP 200.000 -->
                <button @click="togglePriceRange('100-200')" class="custom-checkbox-btn" style="display: flex; align-items: center; gap: 12px; background: none; border: none; cursor: pointer; text-align: left; padding: 3px 0;">
                    <div class="custom-checkbox-box" :class="{ 'checked': selectedPriceRanges.includes('100-200') }">
                        <svg x-show="selectedPriceRanges.includes('100-200')" style="width: 14px; height: 14px; color: #FFFFFF;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span style="color: #000000; font-size: 15px; font-family: 'DM Sans', sans-serif;">Rp100.000 - RP 200.000</span>
                </button>

                <!-- Range 3: > Rp250.000 -->
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

    </div>
</aside>

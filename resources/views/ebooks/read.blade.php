@extends('layouts.dashboard')

@section('title', 'Membaca: ' . $ebook->title)

@section('bodyClass', 'pt-16 w-full overflow-hidden bg-gray-100')

@section('head')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .reader-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .reader-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .reader-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .reader-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection

@section('dashboard-content')
    <div class="w-full flex flex-col h-[calc(100vh-64px)] bg-gray-100 font-sans"
        x-data="ebookReader()"
        data-pdf-url="{{ $pdfUrl }}"
        data-start-page="{{ $startPage }}"
        data-progress-url="{{ route('ebooks.progress.update', $ebook) }}"
        data-refresh-url="{{ route('ebooks.refresh-url', $ebook) }}"
        @keydown.window.arrow-right="nextPage()"
        @keydown.window.arrow-left="prevPage()"
        @contextmenu.prevent>

        <!-- Header Controls Bar (Figma Node #1027:1189) -->
        <div class="bg-white border-b border-[#E5E7EB] flex flex-wrap items-center justify-between px-4 py-3 shrink-0 shadow-xs z-20 gap-3">
            
            <!-- Left: Back Link, Divider & Title -->
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ url()->previous() !== request()->url() ? url()->previous() : route('ebooks.show', $ebook) }}"
                   onclick="if (window.history.length > 1) { window.history.back(); return false; }"
                   class="inline-flex items-center justify-center p-1 rounded-md text-[#111827] hover:text-black hover:bg-gray-100 transition-colors"
                   title="Kembali ke Halaman Sebelumnya">
                    <svg class="w-5 h-5 text-[#111827]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>

                <!-- 1px x 24px Divider -->
                <div class="w-px h-6 bg-[#D1D5DB]"></div>

                <!-- Ebook Title -->
                <h1 class="font-bricolage font-bold text-[#111827] text-lg leading-7 truncate max-w-[200px] sm:max-w-xs md:max-w-md lg:max-w-xl"
                    title="{{ $ebook->title }}">
                    {{ $ebook->title }}
                </h1>
            </div>

            <!-- Right: Page Selector & Figma Zoom Bar (Always aligned to the right) -->
            <div class="flex items-center justify-end gap-2.5 sm:gap-3 flex-wrap ml-auto">

                <!-- Page Navigation Jump Pill -->
                <div class="flex items-center h-[38px] box-border bg-[#FAFAFA] rounded-lg border border-[#E5E7EB] px-3 gap-1.5 text-xs font-dm text-[#09090B]">
                    <span class="text-gray-500">Halaman</span>
                    <input type="number"
                           x-model.number="pageInput"
                           @keydown.enter="goToPage()"
                           @blur="goToPage()"
                           min="1"
                           :max="numPages"
                           :disabled="loading || numPages <= 0"
                           class="w-16 text-center py-0.5 px-1.5 bg-white border border-[#E5E7EB] rounded font-bold text-[#09090B] focus:outline-none focus:ring-1 focus:ring-[#2CB9C0] disabled:opacity-50 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <span class="text-gray-500">dari <strong x-text="numPages || '-'" class="text-[#09090B]"></strong></span>
                </div>

                <!-- Figma "zoom in & out" Component (#1054:1881) -->
                <div class="flex items-center h-[38px] box-border bg-[#FAFAFA] border border-[#E5E7EB] rounded-lg p-1 gap-1">
                    <!-- Zoom Out Button -->
                    <button @click="zoomOut()"
                            :disabled="scale <= 0.5 || loading"
                            class="w-7 h-7 flex items-center justify-center rounded-md text-[#111827] hover:bg-gray-200 disabled:opacity-30 transition-colors"
                            title="Perkecil (-)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>

                    <!-- Zoom Slider (128px track) -->
                    <input type="range"
                           x-model.number="scale"
                           @change="updateZoom()"
                           min="0.5"
                           max="3"
                           step="0.1"
                           :disabled="loading"
                           class="w-24 sm:w-32 h-1.5 bg-[#E5E7EB] rounded-full appearance-none cursor-pointer accent-[#2CB9C0] disabled:opacity-50">

                    <!-- Zoom In Button -->
                    <button @click="zoomIn()"
                            :disabled="scale >= 3 || loading"
                            class="w-7 h-7 flex items-center justify-center rounded-md text-[#111827] hover:bg-gray-200 disabled:opacity-30 transition-colors"
                            title="Perbesar (+)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>

                    <!-- Percentage Label -->
                    <span x-text="Math.round(scale * 100) + '%'"
                          class="w-12 text-center text-xs font-dm font-semibold text-[#09090B]"></span>
                </div>

                <!-- Fit Width Action Button -->
                <button @click="fitWidth()"
                        :disabled="loading"
                        class="hidden sm:inline-flex items-center justify-center gap-1.5 text-xs font-semibold font-dm text-[#09090B] bg-[#FAFAFA] hover:bg-gray-100 border border-[#E5E7EB] px-3.5 h-[38px] rounded-lg transition-colors disabled:opacity-50"
                        title="Fit Width">
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <span>Fit</span>
                </button>

            </div>
        </div>

        <!-- Main Reader Area -->
        <div class="flex-1 overflow-hidden flex relative bg-gray-200/70" x-ref="container">
            
            <!-- Left Side Navigation Overlay Button -->
            <button @click="prevPage()"
                    :disabled="currentPage <= 1 || loading"
                    class="absolute left-0 top-0 bottom-0 w-16 md:w-20 group flex items-center justify-center z-10 disabled:hidden focus:outline-none">
                <div class="bg-white/90 backdrop-blur shadow-md p-3 rounded-full opacity-0 group-hover:opacity-100 transition-opacity text-gray-800 hover:bg-white border border-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
            </button>

            <!-- Canvas Viewport -->
            <div class="flex-1 reader-scroll overflow-auto p-4 md:p-8 select-none relative z-0 flex items-start justify-center">
                
                <!-- Loading Spinner State -->
                <div x-show="loading" class="flex flex-col items-center justify-center h-full text-gray-500 absolute inset-0 bg-gray-100/90 backdrop-blur-sm z-20">
                    <svg class="animate-spin h-10 w-10 text-teal-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="font-dm text-sm tracking-wide text-gray-600 animate-pulse font-medium">Memuat dokumen PDF...</p>
                </div>
                
                <!-- Error State -->
                <div x-show="error" x-cloak class="flex flex-col items-center justify-center h-full absolute inset-0 bg-white z-20 p-6 text-center">
                    <img src="{{ asset('images/marketplace/empty_products_illustration.svg') }}" alt="Empty State" class="w-28 h-28 mb-3 object-contain" />
                    <h3 class="text-base font-bold text-gray-900 mb-1">Gagal Membuka Ebook</h3>
                    <p class="font-dm text-sm text-gray-600 max-w-md mb-4" x-text="error"></p>
                    <button @click="loadDocument()" class="px-5 py-2.5 bg-[#00BBA7] hover:bg-[#009B8A] text-white rounded-lg transition text-xs font-semibold shadow-xs">
                        Coba Lagi
                    </button>
                </div>

                <!-- PDF Canvas -->
                <canvas x-ref="canvas"
                        x-show="!loading && !error"
                        x-cloak
                        class="bg-white shadow-xl mx-auto border border-gray-300 rounded transition-all origin-top max-w-none"
                        @dragstart.prevent></canvas>

            </div>

            <!-- Right Side Navigation Overlay Button -->
            <button @click="nextPage()"
                    :disabled="currentPage >= numPages || loading"
                    class="absolute right-0 top-0 bottom-0 w-16 md:w-20 group flex items-center justify-center z-10 disabled:hidden focus:outline-none">
                <div class="bg-white/90 backdrop-blur shadow-md p-3 rounded-full opacity-0 group-hover:opacity-100 transition-opacity text-gray-800 hover:bg-white border border-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

        </div>

        <!-- Footer Bar / Figma "page navigator" Component (#1049:1753) -->
        <div class="bg-white border-t border-[#E5E7EB] px-4 py-3 flex items-center justify-center shrink-0 z-20 shadow-xs">
            <div class="flex items-center gap-3">
                
                <!-- Left NavButton (Previous Page) -->
                <button @click="prevPage()"
                        :disabled="currentPage <= 1 || loading"
                        class="p-2 rounded-lg border border-[#E5E7EB] transition-colors flex items-center justify-center text-[#111827] disabled:bg-[#F4F4F5] disabled:text-gray-400 disabled:cursor-not-allowed bg-white hover:bg-gray-100"
                        title="Halaman Sebelumnya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <!-- Center Container: Halaman X dari Y -->
                <div class="px-4 py-1.5 bg-white border border-[#E5E7EB] rounded-lg text-sm font-dm text-[#71717A] flex items-center justify-center">
                    <span>Halaman <strong x-text="currentPage" class="font-semibold text-[#111827] ml-1 mr-1"></strong> dari <strong x-text="numPages || '-'" class="font-semibold text-[#111827] ml-1"></strong></span>
                </div>

                <!-- Right NavButton (Next Page) -->
                <button @click="nextPage()"
                        :disabled="currentPage >= numPages || loading"
                        class="p-2 rounded-lg border border-[#E5E7EB] transition-colors flex items-center justify-center text-[#111827] disabled:bg-[#F4F4F5] disabled:text-gray-400 disabled:cursor-not-allowed bg-white hover:bg-gray-100"
                        title="Halaman Selanjutnya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

            </div>
        </div>

    </div>
@endsection

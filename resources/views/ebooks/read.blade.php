@extends('layouts.dashboard')

@section('title', 'Membaca: ' . $ebook->title)

@section('bodyClass', 'pt-16 w-full overflow-hidden bg-gray-50')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        /* Custom scrollbar for a cleaner look */
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
<div class="w-full flex flex-col h-[calc(100vh-64px)]" 
    x-data="ebookReader()"
    data-pdf-url="{{ $pdfUrl }}"
    data-start-page="{{ $startPage }}"
    data-progress-url="{{ route('ebooks.progress.update', $ebook) }}"
    data-refresh-url="{{ route('ebooks.refresh-url', $ebook) }}"
    @keydown.window.arrow-right="nextPage()"
    @keydown.window.arrow-left="prevPage()"
    @contextmenu.prevent>
    
    <!-- Top Bar -->
    <div class="bg-white border-b border-gray-200 flex items-center justify-between px-4 py-3 shrink-0 shadow-sm z-20">
        <div class="flex items-center gap-4">
            <a href="{{ route('ebooks.show', $ebook) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-black text-sm transition-colors font-dm" title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <h1 class="font-bricolage font-bold text-gray-900 text-lg truncate max-w-[200px] sm:max-w-sm md:max-w-xl">{{ $ebook->title }}</h1>
        </div>

        <div class="flex items-center gap-4">
            <!-- Zoom Controls -->
            <div class="flex items-center bg-gray-50 rounded-sm border border-gray-200 p-1 gap-1">
                <button @click="zoomOut()" :disabled="scale <= 0.5 || loading" class="text-gray-600 hover:text-black hover:bg-gray-200 disabled:opacity-30 disabled:hover:bg-transparent p-1.5 rounded transition-colors" title="Perkecil">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" /></svg>
                </button>
                <input type="range" x-model.number="scale" @change="updateZoom()" min="0.5" max="3" step="0.1" :disabled="loading" class="w-20 md:w-32 h-1.5 bg-gray-300 rounded-lg appearance-none cursor-pointer accent-teal-600 disabled:opacity-50 disabled:cursor-not-allowed">
                <button @click="zoomIn()" :disabled="scale >= 3 || loading" class="text-gray-600 hover:text-black hover:bg-gray-200 disabled:opacity-30 disabled:hover:bg-transparent p-1.5 rounded transition-colors" title="Perbesar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                </button>
                <span x-text="Math.round(scale * 100) + '%'" class="text-xs font-mono w-10 text-center text-gray-700 font-medium ml-1"></span>
            </div>
        </div>
    </div>

    <!-- Reader Area -->
    <div class="flex-1 overflow-hidden flex relative bg-[#f8f8f8]">
        
        <!-- Left Navigation Overlay -->
        <button @click="prevPage()" :disabled="currentPage <= 1 || loading" class="absolute left-0 top-0 bottom-0 w-16 md:w-24 group flex items-center justify-center z-10 disabled:cursor-not-allowed">
            <div class="bg-white/90 backdrop-blur shadow-sm p-3 rounded-full opacity-0 group-hover:opacity-100 transition-opacity disabled:hidden text-gray-700 hover:text-black hover:bg-white border border-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </div>
        </button>

        <!-- Canvas Wrapper -->
        <div class="flex-1 reader-scroll overflow-auto p-4 md:p-8 select-none relative z-0">
            
            <!-- Loading State -->
            <div x-show="loading" class="flex flex-col items-center justify-center h-full text-gray-400 absolute inset-0 bg-[#f8f8f8] z-10">
                <svg class="animate-spin h-10 w-10 text-teal-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="font-dm text-sm tracking-wide text-gray-500 animate-pulse">Memuat halaman...</p>
            </div>
            
            <!-- Error State -->
            <div x-show="error" x-cloak class="flex flex-col items-center justify-center h-full text-red-500 absolute inset-0 bg-[#f8f8f8] z-10">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="font-dm text-sm font-medium text-red-600" x-text="error"></p>
                <button @click="loadDocument()" class="mt-4 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-sm transition text-xs border border-red-200 font-medium font-dm">Coba Lagi</button>
            </div>

            <!-- PDF Canvas -->
            <canvas x-ref="canvas" x-show="!loading && !error" x-cloak class="bg-white shadow-md mx-auto border border-gray-200 transition-transform origin-top max-w-none" @dragstart.prevent></canvas>
            
        </div>

        <!-- Right Navigation Overlay -->
        <button @click="nextPage()" :disabled="currentPage >= numPages || loading" class="absolute right-0 top-0 bottom-0 w-16 md:w-24 group flex items-center justify-center z-10 disabled:cursor-not-allowed">
            <div class="bg-white/90 backdrop-blur shadow-sm p-3 rounded-full opacity-0 group-hover:opacity-100 transition-opacity disabled:hidden text-gray-700 hover:text-black hover:bg-white border border-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </button>

    </div>

    <!-- Bottom Bar (Pagination) -->
    <div class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-center shrink-0 z-20 shadow-[0_-1px_2px_rgba(0,0,0,0.02)]">
        <div class="flex items-center gap-3 font-dm text-sm text-gray-600">
            <button @click="prevPage()" :disabled="currentPage <= 1 || loading" class="hover:text-black disabled:opacity-30 disabled:hover:text-gray-600 transition-colors p-1" title="Halaman Sebelumnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            
            <div class="font-medium bg-gray-50 px-3 py-1 rounded-sm border border-gray-200">
                Halaman <span x-text="currentPage" class="font-bold text-black"></span> dari <span x-text="numPages" class="font-bold text-black"></span>
            </div>

            <button @click="nextPage()" :disabled="currentPage >= numPages || loading" class="hover:text-black disabled:opacity-30 disabled:hover:text-gray-600 transition-colors p-1" title="Halaman Selanjutnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>

</div>
@endsection

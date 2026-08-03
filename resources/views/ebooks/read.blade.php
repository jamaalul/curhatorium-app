@extends('layouts.dashboard')

@section('title', 'Membaca: ' . $ebook->title)

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-gray-50')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection

@section('dashboard-content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-7xl"
    x-data="ebookReader()"
    data-pdf-url="{{ $pdfUrl }}"
    data-start-page="{{ $startPage }}"
    data-progress-url="{{ route('ebooks.progress.update', $ebook) }}"
    data-refresh-url="{{ route('ebooks.refresh-url', $ebook) }}"
    @keydown.window.arrow-right="nextPage()"
    @keydown.window.arrow-left="prevPage()"
    @contextmenu.prevent>
    
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('ebooks.show', $ebook) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-black text-sm transition-colors font-dm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Ebook
        </a>
        <h1 class="font-bricolage font-bold text-gray-900 text-xl truncate max-w-md">{{ $ebook->title }}</h1>
        <div></div>
    </div>

    <!-- Reader Container -->
    <div class="bg-zinc-800 rounded-lg shadow-xl overflow-hidden flex flex-col max-w-4xl mx-auto select-none border border-zinc-700">
        
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-3 p-3 bg-zinc-900 text-zinc-300 sticky top-0 z-10 border-b border-zinc-700 shadow-sm">
            <div class="flex items-center gap-2">
                <button @click="prevPage()" :disabled="currentPage <= 1 || loading" class="bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white p-2 rounded transition-colors" title="Halaman Sebelumnya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="px-2 font-mono text-sm tracking-wide">
                    Halaman <span x-text="currentPage" class="text-white font-bold"></span> / <span x-text="numPages" class="text-white font-bold"></span>
                </div>
                <button @click="nextPage()" :disabled="currentPage >= numPages || loading" class="bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white p-2 rounded transition-colors" title="Halaman Selanjutnya">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
            
            <div class="ml-auto flex items-center gap-2">
                <button @click="zoomOut()" :disabled="scale <= 0.5 || loading" class="bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white p-2 rounded transition-colors" title="Perkecil">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" /></svg>
                </button>
                <span x-text="Math.round(scale * 100) + '%'" class="text-xs font-mono w-12 text-center inline-block"></span>
                <button @click="zoomIn()" :disabled="scale >= 3 || loading" class="bg-zinc-700 hover:bg-zinc-600 disabled:opacity-50 disabled:cursor-not-allowed text-white p-2 rounded transition-colors" title="Perbesar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                </button>
            </div>
        </div>

        <!-- Canvas Wrapper -->
        <div class="flex justify-center items-start bg-zinc-600 py-6 px-4 overflow-auto min-h-[60vh] max-h-[75vh]" style="scrollbar-color: #52525b #3f3f46;">
            <!-- Loading State -->
            <div x-show="loading" class="flex flex-col items-center justify-center py-16 text-zinc-400">
                <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-white mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="font-dm text-sm tracking-wide animate-pulse">Memuat Ebook...</p>
            </div>
            
            <!-- Error State -->
            <div x-show="error" x-cloak class="flex flex-col items-center justify-center py-16 text-red-400">
                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <p class="font-dm text-sm font-medium" x-text="error"></p>
                <button @click="loadDocument()" class="mt-4 px-4 py-2 bg-red-900/50 hover:bg-red-900/80 text-white rounded transition text-xs border border-red-800">Coba Lagi</button>
            </div>

            <!-- PDF Canvas -->
            <canvas x-ref="canvas" x-show="!loading && !error" x-cloak class="bg-white shadow-2xl rounded-sm max-w-full h-auto" @dragstart.prevent></canvas>
        </div>
    </div>
</div>
@endsection

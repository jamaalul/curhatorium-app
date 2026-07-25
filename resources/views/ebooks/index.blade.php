@extends('layouts.dashboard')

@section('title', 'Ebook | Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-white')

@section('head')
    <meta name="description" content="Katalog ebook Curhatorium">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <!-- Banner Section -->
    <div class="relative bg-teal-500 pt-16 pb-20 text-center">
        <h1 class="z-10 relative font-bricolage drop-shadow-md font-extrabold text-white text-4xl md:text-5xl tracking-wider">Ebook Curhatorium</h1>
        <div class="bottom-0 left-0 z-20 absolute bg-yellow-400 py-2 w-full">
            <p class="font-dm text-black text-sm md:text-base">Pilih bacaan digital untuk mendukung proses refleksi dan pengembangan diri.</p>
        </div>
    </div>

    <div class="flex md:flex-row flex-col gap-10 mx-auto px-4 sm:px-6 lg:px-8 py-12 max-w-7xl">
        <!-- Sidebar -->
        <div class="flex-shrink-0 w-full md:w-1/4">
            <h2 class="mb-6 font-bold text-gray-900 text-lg font-bricolage">Kategori Ebook</h2>
            <ul class="space-y-4 text-gray-700 font-dm">
                <li><a href="{{ route('ebooks.index') }}" class="hover:text-black transition-colors @if(!request('category')) font-bold text-black @endif">Semua Ebook</a></li>
                @foreach($categories as $category)
                    <li><a href="{{ route('ebooks.index', ['category' => $category->slug]) }}" class="hover:text-black transition-colors @if(request('category') === $category->slug) font-bold text-black @endif">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <!-- Main Content -->
        <div class="w-full md:w-3/4">
            <!-- Top Bar -->
            <div class="flex justify-between items-center mb-8 pb-4 border-gray-200 border-b">
                <div></div>
                <div class="flex items-center gap-3">
                    <label for="sort" class="font-bold text-gray-900 text-sm font-dm">Sortir</label>
                    <select id="sort" class="px-3 py-1.5 border border-gray-300 focus:border-black rounded-sm outline-none focus:ring-black text-sm font-dm">
                        <option>Terbaru</option>
                        <option>Harga Terendah</option>
                        <option>Harga Tertinggi</option>
                    </select>
                </div>
            </div>

            @if($ebooks->isEmpty())
                <div class="py-12 text-center rounded-2xl border border-gray-200 bg-gray-50">
                    <p class="font-dm text-gray-500">Belum ada ebook yang tersedia.</p>
                </div>
            @else
                <div class="gap-x-8 gap-y-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($ebooks as $ebook)
                        <article class="group flex flex-col h-full">
                            <!-- Image -->
                            <a href="{{ route('ebooks.show', $ebook) }}" class="block relative flex justify-center items-center bg-[#f8f8f8] mb-4 aspect-[3/4] overflow-hidden rounded-xl">
                                @if($ebook->cover_image)
                                    <img src="{{ asset('storage/'.$ebook->cover_image) }}" alt="{{ $ebook->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="flex flex-col items-center gap-3 text-gray-300">
                                        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                        </svg>
                                        <span class="font-dm text-sm">No cover</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Details -->
                            <div class="flex flex-col flex-1">
                                @if($ebook->category)
                                    <p class="mb-2 font-dm text-xs font-semibold uppercase tracking-wider text-teal-600">{{ $ebook->category->name }}</p>
                                @endif
                                <h3 class="mb-1 font-bricolage font-bold text-gray-900 text-lg leading-snug">
                                    <a href="{{ route('ebooks.show', $ebook) }}" class="hover:underline">{{ $ebook->title }}</a>
                                </h3>
                                
                                <div class="mt-auto pt-4 flex items-center justify-between gap-4">
                                    <span class="font-dm text-lg font-bold text-gray-900">Rp{{ number_format((float) $ebook->price, 0, ',', '.') }}</span>
                                    <a href="{{ route('ebooks.show', $ebook) }}" class="rounded-xl bg-primary-500 px-4 py-2 font-dm text-sm font-medium text-white transition hover:bg-primary-600">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $ebooks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Library Saya | Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-gray-50')

@section('head')
    <meta name="description" content="Library Ebook Saya">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-12 max-w-7xl">

        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('ebooks.index') }}"
                    class="inline-flex items-center gap-2 mb-4 font-dm text-gray-500 hover:text-black text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Katalog
                </a>
                <h1 class="font-bricolage font-extrabold text-gray-900 text-3xl md:text-4xl tracking-tight">Library Ebook
                </h1>
                <p class="mt-2 font-dm text-gray-600">Koleksi ebook yang telah Anda beli.</p>
            </div>
        </div>

        <div class="flex md:flex-row flex-col gap-10">
            <!-- Sidebar -->
            <div class="flex-shrink-0 w-full md:w-1/4">
                <h2 class="mb-6 font-bricolage font-bold text-gray-900 text-lg">Kategori Ebook</h2>
                <ul class="space-y-4 font-dm text-gray-700">
                    <li><a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="hover:text-black transition-colors @if(!request('category')) font-bold text-black @endif">Semua Ebook</a></li>
                    @foreach($categories as $category)
                        <li><a href="{{ request()->fullUrlWithQuery(['category' => $category->slug, 'page' => null]) }}" class="hover:text-black transition-colors @if(request('category') === $category->slug) font-bold text-black @endif">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Main Content -->
            <div class="w-full md:w-3/4">
                <!-- Top Bar -->
                <div class="flex justify-between items-center mb-8 pb-4 border-gray-200 border-b">
                    <div></div>
                    <form method="GET" action="{{ route('ebooks.library') }}" class="flex items-center gap-3">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <label for="sort" class="font-dm font-bold text-gray-900 text-sm">Sortir</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-300 focus:border-black rounded-sm outline-none focus:ring-black font-dm text-sm">
                            <option value="latest" @selected(request('sort') === 'latest' || !request('sort'))>Pembelian Terbaru</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Pembelian Terlama</option>
                            <option value="title_asc" @selected(request('sort') === 'title_asc')>Judul A-Z</option>
                            <option value="title_desc" @selected(request('sort') === 'title_desc')>Judul Z-A</option>
                        </select>
                    </form>
                </div>

                @if($orders->isEmpty())
                    <div class="bg-white shadow-sm py-16 border border-gray-200 rounded-2xl text-center">
                        <div class="inline-flex justify-center items-center bg-gray-100 mb-4 rounded-full w-16 h-16 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="mb-1 font-bricolage font-bold text-gray-900 text-lg">Library Anda Kosong</h3>
                        <p class="mx-auto mb-6 max-w-md font-dm text-gray-500">Anda belum membeli ebook apapun di kategori ini. Yuk jelajahi katalog kami dan temukan bacaan menarik untuk Anda.</p>
                        <a href="{{ route('ebooks.index') }}"
                            class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 shadow-sm px-6 py-3 rounded-xl font-dm font-bold text-white text-sm transition-colors">
                            Jelajahi Katalog
                        </a>
                    </div>
                @else
                    <div class="gap-x-8 gap-y-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($orders as $order)
                            @php $ebook = $order->orderable; @endphp
                            @if($ebook)
                                <article
                                    class="group flex flex-col bg-white shadow-sm hover:shadow-md p-4 border border-gray-100 rounded-2xl h-full transition-shadow">
                                    <!-- Image -->
                                    <a href="{{ route('ebooks.show', $ebook) }}"
                                        class="block relative flex justify-center items-center bg-[#f8f8f8] mb-4 rounded-xl aspect-[3/4] overflow-hidden">
                                        @if($ebook->cover_image)
                                            <img src="{{ asset('storage/' . $ebook->cover_image) }}" alt="{{ $ebook->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="flex flex-col items-center gap-3 text-gray-300">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                        @endif
                                    </a>

                                    <!-- Details -->
                                    <div class="flex flex-col flex-1">
                                        @if($ebook->category)
                                            <p class="mb-1 font-dm font-bold text-[11px] text-teal-600 uppercase tracking-wider">
                                                {{ $ebook->category->name }}</p>
                                        @endif
                                        <h3 class="mb-1 font-bricolage font-bold text-gray-900 text-base line-clamp-2 leading-snug">
                                            <a href="{{ route('ebooks.read', $ebook) }}"
                                                class="hover:text-teal-600 transition-colors">{{ $ebook->title }}</a>
                                        </h3>
                                        <p class="mt-1 mb-4 font-dm text-gray-500 text-xs">Dibeli pada {{ $order->created_at->format('d M Y') }}
                                        </p>

                                        <div class="mt-auto pt-3 border-gray-100 border-t">
                                            <a href="{{ route('ebooks.read', $ebook) }}"
                                                class="flex justify-center items-center bg-gray-50 hover:bg-gray-100 px-4 py-2.5 rounded-xl w-full font-dm font-bold text-gray-700 hover:text-gray-900 text-sm transition">
                                                Baca
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.dashboard')

@section('title', $ebook->title . ' | Ebook Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-white')

@section('head')
    <meta name="description" content="{{ Str::limit(strip_tags($ebook->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-gray-200 border-b">
        <div class="flex items-center gap-2 mx-auto px-4 sm:px-6 lg:px-8 py-3 max-w-7xl text-gray-500 text-xs">
            <a href="{{ route('ebooks.index') }}" class="hover:text-gray-900 transition-colors">Ebook</a>
            <svg class="flex-shrink-0 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-medium text-gray-900 truncate">{{ $ebook->title }}</span>
        </div>
    </div>

    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16 max-w-7xl">
        <div class="flex md:flex-row flex-col gap-10 lg:gap-16">

            {{-- ── LEFT: Cover Image ── --}}
            <div class="flex flex-col gap-4 mx-auto w-[60vw] md:w-full lg:w-5/12">
                <div class="relative flex justify-center items-center bg-[#f8f8f8] rounded-sm aspect-[3/4] overflow-hidden">
                    @if($ebook->cover_image)
                        <img src="{{ asset('storage/'.$ebook->cover_image) }}" alt="{{ $ebook->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center gap-2 text-gray-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="text-sm">No cover</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── RIGHT: Product Info ── --}}
            <div class="flex flex-col gap-6 w-full lg:w-7/12">
                <div>
                    @if($ebook->category)
                        <p class="mb-2 font-semibold text-teal-600 text-sm uppercase tracking-wider">{{ $ebook->category->name }}</p>
                    @endif
                    <h1 class="mb-3 font-bold text-gray-900 text-2xl sm:text-3xl leading-snug">
                        {{ $ebook->title }}
                    </h1>
                    <div class="flex items-center gap-4">
                        <p class="font-bold text-gray-900 text-2xl">
                            Rp{{ number_format((float) $ebook->price, 0, ',', '.') }}
                        </p>
                        @if($ebook->page_count)
                            <span class="pl-4 border-gray-300 border-l text-gray-500 text-sm">{{ $ebook->page_count }} halaman</span>
                        @endif
                    </div>
                </div>

                <div class="border-gray-200 border-t"></div>

                @if($ebook->description)
                    <div class="max-w-none text-gray-700 leading-relaxed prose prose-sm">
                        {!! $ebook->description !!}
                    </div>
                @endif

                <div class="border-gray-200 border-t"></div>

                {{-- Action / Buy --}}
                <div class="flex flex-col gap-3">
                    @if($hasPurchased)
                        <div class="bg-green-50 mb-2 p-4 border border-green-200 rounded-sm">
                            <p class="text-green-800 text-sm">Anda telah memiliki akses ke ebook ini.</p>
                        </div>
                        <a href="{{ route('ebooks.read', $ebook) }}" class="flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 px-6 py-3.5 rounded-sm font-semibold text-white text-sm transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                            </svg>
                            Baca Ebook
                        </a>
                    @else
                        @auth
                            <form method="POST" action="{{ route('ebooks.checkout', $ebook) }}">
                                @csrf
                                <button type="submit" class="flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 px-6 py-3.5 rounded-sm w-full font-semibold text-white text-sm transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                                    </svg>
                                    Beli Sekarang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 px-6 py-3.5 rounded-sm font-semibold text-white text-sm transition-all duration-200">
                                Masuk untuk Membeli
                            </a>
                        @endauth
                    @endif
                </div>

                {{-- Back --}}
                <div class="flex items-center gap-4 mt-auto pt-4">
                    <a href="{{ route('ebooks.index') }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-900 text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Daftar Ebook
                    </a>
                </div>
            </div>

        </div>

        {{-- ── Reviews / Comments ── --}}
        <div id="reviews" class="mt-16 pt-12 border-gray-200 border-t">
            <h2 class="mb-8 font-bold text-gray-900 text-xl">Ulasan & Komentar</h2>

            @if(session('success'))
                <div class="mb-8 bg-green-50 border border-green-200 text-green-800 rounded-sm p-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 bg-red-50 border border-red-200 text-red-800 rounded-sm p-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($hasPurchased && !$hasReviewed)
                <div class="mb-8 bg-gray-50 border border-gray-200 rounded-sm p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900 text-base mb-3">Tulis Ulasan</h3>
                    <form action="{{ route('ebooks.review', $ebook) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="content" class="sr-only">Ulasan</label>
                            <textarea id="content" name="content" rows="3" class="block w-full rounded-sm border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Bagaimana pendapat Anda tentang ebook ini? (Min. 3 karakter)" required minlength="3" maxlength="500"></textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-sm text-sm transition-colors">
                                Kirim Ulasan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if($comments->isNotEmpty())
                <div class="flex flex-col gap-6">
                    @foreach($comments as $comment)
                        <div class="bg-gray-50 shadow-sm p-5 border border-gray-100 rounded-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex flex-shrink-0 justify-center items-center bg-gray-200 rounded-full w-10 h-10 font-bold text-gray-500">
                                    {{ substr($comment->user?->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ $comment->user?->name ?? 'Anonim' }}</h4>
                                    <p class="text-gray-500 text-xs">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->content }}</p>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $comments->fragment('reviews')->links() }}
                </div>
            @else
                <div class="flex flex-col justify-center items-center bg-[#f8f8f8] py-12 border border-gray-300 border-dashed rounded-sm">
                    <svg class="mb-3 w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="font-medium text-gray-900 text-sm">Belum ada ulasan</p>
                    <p class="mt-1 text-gray-500 text-xs">Jadilah yang pertama memberikan ulasan untuk ebook ini.</p>
                </div>
            @endif
        </div>

        {{-- ── Related Ebooks ── --}}
        @if(isset($relatedEbooks) && $relatedEbooks->isNotEmpty())
            <div class="mt-16 pt-12 border-gray-200 border-t">
                <h2 class="mb-8 font-bold text-gray-900 text-xl">Ebook Lainnya</h2>
                <div class="gap-x-6 gap-y-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach($relatedEbooks as $rel)
                        <div class="group flex flex-col">
                            <a href="{{ route('ebooks.show', $rel) }}" class="relative flex justify-center items-center bg-[#f8f8f8] mb-3 rounded-sm aspect-[3/4] overflow-hidden">
                                @if($rel->cover_image)
                                    <img src="{{ asset('storage/'.$rel->cover_image) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="text-gray-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            @if($rel->category)
                                <p class="mb-1 font-semibold text-teal-600 text-xs uppercase tracking-wider">{{ $rel->category->name }}</p>
                            @endif
                            <h3 class="mb-1 font-bold text-gray-900 text-sm leading-snug">
                                <a href="{{ route('ebooks.show', $rel) }}" class="hover:underline">{{ $rel->title }}</a>
                            </h3>
                            <p class="text-gray-700 text-sm">Rp{{ number_format((float) $rel->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

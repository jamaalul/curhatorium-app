@extends('layouts.dashboard')

@section('title', $ebook->title . ' | Ebook Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-white')

@section('head')
    <meta name="description" content="{{ Str::limit(strip_tags($ebook->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <div class="border-b border-gray-200 bg-gray-50">
        <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-3 text-xs text-gray-500 sm:px-6 lg:px-8">
            <a href="{{ route('ebooks.index') }}" class="transition hover:text-gray-900">Ebook</a>
            <svg class="h-3 w-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="truncate font-medium text-gray-900">{{ $ebook->title }}</span>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-16">
            <div class="overflow-hidden rounded-2xl bg-gray-100">
                <div class="flex aspect-[4/5] items-center justify-center">
                    @if($ebook->cover_image)
                        <img src="{{ asset('storage/'.$ebook->cover_image) }}" alt="{{ $ebook->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex flex-col items-center gap-3 text-gray-300">
                            <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="font-dm text-sm">No cover</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div>
                    @if($ebook->category)
                        <p class="mb-3 font-dm text-xs font-semibold uppercase tracking-wider text-teal-600">{{ $ebook->category->name }}</p>
                    @endif
                    <h1 class="font-bricolage text-3xl font-bold leading-tight text-gray-900 md:text-4xl">{{ $ebook->title }}</h1>
                    <div class="mt-4 flex flex-wrap items-center gap-4 font-dm text-sm text-gray-500">
                        @if($ebook->page_count)
                            <span>{{ $ebook->page_count }} halaman</span>
                        @endif
                        <span>Rp{{ number_format((float) $ebook->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($ebook->description)
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! $ebook->description !!}
                    </div>
                @endif

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                    <p class="font-dm text-sm text-gray-600">
                        File ebook hanya akan diberikan melalui alur akses setelah pembayaran berhasil.
                    </p>

                    <div class="mt-4">
                        @auth
                            <form method="POST" action="{{ route('ebooks.checkout', $ebook) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-primary-500 px-5 py-4 font-dm text-base font-medium text-white transition hover:bg-primary-600 sm:w-auto">
                                    Beli Ebook
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex rounded-xl bg-primary-500 px-5 py-4 font-dm text-base font-medium text-white transition hover:bg-primary-600">
                                Masuk untuk membeli
                            </a>
                        @endauth
                    </div>
                </div>

                @if($ebook->comments->isNotEmpty())
                    <section class="border-t border-gray-200 pt-6">
                        <h2 class="font-bricolage text-xl font-semibold text-gray-900">Komentar</h2>
                        <div class="mt-4 space-y-4">
                            @foreach($ebook->comments as $comment)
                                <div class="rounded-xl border border-gray-200 p-4">
                                    <p class="font-dm text-sm leading-6 text-gray-700">{{ $comment->content }}</p>
                                    <p class="mt-2 font-dm text-xs text-gray-400">{{ $comment->user?->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
@endsection

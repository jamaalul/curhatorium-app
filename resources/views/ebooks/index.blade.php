@extends('layouts.dashboard')

@section('title', 'Ebook | Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-white')

@section('head')
    <meta name="description" content="Katalog ebook Curhatorium">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <div class="bg-teal-500 px-4 py-16 text-center">
        <h1 class="font-bricolage text-4xl font-extrabold tracking-wider text-white md:text-5xl">Ebook Curhatorium</h1>
        <p class="mx-auto mt-4 max-w-2xl font-dm text-base text-white/90">
            Pilih bacaan digital untuk mendukung proses refleksi dan pengembangan diri.
        </p>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if($ebooks->isEmpty())
            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-6 py-12 text-center">
                <p class="font-dm text-gray-500">Belum ada ebook yang tersedia.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($ebooks as $ebook)
                    <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <a href="{{ route('ebooks.show', $ebook) }}" class="flex aspect-[4/3] items-center justify-center bg-gray-100">
                            @if($ebook->cover_image)
                                <img src="{{ asset('storage/'.$ebook->cover_image) }}" alt="{{ $ebook->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex flex-col items-center gap-3 text-gray-300">
                                    <svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="font-dm text-sm">No cover</span>
                                </div>
                            @endif
                        </a>

                        <div class="flex flex-1 flex-col gap-4 p-6">
                            <div>
                                @if($ebook->category)
                                    <p class="mb-2 font-dm text-xs font-semibold uppercase tracking-wider text-teal-600">{{ $ebook->category->name }}</p>
                                @endif
                                <h2 class="font-bricolage text-xl font-semibold text-gray-900">
                                    <a href="{{ route('ebooks.show', $ebook) }}" class="hover:underline">{{ $ebook->title }}</a>
                                </h2>
                                @if($ebook->description)
                                    <p class="mt-3 line-clamp-3 font-dm text-sm leading-6 text-gray-600">
                                        {{ Str::limit(strip_tags($ebook->description), 140) }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-auto flex items-center justify-between gap-4">
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
@endsection

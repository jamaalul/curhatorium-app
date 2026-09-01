@extends('layouts.dashboard')

@section('title', $module->title)

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-5xl">
        <a href="{{ route('e-class.index') }}" class="text-primary-600">← Katalog E-Class</a>
        <div class="gap-8 grid lg:grid-cols-[1fr_320px] mt-6">
            <section>
                <h1 class="font-bricolage font-semibold text-zinc-900 text-4xl">{{ $module->title }}</h1>
                <div class="mt-4 text-zinc-600 prose max-w-none">{!! $module->description !!}</div>

                <h2 class="mt-10 font-bricolage font-semibold text-zinc-900 text-2xl">Daftar chapter</h2>
                <ol class="space-y-3 mt-4">
                    @forelse ($module->chapters as $chapter)
                        <li class="flex justify-between bg-white p-4 border border-zinc-200 rounded-xl">
                            <span>{{ $chapter->order_number }}. {{ $chapter->title }}</span>
                            <span class="text-zinc-500 text-sm">{{ $chapter->type->value }}</span>
                        </li>
                    @empty
                        <li class="text-zinc-500">Belum ada chapter.</li>
                    @endforelse
                </ol>
            </section>

            <aside class="bg-white shadow-sm p-6 border border-zinc-200 rounded-2xl h-fit">
                <p class="font-bricolage font-semibold text-zinc-900 text-3xl">Rp{{ number_format((float) $module->price, 0, ',', '.') }}</p>
                @if ($hasAccess)
                    <p class="mt-3 text-teal-700">Module ini sudah Anda miliki.</p>
                    <a href="{{ route('e-class.library') }}" class="block bg-primary-500 mt-5 px-4 py-3 rounded-xl font-medium text-white text-center">Buka Library</a>
                @elseif (auth()->check())
                    <form action="{{ route('e-class.checkout', $module) }}" method="POST" class="mt-5">
                        @csrf
                        <button class="bg-primary-500 px-4 py-3 rounded-xl w-full font-medium text-white">Beli module</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block bg-primary-500 mt-5 px-4 py-3 rounded-xl font-medium text-white text-center">Masuk untuk membeli</a>
                @endif
            </aside>
        </div>
    </main>
@endsection

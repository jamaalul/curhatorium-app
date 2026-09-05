@extends('layouts.dashboard')

@section('title', 'E-Class')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-6xl">
        <div class="flex sm:flex-row flex-col sm:justify-between sm:items-end gap-4 mb-8">
            <div>
                <p class="font-medium text-primary-600">Curhatorium Class</p>
                <h1 class="font-bricolage font-semibold text-zinc-900 text-4xl">E-Class</h1>
                <p class="mt-2 text-zinc-600">Belajar terarah melalui bacaan, video, dan quiz.</p>
            </div>
            @auth
                <a href="{{ route('e-class.library') }}" class="bg-primary-500 px-5 py-3 rounded-xl font-medium text-white">
                    Library Saya
                </a>
            @endauth
        </div>

        <div class="gap-6 grid md:grid-cols-2 lg:grid-cols-3">
            @forelse ($modules as $module)
                <article class="flex flex-col bg-white shadow-sm p-6 border border-zinc-200 rounded-2xl">
                    <h2 class="font-bricolage font-semibold text-zinc-900 text-2xl">{{ $module->title }}</h2>
                    <p class="flex-1 mt-3 text-zinc-600">{{ \Illuminate\Support\Str::limit(strip_tags($module->description), 150) }}</p>
                    <div class="flex justify-between mt-6 text-sm text-zinc-500">
                        <span>{{ $module->chapters_count }} chapter</span>
                        <span class="font-semibold text-zinc-900">Rp{{ number_format((float) $module->price, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('e-class.show', $module) }}" class="mt-5 font-semibold text-primary-600">Lihat module →</a>
                </article>
            @empty
                <p class="col-span-full py-16 text-zinc-500 text-center">Belum ada module E-Class yang diterbitkan.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $modules->links() }}</div>
    </main>
@endsection

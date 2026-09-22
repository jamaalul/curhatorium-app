@extends('layouts.dashboard')

@section('title', $chapter->title)

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-4xl">
        <a href="{{ route('e-class.library') }}" class="text-primary-600">← Library E-Class</a>
        <p class="mt-6 font-medium text-zinc-500">{{ $module->title }}</p>
        <h1 class="mt-1 font-bricolage font-semibold text-zinc-900 text-4xl">{{ $chapter->title }}</h1>
        <div class="bg-black mt-8 rounded-2xl overflow-hidden aspect-video">
            <video controls preload="metadata" class="w-full h-full" src="{{ $videoUrl }}"></video>
        </div>
        <form action="{{ route('e-class.chapters.complete', [$module, $chapter]) }}" method="POST" class="mt-6">
            @csrf
            <button class="bg-primary-500 px-5 py-3 rounded-xl font-medium text-white">Tandai selesai</button>
        </form>
    </main>
@endsection

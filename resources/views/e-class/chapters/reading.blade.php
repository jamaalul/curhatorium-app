@extends('layouts.dashboard')

@section('title', $chapter->title)

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-3xl">
        <a href="{{ route('e-class.library') }}" class="text-primary-600">← Library E-Class</a>
        <p class="mt-6 font-medium text-zinc-500">{{ $module->title }}</p>
        <h1 class="mt-1 font-bricolage font-semibold text-zinc-900 text-4xl">{{ $chapter->title }}</h1>
        <article class="bg-white mt-8 p-6 border border-zinc-200 rounded-2xl prose max-w-none">{!! $chapter->text_content !!}</article>
        <form action="{{ route('e-class.chapters.complete', [$module, $chapter]) }}" method="POST" class="mt-6">
            @csrf
            <button class="bg-primary-500 px-5 py-3 rounded-xl font-medium text-white">Tandai selesai</button>
        </form>
    </main>
@endsection

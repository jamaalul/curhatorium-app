@extends('layouts.dashboard')

@section('title', 'Hasil Quiz')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-3xl">
        <p class="font-medium text-zinc-500">{{ $attempt->chapter->module->title }}</p>
        <h1 class="mt-1 font-bricolage font-semibold text-zinc-900 text-4xl">Hasil {{ $attempt->chapter->title }}</h1>
        <div class="bg-white mt-8 p-8 border border-zinc-200 rounded-2xl text-center">
            <p class="text-zinc-500">Attempt ke-{{ $attempt->attempt_number }}</p>
            <p class="mt-3 font-bricolage font-semibold text-primary-600 text-5xl">{{ (float) $attempt->score }} / {{ (float) $attempt->max_score }}</p>
            <p class="mt-4 text-zinc-600">Quiz selesai dan progres chapter telah diperbarui.</p>
        </div>
        <div class="flex gap-4 mt-6">
            <form action="{{ route('e-class.quiz-attempts.start', [$attempt->chapter->module, $attempt->chapter]) }}" method="POST">
                @csrf
                <button class="bg-primary-500 px-5 py-3 rounded-xl font-medium text-white">Ulangi quiz</button>
            </form>
            <a href="{{ route('e-class.library') }}" class="px-5 py-3 border border-zinc-300 rounded-xl font-medium">Kembali ke library</a>
        </div>
    </main>
@endsection

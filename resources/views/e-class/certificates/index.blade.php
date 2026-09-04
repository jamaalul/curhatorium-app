@extends('layouts.dashboard')

@section('title', 'Sertifikat E-Class')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-5xl">
        <a href="{{ route('e-class.library') }}" class="text-primary-600">← Library E-Class</a>
        <h1 class="mt-6 font-bricolage font-semibold text-zinc-900 text-4xl">Sertifikat Saya</h1>
        <div class="space-y-4 mt-8">
            @forelse ($certificates as $certificate)
                <a href="{{ route('e-class.certificates.show', $certificate) }}" class="flex sm:flex-row flex-col sm:justify-between bg-white p-5 border border-zinc-200 rounded-2xl">
                    <span class="font-semibold text-zinc-900">{{ $certificate->module?->title ?? 'Module E-Class' }}</span>
                    <span class="text-zinc-500">{{ $certificate->certificate_number }}</span>
                </a>
            @empty
                <p class="py-16 text-zinc-500 text-center">Belum ada sertifikat yang diterbitkan.</p>
            @endforelse
        </div>
    </main>
@endsection

@extends('layouts.dashboard')

@section('title', 'Library E-Class')

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-6xl">
        <div class="flex sm:flex-row flex-col sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="font-bricolage font-semibold text-zinc-900 text-4xl">Library E-Class</h1>
                <p class="mt-2 text-zinc-600">Lanjutkan module yang sudah Anda miliki.</p>
            </div>
            <a href="{{ route('e-class.certificates.index') }}" class="font-semibold text-primary-600">Sertifikat Saya</a>
        </div>

        <div class="gap-6 grid md:grid-cols-2">
            @forelse ($entitlements as $entitlement)
                @php
                    $module = $entitlement->module;
                    $moduleProgress = $module->moduleProgresses->first();
                    $percentage = $module->chapters_count > 0
                        ? round(($module->completed_chapters_count / $module->chapters_count) * 100, 2)
                        : 0;
                    $firstChapter = $module->chapters->first();
                @endphp
                <article class="bg-white p-6 border border-zinc-200 rounded-2xl">
                    <h2 class="font-bricolage font-semibold text-zinc-900 text-2xl">{{ $module->title }}</h2>
                    <p class="mt-2 text-zinc-500 text-sm">
                        {{ $module->completed_chapters_count }}/{{ $module->chapters_count }} chapter selesai
                    </p>
                    <progress class="mt-4 w-full h-2 accent-teal-500" max="100" value="{{ $percentage }}">{{ $percentage }}%</progress>
                    <div class="flex justify-between mt-2 text-zinc-500 text-sm">
                        <span>{{ $percentage }}%</span>
                        <span>Terakhir diakses {{ $moduleProgress?->last_accessed_at?->diffForHumans() ?? 'belum pernah' }}</span>
                    </div>
                    <ol class="space-y-2 mt-5">
                        @foreach ($module->chapters as $chapter)
                            <li>
                                <a href="{{ route('e-class.chapters.show', [$module, $chapter]) }}" class="font-medium text-primary-600">
                                    {{ $chapter->order_number }}. {{ $chapter->title }}
                                </a>
                            </li>
                        @endforeach
                    </ol>
                    @if ($moduleProgress?->status === \App\ProgressStatus::Completed && $firstChapter)
                        <form action="{{ route('e-class.certificates.claim', $module) }}" method="POST" class="mt-5">
                            @csrf
                            <button class="bg-teal-600 px-4 py-2 rounded-xl font-medium text-white">Klaim sertifikat</button>
                        </form>
                    @endif
                </article>
            @empty
                <p class="col-span-full py-16 text-zinc-500 text-center">Anda belum memiliki module E-Class aktif.</p>
            @endforelse
        </div>
    </main>
@endsection

@extends('layouts.dashboard')

@section('title', 'Sertifikat ' . $certificate->certificate_number)

@section('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('dashboard-content')
    <main class="mx-auto px-4 py-10 max-w-3xl">
        <a href="{{ route('e-class.certificates.index') }}" class="text-primary-600">← Daftar sertifikat</a>
        <section class="bg-white shadow-sm mt-6 p-8 border border-zinc-200 rounded-2xl text-center">
            <p class="font-semibold text-primary-600">CURHATORIUM E-CLASS</p>
            <h1 class="mt-4 font-bricolage font-semibold text-zinc-900 text-4xl">Sertifikat Penyelesaian</h1>
            <p class="mt-6 text-zinc-500">Diberikan kepada</p>
            <p class="mt-2 font-bricolage font-semibold text-zinc-900 text-3xl">{{ $certificate->user->name }}</p>
            <p class="mt-6 text-zinc-500">atas penyelesaian module</p>
            <p class="mt-2 font-semibold text-zinc-900 text-xl">{{ $certificate->module->title }}</p>
            <p class="mt-8 text-zinc-500 text-sm">{{ $certificate->certificate_number }} · {{ $certificate->issued_at->translatedFormat('d F Y') }}</p>
        </section>
        <div class="flex sm:flex-row flex-col gap-3 mt-6">
            <a href="{{ route('e-class.certificates.download', $certificate) }}" class="bg-primary-500 px-5 py-3 rounded-xl font-medium text-white text-center">Unduh PDF</a>
            <form action="{{ route('e-class.certificates.email', $certificate) }}" method="POST">
                @csrf
                <button class="px-5 py-3 border border-zinc-300 rounded-xl w-full font-medium">Kirim ke email saya</button>
            </form>
        </div>
    </main>
@endsection

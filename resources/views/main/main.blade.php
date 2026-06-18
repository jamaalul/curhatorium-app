@extends('layouts.dashboard')

@section('title', 'Dashboard | Curhatorium')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden')

@section('head')
    @vite('resources/css/app.css')
@endsection

@section('dashboard-content')
    @include('main.announcement')
    @include('components.error', ['msg' => $errors])
    <section class="relative flex flex-col justify-center items-center gap-2 bg-cover shadow-inner px-4 py-8 w-full h-fit"
        style="background-image: url('{{ asset('images/background.webp') }}');">
        <h1 class="font-bold text-[#222222] text-3xl md:text-5xl text-center">
            Halo {{ $user->username }} !
        </h1>
        <p class="text-[#222222] text-base text-center">Siap untuk mencari tempat nyamanmu di Curhatorium?</p>
        <button
            class="flex justify-center items-center gap-2 bg-[#48A6A6] hover:bg-[#357979] shadow-md mt-4 px-6 py-3 rounded-md w-fit md:w-auto text-white transition-colors duration-200"
            onclick="window.location.href='/share-and-talk'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            Mulai Share and Talk
        </button>
    </section>
    @include('main.qotd')
    @include('main.cards')
    @include('main.stats')
    @include('main.features')
    @include('main.article-list')
    @include('main.xp-redemption')
@endsection
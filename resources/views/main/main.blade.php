<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @vite('resources/css/app.css')
</head>

<body class="pt-16 w-full overflow-x-hidden">
    @include('components.navbar')
    @include('main.announcement')
    @include('components.error', ['msg' => $errors])
    @include('components.starter-claimer')
    <section
        class="w-full h-fit px-4 py-8 flex flex-col gap-2 items-center justify-center bg-cover shadow-inner relative"
        style="background-image: url('{{ asset('images/background.webp') }}');">
        <h1 class="text-3xl md:text-5xl font-bold text-center text-[#222222]">
            Halo {{ $user->username }} !
        </h1>
        <p class="text-base text-center text-[#222222]">Siap untuk mencari tempat nyamanmu di Curhatorium?</p>
        <button
            class="bg-[#48A6A6] px-6 py-3 text-white rounded-md transition-colors duration-200 hover:bg-[#357979] w-fit md:w-auto mt-4 shadow-md flex items-center justify-center gap-2"
            onclick="window.location.href='/share-and-talk'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
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
    @include('components.footer')
</body>

</html>

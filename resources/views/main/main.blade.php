<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard | Curhatorium</title>
    <link rel="stylesheet" href="{{asset('css/global.css')}}">
    @vite('resources/css/app.css')
</head>
<body class="pt-16 w-full overflow-x-hidden">
    @include('components.navbar')
    @include('main.announcement')
    <section class="w-full h-fit px-4 py-8 flex flex-col gap-2 items-center justify-center bg-cover shadow-inner relative" style="background-image: url('{{ asset('images/background.jpg') }}');">
        <h1 class="text-3xl md:text-5xl font-bold text-center text-[#222222]">
            Halo {{ $user->username }} !
        </h1>
        <p class="text-base text-center text-[#222222]">Siap untuk mencari tempat nyamanmu di Curhatorium?</p>
        <button class="bg-[#48A6A6] px-4 py-2 text-white rounded-md transition-colors duration-200 hover:bg-[#357979] w-fit md:w-auto mt-4 shadow-md flex items-center justify-center gap-2" onclick="window.location.href='/support-group-discussion'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            Gabung Support Group Discussion
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
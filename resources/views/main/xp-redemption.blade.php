<section class="w-full h-fit px-4 py-8 flex flex-col gap-2 items-center justify-center bg-cover shadow-inner relative" style="background-image: url('{{ asset('images/background.jpg') }}');">
    <h1 class="text-3xl md:text-5xl font-bold text-center text-[#222222]">
        Tukar XP Kamu
    </h1>
    <p class="text-base text-center text-[#222222]">Dapatkan hadiah menarik dengan menukarkan XP yang kamu miliki!</p>
    <button class="bg-[#48A6A6] px-4 py-2 text-white rounded-md transition-colors duration-200 hover:bg-[#357979] w-fit md:w-auto mt-4 shadow-md flex items-center justify-center gap-2" onclick="window.location.href='{{ route('xp-redemption.index') }}'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-check" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
            <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
        </svg>
        Lihat Hadiah
    </button>
</section>
@vite('resources/css/app.css')

<div>
    @if (!$hasCalmStarter)
        <div class="z-50 fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 w-screen h-screen">
            <div class="relative flex flex-col bg-white shadow-lg px-6 py-4 border border-[#48A6A6] rounded text-[#48A6A6]"
                role="alert">
                @if(!$hasEverHadCalmStarter)
                    <strong class="mb-2 font-bold text-lg">Selamat Datang di Curhatorium! 🌟</strong>
                    <p class="mt-2">Untuk memulai perjalanan kesejahteraan mental Anda, ayo klaim membership Calm Starter
                        gratis!</p>
                @else
                    <strong class="mb-2 font-bold text-lg">Membership Calm Starter Anda Bulan Lalu Telah Berakhir</strong>
                    <p class="mt-2">Untuk melanjutkan akses ke fitur-fitur Curhatorium, silakan perpanjang membership Calm
                        Starter Anda.</p>
                @endif
                <div class="flex justify-end w-full">
                    <button
                        class="bg-[#48A6A6] hover:bg-[#357979] mt-4 px-4 py-1 rounded-sm w-fit text-white transition-colors duration-200"
                        type="button">
                        @if(!$hasEverHadCalmStarter)
                            Klaim Membership
                        @else
                            Perpanjang Membership
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
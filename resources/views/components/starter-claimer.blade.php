@vite('resources/css/app.css')

<div>
        @if (!$hasCalmStarter)
            <div class="fixed inset-0 flex items-center justify-center z-50 w-screen h-screen bg-black bg-opacity-50">
                <div class="relative bg-white border border-[#48A6A6] text-[#48A6A6] px-6 py-4 rounded shadow-lg flex flex-col" role="alert">
                    @if(!$hasEverHadCalmStarter)
                        <strong class="font-bold text-lg mb-2">Selamat Datang di Curhatorium! 🌟</strong>
                        <p class="mt-2">Untuk memulai perjalanan kesejahteraan mental Anda, ayo klaim membership Calm Starter gratis!</p>
                    @else
                        <strong class="font-bold text-lg mb-2">Membership Calm Starter Anda Bulan Lalu Telah Berakhir</strong>
                        <p class="mt-2">Untuk melanjutkan akses ke fitur-fitur Curhatorium, silakan perpanjang membership Calm Starter Anda.</p>
                    @endif
                    <div class="w-full flex justify-end">
                        <button
                            class="bg-[#48A6A6] text-white hover:bg-[#357979] mt-4 rounded-sm w-fit px-4 py-1 transition-colors duration-200"
                            type="button"
                            onclick="window.location.href='{{ route('membership.claim-starter') }}'"
                        >
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

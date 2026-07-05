<aside x-data class="bg-zinc-100 w-72 h-full">
    <div class="flex flex-row items-center gap-4 p-4 w-full">
        <button class="flex justify-center items-center h-fit aspect-square">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <div class="flex items-center gap-2">
            <img src="{{ asset('assets/mini_logo.png') }}" alt="Logo Curhatorium" class="h-8">
            <h3 class="text-2xl">Facilitator</h3>
        </div>
    </div>
    <div class="flex mt-4 p-4 w-full">
        <button
            @click="$dispatch('open-create-slot-drawer')"
            class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 hover:shadow-sm p-2 rounded-xl w-full text-white hover:scale-105 active:scale-95 transition-all duration-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat slot
        </button>
    </div>
    <div class="flex flex-col gap-2 px-4 pt-4 w-full">
        <p class="">Sesi baru</p>
        <div class="flex flex-col gap-2 w-full h-fit max-h-32 overflow-y-auto">
            {{-- FOR LOOP --}}
            <button
                class="flex justify-between items-center bg-white hover:bg-zinc-200 p-2 rounded-lg w-full transition-all duration-100">
                <p class="text-zinc-600 text-sm">Jamaalul</p>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd"
                        d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd"
                        d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            {{-- END FOR LOOP --}}
        </div>
    </div>
    <div class="flex flex-col gap-2 mt-4 p-4 w-full">
        <p class="">Sesi mendatang</p>
        <div class="flex flex-col gap-2 w-full h-fit max-h-64 overflow-y-auto">
            {{-- FOR LOOP --}}
            <div
                class="flex justify-between items-center bg-white hover:bg-zinc-200 p-2 rounded-lg w-full transition-all duration-100">
                <p class="flex overflow-hidden text-zinc-600 text-sm text-left grow">Jamaalul</p>
                <button
                    class="flex justify-center items-center rounded text-teal-600 text-sm hover:scale-110 active:scale-95 transition-all duration-100">
                    Masuk
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd"
                            d="M5.22 14.78a.75.75 0 0 0 1.06 0l7.22-7.22v5.69a.75.75 0 0 0 1.5 0v-7.5a.75.75 0 0 0-.75-.75h-7.5a.75.75 0 0 0 0 1.5h5.69l-7.22 7.22a.75.75 0 0 0 0 1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div
                class="flex justify-between items-center bg-white hover:bg-zinc-200 p-2 rounded-lg w-full transition-all duration-100">
                <p class="flex overflow-hidden text-zinc-600 text-sm text-left grow">Jamaalul</p>
                <p class="text-zinc-400 text-xs">12 Des 2026 - 16:00</p>
            </div>
            {{-- END FOR LOOP --}}
        </div>
    </div>

    @include('professional.components.create-slot-modal')
</aside>
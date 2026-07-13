<div x-data="{ open: false, data: null }" @open-waiting-drawer.window="data = $event.detail; open = true"
    @keydown.escape.window="open = false" class="z-50 relative" aria-labelledby="slide-over-title" role="dialog"
    aria-modal="true" x-show="open" style="display: none;">

    <!-- Background backdrop -->
    <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="z-50 fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="left-0 fixed inset-y-0 flex pr-10 max-w-full pointer-events-none">
                <div x-show="open" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                    class="relative w-screen max-w-md pointer-events-auto">

                    <!-- Drawer Content -->
                    <div class="flex flex-col bg-white shadow-xl py-6 h-full overflow-y-auto">
                        <div class="flex justify-between items-center px-4 sm:px-6">
                            <h2 class="font-semibold text-zinc-900 text-xl leading-6" id="slide-over-title">Detail Sesi
                                Baru</h2>
                            <button @click="open = false"
                                class="hover:bg-zinc-100 p-1 rounded-full outline-none text-zinc-400 hover:text-zinc-500 transition-colors">
                                <span class="sr-only">Tutup</span>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex-1 mt-6 px-4 sm:px-6 overflow-y-auto">
                            <!-- FIX: added min-h-full so this flex column has real
                                 height to distribute leftover space into. Without it,
                                 the column shrinks to its content's height and mt-auto
                                 on the button row has nowhere to push to. -->
                            <div class="flex flex-col gap-6 min-h-full" x-show="data">
                                <div class="flex flex-col gap-1">
                                    <p class="font-medium text-zinc-500 text-sm">Partisipan</p>
                                    <p class="font-medium text-zinc-800 text-base" x-text="data?.patientName"></p>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <p class="font-medium text-zinc-500 text-sm">Jadwal</p>
                                    <p class="text-zinc-800 text-base">
                                        <span x-text="data?.date"></span><br>
                                        <span class="font-medium text-zinc-500" x-text="data?.time"></span>
                                    </p>
                                </div>

                                <template x-if="data?.type">
                                    <div class="flex flex-col gap-1">
                                        <p class="font-medium text-zinc-500 text-sm">Tipe
                                            Konsultasi</p>
                                        <p class="text-zinc-800 text-base" x-text="data?.type"></p>
                                    </div>
                                </template>

                                <div class="flex gap-3 mt-auto pt-6">
                                    <form method="POST" :action="data?.declineUrl" class="flex-1"
                                        x-data="{ confirming: false }">
                                        @csrf
                                        <template x-if="!confirming">
                                            <button type="button" @click="confirming = true"
                                                class="bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl w-full text-red-600 text-sm active:scale-95 transition-colors duration-100">Tolak</button>
                                        </template>
                                        <template x-if="confirming">
                                            <div class="flex flex-col gap-2">
                                                <p class="mb-1 font-medium text-red-600 text-xs text-center">Yakin
                                                    menolak sesi ini?</p>
                                                <div class="flex gap-2">
                                                    <button type="submit"
                                                        class="flex-1 bg-red-600 hover:bg-red-700 px-2 py-2 rounded-lg text-white text-sm active:scale-95 transition-colors duration-100">Ya,
                                                        Tolak</button>
                                                    <button type="button" @click="confirming = false"
                                                        class="flex-1 bg-zinc-200 hover:bg-zinc-300 px-2 py-2 rounded-lg font-medium text-zinc-700 text-sm active:scale-95 transition-colors duration-100">Batal</button>
                                                </div>
                                            </div>
                                        </template>
                                    </form>

                                    <form method="POST" :action="data?.acceptUrl" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                            class="bg-teal-600 hover:bg-teal-700 shadow-sm hover:shadow px-4 py-2.5 rounded-xl w-full text-white text-sm active:scale-95 transition-all duration-100">Terima</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
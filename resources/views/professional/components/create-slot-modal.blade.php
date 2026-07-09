<div x-data="{ open: false }" @open-create-slot-drawer.window="open = true" @keydown.escape.window="open = false"
    class="z-50 relative" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-show="open"
    style="display: none;">

    <!-- Background backdrop -->
    <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="fixed inset-0 overflow-hidden">
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
                            <h2 class="font-semibold text-zinc-900 text-xl leading-6" id="slide-over-title">Buat Slot
                            </h2>
                            <button @click="open = false"
                                class="hover:bg-zinc-100 p-1 rounded-full outline-none text-zinc-400 hover:text-zinc-500 transition-colors">
                                <span class="sr-only">Tutup</span>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('professional.set-availability') }}"
                            class="relative flex flex-col flex-1 gap-4 mt-6 px-4 sm:px-6"
                            x-data="{
                                submitting: false,
                                startDate: '{{ now()->format('Y-m-d') }}',
                                endDate: '{{ now()->addDays(6)->format('Y-m-d') }}',
                                days: ['senin', 'selasa', 'rabu', 'kamis', 'jumat'],
                                startTime: '16:00',
                                endTime: '21:00',
                                get totalSlots() {
                                    if (!this.startDate || !this.endDate || this.days.length === 0) return 0;
                                    let start = new Date(this.startDate);
                                    let end = new Date(this.endDate);
                                    if (start > end) return 0;
                                    let dayMap = { minggu: 0, senin: 1, selasa: 2, rabu: 3, kamis: 4, jumat: 5, sabtu: 6 };
                                    let selectedDays = this.days.map(d => dayMap[d]);
                                    let count = 0;
                                    let curr = new Date(start);
                                    while(curr <= end) {
                                        if(selectedDays.includes(curr.getDay())) count++;
                                        curr.setDate(curr.getDate() + 1);
                                    }
                                    if (this.startTime && this.endTime) {
                                        let s = parseInt(this.startTime.split(':')[0]);
                                        let e = parseInt(this.endTime.split(':')[0]);
                                        let hours = e - s;
                                        if (hours > 0) return count * hours;
                                    }
                                    return count;
                                }
                            }"
                            @submit="submitting = true">
                            @csrf

                            @if ($errors->any())
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <ul class="text-red-600 text-sm space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="flex gap-2 w-full">
                                <div class="flex flex-col gap-1 grow">
                                    <label for="start-date" class="text-zinc-600 text-sm">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start-date"
                                        x-model="startDate"
                                        class="border border-zinc-300 rounded-lg text-zinc-600">
                                </div>
                                <div class="flex flex-col gap-1 grow">
                                    <label for="end-date" class="text-zinc-600 text-sm">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end-date"
                                        x-model="endDate"
                                        class="border border-zinc-300 rounded-lg text-zinc-600">
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <p class="text-zinc-600 text-sm">Hari</p>
                                <div class="gap-2 grid grid-cols-7 w-full max-w-md">

                                    <!-- Senin -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="senin" class="sr-only peer" checked x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Sen
                                        </div>
                                    </label>

                                    <!-- Selasa -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="selasa" class="sr-only peer" checked x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Sel
                                        </div>
                                    </label>

                                    <!-- Rabu -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="rabu" class="sr-only peer" checked x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Rab
                                        </div>
                                    </label>

                                    <!-- Kamis -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="kamis" class="sr-only peer" checked x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Kam
                                        </div>
                                    </label>

                                    <!-- Jumat -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="jumat" class="sr-only peer" checked x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Jum
                                        </div>
                                    </label>

                                    <!-- Sabtu -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="sabtu" class="sr-only peer" x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Sab
                                        </div>
                                    </label>

                                    <!-- Minggu -->
                                    <label class="text-center cursor-pointer">
                                        <input type="checkbox" name="days[]" value="minggu" class="sr-only peer" x-model="days">
                                        <div
                                            class="flex justify-center items-center bg-white hover:bg-gray-50 peer-checked:bg-teal-600 border border-gray-300 peer-checked:border-teal-600 rounded-lg h-14 font-medium text-gray-700 peer-checked:text-white text-xs transition-all">
                                            Min
                                        </div>
                                    </label>

                                </div>

                            </div>
                            <div class="flex gap-2 w-full">
                                <div class="flex flex-col gap-1 grow">
                                    <label for="start-time" class="text-zinc-600 text-sm">Dari jam</label>
                                    <input type="time" name="start_time" id="start-time"
                                        x-model="startTime"
                                        class="border border-zinc-300 rounded-lg text-zinc-600">
                                </div>
                                <div class="flex flex-col gap-1 grow">
                                    <label for="end-time" class="text-zinc-600 text-sm">Hingga jam</label>
                                    <input type="time" name="end_time" id="end-time"
                                        x-model="endTime"
                                        class="border border-zinc-300 rounded-lg text-zinc-600">
                                </div>
                            </div>
                            <div class="flex flex-col gap-3 mt-auto w-full">
                                <div class="bg-zinc-100 p-3 border border-zinc-200 rounded-lg text-zinc-600 text-sm text-center leading-relaxed">
                                    Akan membuat <span class="font-bold" x-text="totalSlots"></span> slot jadwal sesi dari tanggal <span class="font-bold" x-text="startDate"></span> sampai <span class="font-bold" x-text="endDate"></span> pada hari <span class="font-bold capitalize" x-text="days.join(', ')"></span> di jam <span class="font-bold" x-text="startTime"></span> hingga <span class="font-bold" x-text="endTime"></span>.
                                </div>
                                <button type="submit" :disabled="submitting || totalSlots === 0"
                                    class="flex items-center justify-center gap-2 bg-teal-600 hover:bg-teal-700 disabled:bg-teal-400 disabled:cursor-not-allowed hover:shadow-sm px-4 py-2 rounded-lg w-full text-white transition-all duration-100">
                                    <svg x-show="submitting" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                    <span x-text="submitting ? 'Membuat slot...' : 'Buat slot'"></span>
                                </button>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
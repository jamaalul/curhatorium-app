@extends('layouts.professional-dashboard')

@section('dashboard-content')
    <div class="flex flex-col bg-zinc-100 p-4 lg:p-8 h-full min-h-0 overflow-y-auto grow">
        <div class="space-y-6 mx-auto w-full max-w-3xl">

            <div class="flex items-center gap-4">
                <a href="{{ route('professional.dashboard') }}"
                    class="hover:bg-zinc-200 -ml-2 p-2 rounded-lg text-zinc-500 hover:text-zinc-900 transition-colors"
                    title="Kembali ke Dashboard">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="font-bold text-zinc-900 text-2xl">Riwayat Konsultasi</h1>
            </div>

            @if ($paginatedHistory->isEmpty())
                <div class="bg-white shadow-sm p-8 border border-zinc-200 rounded-2xl text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="mx-auto mb-4 size-12 text-zinc-300">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-zinc-500">Belum ada riwayat konsultasi.</p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($paginatedHistory as $slot)
                        <div class="bg-white p-4 sm:p-5 rounded-2xl">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-zinc-800 truncate">
                                        {{ $slot->bookedBy?->name ?? $slot->bookedBy?->username ?? 'Unknown' }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5">
                                        <span class="flex items-center gap-1 text-zinc-500 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                class="size-4 shrink-0">
                                                <path fill-rule="evenodd"
                                                    d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $slot->slot_start_time ? \Carbon\Carbon::parse($slot->slot_start_time)->translatedFormat('l, d M Y') : '-' }}
                                        </span>
                                        <span class="flex items-center gap-1 text-zinc-500 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                                class="size-4 shrink-0">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $slot->slot_start_time ? \Carbon\Carbon::parse($slot->slot_start_time)->translatedFormat('H:i') : '' }}
                                            -
                                            {{ $slot->slot_end_time ? \Carbon\Carbon::parse($slot->slot_end_time)->translatedFormat('H:i') : '' }}
                                        </span>
                                    </div>
                                    @if ($slot->consultation?->consultation_type)
                                        <span
                                            class="inline-block bg-teal-50 mt-2 px-2.5 py-0.5 rounded-full font-medium text-teal-700 text-xs">
                                            {{ $slot->consultation->consultation_type }}
                                        </span>
                                    @endif
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 bg-zinc-100 px-2.5 py-1 rounded-full font-medium text-zinc-500 text-xs shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="size-3.5">
                                        <path fill-rule="evenodd"
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Selesai
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $paginatedHistory->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
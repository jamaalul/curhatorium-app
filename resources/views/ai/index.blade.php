@extends('layouts.app')

@section('title', 'MentAI - Mental Support Chatbot | Curhatorium')

@section('head')
    @include('ai.partials._styles')
@endsection

@section('content')
    <script>
        var __mentaiInitialConversations = {{ Js::from($initialConversations) }};
    </script>

    @include('ai.partials._scripts-shared')

    <div class="flex w-screen h-screen overflow-hidden bg-[#F4F4F5] max-md:bg-white font-dm text-[#1E1E1E]"
         x-data="mentaiIndex()"
         x-init="initChat()">

        @include('ai.partials._sidebar', ['spaNav' => false])

        {{-- ── Main Stage ── --}}
        <main class="flex-1 h-screen overflow-hidden bg-[#F4F4F5] max-md:bg-white flex flex-col items-center justify-center p-6 md:p-8 [@media(max-height:750px)]:p-4 max-md:p-0 box-border max-md:h-[100dvh]">
            <div class="w-full h-full bg-white border border-[#E4E4E7] max-md:border-0 rounded-3xl max-md:rounded-none flex flex-col items-center justify-center p-9 pb-7 [@media(max-height:750px)]:p-7 [@media(max-height:750px)]:pb-5 [@media(max-height:620px)]:p-4 max-md:p-4 max-md:pb-5 box-border overflow-y-auto scrollbar-none relative max-md:justify-start">

                {{-- Mobile top bar for index (Figma #1231:2040) --}}
                <div class="md:hidden w-full flex items-center justify-start shrink-0 mb-2">
                    <button type="button"
                            @click="sidebarOpen = true"
                            class="w-[30px] h-[30px] rounded-lg bg-transparent border-0 flex items-center justify-center cursor-pointer text-zinc-900 hover:text-[#00BBA7] transition-colors p-1"
                            title="Buka menu riwayat">
                        <svg class="w-5 h-5" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.646 10V6c0-3.333-1.333-4.667-4.667-4.667H5.98C2.646 1.333 1.313 2.667 1.313 6v4c0 3.333 1.333 4.667 4.666 4.667h3.98C13.313 14.667 14.646 13.333 14.646 10z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.313 1.333v13.334" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m9.98 6.293-1.706 1.707 1.706 1.707" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                {{-- Content Container (Responsive Gaps by Viewport Height: 128px -> 96px -> 64px -> 32px) --}}
                <div class="flex w-[700px] max-w-full flex-col items-center gap-[128px] [@media(max-height:900px)]:gap-[96px] [@media(max-height:750px)]:gap-[64px] [@media(max-height:620px)]:gap-[32px] max-md:gap-[120px] max-md:[@media(max-height:850px)]:gap-[92px] max-md:[@media(max-height:750px)]:gap-[58px] max-md:[@media(max-height:650px)]:gap-[32px] mt-auto mb-0 max-md:w-full">

                    {{-- Welcome Header (Top Section, Figma #1231:1989) --}}
                    <div class="flex flex-col items-center text-center w-full max-w-[520px]">
                        <div class="w-[76px] h-[76px] [@media(max-height:750px)]:w-16 [@media(max-height:750px)]:h-16 [@media(max-height:620px)]:w-14 [@media(max-height:620px)]:h-14 max-md:[@media(max-height:750px)]:w-[68px] max-md:[@media(max-height:750px)]:h-[68px] max-md:[@media(max-height:650px)]:w-[56px] max-md:[@media(max-height:650px)]:h-[56px] rounded-full bg-zinc-50 border border-zinc-100 flex items-center justify-center shrink-0">
                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" class="w-10 h-10 [@media(max-height:750px)]:w-8 [@media(max-height:750px)]:h-8 [@media(max-height:620px)]:w-7 [@media(max-height:620px)]:h-7 max-md:[@media(max-height:750px)]:w-[34px] max-md:[@media(max-height:750px)]:h-[34px] max-md:[@media(max-height:650px)]:w-[28px] max-md:[@media(max-height:650px)]:h-[28px] object-contain" />
                        </div>
                        <h1 class="font-bricolage font-bold text-[32px] leading-[38px] tracking-[-0.02em] text-gray-800 mt-4.5 mb-2 [@media(max-height:750px)]:text-[28px] [@media(max-height:750px)]:leading-[34px] [@media(max-height:750px)]:mt-3.5 [@media(max-height:750px)]:mb-1.5 [@media(max-height:620px)]:text-[20px] [@media(max-height:620px)]:leading-[26px] [@media(max-height:620px)]:mt-2.5 [@media(max-height:620px)]:mb-1 max-md:text-2xl max-md:[@media(max-height:750px)]:text-[22px] max-md:[@media(max-height:750px)]:leading-[28px] max-md:[@media(max-height:650px)]:text-[20px] max-md:[@media(max-height:650px)]:leading-[26px]">Halo, Aku MentAI</h1>
                        <p class="text-[14.5px] leading-6 text-zinc-500 m-0 max-w-[460px] [@media(max-height:750px)]:text-[13.5px] [@media(max-height:750px)]:leading-5 [@media(max-height:620px)]:text-[12.5px] [@media(max-height:620px)]:leading-4 max-md:text-sm max-md:[@media(max-height:750px)]:text-[13px] max-md:[@media(max-height:750px)]:leading-5">
                            Teman cerita 24/7 yang siap mendengarkanmu tanpa menghakimi.<br>
                            Ada yang ingin kamu sampaikan hari ini?
                        </p>
                    </div>

                    {{-- Middle + Bottom Group (Responsive Gap between Prompt Cards & Input: 80px -> 64px -> 40px -> 20px) --}}
                    <div class="flex flex-col items-start gap-[80px] [@media(max-height:900px)]:gap-[64px] [@media(max-height:750px)]:gap-[40px] [@media(max-height:620px)]:gap-[20px] max-md:gap-[80px] max-md:[@media(max-height:850px)]:gap-[48px] max-md:[@media(max-height:750px)]:gap-[32px] max-md:[@media(max-height:650px)]:gap-[20px] self-stretch w-full">

                        {{-- 3 Starter Cards (Middle Section, Figma #1257:2324) --}}
                        <div class="w-full flex flex-row overflow-x-auto sm:grid sm:grid-cols-3 gap-3.5 max-md:-mx-4 max-md:px-4 max-md:w-[calc(100%+32px)] scrollbar-none box-border">

                            <div @click="selectStarter('Cerita apa aja, aku di sini buat dengerin 🫰🏼')"
                                 class="bg-[#00BBA7] hover:bg-[#009689] rounded-2xl p-1.5 pb-2.5 flex flex-col justify-between gap-2 cursor-pointer transition-colors duration-200 box-border min-w-[220px] max-w-[224px] sm:min-w-0 sm:max-w-none shrink-0 sm:shrink max-md:shadow-xs">
                                <div class="bg-white rounded-xl p-3 min-h-[86px] sm:min-h-[94px] [@media(max-height:750px)]:min-h-[82px] [@media(max-height:750px)]:p-2.5 flex flex-col justify-between gap-2 box-border">
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center gap-1.5">
                                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" class="w-4 h-4 object-contain" />
                                            <span class="font-bricolage text-[13px] font-semibold text-[#00BBA7]">MentAI</span>
                                        </div>
                                        <span class="bg-[#00BBA7] text-white text-[11px] font-medium px-1.5 py-0.5 rounded">Temen curhat</span>
                                    </div>
                                    <p class="text-[13.5px] leading-5 font-normal text-zinc-800 m-0 [@media(max-height:750px)]:text-[12.5px] [@media(max-height:750px)]:leading-4">
                                        Cerita apa aja, aku di sini buat dengerin 🫰🏼
                                    </p>
                                </div>
                                <div class="px-2.5 pt-0.5">
                                    <span class="text-white text-[11.5px] font-medium tracking-wide">Prompt disarankan</span>
                                </div>
                            </div>

                            <div @click="selectStarter('Saya merasa sedikit cemas dan butuh teman bicara.')"
                                 class="bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 hover:border-zinc-300 rounded-2xl p-1.5 pb-2.5 flex flex-col justify-between gap-2 cursor-pointer transition-colors duration-200 box-border min-w-[220px] max-w-[224px] sm:min-w-0 sm:max-w-none shrink-0 sm:shrink max-md:shadow-xs">
                                <div class="bg-white rounded-xl p-3 min-h-[86px] sm:min-h-[94px] [@media(max-height:750px)]:min-h-[82px] [@media(max-height:750px)]:p-2.5 flex flex-col justify-start box-border">
                                    <p class="text-[13.5px] leading-5 font-normal text-zinc-800 m-0 [@media(max-height:750px)]:text-[12.5px] [@media(max-height:750px)]:leading-4">
                                        Saya merasa sedikit cemas dan butuh teman bicara.
                                    </p>
                                </div>
                                <div class="px-2.5 pt-0.5">
                                    <span class="text-zinc-400 text-[11.5px] font-medium tracking-wide">Prompt disarankan</span>
                                </div>
                            </div>

                            <div @click="selectStarter('Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?')"
                                 class="bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 hover:border-zinc-300 rounded-2xl p-1.5 pb-2.5 flex flex-col justify-between gap-2 cursor-pointer transition-colors duration-200 box-border min-w-[220px] max-w-[224px] sm:min-w-0 sm:max-w-none shrink-0 sm:shrink max-md:shadow-xs">
                                <div class="bg-white rounded-xl p-3 min-h-[86px] sm:min-h-[94px] [@media(max-height:750px)]:min-h-[82px] [@media(max-height:750px)]:p-2.5 flex flex-col justify-start box-border">
                                    <p class="text-[13.5px] leading-5 font-normal text-zinc-800 m-0 [@media(max-height:750px)]:text-[12.5px] [@media(max-height:750px)]:leading-4">
                                        Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?
                                    </p>
                                </div>
                                <div class="px-2.5 pt-0.5">
                                    <span class="text-zinc-400 text-[11.5px] font-medium tracking-wide">Prompt disarankan</span>
                                </div>
                            </div>

                        </div>

                        {{-- Input Box (Bottom Section) --}}
                        <div class="w-full max-w-[700px] flex flex-col gap-2.5 box-border">
                            @include('ai.partials._input-box')
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

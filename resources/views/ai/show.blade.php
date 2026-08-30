@extends('layouts.app')

@section('title', ($conversation->title ?? 'MentAI Chat') . ' | Curhatorium')

@section('head')
    @include('ai.partials._styles')
@endsection

@section('content')
    <script>
        var __mentaiInitialMessages      = {{ Js::from($initialMessages) }};
        var __mentaiInitialConversations = {{ Js::from($initialConversations) }};
    </script>

    @include('ai.partials._scripts-shared')

    <div class="flex w-screen h-screen overflow-hidden bg-[#F4F4F5] max-md:bg-white font-dm text-[#1E1E1E]"
         x-data="mentaiShow('{{ $conversation->id }}')"
         x-init="initChat()">

        @include('ai.partials._sidebar', ['spaNav' => true])

        {{-- ── Main Stage (identical wrapper to index) ── --}}
        <main class="flex-1 h-screen overflow-hidden bg-[#F4F4F5] max-md:bg-white flex flex-col items-center justify-center p-6 md:p-8 max-md:p-0 box-border max-md:h-[100dvh]">
            <div class="w-full h-full bg-white border border-[#E4E4E7] max-md:border-0 rounded-3xl max-md:rounded-none flex flex-col items-center box-border overflow-hidden relative max-md:h-screen max-md:h-[100dvh]">

                {{-- Mobile sidebar trigger button --}}
                <div class="md:hidden w-full flex items-center justify-start p-3 pb-0 shrink-0">
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

                {{-- Chat scroll area --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-7 pt-4 sm:pt-6 pb-2 flex flex-col w-full box-border scrollbar-none" x-ref="scrollArea">
                    <div class="flex flex-col gap-6 w-full max-w-[700px] mx-auto pb-4">

                        {{-- SPA loading dots (switching chats) --}}
                        <template x-if="loadingChat">
                            <div class="flex justify-center items-center py-24">
                                <div class="flex items-center gap-1.5">
                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce"></div>
                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce" style="animation-delay:.15s"></div>
                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce" style="animation-delay:.3s"></div>
                                </div>
                            </div>
                        </template>

                        {{-- Messages list --}}
                        <template x-if="!loadingChat">
                            <div class="flex flex-col gap-6 w-full">
                                <template x-for="message in messages" :key="message.id">
                                    <div class="flex w-full" :class="(message.role === 'user' || message.role === 'User' || (message.role && message.role.value === 'user')) ? 'justify-end' : 'justify-start'">

                                        {{-- Assistant plain clean text (left-aligned, no avatar) --}}
                                        <template x-if="message.role !== 'user' && message.role !== 'User' && (!message.role || message.role.value !== 'user')">
                                            <div class="w-full max-w-[95%] sm:max-w-[90%] mr-auto">
                                                {{-- Streamed Markdown Text with live typing cursor --}}
                                                <div x-show="message.content" class="inline">
                                                    <div x-html="renderMarkdown(message.content || '')" class="mentai-prose"></div>
                                                    <template x-if="streaming && message.id === messages[messages.length-1].id">
                                                        <span class="mentai-cursor"></span>
                                                    </template>
                                                </div>

                                                {{-- Action Bar: Copy Button & Timestamp --}}
                                                <template x-if="message.content && (!streaming || message.id !== messages[messages.length-1].id)">
                                                    <div class="flex items-center gap-1.5 mt-2 opacity-85">
                                                        {{-- Copy Button --}}
                                                        <button type="button"
                                                                @click="copyMessage(message.content, message.id)"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md border-0 text-xs font-dm cursor-pointer transition-colors"
                                                                :class="copiedId === message.id ? 'text-[#00BBA7] bg-[#F0FDFA]' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900'"
                                                                :title="copiedId === message.id ? 'Tersalin ke clipboard' : 'Salin pesan'">
                                                            <template x-if="copiedId !== message.id">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                </svg>
                                                            </template>
                                                            <template x-if="copiedId === message.id">
                                                                <svg class="w-3.5 h-3.5 text-[#00BBA7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </template>
                                                            <span x-text="copiedId === message.id ? 'Tersalin' : 'Salin'"></span>
                                                        </button>
                                                    </div>
                                                </template>

                                                {{-- Thinking state indicator before first token arrives --}}
                                                <template x-if="!message.content && streaming && !hasStreamedText && message.id === messages[messages.length-1].id">
                                                    <div class="inline-flex items-center gap-2 py-1 text-zinc-500 text-sm font-dm">
                                                        <div class="flex items-center gap-1">
                                                            <span class="mentai-thinking-dot"></span>
                                                            <span class="mentai-thinking-dot" style="animation-delay:0.2s;"></span>
                                                            <span class="mentai-thinking-dot" style="animation-delay:0.4s;"></span>
                                                        </div>
                                                        <span class="text-[13.5px] text-zinc-500 font-normal">MentAI sedang mengetik...</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- User full-rounded pill bubble (dynamically hugs text width) --}}
                                        <template x-if="message.role === 'user' || message.role === 'User' || (message.role && message.role.value === 'user')">
                                            <div class="bg-[#F3F4F6] text-zinc-900 px-5 py-2.5 rounded-full w-fit max-w-[80%] sm:max-w-[75%] text-[14.5px] sm:text-[15px] leading-relaxed font-dm break-words ml-auto text-left box-border">
                                                <div x-html="(message.content || '').replace(/\n/g, '<br>')"></div>
                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                </div>

                {{-- Bottom Input Form (Docked snug, with subtle smooth gradient on top edge) --}}
                <div class="w-full shrink-0 bg-white px-4 sm:px-7 pb-4 sm:pb-6 relative z-10 box-border">
                    {{-- Eased smooth feather gradient edge (Apple-style non-linear bezier transition) --}}
                    <div class="mentai-smooth-top-fade"></div>

                    <div class="w-full max-w-[700px] mx-auto flex flex-col gap-2">
                        @include('ai.partials._input-box')
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection

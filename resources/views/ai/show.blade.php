@extends('layouts.app')

@section('title', ($conversation->title ?? 'MentAI Chat') . ' | Curhatorium')

@section('head')
    @include('ai.partials._styles')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        /* ── Show-page specific ── */
        .mentai-chat-area {
            flex: 1;
            overflow-y: auto;
            padding: 24px 28px 0;
            display: flex;
            flex-direction: column;
            width: 100%;
            box-sizing: border-box;
        }
        .mentai-chat-messages {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding-bottom: 180px; /* space for input overlay */
        }
        .mentai-input-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(to top, #FFFFFF 65%, transparent);
            padding: 0 28px 24px;
            border-radius: 0 0 24px 24px;
        }
        .mentai-input-overlay-inner {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Mobile top bar inside card */
        .mentai-card-topbar {
            display: none;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px 0;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        @media (max-width: 768px) {
            .mentai-card-topbar { display: flex; }
            .mentai-chat-area { padding: 16px 16px 0; }
            .mentai-input-overlay { padding: 0 16px 16px; }
        }
    </style>
@endsection

@section('content')
    <script>
        var __mentaiInitialMessages      = {{ Js::from($initialMessages) }};
        var __mentaiInitialConversations = {{ Js::from($initialConversations) }};
    </script>

    @include('ai.partials._scripts-shared')

    <div class="mentai-page-wrap"
         x-data="mentaiShow('{{ $conversation->id }}')"
         x-init="initChat()">

        @include('ai.partials._sidebar', ['spaNav' => true])

        {{-- ── Main Stage (identical wrapper to index) ── --}}
        <main class="mentai-main-stage">
            <div class="mentai-floating-card mentai-scrollbar">

                {{-- Mobile top bar (hamburger + MentAI label) --}}
                <div class="mentai-card-topbar">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="sidebarOpen = true"
                                style="padding:4px;border-radius:8px;background:transparent;border:none;cursor:pointer;color:#09090B;display:flex;align-items:center;justify-content:center;"
                                title="Buka menu riwayat">
                            <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('assets/mentai/mentai_icon.svg') }}" alt="MentAI" style="width:18px;height:18px;" />
                            <span class="font-bricolage" style="font-weight:600;font-size:15px;color:#1F2937;">MentAI</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openSearchModal()"
                                style="display:flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:8px;background:#FFFFFF;border:1px solid #E4E4E7;color:#71717A;cursor:pointer;"
                                title="Cari percakapan">
                            <svg style="width:14px;height:14px;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.667 14.5C3.9 14.5.834 11.433.834 7.667.834 3.9 3.9.833 7.667.833c3.767 0 6.833 3.067 6.833 6.834 0 3.766-3.066 6.833-6.833 6.833zm0-12.667C4.447 1.833 1.834 4.453 1.834 7.667c0 3.213 2.613 5.833 5.833 5.833 3.22 0 5.834-2.62 5.834-5.833C13.5 4.453 10.887 1.833 7.667 1.833z" fill="currentColor"/>
                                <path d="M14.666 15.167a.664.664 0 0 1-.473-.2l-1.333-1.334a.669.669 0 0 1 0-.946.669.669 0 0 1 .946 0l1.333 1.333a.669.669 0 0 1-.473 1.147z" fill="currentColor"/>
                            </svg>
                        </button>
                        <a href="{{ route('dashboard') }}"
                           style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;background:#FFFFFF;border:1px solid #E4E4E7;color:#71717A;font-size:12px;font-weight:500;text-decoration:none;">
                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Dashboard
                        </a>
                    </div>
                </div>

                {{-- Chat scroll area --}}
                <div class="mentai-chat-area mentai-scrollbar" x-ref="scrollArea">
                    <div class="mentai-chat-messages">

                        {{-- SPA loading dots (switching chats) --}}
                        <template x-if="loadingChat">
                            <div style="display:flex;justify-content:center;align-items:center;padding:96px 0;">
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce"></div>
                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce" style="animation-delay:.15s"></div>
                                    <div class="bg-[#00BBA7] rounded-full w-2 h-2 animate-bounce" style="animation-delay:.3s"></div>
                                </div>
                            </div>
                        </template>

                        {{-- Messages list --}}
                        <template x-if="!loadingChat">
                            <div style="display:flex;flex-direction:column;gap:26px;width:100%;">
                                <template x-for="message in messages" :key="message.id">
                                    <div style="display:flex;width:100%;" :style="(message.role === 'user' || message.role === 'User' || (message.role && message.role.value === 'user')) ? 'justify-content:flex-end;' : 'justify-content:flex-start;'">

                                        {{-- Assistant plain clean text (left-aligned) --}}
                                        <template x-if="message.role !== 'user' && message.role !== 'User' && (!message.role || message.role.value !== 'user')">
                                            <div style="width:100%;max-width:90%;margin-right:auto;">
                                                
                                                {{-- Streamed Markdown Text with live typing cursor --}}
                                                <div x-show="message.content" style="display:inline;">
                                                    <div x-html="renderMarkdown(message.content || '')" class="mentai-prose"></div>
                                                    <template x-if="streaming && message.id === messages[messages.length-1].id">
                                                        <span class="mentai-cursor"></span>
                                                    </template>
                                                </div>

                                                {{-- Action Bar: Copy Button, Regenerate, & Timestamp --}}
                                                <template x-if="message.content && (!streaming || message.id !== messages[messages.length-1].id)">
                                                    <div class="mentai-msg-actions">
                                                        {{-- Copy Button --}}
                                                        <button type="button"
                                                                @click="copyMessage(message.content, message.id)"
                                                                class="mentai-action-icon-btn"
                                                                :class="copiedId === message.id ? 'is-copied' : ''"
                                                                :title="copiedId === message.id ? 'Tersalin ke clipboard' : 'Salin pesan'">
                                                            <template x-if="copiedId !== message.id">
                                                                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                                </svg>
                                                            </template>
                                                            <template x-if="copiedId === message.id">
                                                                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </template>
                                                            <span x-text="copiedId === message.id ? 'Tersalin' : 'Salin'"></span>
                                                        </button>

                                                        {{-- Regenerate Button (Only for latest message) --}}
                                                        <template x-if="message.id === messages[messages.length-1].id && !loading">
                                                            <button type="button"
                                                                    @click="regenerateLastResponse()"
                                                                    class="mentai-action-icon-btn"
                                                                    title="Buat ulang respon">
                                                                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                </svg>
                                                                <span>Coba lagi</span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>

                                                {{-- Thinking state indicator before first token arrives --}}
                                                <template x-if="!message.content && streaming && !hasStreamedText && message.id === messages[messages.length-1].id">
                                                    <div style="display:inline-flex;align-items:center;gap:8px;padding:4px 0;color:#71717A;font-size:14px;font-family:'DM Sans',sans-serif;">
                                                        <div style="display:flex;align-items:center;gap:4px;">
                                                            <span class="mentai-thinking-dot"></span>
                                                            <span class="mentai-thinking-dot" style="animation-delay:0.2s;"></span>
                                                            <span class="mentai-thinking-dot" style="animation-delay:0.4s;"></span>
                                                        </div>
                                                        <span style="font-size:13.5px;color:#71717A;font-weight:400;">MentAI sedang mengetik...</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- User soft gray rounded bubble (dynamically hugs text width, borderless) --}}
                                        <template x-if="message.role === 'user' || message.role === 'User' || (message.role && message.role.value === 'user')">
                                            <div style="background:#F3F4F6;color:#18181B;padding:10px 18px;border-radius:18px;width:fit-content;max-width:78%;font-size:14.5px;line-height:1.55;font-family:'DM Sans',sans-serif;word-break:break-word;margin-left:auto;text-align:left;box-sizing:border-box;">
                                                <div x-html="(message.content || '').replace(/\n/g, '<br>')"></div>
                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                </div>

                {{-- Input overlay (floats at bottom of card) --}}
                <div class="mentai-input-overlay">
                    <div class="mentai-input-overlay-inner">
                        @include('ai.partials._input-box', ['useMentaiClass' => true])
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection

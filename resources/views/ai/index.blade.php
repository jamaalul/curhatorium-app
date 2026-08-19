@extends('layouts.dashboard')

@section('title', 'Ment-AI Chat | Curhatorium')

@section('head')
    @vite('resources/css/app.css')
@endsection

@section('dashboard-content')
    <div class="flex bg-white pt-16 w-full h-screen overflow-hidden font-sans text-gray-800"
        x-data="mentaiIndex()" x-init="initChat()">

        <!-- Sidebar -->
        <div class="z-20 fixed md:relative flex flex-col flex-shrink-0 bg-gray-50 border-gray-200 border-r w-64 h-full transition-transform duration-300"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            <!-- Sidebar Header -->
            <div class="p-3">
                <a href="{{ route('mentai.index') }}"
                    class="flex justify-between items-center gap-2 bg-white hover:bg-gray-50 shadow-sm px-3 py-2 border border-gray-200 rounded-lg w-full font-medium transition-colors">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#48a6a6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New chat
                    </div>
                </a>
            </div>

            <!-- Conversation List -->
            <div class="flex-1 space-y-1 p-3 overflow-y-auto">
                <template x-for="conv in conversations" :key="conv.id">
                    <a :href="`/mental-support-chatbot/${conv.id}`"
                        class="block hover:bg-gray-100 px-3 py-2 rounded-lg text-gray-700 text-sm truncate transition-colors"
                        x-text="conv.title || 'Percakapan'">
                    </a>
                </template>
                <div x-show="loadingConversations" class="px-3 py-2 text-gray-400 text-sm">Memuat...</div>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-3 border-gray-200 border-t">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2 hover:bg-gray-100 px-3 py-2 rounded-lg text-gray-600 text-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="relative flex flex-col flex-1 w-full h-full overflow-hidden">

            <!-- Mobile Header (Visible only on small screens) -->
            <div class="md:hidden z-10 flex justify-between items-center bg-white p-3 border-gray-100 border-b">
                <button @click="sidebarOpen = !sidebarOpen" class="hover:bg-gray-100 p-2 rounded-lg text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
                <span class="font-semibold text-gray-800">MentAI</span>
                <div class="w-10"></div>
            </div>

            <!-- Overlay for mobile sidebar -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                class="md:hidden z-10 fixed inset-0 bg-black bg-opacity-20" style="display: none;"></div>

            <!-- Content Area / Empty State -->
            <div class="flex-1 overflow-y-auto">
                <div class="flex flex-col justify-center items-center mx-auto px-4 pt-12 pb-36 max-w-3xl min-h-full text-center">
                    <div class="flex justify-center items-center bg-[#48a6a6] shadow-md mb-6 rounded-full w-16 h-16">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                            </path>
                        </svg>
                    </div>
                    <h1 class="mb-2 font-bold text-gray-800 text-3xl">Halo, Aku MentAI</h1>
                    <p class="max-w-md text-gray-500">Teman cerita 24/7 yang siap mendengarkanmu tanpa menghakimi.
                        Ada yang ingin kamu sampaikan hari ini?</p>

                    <!-- Quick Starter Prompts -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-8 w-full max-w-lg text-left">
                        <button type="button" @click="selectStarter('Saya merasa sedikit cemas dan butuh teman bicara.')"
                            class="p-3 border border-gray-200 hover:border-[#48a6a6] rounded-xl text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-all text-left">
                            💬 "Saya merasa sedikit cemas dan butuh teman bicara."
                        </button>
                        <button type="button" @click="selectStarter('Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?')"
                            class="p-3 border border-gray-200 hover:border-[#48a6a6] rounded-xl text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-all text-left">
                            🌿 "Hari ini cukup melelahkan, bagaimana cara menenangkan pikiran?"
                        </button>
                    </div>
                </div>
            </div>

            <!-- Input Area (Fixed Bottom Center) -->
            <div class="bottom-0 left-0 absolute bg-gradient-to-t from-white via-white to-transparent px-4 pt-6 pb-4 sm:pb-6 w-full">
                <div class="relative mx-auto max-w-3xl">
                    <form @submit.prevent="sendMessage"
                        class="relative flex items-end bg-gray-50 focus-within:bg-white shadow-[0_0_15px_rgba(0,0,0,0.05)] border border-gray-200 rounded-[30px] focus-within:ring-[#48a6a6] focus-within:ring-1 transition-colors">
                        <textarea x-model="input" x-ref="messageInput"
                            @keydown.enter.prevent="if(!loading) { sendMessage(); }" @input="autoResize()"
                            placeholder="Kirim pesan ke MentAI..."
                            class="bg-transparent py-4 pr-14 pl-5 border-none rounded-3xl outline-none focus:ring-0 w-full max-h-[200px] overflow-y-auto resize-none"
                            rows="1" :disabled="loading"></textarea>

                        <button type="submit"
                            class="right-2 bottom-2 absolute flex justify-center items-center p-2 rounded-full w-10 h-10 transition-colors"
                            :class="input.trim() === '' || loading ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-[#48a6a6] text-white hover:bg-[#357979]'"
                            :disabled="loading || input.trim() === ''">
                            <template x-if="!loading">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </template>
                            <template x-if="loading">
                                <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </template>
                        </button>
                    </form>
                    <div class="mt-2 text-gray-400 text-xs text-center">
                        MentAI dapat membuat kesalahan. Harap pertimbangkan untuk memverifikasi informasi penting.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function mentaiIndex() {
            return {
                conversations: [],
                input: '',
                loading: false,
                loadingConversations: false,
                sidebarOpen: false,

                _getCSRFToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : '';
                },

                autoResize() {
                    const el = this.$refs.messageInput;
                    if (!el) return;
                    el.style.height = 'auto';
                    el.style.height = (el.scrollHeight) + 'px';
                },

                selectStarter(text) {
                    this.input = text;
                    this.$nextTick(() => {
                        this.autoResize();
                        if (this.$refs.messageInput) {
                            this.$refs.messageInput.focus();
                        }
                    });
                },

                async initChat() {
                    await this.fetchConversations();
                },

                async fetchConversations() {
                    this.loadingConversations = true;
                    try {
                        const res = await fetch('/ai/conversations', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.conversations = data.data || [];
                        }
                    } catch (e) {
                        console.error("Error fetching conversations:", e);
                    } finally {
                        this.loadingConversations = false;
                    }
                },

                async sendMessage() {
                    if (this.loading || !this.input.trim()) return;

                    const userText = this.input.trim();
                    this.loading = true;

                    try {
                        const startRes = await fetch('/ai/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this._getCSRFToken(),
                                'Accept': 'application/json'
                            }
                        });

                        if (!startRes.ok) {
                            throw new Error('Network error starting conversation');
                        }

                        const startData = await startRes.json();
                        const conversationId = startData.conversationId;

                        sessionStorage.setItem('mentai_pending_prompt', JSON.stringify({
                            conversationId: conversationId,
                            message: userText
                        }));

                        window.location.href = `/mental-support-chatbot/${conversationId}`;
                    } catch (error) {
                        console.error('Failed to initiate chat:', error);
                        this.loading = false;
                        alert('Gagal memulai percakapan. Silakan coba lagi.');
                    }
                }
            }
        }
    </script>
@endsection

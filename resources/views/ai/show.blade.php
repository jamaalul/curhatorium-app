@extends('layouts.dashboard')

@section('title', ($conversation->title ?? 'Ment-AI Chat') . ' | Curhatorium')

@section('head')
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
@endsection

@section('dashboard-content')
    <div class="flex bg-white pt-16 w-full h-screen overflow-hidden font-sans text-gray-800"
        x-data="mentaiShow('{{ $conversation->id }}')" x-init="initChat()">

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
                        class="block px-3 py-2 rounded-lg text-sm truncate transition-colors"
                        :class="conv.id === conversationId ? 'bg-gray-200 font-medium text-gray-900' : 'hover:bg-gray-100 text-gray-700'"
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

            <!-- Chat Scroll Area -->
            <div class="flex-1 overflow-y-auto" x-ref="scrollArea">
                <div class="flex flex-col mx-auto px-4 pt-8 pb-32 max-w-3xl min-h-full">

                    <!-- Loading Messages Spinner -->
                    <template x-if="loadingMessages">
                        <div class="flex flex-1 justify-center items-center py-20">
                            <svg class="w-8 h-8 text-[#48a6a6] animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </template>

                    <!-- Messages List -->
                    <div class="flex flex-col space-y-6 w-full" x-show="!loadingMessages || messages.length > 0">
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex w-full" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">

                                <!-- Assistant Message -->
                                <template x-if="message.role === 'assistant'">
                                    <div class="flex">
                                        <div class="pt-1 max-w-none text-gray-800 prose prose-sm sm:prose-base">
                                            <div x-show="message.content" x-html="marked.parse(message.content || '')">
                                            </div>
                                            <template
                                                x-if="!message.content && streaming && !hasStreamedText && message.id === messages[messages.length-1].id">
                                                <div class="flex items-center gap-1 opacity-50 pt-2 h-full">
                                                    <div class="bg-gray-500 rounded-full w-2 h-2 animate-bounce"></div>
                                                    <div class="bg-gray-500 rounded-full w-2 h-2 animate-bounce"
                                                        style="animation-delay: 0.2s"></div>
                                                    <div class="bg-gray-500 rounded-full w-2 h-2 animate-bounce"
                                                        style="animation-delay: 0.4s"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- User Message -->
                                <template x-if="message.role === 'user'">
                                    <div class="bg-gray-100 px-5 py-3 rounded-3xl max-w-[85%] sm:max-w-[75%] text-gray-800">
                                        <div x-html="message.content.replace(/\n/g, '<br>')"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
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
        function mentaiShow(conversationId) {
            return {
                conversationId: conversationId,
                messages: [],
                conversations: [],
                input: '',
                loading: false,
                loadingConversations: false,
                loadingMessages: false,
                streaming: false,
                hasStreamedText: false,
                sidebarOpen: false,

                _getCSRFToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : '';
                },

                _scrollToBottom() {
                    this.$nextTick(() => {
                        if (this.$refs.scrollArea) {
                            this.$refs.scrollArea.scrollTop = this.$refs.scrollArea.scrollHeight;
                        }
                    });
                },

                autoResize() {
                    const el = this.$refs.messageInput;
                    if (!el) return;
                    el.style.height = 'auto';
                    el.style.height = (el.scrollHeight) + 'px';
                },

                async initChat() {
                    this.fetchConversations();
                    await this.fetchMessages();

                    // Check if an initial prompt was queued from the landing page
                    const pendingRaw = sessionStorage.getItem('mentai_pending_prompt');
                    if (pendingRaw) {
                        try {
                            const pending = JSON.parse(pendingRaw);
                            if (pending.conversationId === this.conversationId && pending.message) {
                                sessionStorage.removeItem('mentai_pending_prompt');
                                await this.executeSendMessage(pending.message, true);
                            }
                        } catch (e) {
                            sessionStorage.removeItem('mentai_pending_prompt');
                            console.error('Error parsing pending prompt:', e);
                        }
                    }
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

                async fetchMessages() {
                    this.loadingMessages = true;
                    try {
                        const res = await fetch(`/ai/${this.conversationId}/messages`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            this.messages = await res.json();
                            this._scrollToBottom();
                        }
                    } catch (e) {
                        console.error("Error fetching messages:", e);
                    } finally {
                        this.loadingMessages = false;
                    }
                },

                async sendMessage() {
                    if (this.loading || !this.input.trim()) return;

                    const userText = this.input.trim();
                    this.input = '';
                    this.$nextTick(() => this.autoResize());

                    const isFirstExchange = this.messages.length === 0;
                    await this.executeSendMessage(userText, isFirstExchange);
                },

                async executeSendMessage(userText, isFirstExchange = false) {
                    this.messages.push({
                        id: Date.now().toString(),
                        role: 'user',
                        content: userText
                    });

                    const assistantMessageId = (Date.now() + 1).toString();
                    this.messages.push({
                        id: assistantMessageId,
                        role: 'assistant',
                        content: ''
                    });

                    this.loading = true;
                    this.streaming = true;
                    this.hasStreamedText = false;
                    this._scrollToBottom();

                    try {
                        const response = await fetch(`/ai/${this.conversationId}/message`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this._getCSRFToken(),
                                'Accept': 'text/event-stream'
                            },
                            body: JSON.stringify({ message: userText })
                        });

                        if (!response.ok) {
                            let errorMsg = 'NETWORK_ERROR';
                            if (response.status === 429) {
                                try {
                                    const errData = await response.json();
                                    if (errData && errData.message) {
                                        errorMsg = 'QUOTA_EXCEEDED:' + errData.message;
                                    }
                                } catch (e) { }
                            }
                            throw new Error(errorMsg);
                        }

                        if (!response.body) throw new Error('ReadableStream not supported');

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder('utf-8');
                        let buffer = '';

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            buffer += decoder.decode(value, { stream: true });
                            const parts = buffer.split('\n\n');
                            buffer = parts.pop() || '';

                            for (const part of parts) {
                                const lines = part.split('\n');
                                for (const line of lines) {
                                    if (line.startsWith('data:')) {
                                        const jsonStr = line.replace(/^data:/, '').trim();
                                        if (!jsonStr || jsonStr === '[DONE]') continue;

                                        try {
                                            const event = JSON.parse(jsonStr);
                                            const chunkText = event.text || event.delta || event.content || '';
                                            if (chunkText) {
                                                this.hasStreamedText = true;
                                                const messageIndex = this.messages.findIndex(m => m.id === assistantMessageId);
                                                if (messageIndex !== -1) {
                                                    this.messages[messageIndex].content += chunkText;
                                                    this._scrollToBottom();
                                                }
                                            }
                                        } catch (e) {
                                            // Skip malformed chunk
                                        }
                                    }
                                }
                            }
                        }
                    } catch (error) {
                        console.error('MentAI Stream Error:', error);
                        const messageIndex = this.messages.findIndex(m => m.id === assistantMessageId);
                        if (messageIndex !== -1) {
                            let displayMsg = "Maaf, terjadi kesalahan. Coba lagi ya.";
                            if (error.message && error.message.startsWith('QUOTA_EXCEEDED:')) {
                                displayMsg = error.message.substring(15);
                            }
                            this.messages[messageIndex].content = displayMsg;
                            this._scrollToBottom();
                        }
                    } finally {
                        this.loading = false;
                        this.streaming = false;

                        if (isFirstExchange) {
                            const assistantContent = this.messages.find(m => m.id === assistantMessageId)?.content || '';
                            this.generateTitle(userText, assistantContent);
                        }
                    }
                },

                async generateTitle(userMessage, assistantResponse) {
                    try {
                        const res = await fetch(`/ai/${this.conversationId}/generate-title`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this._getCSRFToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: userMessage.substring(0, 500),
                                response: assistantResponse.substring(0, 500)
                            })
                        });

                        if (!res.ok) return;

                        const data = await res.json();
                        const conv = this.conversations.find(c => c.id === this.conversationId);
                        if (conv) {
                            conv.title = data.title;
                        }
                    } catch (e) {
                        console.error('Title generation failed:', e);
                    }
                }
            }
        }
    </script>
@endsection

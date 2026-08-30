{{--
    Shared Alpine.js logic for MentAI sidebar + conversation management.
    Consumed by both mentaiShow() and mentaiIndex() via Object.assign / spread.

    Exposes:
      State : conversations, currentPage, hasMorePages, loadingConversations,
              loadingMoreConversations, sidebarOpen, sidebarCollapsed,
              showSearch, searchQuery
      Methods: toggleSearch, onSidebarScroll, loadMoreConversations, fetchConversations,
               filteredConversations, todayConversations, yesterdayConversations,
               last7DaysConversations, olderConversations
--}}
<script>
    function renderMarkdown(content) {
        if (!content) return '';
        const markedLib = typeof marked !== 'undefined' ? marked : (window.marked || null);
        if (markedLib && typeof markedLib.parse === 'function') {
            try {
                return markedLib.parse(content);
            } catch (e) {
                console.warn('marked.parse error:', e);
            }
        }
        const div = document.createElement('div');
        div.textContent = content;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
    window.renderMarkdown = renderMarkdown;

    function parseConversationDate(conv) {
        const dateStr = conv.updated_at || conv.created_at;
        if (!dateStr) return null;
        const formatted = typeof dateStr === 'string' && dateStr.includes(' ') && !dateStr.includes('T')
            ? dateStr.replace(' ', 'T')
            : dateStr;
        const d = new Date(formatted);
        return isNaN(d.getTime()) ? null : d;
    }

    function mentaiSidebarMixin() {
        return {
            conversations: [],
            currentPage: 1,
            hasMorePages: true,
            loadingConversations: false,
            loadingMoreConversations: false,
            sidebarOpen: false,
            sidebarCollapsed: false,
            showSearch: false,
            searchQuery: '',
            searchModalOpen: false,
            modalSearchQuery: '',
            searchSelectedIndex: -1,

            _initSidebar(initialConversations) {
                if (initialConversations && initialConversations.data) {
                    this.conversations = Array.isArray(initialConversations.data) ? initialConversations.data : [];
                    this.currentPage  = initialConversations.current_page || 1;
                    this.hasMorePages = !!initialConversations.next_page_url;
                }
            },

            ensureCurrentConversationInList(defaultTitle = 'Percakapan baru') {
                if (!this.conversationId) return;
                const exists = this.conversations.some(c => c.id === this.conversationId);
                if (!exists) {
                    this.conversations.unshift({
                        id: this.conversationId,
                        title: defaultTitle,
                        created_at: new Date().toISOString(),
                        updated_at: new Date().toISOString()
                    });
                }
            },

            openSearchModal() {
                this.searchModalOpen = true;
                this.modalSearchQuery = '';
                this.searchSelectedIndex = -1;
                this.$nextTick(() => {
                    const el = this.$refs.searchModalInput;
                    if (el) el.focus();
                });
            },

            closeSearchModal() {
                this.searchModalOpen = false;
                this.modalSearchQuery = '';
                this.searchSelectedIndex = -1;
            },

            modalFilteredConversations() {
                if (!Array.isArray(this.conversations)) return [];
                if (!this.modalSearchQuery.trim()) {
                    return this.conversations.slice(0, 5);
                }
                const q = this.modalSearchQuery.toLowerCase();
                return this.conversations
                    .filter(c => (c.title || 'Percakapan baru').toLowerCase().includes(q))
                    .slice(0, 5);
            },

            navigateModalResults(direction) {
                const list = this.modalFilteredConversations();
                if (list.length === 0) return;
                let newIndex = this.searchSelectedIndex + direction;
                if (newIndex < 0) newIndex = list.length - 1;
                if (newIndex >= list.length) newIndex = 0;
                this.searchSelectedIndex = newIndex;
            },

            selectActiveModalResult(spaNav = false) {
                const list = this.modalFilteredConversations();
                if (list.length === 0) return;
                const targetIdx = this.searchSelectedIndex >= 0 ? this.searchSelectedIndex : 0;
                const conv = list[targetIdx];
                if (!conv) return;
                this.closeSearchModal();
                if (spaNav && typeof this.navigateTo === 'function') {
                    this.navigateTo(conv);
                } else {
                    window.location.href = `/mental-support-chatbot/${conv.id}`;
                }
            },

            toggleSearch() {
                this.openSearchModal();
            },

            onSidebarScroll(e) {
                const el = e.target;
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 60) {
                    this.loadMoreConversations();
                }
            },

            async fetchConversations() {
                this.loadingConversations = true;
                this.currentPage = 1;
                try {
                    const res = await fetch('{{ route("ai.conversations") }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        const items = data.data || [];
                        if (items.length > 0) {
                            this.conversations = items;
                            this.currentPage   = data.current_page || 1;
                            this.hasMorePages  = !!data.next_page_url;
                        }
                    }
                } catch (e) {
                    console.error('Error fetching conversations:', e);
                } finally {
                    this.loadingConversations = false;
                }
            },

            async loadMoreConversations() {
                if (this.loadingMoreConversations || !this.hasMorePages || this.loadingConversations) return;
                this.loadingMoreConversations = true;
                try {
                    const nextPage = this.currentPage + 1;
                    const res = await fetch(`{{ route('ai.conversations') }}?page=${nextPage}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data  = await res.json();
                        const items = data.data || [];
                        if (items.length > 0) {
                            const existingIds = new Set(this.conversations.map(c => c.id));
                            this.conversations.push(...items.filter(c => !existingIds.has(c.id)));
                            this.currentPage  = data.current_page || nextPage;
                            this.hasMorePages = !!data.next_page_url;
                        } else {
                            this.hasMorePages = false;
                        }
                    }
                } catch (e) {
                    console.error('Error loading more conversations:', e);
                } finally {
                    this.loadingMoreConversations = false;
                }
            },

            filteredConversations() {
                if (!Array.isArray(this.conversations)) return [];
                if (!this.searchQuery.trim()) return this.conversations;
                const q = this.searchQuery.toLowerCase();
                return this.conversations.filter(c => (c.title || 'Percakapan baru').toLowerCase().includes(q));
            },

            todayConversations() {
                const today = new Date(); today.setHours(0, 0, 0, 0);
                return this.filteredConversations().filter(c => {
                    const d = parseConversationDate(c);
                    if (!d) return false;
                    d.setHours(0, 0, 0, 0);
                    return d.getTime() === today.getTime();
                });
            },

            yesterdayConversations() {
                const today = new Date(); today.setHours(0, 0, 0, 0);
                const yesterday = new Date(today); yesterday.setDate(yesterday.getDate() - 1);
                return this.filteredConversations().filter(c => {
                    const d = parseConversationDate(c);
                    if (!d) return false;
                    d.setHours(0, 0, 0, 0);
                    return d.getTime() === yesterday.getTime();
                });
            },

            last7DaysConversations() {
                const today = new Date(); today.setHours(0, 0, 0, 0);
                const sevenDaysAgo = new Date(today); sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
                const yesterday    = new Date(today); yesterday.setDate(yesterday.getDate() - 1);
                return this.filteredConversations().filter(c => {
                    const d = parseConversationDate(c);
                    if (!d) return false;
                    d.setHours(0, 0, 0, 0);
                    return d.getTime() < yesterday.getTime() && d.getTime() >= sevenDaysAgo.getTime();
                });
            },

            olderConversations() {
                const today = new Date(); today.setHours(0, 0, 0, 0);
                const sevenDaysAgo = new Date(today); sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
                return this.filteredConversations().filter(c => {
                    const d = parseConversationDate(c);
                    if (!d) return true; // Fallback so no valid conversation is hidden
                    d.setHours(0, 0, 0, 0);
                    return d.getTime() < sevenDaysAgo.getTime();
                });
            },
        };
    }

    function mentaiShow(conversationId) {
        return {
            ...mentaiSidebarMixin(),

            conversationId: conversationId,
            messages:       Array.isArray(window.__mentaiInitialMessages) ? window.__mentaiInitialMessages : [],
            loadingChat:    false,
            input:          '',
            loading:        false,
            streaming:      false,
            hasStreamedText: false,
            abortController: null,
            copiedId:       null,

            renderMarkdown(content) {
                return renderMarkdown(content);
            },

            formatTime(dateStr) {
                if (!dateStr) return '';
                try {
                    const d = new Date(dateStr);
                    if (isNaN(d.getTime())) return '';
                    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                } catch (e) {
                    return '';
                }
            },

            async copyMessage(text, id) {
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                    this.copiedId = id;
                    setTimeout(() => {
                        if (this.copiedId === id) this.copiedId = null;
                    }, 2000);
                } catch (e) {
                    console.error('Failed to copy text:', e);
                }
            },

            async regenerateLastResponse() {
                if (this.loading || this.messages.length === 0) return;
                let lastUserIndex = -1;
                for (let i = this.messages.length - 1; i >= 0; i--) {
                    const m = this.messages[i];
                    if (m.role === 'user' || m.role === 'User' || (m.role && m.role.value === 'user')) {
                        lastUserIndex = i;
                        break;
                    }
                }
                if (lastUserIndex === -1) return;

                const userText = this.messages[lastUserIndex].content;
                this.messages = this.messages.slice(0, lastUserIndex + 1);
                await this.executeSendMessage(userText, false, true);
            },

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
                el.style.height = Math.min(el.scrollHeight, 140) + 'px';
            },

            stopGeneration() {
                if (this.abortController) {
                    this.abortController.abort();
                    this.abortController = null;
                }
                this.loading = false;
                this.streaming = false;
            },

            async initChat() {
                this._initSidebar(window.__mentaiInitialConversations);
                this.ensureCurrentConversationInList();
                this._scrollToBottom();

                window.addEventListener('popstate', (e) => {
                    if (e.state && e.state.conversationId) {
                        this._loadConversation(e.state.conversationId, false);
                    }
                });

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

            async navigateTo(conv) {
                await this._loadConversation(conv.id, true);
            },

            async _loadConversation(targetId, pushState = true) {
                if (this.conversationId === targetId && this.messages.length > 0) {
                    this.sidebarOpen = false;
                    return;
                }

                this.conversationId = targetId;
                this.messages       = [];
                this.loadingChat    = true;
                this.sidebarOpen    = false;

                if (pushState) {
                    history.pushState({ conversationId: targetId }, '', `/mental-support-chatbot/${targetId}`);
                }

                try {
                    const res = await fetch(`/ai/${targetId}/messages`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        this.messages = await res.json();
                        this._scrollToBottom();
                    }
                } catch (e) {
                    console.error('Error loading conversation:', e);
                } finally {
                    this.loadingChat = false;
                }
            },

            async sendMessage() {
                if (this.loading || !this.input.trim()) return;
                const userText = this.input.trim();
                this.input = '';
                this.$nextTick(() => this.autoResize());
                await this.executeSendMessage(userText, this.messages.length === 0);
            },

            async executeSendMessage(userText, isFirstExchange = false, isRegeneration = false) {
                if (!isRegeneration) {
                    this.messages.push({
                        id: Date.now().toString(),
                        role: 'user',
                        content: userText,
                        created_at: new Date().toISOString()
                    });
                }

                const assistantId = (Date.now() + 1).toString();
                this.messages.push({
                    id: assistantId,
                    role: 'assistant',
                    content: '',
                    created_at: new Date().toISOString()
                });

                this.loading = true;
                this.streaming = true;
                this.hasStreamedText = false;
                this._scrollToBottom();

                // Optimistic title update in sidebar
                if (isFirstExchange) {
                    const quickTitle = userText.length > 30 ? userText.substring(0, 30) + '...' : userText;
                    const conv = this.conversations.find(c => c.id === this.conversationId);
                    if (conv && (!conv.title || conv.title === 'Percakapan baru')) {
                        conv.title = quickTitle;
                    } else if (!conv) {
                        this.conversations.unshift({
                            id: this.conversationId,
                            title: quickTitle,
                            created_at: new Date().toISOString(),
                            updated_at: new Date().toISOString()
                        });
                    }
                }

                this.abortController = new AbortController();

                try {
                    const response = await fetch(`/ai/${this.conversationId}/message`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this._getCSRFToken(),
                            'Accept': 'text/event-stream'
                        },
                        signal: this.abortController.signal,
                        body: JSON.stringify({ message: userText })
                    });

                    if (!response.ok) {
                        let errorMsg = 'NETWORK_ERROR';
                        try {
                            const errData = await response.json();
                            if (errData && errData.message) {
                                if (response.status === 429) errorMsg = 'QUOTA_EXCEEDED:' + errData.message;
                                else if (response.status === 403) errorMsg = 'SUBSCRIPTION_REQUIRED:' + errData.message;
                                else errorMsg = errData.message;
                            }
                        } catch (e) {}
                        throw new Error(errorMsg);
                    }

                    if (!response.body) throw new Error('ReadableStream not supported');

                    const reader  = response.body.getReader();
                    const decoder = new TextDecoder('utf-8');
                    let buffer    = '';

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;

                        buffer += decoder.decode(value, { stream: true });
                        const parts = buffer.split('\n\n');
                        buffer = parts.pop() || '';

                        for (const part of parts) {
                            for (const line of part.split('\n')) {
                                if (!line.startsWith('data:')) continue;
                                const jsonStr = line.replace(/^data:/, '').trim();
                                if (!jsonStr || jsonStr === '[DONE]') continue;
                                try {
                                    const event = JSON.parse(jsonStr);
                                    if (event.type === 'error' || event.error) {
                                        throw new Error(event.message || event.errorText || 'STREAM_ERROR');
                                    }
                                    const chunkText = event.delta || event.text || event.content || '';
                                    if (chunkText) {
                                        this.hasStreamedText = true;
                                        const idx = this.messages.findIndex(m => m.id === assistantId);
                                        if (idx !== -1) {
                                            this.messages[idx].content += chunkText;
                                            this._scrollToBottom();
                                        }
                                    }
                                } catch (e) {
                                    if (e.message && e.message.startsWith('STREAM_ERROR')) {
                                        throw e;
                                    }
                                }
                            }
                        }
                    }

                    if (!this.hasStreamedText) {
                        throw new Error('NO_STREAM_OUTPUT');
                    }
                } catch (error) {
                    if (error.name === 'AbortError') {
                        // User stopped generation manually
                        return;
                    }

                    console.error('MentAI Stream Error:', error);
                    const idx = this.messages.findIndex(m => m.id === assistantId);
                    if (idx !== -1) {
                        let msg = 'Maaf, terjadi kesalahan saat menghubungi MentAI. Silakan coba lagi.';
                        if (error.message) {
                            if (error.message.startsWith('QUOTA_EXCEEDED:')) {
                                msg = error.message.substring(15);
                            } else if (error.message.startsWith('SUBSCRIPTION_REQUIRED:')) {
                                msg = error.message.substring(22);
                            } else if (error.message.includes('429') || error.message.toLowerCase().includes('rate limit') || error.message.includes('RateLimitedException')) {
                                msg = 'MentAI sedang menerima banyak pesan dalam waktu singkat (limit request per menit dari Google Gemini AI). Mohon tunggu sekitar 15-30 detik lalu klik tombol "Coba lagi" ya! 🙏';
                            } else if (error.message !== 'NETWORK_ERROR' && error.message !== 'NO_STREAM_OUTPUT') {
                                msg = error.message;
                            }
                        }
                        this.messages[idx].content = msg;
                        this._scrollToBottom();
                    }
                } finally {
                    this.abortController = null;
                    this.loading   = false;
                    this.streaming = false;
                    if (isFirstExchange) {
                        const content = this.messages.find(m => m.id === assistantId)?.content || '';
                        this.generateTitle(userText, content);
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
                            message:  userMessage.substring(0, 300),
                            response: assistantResponse.substring(0, 300)
                        })
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (data && data.title) {
                        const conv = this.conversations.find(c => c.id === this.conversationId);
                        if (conv) conv.title = data.title;
                    }
                } catch (e) {
                    console.error('Title generation failed:', e);
                }
            },
        };
    }

    function mentaiIndex() {
        return {
            ...mentaiSidebarMixin(),

            input:   '',
            loading: false,

            stopGeneration() {
                this.loading = false;
            },

            _getCSRFToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            },

            autoResize() {
                const el = this.$refs.messageInput;
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 100) + 'px';
            },

            selectStarter(text) {
                this.input = text;
                this.$nextTick(() => {
                    this.autoResize();
                    if (this.$refs.messageInput) this.$refs.messageInput.focus();
                });
            },

            async initChat() {
                this._initSidebar(window.__mentaiInitialConversations);
            },

            async sendMessage() {
                if (this.loading || !this.input.trim()) return;
                const userText = this.input.trim();
                this.loading = true;
                try {
                    const res = await fetch('{{ route("ai.start") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this._getCSRFToken(),
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Network error starting conversation');
                    const { conversationId } = await res.json();
                    sessionStorage.setItem('mentai_pending_prompt', JSON.stringify({ conversationId, message: userText }));
                    window.location.href = `/mental-support-chatbot/${conversationId}`;
                } catch (error) {
                    console.error('Failed to initiate chat:', error);
                    this.loading = false;
                    alert('Gagal memulai percakapan. Silakan coba lagi.');
                }
            },
        };
    }

    window.mentaiSidebarMixin = mentaiSidebarMixin;
    window.mentaiShow = mentaiShow;
    window.mentaiIndex = mentaiIndex;

    if (window.Alpine) {
        Alpine.data('mentaiShow', mentaiShow);
        Alpine.data('mentaiIndex', mentaiIndex);
    } else {
        document.addEventListener('alpine:init', () => {
            Alpine.data('mentaiShow', mentaiShow);
            Alpine.data('mentaiIndex', mentaiIndex);
        });
    }
</script>

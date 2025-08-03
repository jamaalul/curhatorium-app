/**
 * Chatbot Module
 * Handles chat interface, message rendering, and AI interactions
 */

class Chatbot {
    constructor() {
        this.currentSessionId = null;
        this.messages = [];
        
        // DOM elements
        this.chatMessages = document.getElementById('chat-messages');
        this.chatContainer = document.querySelector('.chat-container');
        this.chatbotForm = document.getElementById('chatbot-form');
        this.userInput = document.getElementById('user-input');
        this.sendBtn = document.getElementById('send-btn');
        this.loadingIndicator = document.getElementById('loading');
        this.newChatBtn = document.getElementById('new-chat-btn');
        this.sidebarSessions = document.querySelector('.sidebar-sessions');
        this.sessionSearch = document.getElementById('session-search');
        this.chatbotMobileMenuBtn = document.getElementById('chatbot-mobile-menu-btn');
        this.chatbotSidebar = document.getElementById('chatbot-sidebar');
        this.mobileOverlay = document.getElementById('mobile-overlay');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadMarkdownLibraries();
        this.updateSessionList();
        this.setupMobileMenu();
    }

    setupEventListeners() {
        // Form submission
        if (this.chatbotForm) {
            this.chatbotForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        // New chat button
        if (this.newChatBtn) {
            this.newChatBtn.addEventListener('click', () => {
                this.createNewSession();
            });
        }

        // Session search
        if (this.sessionSearch) {
            this.sessionSearch.addEventListener('input', (e) => {
                this.filterSessions(e.target.value);
            });
        }

        // Mobile menu toggle
        if (this.chatbotMobileMenuBtn) {
            this.chatbotMobileMenuBtn.addEventListener('click', () => {
                this.toggleMobileMenu();
            });
        }

        // Mobile overlay
        if (this.mobileOverlay) {
            this.mobileOverlay.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        }
    }

    loadMarkdownLibraries() {
        // Load marked.js for markdown parsing
        if (typeof marked === 'undefined') {
            const markedScript = document.createElement('script');
            markedScript.src = 'https://cdn.jsdelivr.net/npm/marked/marked.min.js';
            document.head.appendChild(markedScript);
        }

        // Load DOMPurify for sanitization
        if (typeof DOMPurify === 'undefined') {
            const purifyScript = document.createElement('script');
            purifyScript.src = 'https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js';
            document.head.appendChild(purifyScript);
        }
    }

    renderMarkdownSafe(text) {
        if (typeof marked === 'undefined' || typeof DOMPurify === 'undefined') {
            return text; // Fallback to plain text
        }
        
        const rawHtml = marked.parse(text, { breaks: true });
        return DOMPurify.sanitize(rawHtml, { USE_PROFILES: { html: true } });
    }

    scrollToBottom() {
        setTimeout(() => {
            if (this.chatContainer) {
                this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
            }
        }, 100);
    }

    scrollToBottomSmooth() {
        if (this.chatContainer) {
            this.chatContainer.scrollTo({
                top: this.chatContainer.scrollHeight,
                behavior: 'smooth'
            });
        }
    }

    renderMessages() {
        if (!this.chatMessages) return;
        
        this.chatMessages.innerHTML = '';
        this.messages.forEach(msg => {
            const row = document.createElement('div');
            row.className = 'message-row ' + (msg.role === 'user' ? 'user' : 'bot');
            
            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            
            if (msg.role === 'assistant') {
                bubble.innerHTML = this.renderMarkdownSafe(msg.content);
            } else {
                bubble.textContent = msg.content;
            }
            
            row.appendChild(bubble);
            this.chatMessages.appendChild(row);
        });
        
        // Scroll after all messages are rendered
        setTimeout(() => {
            console.log('Scroll ke bawah...');
            if (this.chatContainer) {
                console.log('Tinggi scroll chat container:', this.chatContainer.scrollHeight);
                console.log('Tinggi client chat container:', this.chatContainer.clientHeight);
            }
            this.scrollToBottomSmooth();
        }, 100);
    }

    updateSessionList() {
        fetch('/api/chatbot/sessions')
            .then(response => response.json())
            .then(sessions => {
                this.renderSessionList(sessions);
            })
            .catch(error => {
                console.error('Error fetching sessions:', error);
            });
    }

    renderSessionList(sessions) {
        if (!this.sidebarSessions) return;
        
        this.sidebarSessions.innerHTML = '';
        
        if (sessions.length === 0) {
            this.sidebarSessions.innerHTML = '<div class="no-sessions">No sessions yet</div>';
            return;
        }
        
        sessions.forEach(session => {
            const sessionElement = document.createElement('div');
            sessionElement.className = 'session-item';
            if (session.id === this.currentSessionId) {
                sessionElement.classList.add('active');
            }
            
            sessionElement.innerHTML = `
                <div class="session-title">${session.title || 'New Chat'}</div>
                <div class="session-date">${new Date(session.created_at).toLocaleDateString()}</div>
            `;
            
            sessionElement.addEventListener('click', () => {
                this.loadSession(session.id);
            });
            
            this.sidebarSessions.appendChild(sessionElement);
        });
    }

    filterSessions(searchTerm) {
        const sessionItems = this.sidebarSessions?.querySelectorAll('.session-item');
        if (!sessionItems) return;
        
        sessionItems.forEach(item => {
            const title = item.querySelector('.session-title').textContent.toLowerCase();
            const matches = title.includes(searchTerm.toLowerCase());
            item.style.display = matches ? 'block' : 'none';
        });
    }

    async createNewSession() {
        try {
            const response = await fetch('/api/chatbot/sessions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const session = await response.json();
            this.currentSessionId = session.id;
            this.messages = [];
            this.renderMessages();
            this.updateSessionList();
            this.enableInput();
            this.hideNoSessionOverlay();
        } catch (error) {
            console.error('Error creating new session:', error);
        }
    }

    async loadSession(sessionId) {
        try {
            const response = await fetch(`/api/chatbot/sessions/${sessionId}`);
            const session = await response.json();
            
            this.currentSessionId = sessionId;
            this.messages = session.messages || [];
            this.renderMessages();
            this.enableInput();
            this.hideNoSessionOverlay();
        } catch (error) {
            console.error('Error loading session:', error);
        }
    }

    async sendMessage() {
        if (!this.currentSessionId || !this.userInput) return;
        
        const message = this.userInput.value.trim();
        if (!message) return;
        
        // Add user message to UI
        this.messages.push({ role: 'user', content: message });
        this.renderMessages();
        
        // Clear input and disable
        this.userInput.value = '';
        this.disableInput();
        this.showLoading();
        
        try {
            const response = await fetch('/api/chatbot/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: this.currentSessionId,
                    message: message
                })
            });
            
            const data = await response.json();
            
            // Add bot response to UI
            this.messages.push({ role: 'assistant', content: data.message });
            this.renderMessages();
            
            // Update session list to reflect new title
            this.updateSessionList();
            
        } catch (error) {
            console.error('Error sending message:', error);
            // Add error message
            this.messages.push({ role: 'assistant', content: 'Sorry, I encountered an error. Please try again.' });
            this.renderMessages();
        } finally {
            this.hideLoading();
            this.enableInput();
        }
    }

    enableInput() {
        if (this.userInput) this.userInput.disabled = false;
        if (this.sendBtn) this.sendBtn.disabled = false;
    }

    disableInput() {
        if (this.userInput) this.userInput.disabled = true;
        if (this.sendBtn) this.sendBtn.disabled = true;
    }

    showLoading() {
        if (this.loadingIndicator) this.loadingIndicator.style.display = 'block';
    }

    hideLoading() {
        if (this.loadingIndicator) this.loadingIndicator.style.display = 'none';
    }

    hideNoSessionOverlay() {
        const overlay = document.getElementById('no-session-overlay');
        if (overlay) overlay.style.display = 'none';
    }

    setupMobileMenu() {
        // Mobile menu functionality
        if (this.chatbotMobileMenuBtn && this.chatbotSidebar) {
            this.chatbotMobileMenuBtn.addEventListener('click', () => {
                this.toggleMobileMenu();
            });
        }
    }

    toggleMobileMenu() {
        if (this.chatbotSidebar) {
            this.chatbotSidebar.classList.toggle('active');
        }
        if (this.mobileOverlay) {
            this.mobileOverlay.classList.toggle('active');
        }
    }

    closeMobileMenu() {
        if (this.chatbotSidebar) {
            this.chatbotSidebar.classList.remove('active');
        }
        if (this.mobileOverlay) {
            this.mobileOverlay.classList.remove('active');
        }
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the chatbot page
    if (document.getElementById('chat-messages')) {
        new Chatbot();
    }
});

// Make available globally for debugging
window.Chatbot = Chatbot; 
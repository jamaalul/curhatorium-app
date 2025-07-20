<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>Curhatorium | Ment-AI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
</head>
<body>
  @include('components.navbar')
  <main class="chatbot-main">
    <div class="chatbot-layout">
      <!-- Sidebar -->
      <div class="chatbot-sidebar">
        <div class="sidebar-header">
          <div class="search-container">
            <input type="text" id="session-search" class="session-search" placeholder="Search chats..." />
            <div class="search-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.562 21.4452L18.9165 16.7981C22.3925 12.1533 21.4447 5.57034 16.7995 2.09462C12.1544 -1.3811 5.57081 -0.433388 2.09479 4.21139C-1.38122 8.85617 -0.433425 15.4392 4.21174 18.9149C7.94312 21.7069 13.0681 21.7069 16.7995 18.9149L21.447 23.562C22.0311 24.146 22.978 24.146 23.562 23.562C24.146 22.978 24.146 22.0312 23.562 21.4472L23.562 21.4452ZM10.5444 18.018C6.41596 18.018 3.06927 14.6715 3.06927 10.5435C3.06927 6.41543 6.41596 3.06901 10.5444 3.06901C14.6728 3.06901 18.0195 6.41543 18.0195 10.5435C18.0151 14.6697 14.6709 18.0136 10.5444 18.018Z" fill="black"/>
              </svg>
            </div>
          </div>
          <button id="new-chat-btn" class="new-chat-btn">+ New Chat</button>
        </div>
        <div class="sidebar-sessions">
          @foreach($sessions as $session)
            <div class="session-item" data-session-id="{{ $session->id }}">
              <div class="session-title">{{ $session->title }}</div>
              <button class="delete-session-btn" data-session-id="{{ $session->id }}">×</button>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Main Chat Area -->
      <div class="chatbot-content">
        <div class="chat-container">
          <div id="chat-messages" class="chat-messages"></div>
          <div id="loading" class="loading-indicator" style="display:none;">Thinking...</div>
        </div>
        
        <div class="chatbot-form-container">
          <form id="chatbot-form" class="chatbot-form" autocomplete="off">
            <input type="text" id="user-input" placeholder="Type your message..." required autocomplete="off" />
            <button type="submit" id="send-btn">Send</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
  <script>
    let currentSessionId = null;
    let messages = [];

    const chatMessages = document.getElementById('chat-messages');
    const chatbotForm = document.getElementById('chatbot-form');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const loadingIndicator = document.getElementById('loading');
    const newChatBtn = document.getElementById('new-chat-btn');
    const sidebarSessions = document.querySelector('.sidebar-sessions');
    const sessionSearch = document.getElementById('session-search');

    function renderMarkdownSafe(text) {
      const rawHtml = marked.parse(text, { breaks: true });
      return DOMPurify.sanitize(rawHtml, { USE_PROFILES: { html: true } });
    }

    function scrollToBottom() {
      setTimeout(() => {
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }, 0);
    }

    function renderMessages() {
      chatMessages.innerHTML = '';
      messages.forEach(msg => {
        const row = document.createElement('div');
        row.className = 'message-row ' + (msg.role === 'user' ? 'user' : 'bot');
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        if (msg.role === 'assistant') {
          bubble.innerHTML = renderMarkdownSafe(msg.content);
        } else {
          bubble.textContent = msg.content;
        }
        row.appendChild(bubble);
        chatMessages.appendChild(row);
      });
      scrollToBottom();
    }

    function updateSessionList() {
      fetch('/api/chatbot/sessions')
        .then(response => response.json())
        .then(sessions => {
          renderSessionList(sessions);
        });
    }

    function renderSessionList(sessions) {
      sidebarSessions.innerHTML = '';
      sessions.forEach(session => {
        const sessionItem = document.createElement('div');
        sessionItem.className = 'session-item';
        sessionItem.setAttribute('data-session-id', session.id);
        sessionItem.innerHTML = `
          <div class="session-title">${session.title}</div>
          <button class="delete-session-btn" data-session-id="${session.id}">×</button>
        `;
        sidebarSessions.appendChild(sessionItem);
      });
    }

    function filterSessions(searchTerm) {
      const sessionItems = document.querySelectorAll('.session-item');
      sessionItems.forEach(item => {
        const title = item.querySelector('.session-title').textContent.toLowerCase();
        const matches = title.includes(searchTerm.toLowerCase());
        item.style.display = matches ? 'flex' : 'none';
      });
    }

    function loadSession(sessionId) {
      currentSessionId = sessionId;
      fetch(`/api/chatbot/session/${sessionId}`)
        .then(response => response.json())
        .then(session => {
          messages = session.messages.map(msg => ({
            role: msg.role,
            content: msg.content
          }));
          renderMessages();
          
          // Update active session in sidebar
          document.querySelectorAll('.session-item').forEach(item => {
            item.classList.remove('active');
          });
          document.querySelector(`[data-session-id="${sessionId}"]`).classList.add('active');
        });
    }

    function createNewSession() {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      fetch('/api/chatbot/session', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ title: 'New Chat' })
      })
      .then(response => response.json())
      .then(session => {
        currentSessionId = session.id;
        messages = session.messages.map(msg => ({
          role: msg.role,
          content: msg.content
        }));
        renderMessages();
        updateSessionList();
      });
    }

    function deleteSession(sessionId) {
      if (!confirm('Are you sure you want to delete this chat?')) return;
      
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      
      fetch(`/api/chatbot/session/${sessionId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (currentSessionId === sessionId) {
            currentSessionId = null;
            messages = [];
            renderMessages();
          }
          updateSessionList();
        }
      });
    }

    async function sendMessageToChatbot(userMessage) {
      if (!currentSessionId) {
        await createNewSession();
      }

      messages.push({ role: "user", content: userMessage });
      renderMessages();
      loadingIndicator.style.display = '';
      sendBtn.disabled = true;
      userInput.disabled = true;

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const response = await fetch('/api/chatbot', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ 
            session_id: currentSessionId,
            message: userMessage 
          })
        });

        if (!response.ok) throw new Error('Chatbot API error: ' + response.status);

        const data = await response.json();
        
        messages.push({ role: "assistant", content: data.message });
        renderMessages();
        
        // Update session list to reflect new title
        updateSessionList();
      } catch (err) {
        console.error('Error in sendMessageToChatbot:', err);
        messages.push({ role: "assistant", content: "Maaf, ada masalah dengan koneksi chatbot." });
        renderMessages();
      } finally {
        loadingIndicator.style.display = 'none';
        sendBtn.disabled = false;
        userInput.disabled = false;
        userInput.value = '';
        userInput.focus();
      }
    }

    // Event Listeners
    chatbotForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const text = userInput.value.trim();
      if (!text) return;
      sendMessageToChatbot(text);
    });

    newChatBtn.addEventListener('click', createNewSession);

    // Search functionality
    sessionSearch.addEventListener('input', function(e) {
      const searchTerm = e.target.value.trim();
      filterSessions(searchTerm);
    });

    // Session click events
    sidebarSessions.addEventListener('click', function(e) {
      if (e.target.classList.contains('session-item') || e.target.closest('.session-item')) {
        const sessionItem = e.target.classList.contains('session-item') ? e.target : e.target.closest('.session-item');
        const sessionId = sessionItem.getAttribute('data-session-id');
        loadSession(sessionId);
      }
      
      if (e.target.classList.contains('delete-session-btn')) {
        e.stopPropagation();
        const sessionId = e.target.getAttribute('data-session-id');
        deleteSession(sessionId);
      }
    });

    // Initialize
    window.addEventListener('DOMContentLoaded', () => {
      // Load first session if available
      const firstSession = document.querySelector('.session-item');
      if (firstSession) {
        const sessionId = firstSession.getAttribute('data-session-id');
        loadSession(sessionId);
      } else {
        // Create new session if none exist
        createNewSession();
      }
    });
  </script>
</body>
</html>

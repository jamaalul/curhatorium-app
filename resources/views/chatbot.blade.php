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
    <div class="chatbot-container">
      <div id="chat-messages" class="chat-messages"></div>
      <div id="loading" class="loading-indicator" style="display:none;">Thinking...</div>
    </div>
  </main>
  <div class="chatbot-form-container">
    <form id="chatbot-form" class="chatbot-form" autocomplete="off">
      <input type="text" id="user-input" placeholder="Type your message..." required autocomplete="off" />
      <button type="submit" id="send-btn">Send</button>
      <button type="button" id="reset-btn">Reset</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
  <script>
    let messages = [];

    // Load chat history from localStorage
    function loadChatHistory() {
      const saved = localStorage.getItem('chatbot_messages');
      if (saved) {
        try {
          messages = JSON.parse(saved);
        } catch {
          messages = [];
        }
      } else {
        messages = [];
      }
    }

    // Save chat history to localStorage
    function saveChatHistory() {
      localStorage.setItem('chatbot_messages', JSON.stringify(messages));
    }

    const chatMessages = document.getElementById('chat-messages');
    const chatbotForm = document.getElementById('chatbot-form');
    const userInput = document.getElementById('user-input');
    const sendBtn = document.getElementById('send-btn');
    const resetBtn = document.getElementById('reset-btn');
    const loadingIndicator = document.getElementById('loading');

    function renderMarkdownBold(text) {
      return text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    }

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
      saveChatHistory();
    }

    async function sendMessageToChatbot(userMessage) {
      console.log('Starting to send message:', userMessage);
      messages.push({ role: "user", content: userMessage });
      renderMessages();
      loadingIndicator.style.display = '';
      sendBtn.disabled = true;
      userInput.disabled = true;

      try {
        console.log('Sending request to /api/chatbot');
        console.log('Messages being sent:', messages);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('CSRF Token:', csrfToken);
        
        const response = await fetch('/api/chatbot', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ messages })
        });

        console.log('Response status:', response.status);
        
        if (!response.ok) throw new Error('Chatbot API error: ' + response.status);

        const data = await response.json();
        console.log('Response data:', data);
        
        const botReply = data.choices?.[0]?.message?.content?.trim() || "Maaf. Saya tidak mengerti.";
        messages.push({ role: "assistant", content: botReply });
        renderMessages();
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

    chatbotForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const text = userInput.value.trim();
      if (!text) return;
      sendMessageToChatbot(text);
    });

    // Reset button functionality
    resetBtn.addEventListener('click', function() {
      messages = [];
      saveChatHistory();
      renderMessages();
      userInput.value = '';
      userInput.focus();
    });

    window.addEventListener('DOMContentLoaded', () => {
      loadChatHistory();
      if (messages.length === 0) {
        messages.push({ role: "assistant", content: "Haiiii. Ada cerita apa hari ini?" });
      }
      renderMessages();
    });
  </script>
</body>
</html>

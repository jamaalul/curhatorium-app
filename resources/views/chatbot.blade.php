<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>Curhatorium | Ment-AI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
</head>
<body>
  <!-- Navbar Chatbot Kustom -->
  <nav class="chatbot-navbar">
    <button class="chatbot-mobile-menu-btn" id="chatbot-mobile-menu-btn" aria-label="Buka/tutup sidebar">
      <span></span>
      <span></span>
      <span></span>
    </button>
    
    <div class="chatbot-logo-box" onclick="window.location.href = '/dashboard'">
      <img src="{{ asset('assets/mini_logo.png') }}" alt="mini_logo" class="chatbot-mini-logo">
      <h1>Curhatorium</h1>
    </div>
  </nav>

  <main class="chatbot-main">
    <div class="chatbot-layout">

      <!-- Sidebar -->
      <div class="chatbot-sidebar" id="chatbot-sidebar">
        <div class="sidebar-header">
          <div class="search-container">
            <input type="text" id="session-search" class="session-search" placeholder="Cari percakapan..." />
            <div class="search-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.562 21.4452L18.9165 16.7981C22.3925 12.1533 21.4447 5.57034 16.7995 2.09462C12.1544 -1.3811 5.57081 -0.433388 2.09479 4.21139C-1.38122 8.85617 -0.433425 15.4392 4.21174 18.9149C7.94312 21.7069 13.0681 21.7069 16.7995 18.9149L21.447 23.562C22.0311 24.146 22.978 24.146 23.562 23.562C24.146 22.978 24.146 22.0312 23.562 21.4472L23.562 21.4452ZM10.5444 18.018C6.41596 18.018 3.06927 14.6715 3.06927 10.5435C3.06927 6.41543 6.41596 3.06901 10.5444 3.06901C14.6728 3.06901 18.0195 6.41543 18.0195 10.5435C18.0151 14.6697 14.6709 18.0136 10.5444 18.018Z" fill="black"/>
              </svg>
            </div>
          </div>
          <button id="new-chat-btn" class="new-chat-btn">+ Percakapan Baru</button>
        </div>
        <div class="sidebar-sessions">
          @foreach($sessions as $session)
            <div class="session-item" data-session-id="{{ $session['id'] }}">
              <div class="session-title">{{ $session['title'] }}</div>
              <button class="delete-session-btn" data-session-id="{{ $session['id'] }}">×</button>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Overlay Mobile -->
      <div class="mobile-overlay" id="mobile-overlay"></div>

      <!-- Area Utama Chat -->
      <div class="chatbot-content">
        @if(isset($chatbotSecondsLeft) && $chatbotSecondsLeft)
        <div id="chatbot-timer" style="font-weight:bold;color:#2a7; margin-bottom:1rem;"></div>
        <script>
          let chatbotSecondsLeft = {{ $chatbotSecondsLeft }};
          function updateChatbotTimer() {
            if (chatbotSecondsLeft <= 0) {
              document.getElementById('chatbot-timer').innerText = 'Waktu anda sudah habis';
              document.getElementById('user-input').disabled = true;
              document.getElementById('send-btn').disabled = true;
              setTimeout(function() {
                window.location.href = '/dashboard?error=Waktu+anda+sudah+habis';
              }, 1500);
              return;
            }
            let h = Math.floor(chatbotSecondsLeft / 3600);
            let m = Math.floor((chatbotSecondsLeft % 3600) / 60);
            let s = chatbotSecondsLeft % 60;
            document.getElementById('chatbot-timer').innerText = `Sisa waktu: ${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
            chatbotSecondsLeft--;
          }
          updateChatbotTimer();
          setInterval(updateChatbotTimer, 1000);
        </script>
        @endif
        <div class="chat-container">
          <div id="chat-messages" class="chat-messages"></div>
          <div id="loading" class="loading-indicator" style="display:none;">Sedang berpikir...</div>
          <div id="no-session-overlay" style="display:none; text-align:center; margin-top:40px;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 24px; padding: 40px 0;">
              <div style="font-size: 1.2em; color: #595959; font-weight: 500;">No conversation started</div>
              <button id="center-new-chat-btn" class="new-chat-btn center-new-chat-btn" style="font-size:1.1em; padding: 12px 32px; border-radius: 24px; box-shadow: 0 2px 8px rgba(72,166,166,0.08); background: linear-gradient(90deg, #48a6a6 60%, #4ecdc4 100%); min-width: 0; width: auto; max-width: 100%;">Mulai percakapan</button>
            </div>
          </div>
        </div>
        
        <div class="chatbot-form-container">
          <form id="chatbot-form" class="chatbot-form" autocomplete="off">
            <input type="text" id="user-input" placeholder="Ketik pesan Anda..." required autocomplete="off" disabled />
            <button type="submit" id="send-btn" disabled>Kirim</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="/js/modules/chatbot.js"></script>
</body>
</html>

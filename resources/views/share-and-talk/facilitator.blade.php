<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Chatroom - Share and Talk</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <link rel="stylesheet" href="{{ asset('css/share-and-talk/chat.css') }}">
</head>
<body style="height: 100vh; overflow: hidden; margin: 0; padding: 0;">
  <div class="app">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>Lunar</h2>
      <div class="channel active">{{ $user['username'] }}</div>
      <div class="dashboard-link">
        <a href="{{ route('professional.dashboard', ['professionalId' => $professionalId]) }}" 
           style="color: #fff; text-decoration: none; padding: 8px 12px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-top: 20px; display: block; font-size: 0.9rem;">
          📊 Dashboard
        </a>
        <a href="{{ route('professional.login') }}" 
           style="color: #fff; text-decoration: none; padding: 8px 12px; background: rgba(255,255,255,0.1); border-radius: 4px; margin-top: 10px; display: block; font-size: 0.9rem;">
          🔐 Login
        </a>
      </div>
    </div>

    <!-- Chat Main Area -->
    <div class="chat-area">
      <div class="chat-header">
        <div>Konsultasi dengan <strong>{{ $user['username'] }}</strong></div>
        <div id="session-timer" style="font-size: 0.9rem; color: var(--text-muted);">
          Sisa waktu: 60:00
        </div>
      </div>

      <div id="activation-section" style="text-align: center; display: {{ $sessionStatus === 'waiting' || $sessionStatus === 'pending' ? 'block' : 'none' }};">
        <h3 style="margin: 0 0 15px 0; color: #333;">Sesi Menunggu Aktivasi</h3>
        <p style="margin: 0 0 15px 0; color: #666;">Klik tombol di bawah untuk memulai sesi konsultasi</p>
        <button id="activate-session-btn" style="padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px;">
          🚀 Mulai Sesi
        </button>
      </div>

      <div class="chat-body" id="chat-body">
        <div style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center; color: #b9b9b9;">Waiting...</div>
      </div>

      <form class="chat-input" id="chatForm">
        @csrf
        <input type="hidden" name="session_id" value="{{ $sessionId }}">
        <input type="text" placeholder="Tulis pesan..." id="chat-input-field" name="message" autocomplete="off" autocorrect="off" autocapitalize="off" />
        <button type="submit" id="send-btn">Kirim</button>
      </form>
      {{-- <div id="response"></div> --}}
    </div>
  </div>

  <script>
    // Mobile viewport fix
    function setViewportHeight() {
      const vh = window.innerHeight * 0.01;
      document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setViewportHeight();
    window.addEventListener('resize', setViewportHeight);
    window.addEventListener('orientationchange', setViewportHeight);
    
    const input = document.getElementById('chat-input-field');
    const btn = document.getElementById('send-btn');
    const chatBody = document.getElementById('chat-body');
    const timerEl = document.getElementById('session-timer');
    const activationSection = document.getElementById('activation-section');
    const activateBtn = document.getElementById('activate-session-btn');
    const interval = {{ $interval }};
    let sessionStart = new Date();
    let sessionStatus = '{{ $sessionStatus ?? "waiting" }}';
    const sessionId = '{{ $sessionId }}';

    function updateTimer() {
      const now = new Date();
      const end = new Date(sessionStart.getTime() + interval * 60 * 1000);
      const diff = end - now;

      if (diff <= 0) {
        input.disabled = true;
        btn.disabled = true;
        timerEl.innerText = 'Sesi telah berakhir';
        timerEl.style.color = 'red';
      } else {
        const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
        const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        timerEl.innerText = `Sisa waktu: ${mins}:${secs}`;
      }
    }

    // Check session status and control UI
    async function checkSessionStatus() {
      try {
        const response = await fetch(`/api/share-and-talk/session-status/${sessionId}`);
        if (!response.ok) return;
        const data = await response.json();
        sessionStatus = data.status;
        
        if (sessionStatus === 'waiting' || sessionStatus === 'pending') {
          // Show activation section
          activationSection.style.display = 'block';
          input.disabled = true;
          btn.disabled = true;
          timerEl.innerText = 'Menunggu aktivasi...';
          timerEl.style.color = 'orange';
        } else if (sessionStatus === 'active') {
          // Hide activation section and enable chat
          activationSection.style.display = 'none';
          input.disabled = false;
          btn.disabled = false;
          timerEl.style.color = 'var(--text-muted)';
          updateTimer();
        } else if (sessionStatus === 'cancelled') {
          // Session was cancelled
          activationSection.style.display = 'none';
          input.disabled = true;
          btn.disabled = true;
          timerEl.innerText = 'Sesi dibatalkan';
          timerEl.style.color = 'red';
        }
      } catch (error) {
        console.error('Error checking session status:', error);
      }
    }

    // Handle session activation
    activateBtn.addEventListener('click', async function() {
      try {
        const response = await fetch(`/api/share-and-talk/manual-activate/${sessionId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          // Session activated, refresh the page to show the chat interface
          window.location.reload();
        } else {
          alert('Gagal mengaktifkan sesi. Silakan coba lagi.');
        }
      } catch (error) {
        console.error('Error activating session:', error);
        alert('Gagal mengaktifkan sesi. Silakan coba lagi.');
      }
    });

    // Check session status every 2 seconds
    setInterval(checkSessionStatus, 2000);
    checkSessionStatus(); // Initial check

    setInterval(updateTimer, 1000);
    updateTimer();

    // Remove sendMessage function
    // function sendMessage(e) {
    //   e.preventDefault();
    //   const text = input.value.trim();
    //   if (!text || input.disabled) return;

    //   const bubble = document.createElement('div');
    //   bubble.className = 'message user';
    //   bubble.innerText = text;
    //   chatBody.appendChild(bubble);
    //   chatBody.scrollTop = chatBody.scrollHeight;
    //   input.value = '';
    // }
    
    
          // Simulasi balasan psikiater
          // setTimeout(() => {
          //   const reply = document.createElement('div');
          //   reply.className = 'message other';
          //   reply.innerText = 'Hello World';
          //   chatBody.appendChild(reply);
          //   chatBody.scrollTop = chatBody.scrollHeight;
          // }, 1000);

  document.getElementById('chatForm').addEventListener('submit', async function (e) {
    e.preventDefault(); // Prevents page reload

    const form = e.target;
    const session_id = form.session_id.value;
    const message = form.message.value.trim();

    if (!message || input.disabled) return;

    try {
        const response = await fetch('{{ route('share-and-talk.facilitatorSend') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json', // Added for Laravel JSON response
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                session_id,
                message
            })
        });

        // const result = await response.json();
        // document.getElementById('response').innerText = JSON.stringify(result);

        // Add the message bubble to the chat UI
        const bubble = document.createElement('div');
        bubble.className = 'message user';
        bubble.innerText = message;
        chatBody.appendChild(bubble);
        chatBody.scrollTop = chatBody.scrollHeight;
        input.value = '';
    } catch (err) {
        console.error('Error sending request:', err);
    }
  });

  // Polling fetch to get new messages every 2.5 seconds
  setInterval(async function () {
    try {
      // Replace with your actual API endpoint for fetching messages for this session
      const sessionId = document.querySelector('input[name="session_id"]').value;
      const response = await fetch(`/api/share-and-talk/messages/${sessionId}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      if (!response.ok) return;
      const data = await response.json();

      // Clear chat body and re-render messages
      // The problem is likely that the API at /api/share-and-talk/messages/{sessionId} returns an array, not an object with a "messages" property.
      // So data.messages is undefined, and Array.isArray(data.messages) is false, so nothing runs.
      // Instead, check if data itself is an array.

      if (Array.isArray(data)) {
        chatBody.innerHTML = '';
        data.forEach(msg => {
          const bubble = document.createElement('div');
          bubble.className = 'message ' + (msg.sender_type === 'user' ? 'other' : 'user');
          bubble.innerText = msg.message;
          chatBody.appendChild(bubble);
        });
        chatBody.scrollTop = chatBody.scrollHeight;
      }
      console.log('fetching')
    } catch (err) {
      // Optionally handle error
      console.error('Polling error:', err);
    }
  }, 2500);
  </script>
</body>
</html>

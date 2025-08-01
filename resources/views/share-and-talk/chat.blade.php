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
  <div class="app" data-session-end="{{ now()->addMinutes($interval)->toIso8601String() }}" data-session-id="{{ $session_id }}">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>Psikolog</h2>
      <div class="channel active">{{ $professional['name'] }}</div>
    </div>

    <!-- Chat Main Area -->
    <div class="chat-area">
      <div class="chat-header">
        <div>Konsultasi dengan <strong>{{ $professional['name'] }}</strong></div>
        <div id="session-timer" style="font-size: 0.9rem; color: var(--text-muted);">
          Sisa waktu: 60:00
        </div>
      </div>
      <div id="waiting-timer" style="font-size:0.95em;color:#b97c00;margin-bottom:0.5em;display:none;">
        Menunggu fasilitator... (<span id="waiting-countdown">05:00</span>)
      </div>
      <div id="cancel-warning" style="font-size:0.95em;color:#b97c00;margin-bottom:0.5em;display:none;">
        Sesi akan dibatalkan otomatis jika fasilitator tidak merespons dalam 5 menit. Jangan khawatir, tiketmu akan dikembalikan.<br>Tolong jangan berpindah dari halaman ini.
      </div>
      <div id="cancelled-redirect" style="display:none;text-align:center;margin-top:1em;">
        <button onclick="window.location.href='/dashboard'" style="padding:0.7em 1.5em;font-size:1em;background:#b97c00;color:#fff;border:none;border-radius:6px;cursor:pointer;">Kembali ke Dashboard</button>
      </div>

      <div class="chat-body" id="chat-body">
        {{-- <div class="message other">Halo, saya Nadya. Apa yang ingin kamu bahas hari ini?</div>
        <div class="message user">Akhir-akhir ini saya merasa cemas berlebihan...</div> --}}
      </div>

      <form class="chat-input" id="chatForm">
        @csrf
        <input type="hidden" name="session_id" value="{{ $session_id }}">
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
    const sessionId = document.querySelector('.app').dataset.sessionId;
    const interval = {{ $interval }};
    let sessionStatus = 'waiting';
    let timerInterval = null;
    let waitingCountdown = 300; // 5 minutes in seconds
    let waitingInterval = null;
    let cancelledByTimeout = false;
    let createdAt = null;
    let fallbackStart = new Date(); // Fallback start time

    async function fetchSessionStatus() {
      try {
        const res = await fetch(`/api/share-and-talk/session-status/${sessionId}`);
        if (!res.ok) return 'waiting';
        const data = await res.json();
        if (!createdAt && data.created_at) {
          // Parse 'YYYY-MM-DD HH:mm:ss' as local time
          const dt = data.created_at.replace(' ', 'T');
          let parsed = Date.parse(dt);
          if (isNaN(parsed)) {
            // Fallback: parse as local time
            const parts = data.created_at.split(/[- :]/);
            parsed = new Date(parts[0], parts[1]-1, parts[2], parts[3], parts[4], parts[5]).getTime();
          }
          createdAt = new Date(parsed);
          console.log('Session created at:', createdAt);
        }
        return data.status;
      } catch (error) {
        console.error('Error fetching session status:', error);
        return 'waiting';
      }
    }

    function updateTimer() {
      const now = new Date();
      const startTime = createdAt || fallbackStart;
      let end;
      let diff;
      
      if (sessionStatus === 'waiting' || sessionStatus === 'pending') {
        // 5-minute waiting period
        end = new Date(startTime.getTime() + 5 * 60 * 1000);
        diff = end - now;
        if (diff <= 0) {
          input.disabled = true;
          btn.disabled = true;
          timerEl.innerText = 'Sesi dibatalkan (fasilitator tidak merespons)';
          timerEl.style.color = 'red';
          cancelSessionByTimeout();
          return;
        }
        const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
        const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        timerEl.innerText = `Menunggu fasilitator: ${mins}:${secs}`;
        
        // Update waiting countdown display
        const waitingCountdown = document.getElementById('waiting-countdown');
        if (waitingCountdown) {
          waitingCountdown.innerText = `${mins}:${secs}`;
        }
        
        // Debug logging
        if (diff < 60000) { // Log when less than 1 minute remaining
          console.log('Waiting countdown:', `${mins}:${secs}`, 'Status:', sessionStatus);
        }
      } else if (sessionStatus === 'active') {
        // Session period based on interval from backend
        end = new Date(startTime.getTime() + interval * 60 * 1000);
        diff = end - now;
        if (diff <= 0) {
          input.disabled = true;
          btn.disabled = true;
          timerEl.innerText = 'Sesi telah berakhir';
          timerEl.style.color = 'red';
          setProfessionalOnline();
        } else {
          const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
          const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
          timerEl.innerText = `Sisa waktu: ${mins}:${secs}`;
        }
      } else {
        timerEl.innerText = 'Loading...';
      }
    }

    async function setProfessionalOnline() {
      try {
        // Get professional ID from the session data
        const professionalId = '{{ $professional["id"] }}';
        if (professionalId) {
          await fetch(`/api/share-and-talk/professional-online/${professionalId}`, { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } 
          });
        }
      } catch (error) {
        console.error('Error setting professional online:', error);
      }
    }

    function stopWaitingCountdown() {
      const waitingTimer = document.getElementById('waiting-timer');
      waitingTimer.style.display = 'none';
      if (waitingInterval) { clearInterval(waitingInterval); waitingInterval = null; }
    }

    async function cancelSessionByTimeout() {
      // Call API to cancel session
      try {
        await fetch(`/api/share-and-talk/cancel-session/${sessionId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
      } catch (error) {
        console.error('Error cancelling session:', error);
      }
      cancelledByTimeout = true;
      input.disabled = true;
      btn.disabled = true;
      timerEl.innerText = 'Sesi dibatalkan (fasilitator tidak merespons)';
      timerEl.style.color = 'red';
      stopWaitingCountdown();
      
      // Hide warning elements
      const warning = document.getElementById('cancel-warning');
      const waitingTimer = document.getElementById('waiting-timer');
      if (warning) warning.style.display = 'none';
      if (waitingTimer) waitingTimer.style.display = 'none';
      
      document.getElementById('cancelled-redirect').style.display = '';
    }

    async function pollStatusAndControlUI() {
      sessionStatus = await fetchSessionStatus();
      const warning = document.getElementById('cancel-warning');
      const waitingTimer = document.getElementById('waiting-timer');
      
      if (sessionStatus === 'waiting' || sessionStatus === 'pending') {
        input.disabled = true;
        btn.disabled = true;
        timerEl.style.color = 'orange';
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
        warning.style.display = '';
        waitingTimer.style.display = '';
        updateTimer();
        if (!timerInterval) {
          timerInterval = setInterval(updateTimer, 1000);
        }
      } else if (sessionStatus === 'active') {
        input.disabled = false;
        btn.disabled = false;
        timerEl.style.color = 'var(--text-muted)';
        if (!timerInterval) {
          updateTimer();
          timerInterval = setInterval(updateTimer, 1000);
        }
        warning.style.display = 'none';
        waitingTimer.style.display = 'none';
        stopWaitingCountdown();
      } else if (sessionStatus === 'cancelled') {
        input.disabled = true;
        btn.disabled = true;
        timerEl.innerText = 'Sesi dibatalkan';
        timerEl.style.color = 'red';
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
        warning.style.display = 'none';
        waitingTimer.style.display = 'none';
        stopWaitingCountdown();
        document.getElementById('cancelled-redirect').style.display = '';
      }
    }

    // Initial setup
    console.log('Share and Talk Chat initialized');
    console.log('Session ID:', sessionId);
    console.log('Interval:', interval);
    console.log('Fallback start time:', fallbackStart);
    
    setInterval(pollStatusAndControlUI, 2000);
    pollStatusAndControlUI();
    updateTimer(); // Initial timer update

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
        const response = await fetch('{{ route('share-and-talk.userSend') }}', {
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
          bubble.className = 'message ' + (msg.sender_type === 'user' ? 'user' : 'other');
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

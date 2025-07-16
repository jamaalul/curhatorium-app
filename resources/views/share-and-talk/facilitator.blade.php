<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Chatroom - Share and Talk</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <link rel="stylesheet" href="{{ asset('css/share-and-talk/chat.css') }}">
</head>
<body>
  <div class="app" data-session-end="{{ now()->addMinutes(5)->toIso8601String() }}">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2>Lunar</h2>
      <div class="channel active">{{ $user['username'] }}</div>
    </div>

    <!-- Chat Main Area -->
    <div class="chat-area">
      <div class="chat-header">
        <div>Konsultasi dengan <strong>{{ $user['username'] }}</strong></div>
        <div id="session-timer" style="font-size: 0.9rem; color: var(--text-muted);">
          Sisa waktu: 60:00
        </div>
      </div>

      <div class="chat-body" id="chat-body">
        {{-- <div class="message other">Halo, saya Nadya. Apa yang ingin kamu bahas hari ini?</div>
        <div class="message user">Akhir-akhir ini saya merasa cemas berlebihan...</div> --}}
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
    const input = document.getElementById('chat-input-field');
    const btn = document.getElementById('send-btn');
    const chatBody = document.getElementById('chat-body');
    const sessionEnd = new Date(document.querySelector('.app').dataset.sessionEnd);
    const timerEl = document.getElementById('session-timer');

    function updateTimer() {
      const now = new Date();
      const diff = sessionEnd - now;

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

<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src='https://8x8.vc/vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/external_api.js' async></script>
    <style>html, body, #jaas-container { height: 100%; }</style>
    <script type="text/javascript">
      window.onload = () => {
        const api = new JitsiMeetExternalAPI("8x8.vc", {
          roomName: "vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/{{ $session_id }}",
          parentNode: document.querySelector('#jaas-container'),
          // Make sure to include a JWT if you intend to record,
          // make outbound calls or use any other premium features!
          // jwt: "eyJraWQiOiJ2cGFhcy1tYWdpYy1jb29raWUtYWM5ZWUxNDFmYzhhNGMzMDhhYzI0ZjVlYzIyNWFmM2YvMzI0ZGMxLVNBTVBMRV9BUFAiLCJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJqaXRzaSIsImlzcyI6ImNoYXQiLCJpYXQiOjE3NTM1MjI1NjksImV4cCI6MTc1MzUyOTc2OSwibmJmIjoxNzUzNTIyNTY0LCJzdWIiOiJ2cGFhcy1tYWdpYy1jb29raWUtYWM5ZWUxNDFmYzhhNGMzMDhhYzI0ZjVlYzIyNWFmM2YiLCJjb250ZXh0Ijp7ImZlYXR1cmVzIjp7ImxpdmVzdHJlYW1pbmciOmZhbHNlLCJmaWxlLXVwbG9hZCI6ZmFsc2UsIm91dGJvdW5kLWNhbGwiOmZhbHNlLCJzaXAtb3V0Ym91bmQtY2FsbCI6ZmFsc2UsInRyYW5zY3JpcHRpb24iOmZhbHNlLCJsaXN0LXZpc2l0b3JzIjpmYWxzZSwicmVjb3JkaW5nIjpmYWxzZSwiZmxpcCI6ZmFsc2V9LCJ1c2VyIjp7ImhpZGRlbi1mcm9tLXJlY29yZGVyIjpmYWxzZSwibW9kZXJhdG9yIjp0cnVlLCJuYW1lIjoiVGVzdCBVc2VyIiwiaWQiOiJnb29nbGUtb2F1dGgyfDEwMDc3MTIyNDc1ODk2OTIwMTYzNiIsImF2YXRhciI6IiIsImVtYWlsIjoidGVzdC51c2VyQGNvbXBhbnkuY29tIn19LCJyb29tIjoiKiJ9.ewHhAoGqK6wYytWD2_0I8le1UDjOJ3T29Fs9Q4Dzw78NXIDOz-iXbdyd9KOsEKT4YuOTu6fBoCNohtwdpmkZmKG8UenDa2QSXqTWiuvdPlLys-KHtjSCrrFyrvAsgyL-XzRsNRe_2k-Vu3AXz2JrgCklDA3pd45YDHhV8cAcYXtYEaZNVzc0fniRnwjlrri3fqmyAu9niayQl8R4CIxE5irbXDvkLNh7zzUf5ipZtqps9PSRxrfAZQqnaKUC47G5SZsGrTcckQcdcVKRn240n25BZGvBaVkjgVE8DD112S4qDPgKPqojE5JEFZ6pOHvhCQSoUWVWzZXgH4wAY4qr5g"
        });
      }
    </script>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  @include('components.navbar')
    <div id="timer-container" style="position:fixed;bottom:32px;right:32px;z-index:9999;">
      <div id="session-timer" style="background:#222;color:#fff;padding:12px 28px;border-radius:16px;box-shadow:0 2px 12px #0002;display:flex;flex-direction:column;align-items:center;min-width:110px;font-family:'FigtreeReg', Figtree, Arial,sans-serif;">
        <span id="timer-label" style="font-size:0.85em;color:#9acbd0;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;letter-spacing:0.5px;">Waiting: </span>
        <span id="timer-value" style="font-size:1.5em;font-family:'Courier New',monospace;font-weight:700;letter-spacing:1px;">05:00</span>
      </div>
      <div id="warning-message" style="background:#f59e42;color:#fff;padding:8px 12px;border-radius:8px;font-size:0.75rem;margin-top:8px;text-align:center;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;box-shadow:0 2px 8px #0002;">
        ⚠️ Don't leave this page until session is cancelled
      </div>
    </div>
    <div id="jaas-container" />
    <script>
    // --- Timer & Polling Logic ---
    const sessionId = "{{ $session_id }}";
    let polling = true;
    let status = 'waiting';
    let createdAt = null;
    let timerLabel = document.getElementById('timer-label');
    let timerValue = document.getElementById('timer-value');
    let timerContainer = document.getElementById('timer-container');
    let fallbackStart = new Date(); // Fallback start time
    let waitingExpired = false;

    async function fetchSessionStatus() {
      try {
        const res = await fetch(`/api/share-and-talk/session-status/${sessionId}`);
        if (!res.ok) return;
        const data = await res.json();
        status = data.status;
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
        }
        if (status === 'active') {
          polling = false;
          waitingExpired = false;
          // Hide warning message when session is active
          const warningMessage = document.getElementById('warning-message');
          if (warningMessage) warningMessage.style.display = 'none';
          // Restore timer UI if it was replaced
          if (timerContainer.querySelector('button')) {
            timerContainer.innerHTML = '<div id="session-timer" style="background:#222;color:#fff;padding:12px 28px;border-radius:16px;box-shadow:0 2px 12px #0002;display:flex;flex-direction:column;align-items:center;min-width:110px;font-family:\'FigtreeReg\', Figtree, Arial,sans-serif;"><span id="timer-label" style="font-size:0.85em;color:#9acbd0;font-family:\'FigtreeBold\', Figtree, Arial,sans-serif;font-weight:600;letter-spacing:0.5px;">Session: </span><span id="timer-value" style="font-size:1.5em;font-family:\'Courier New\',monospace;font-weight:700;letter-spacing:1px;">60:00</span></div>';
            timerLabel = document.getElementById('timer-label');
            timerValue = document.getElementById('timer-value');
          }
        }
      } catch (e) { }
    }

    function updateTimer() {
      let now = new Date();
      let end;
      let startTime = createdAt || fallbackStart;
      
      if ((status === 'waiting' || status === 'pending') && !waitingExpired) {
        timerLabel.textContent = 'Waiting: ';
        end = new Date(startTime.getTime() + 5 * 60 * 1000);
        let diff = end - now;
        if (diff <= 0) {
          diff = 0;
          waitingExpired = true;
          
          // Cancel the session and refund ticket
          fetch(`/api/share-and-talk/cancel-session/${sessionId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
          }).catch(error => {
            console.error('Error cancelling session:', error);
          });
          
          // Replace timer with Back to Dashboard button
          timerContainer.innerHTML = '<div style="background:#222;color:#fff;padding:16px 24px;border-radius:16px;box-shadow:0 2px 12px #0002;min-width:200px;text-align:center;"><div style="font-size:0.9rem;margin-bottom:8px;color:#9acbd0;font-family:\'FigtreeBold\', Figtree, Arial,sans-serif;">Session Cancelled</div><div style="font-size:0.8rem;margin-bottom:12px;color:#ccc;">Your ticket will be refunded</div><button style="background:#9acbd0;color:#222;padding:8px 16px;border-radius:8px;border:none;cursor:pointer;font-size:0.9rem;font-family:\'FigtreeBold\', Figtree, Arial,sans-serif;font-weight:600;transition:all 0.3s ease;" onmouseover="this.style.background=\'#8ecbcf\'" onmouseout="this.style.background=\'#9acbd0\'" onclick="window.location.href=\'/dashboard\'">Back to Dashboard</button></div>';
          return;
        }
        let mins = String(Math.floor(diff / 60000)).padStart(2, '0');
        let secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        timerValue.textContent = `${mins}:${secs}`;
      } else if (status === 'active') {
        timerLabel.textContent = 'Session: ';
        end = new Date(startTime.getTime() + 65 * 60 * 1000);
        let diff = end - now;
        if (diff < 0) diff = 0;
        let mins = String(Math.floor(diff / 60000)).padStart(2, '0');
        let secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        timerValue.textContent = `${mins}:${secs}`;
      } else if (waitingExpired) {
        // Already replaced with Back button
        return;
      } else {
        timerLabel.textContent = 'Loading...';
        timerValue.textContent = '--:--';
      }
    }

    async function pollAndUpdate() {
      await fetchSessionStatus();
      updateTimer();
      if (polling && (status === 'waiting' || status === 'pending')) {
        setTimeout(pollAndUpdate, 2000);
      }
    }

    updateTimer();
    pollAndUpdate();
    setInterval(updateTimer, 1000);
    </script>
</body>
</html> 
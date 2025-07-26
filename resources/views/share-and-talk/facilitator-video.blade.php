<!DOCTYPE html>
<html>
  <head>
    <script src='https://8x8.vc/vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/external_api.js' async></script>
    <style>html, body, #jaas-container { height: 100%; }</style>
    <script type="text/javascript">
      window.onload = () => {
        const api = new JitsiMeetExternalAPI("8x8.vc", {
          roomName: "vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/{{ $sessionId }}",
          parentNode: document.querySelector('#jaas-container'),
          // Make sure to include a JWT if you intend to record,
          // make outbound calls or use any other premium features!
          // jwt: "eyJraWQiOiJ2cGFhcy1tYWdpYy1jb29raWUtYWM5ZWUxNDFmYzhhNGMzMDhhYzI0ZjVlYzIyNWFmM2YvMzI0ZGMxLVNBTVBMRV9BUFAiLCJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJqaXRzaSIsImlzcyI6ImNoYXQiLCJpYXQiOjE3NTM1MjI1NjksImV4cCI6MTc1MzUyOTc2OSwibmJmIjoxNzUzNTIyNTY0LCJzdWIiOiJ2cGFhcy1tYWdpYy1jb29raWUtYWM5ZWUxNDFmYzhhNGMzMDhhYzI0ZjVlYzIyNWFmM2YiLCJjb250ZXh0Ijp7ImZlYXR1cmVzIjp7ImxpdmVzdHJlYW1pbmciOmZhbHNlLCJmaWxlLXVwbG9hZCI6ZmFsc2UsIm91dGJvdW5kLWNhbGwiOmZhbHNlLCJzaXAtb3V0Ym91bmQtY2FsbCI6ZmFsc2UsInRyYW5zY3JpcHRpb24iOmZhbHNlLCJsaXN0LXZpc2l0b3JzIjpmYWxzZSwicmVjb3JkaW5nIjpmYWxzZSwiZmxpcCI6ZmFsc2V9LCJ1c2VyIjp7ImhpZGRlbi1mcm9tLXJlY29yZGVyIjpmYWxzZSwibW9kZXJhdG9yIjp0cnVlLCJuYW1lIjoiVGVzdCBVc2VyIiwiaWQiOiJnb29nbGUtb2F1dGgyfDEwMDc3MTIyNDc1ODk2OTIwMTYzNiIsImF2YXRhciI6IiIsImVtYWlsIjoidGVzdC51c2VyQGNvbXBhbnkuY29tIn19LCJyb29tIjoiKiJ9.ewHhAoGqK6wYytWD2_0I8le1UDjOJ3T29Fs9Q4Dzw78NXIDOz-iXbdyd9KOsEKT4YuOTu6fBoCNohtwdpmkZmKG8UenDa2QSXqTWiuvdPlLys-KHtjSCrrFyrvAsgyL-XzRsNRe_2k-Vu3AXz2JrgCklDA3pd45YDHhV8cAcYXtYEaZNVzc0fniRnwjlrri3fqmyAu9niayQl8R4CIxE5irbXDvkLNh7zzUf5ipZtqps9PSRxrfAZQqnaKUC47G5SZsGrTcckQcdcVKRn240n25BZGvBaVkjgVE8DD112S4qDPgKPqojE5JEFZ6pOHvhCQSoUWVWzZXgH4wAY4qr5g"
        });
      }
    </script>
  </head>
  <body>
    <div id="timer-container" style="position:fixed;bottom:32px;right:32px;z-index:9999;">
      <div id="session-timer" style="background:#222;color:#fff;padding:12px 28px;border-radius:16px;box-shadow:0 2px 12px #0002;display:flex;flex-direction:column;align-items:center;min-width:110px;font-family:'FigtreeReg', Figtree, Arial,sans-serif;">
        <span id="timer-label" style="font-size:0.85em;color:#9acbd0;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;letter-spacing:0.5px;">Session: </span>
        <span id="timer-value" style="font-size:1.5em;font-family:'Courier New',monospace;font-weight:700;letter-spacing:1px;">{{ $interval }}:00</span>
      </div>
    </div>
    <div id="jaas-container" />
    <script>
    // --- Timer Logic for Professional ---
    const interval = {{ $interval }};
    let timerLabel = document.getElementById('timer-label');
    let timerValue = document.getElementById('timer-value');
    let sessionStart = new Date();

    function updateTimer() {
      let now = new Date();
      let end = new Date(sessionStart.getTime() + interval * 60 * 1000);
      let diff = end - now;
      if (diff < 0) diff = 0;
      let mins = String(Math.floor(diff / 60000)).padStart(2, '0');
      let secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
      timerValue.textContent = `${mins}:${secs}`;
    }

    setInterval(updateTimer, 1000);
    </script>
  </body>
</html> 
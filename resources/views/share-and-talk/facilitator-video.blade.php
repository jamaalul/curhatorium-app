<x-layout title="Facilitator Video Session">
    <x-slot:head>
        <script src='https://8x8.vc/vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/external_api.js' async></script>
        <style>html, body, #jaas-container { height: 100%; }</style>
        <script type="text/javascript">
          window.onload = () => {
            const api = new JitsiMeetExternalAPI("8x8.vc", {
              roomName: "vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/{{ $sessionId }}",
              parentNode: document.querySelector('#jaas-container'),
            });
          }
        </script>
    </x-slot:head>

    <div id="timer-container" style="position:fixed;bottom:32px;right:32px;z-index:9999;">
      <div id="session-timer" style="background:#222;color:#fff;padding:12px 28px;border-radius:16px;box-shadow:0 2px 12px #0002;display:flex;flex-direction:column;align-items:center;min-width:110px;font-family:'FigtreeReg', Figtree, Arial,sans-serif;">
        <span id="timer-label" style="font-size:0.85em;color:#9acbd0;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;letter-spacing:0.5px;">Session: </span>
        <span id="timer-value" style="font-size:1.5em;font-family:'Courier New',monospace;font-weight:700;letter-spacing:1px;">{{ $interval }}:00</span>
      </div>
    </div>
    <div id="dashboard-link" style="position:fixed;top:32px;right:32px;z-index:9999;">
      <a href="{{ route('professional.dashboard', ['professionalId' => $professionalId]) }}" 
         style="background:#222;color:#fff;padding:12px 16px;border-radius:8px;box-shadow:0 2px 12px #0002;text-decoration:none;font-family:'FigtreeReg', Figtree, Arial,sans-serif;font-size:0.9rem;margin-right:10px;">
        📊 Dashboard
      </a>
      <a href="{{ route('professional.login') }}" 
         style="background:#333;color:#fff;padding:12px 16px;border-radius:8px;box-shadow:0 2px 12px #0002;text-decoration:none;font-family:'FigtreeReg', Figtree, Arial,sans-serif;font-size:0.9rem;">
        🔐 Login
      </a>
    </div>
    <div id="jaas-container" />

    <x-slot:scripts>
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
    </x-slot:scripts>
</x-layout>
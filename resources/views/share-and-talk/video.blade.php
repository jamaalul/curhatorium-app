<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Curhatorium | Video Consultation</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/share-and-talk/video.css') }}">
</head>
<body>
    <div class="video-consultation-container" data-session-end="{{ now()->addMinutes($interval)->toIso8601String() }}" data-session-id="{{ $session_id }}">
        <!-- Header -->
        <div class="consultation-header">
            <div class="header-info">
                <h1>Video Consultation</h1>
                <p>with <strong>{{ $professional['name'] }}</strong> - {{ $professional['title'] }}</p>
            </div>
            <div class="session-timer" id="session-timer">
                <span class="timer-label">Session Time:</span>
                <span class="timer-value" id="timer-value">60:00</span>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="waiting-message" class="status-message waiting">
            <div class="status-icon">⏳</div>
            <div class="status-content">
                <h3>Waiting for Professional</h3>
                <p>Your professional is being notified. They will join the video call within 5 minutes.</p>
                <div class="countdown" id="waiting-countdown">05:00</div>
            </div>
        </div>

        <div id="cancelled-message" class="status-message cancelled" style="display: none;">
            <div class="status-icon">❌</div>
            <div class="status-content">
                <h3>Session Cancelled</h3>
                <p>The professional did not respond within the time limit. Your ticket will be refunded.</p>
                <button onclick="window.location.href='/dashboard'" class="btn-primary">Return to Dashboard</button>
            </div>
        </div>

        <!-- Video Call Interface -->
        <div id="video-interface" class="video-interface" style="display: none;">
            <div class="video-container">
                <div class="video-placeholder">
                    <div class="video-placeholder-content">
                        <div class="video-icon">📹</div>
                        <h3>Video Call Ready</h3>
                        <p>Click the button below to join the Google Meet session</p>
                        <a href="{{ $meet_link }}" target="_blank" class="btn-join-meet">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            Join Google Meet
                        </a>
                    </div>
                </div>
            </div>

            <div class="video-controls">
                <div class="control-group">
                    <button class="control-btn" id="toggle-mic">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                            <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                        </svg>
                        <span>Mute</span>
                    </button>
                    <button class="control-btn" id="toggle-camera">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12m-3.2 0a3.2 3.2 0 1 1 6.4 0a3.2 3.2 0 1 1 -6.4 0"/>
                            <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                        </svg>
                        <span>Camera</span>
                    </button>
                </div>
                <div class="control-group">
                    <button class="control-btn danger" id="end-call">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                        </svg>
                        <span>End Call</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Chat Sidebar -->
        <div class="chat-sidebar" id="chat-sidebar">
            <div class="chat-header">
                <h3>Session Chat</h3>
                <button class="toggle-chat" id="toggle-chat">−</button>
            </div>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            <form class="chat-input" id="chat-form">
                <input type="text" placeholder="Type a message..." id="chat-input-field">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script>
        const sessionId = '{{ $session_id }}';
        const meetLink = '{{ $meet_link }}';
        const sessionEnd = new Date(document.querySelector('.video-consultation-container').dataset.sessionEnd);
        let sessionStatus = 'waiting';
        let waitingCountdown = 300; // 5 minutes
        let waitingInterval = null;
        let timerInterval = null;

        // Timer functions
        function updateTimer() {
            const now = new Date();
            const diff = sessionEnd - now;
            
            if (diff <= 0) {
                document.getElementById('timer-value').textContent = '00:00';
                endSession();
                return;
            }
            
            const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
            const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            document.getElementById('timer-value').textContent = `${mins}:${secs}`;
        }

        function startWaitingCountdown() {
            const countdownEl = document.getElementById('waiting-countdown');
            waitingInterval = setInterval(() => {
                waitingCountdown--;
                const mins = String(Math.floor(waitingCountdown / 60)).padStart(2, '0');
                const secs = String(waitingCountdown % 60).padStart(2, '0');
                countdownEl.textContent = `${mins}:${secs}`;
                
                if (waitingCountdown <= 0) {
                    cancelSession();
                }
            }, 1000);
        }

        async function cancelSession() {
            clearInterval(waitingInterval);
            
            try {
                const response = await fetch(`/api/share-and-talk/cancel-session/${sessionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('waiting-message').style.display = 'none';
                    document.getElementById('cancelled-message').style.display = 'flex';
                    console.log('Session cancelled and ticket refunded successfully');
                } else {
                    console.error('Failed to cancel session:', data.message);
                    // Still show cancelled message even if API fails
                    document.getElementById('waiting-message').style.display = 'none';
                    document.getElementById('cancelled-message').style.display = 'flex';
                }
            } catch (error) {
                console.error('Error cancelling session:', error);
                // Still show cancelled message even if API fails
                document.getElementById('waiting-message').style.display = 'none';
                document.getElementById('cancelled-message').style.display = 'flex';
            }
        }

        async function endSession() {
            clearInterval(timerInterval);
            
            try {
                const response = await fetch(`/api/share-and-talk/end-session/${sessionId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    console.log('Session ended successfully');
                } else {
                    console.error('Failed to end session:', data.message);
                }
            } catch (error) {
                console.error('Error ending session:', error);
            }
            
            // Redirect to dashboard regardless of API success/failure
            window.location.href = '/dashboard';
        }

        // Session status polling
        async function checkSessionStatus() {
            try {
                const response = await fetch(`/api/share-and-talk/session-status/${sessionId}`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.status === 'active' && sessionStatus === 'waiting') {
                        sessionStatus = 'active';
                        document.getElementById('waiting-message').style.display = 'none';
                        document.getElementById('video-interface').style.display = 'block';
                        clearInterval(waitingInterval);
                        updateTimer();
                        timerInterval = setInterval(updateTimer, 1000);
                    }
                }
            } catch (error) {
                console.error('Error checking session status:', error);
            }
        }

        // Control button handlers
        document.getElementById('toggle-mic').addEventListener('click', function() {
            this.classList.toggle('active');
            const span = this.querySelector('span');
            span.textContent = this.classList.contains('active') ? 'Unmute' : 'Mute';
        });

        document.getElementById('toggle-camera').addEventListener('click', function() {
            this.classList.toggle('active');
            const span = this.querySelector('span');
            span.textContent = this.classList.contains('active') ? 'Show Camera' : 'Camera';
        });

        document.getElementById('end-call').addEventListener('click', function() {
            if (confirm('Are you sure you want to end the call?')) {
                endSession();
            }
        });

        document.getElementById('toggle-chat').addEventListener('click', function() {
            const sidebar = document.getElementById('chat-sidebar');
            sidebar.classList.toggle('collapsed');
            this.textContent = sidebar.classList.contains('collapsed') ? '+' : '−';
        });

        // Initialize
        startWaitingCountdown();
        setInterval(checkSessionStatus, 2000);
    </script>
</body>
</html> 
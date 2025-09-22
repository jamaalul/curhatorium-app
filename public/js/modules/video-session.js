/**
 * Video Session Module
 * Handles timer, polling, and session management for video sessions
 */

class VideoSession {
    constructor(sessionId) {
        this.sessionId = sessionId;
        this.polling = true;
        this.status = 'waiting';
        this.createdAt = null;
        this.fallbackStart = new Date();
        this.waitingExpired = false;
        
        // DOM elements
        this.timerLabel = document.getElementById('timer-label');
        this.timerValue = document.getElementById('timer-value');
        this.timerContainer = document.getElementById('timer-container');
        this.warningMessage = document.getElementById('warning-message');
        
        this.init();
    }

    init() {
        this.updateTimer();
        this.pollAndUpdate();
        setInterval(() => this.updateTimer(), 1000);
    }

    async fetchSessionStatus() {
        try {
            const res = await fetch(`/api/share-and-talk/session-status/${this.sessionId}`);
            if (!res.ok) return;
            
            const data = await res.json();
            this.status = data.status;
            
            if (!this.createdAt && data.created_at) {
                // Parse 'YYYY-MM-DD HH:mm:ss' as local time
                const dt = data.created_at.replace(' ', 'T');
                let parsed = Date.parse(dt);
                if (isNaN(parsed)) {
                    // Fallback: parse as local time
                    const parts = data.created_at.split(/[- :]/);
                    parsed = new Date(parts[0], parts[1]-1, parts[2], parts[3], parts[4], parts[5]).getTime();
                }
                this.createdAt = new Date(parsed);
            }
            
            if (this.status === 'active') {
                this.polling = false;
                this.waitingExpired = false;
                
                // Hide warning message when session is active
                if (this.warningMessage) {
                    this.warningMessage.style.display = 'none';
                }
                
                // Restore timer UI if it was replaced
                if (this.timerContainer.querySelector('button')) {
                    this.restoreTimerUI();
                }
            }
        } catch (e) {
            console.error('Error fetching session status:', e);
        }
    }

    restoreTimerUI() {
        this.timerContainer.innerHTML = `
            <div id="session-timer" style="background:#222;color:#fff;padding:12px 28px;border-radius:16px;box-shadow:0 2px 12px #0002;display:flex;flex-direction:column;align-items:center;min-width:110px;font-family:'FigtreeReg', Figtree, Arial,sans-serif;">
                <span id="timer-label" style="font-size:0.85em;color:#9acbd0;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;letter-spacing:0.5px;">Session: </span>
                <span id="timer-value" style="font-size:1.5em;font-family:'Courier New',monospace;font-weight:700;letter-spacing:1px;">60:00</span>
            </div>
        `;
        this.timerLabel = document.getElementById('timer-label');
        this.timerValue = document.getElementById('timer-value');
    }

    updateTimer() {
        const now = new Date();
        const startTime = this.createdAt || this.fallbackStart;
        
        if ((this.status === 'waiting' || this.status === 'pending') && !this.waitingExpired) {
            this.timerLabel.textContent = 'Waiting: ';
            const end = new Date(startTime.getTime() + 5 * 60 * 1000);
            let diff = end - now;
            
            if (diff <= 0) {
                diff = 0;
                this.waitingExpired = true;
                this.cancelSession();
                return;
            }
            
            const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
            const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            this.timerValue.textContent = `${mins}:${secs}`;
            
        } else if (this.status === 'active') {
            this.timerLabel.textContent = 'Session: ';
            const end = new Date(startTime.getTime() + 65 * 60 * 1000);
            let diff = end - now;
            if (diff < 0) diff = 0;
            
            const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
            const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            this.timerValue.textContent = `${mins}:${secs}`;
            
        } else if (this.waitingExpired) {
            // Already replaced with Back button
            return;
        } else {
            this.timerLabel.textContent = 'Loading...';
            this.timerValue.textContent = '--:--';
        }
    }

    async cancelSession() {
        try {
            await fetch(`/api/share-and-talk/cancel-session/${this.sessionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
        } catch (error) {
            console.error('Error cancelling session:', error);
        }
        
        // Replace timer with Back to Dashboard button
        this.timerContainer.innerHTML = `
            <div style="background:#222;color:#fff;padding:16px 24px;border-radius:16px;box-shadow:0 2px 12px #0002;min-width:200px;text-align:center;">
                <div style="font-size:0.9rem;margin-bottom:8px;color:#9acbd0;font-family:'FigtreeBold', Figtree, Arial,sans-serif;">Session Cancelled</div>
                <div style="font-size:0.8rem;margin-bottom:12px;color:#ccc;">Your ticket will be refunded</div>
                <button style="background:#9acbd0;color:#222;padding:8px 16px;border-radius:8px;border:none;cursor:pointer;font-size:0.9rem;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;transition:all 0.3s ease;" 
                        onmouseover="this.style.background='#8ecbcf'" 
                        onmouseout="this.style.background='#9acbd0'" 
                        onclick="window.location.href='/dashboard'">Back to Dashboard</button>
            </div>
        `;
    }

    async pollAndUpdate() {
        await this.fetchSessionStatus();
        this.updateTimer();
        
        if (this.polling && (this.status === 'waiting' || this.status === 'pending')) {
            setTimeout(() => this.pollAndUpdate(), 2000);
        }
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const sessionId = document.querySelector('meta[name="session-id"]')?.getAttribute('content');
    if (sessionId) {
        new VideoSession(sessionId);
    }
});

// Make available globally for debugging
window.VideoSession = VideoSession; 
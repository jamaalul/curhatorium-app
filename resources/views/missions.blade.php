<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Misssions of The Day | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/missions.css') }}">
    <style>
        .button-loading { opacity: 0.8; pointer-events: none; }
        .button-loading .spinner { 
            width: 16px; height: 16px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            display: inline-block; vertical-align: middle; margin-right: 8px;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>🎯 Missions of The Day</h1>
                <p>Selesaikan tantangan harian untuk meningkatkan kesejahteraan mental dan membangun kebiasaan sehat</p>
            </div>
        </div>



        <!-- Main Content -->
        <div class="missions-container">
            <div class="tabs">
                <button class="tab easy active" onclick="switchTab('easy')">Mudah</button>
                <button class="tab medium" onclick="switchTab('medium')">Sedang</button>
                <button class="tab hard" onclick="switchTab('hard')">Sulit</button>
            </div>

            @foreach(['easy', 'medium', 'hard'] as $difficulty)
                <div id="{{ $difficulty }}-content" class="tab-content{{ $difficulty == 'easy' ? ' active' : '' }}">
                <div class="difficulty-header">
                        <h2 class="difficulty-title {{ $difficulty }}">
                            @if($difficulty == 'easy') Misi Mudah
                            @elseif($difficulty == 'medium') Misi Sedang
                            @else Misi Sulit
                            @endif
                        </h2>
                        <p class="difficulty-subtitle">
                            @if($difficulty == 'easy') Kebiasaan harian sederhana untuk memulai perjalanan kesehatanmu
                            @elseif($difficulty == 'medium') Tantangan sedang untuk membangun kebiasaan sehat
                            @else Tugas menantang untuk pertumbuhan diri yang signifikan
                            @endif
                        </p>
                </div>

                    @php
                        $missionsList = $missions[$difficulty] ?? collect();
                        $completedCount = $missionsList->whereIn('id', $completedMissions)->count();
                        $totalCount = $missionsList->count();
                        $progress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
                        
                        // Get XP values based on user's membership type
                        $user = auth()->user();
                        $xpService = app(\App\Services\XpService::class);
                        $membershipType = $xpService->getUserMembershipType($user);
                        
                        $xpValues = [
                            'easy' => $membershipType === 'subscription' ? 6 : 6,
                            'medium' => $membershipType === 'subscription' ? 8 : 8,
                            'hard' => $membershipType === 'subscription' ? 16 : 16
                        ];
                        
                        $totalXp = $xpValues[$difficulty] * $totalCount;
                    @endphp

                <div class="stats">
                    <div class="stat-item">
                            <div class="stat-number" style="color: var(--{{ $difficulty == 'easy' ? 'success' : ($difficulty == 'medium' ? 'warning' : 'error') }});">
                                {{ $completedCount }}/{{ $totalCount }}
                    </div>
                        <div class="stat-label">Selesai</div>
                    </div>
                    <div class="stat-item">
                            <div class="stat-number" style="color: var(--{{ $difficulty == 'easy' ? 'success' : ($difficulty == 'medium' ? 'warning' : 'error') }});">
                                {{ $totalXp }}
                            </div>
                        <div class="stat-label">Total XP</div>
                    </div>
                </div>

                <div class="progress-bar">
                        <div class="progress-fill {{ $difficulty }}" style="width: {{ $progress }}%"></div>
                </div>

                <div class="missions-grid">
                        @foreach($missionsList as $mission)
                            @php $isCompleted = in_array($mission->id, $completedMissions); @endphp
                            <div class="mission-card {{ $difficulty }}{{ $isCompleted ? ' completed' : '' }}">
                        <div class="mission-header">
                            <div class="mission-info">
                                        <h3 class="mission-title">{{ $mission->title }}</h3>
                                        <p class="mission-description">{{ $mission->description }}</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                                    <div class="mission-points {{ $difficulty }}">
                                        {{ $xpValues[$difficulty] }} XP
                            </div>
                            <div class="completion-toggle">
                                        @if($isCompleted)
                                            <span class="badge badge-success">Selesai</span>
                                        @else
                                            <button class="complete-btn" data-mission-id="{{ $mission->id }}" data-mission-title="{{ $mission->title }}" data-difficulty="{{ $difficulty }}" onclick="openCompletionModal(this)">Selesaikan</button>
                                        @endif
                            </div>
                        </div>
                    </div>
                        @endforeach
                            </div>
                        </div>
            @endforeach
                        </div>
                    </div>

    <!-- Completion Modal -->
    <div id="completion-modal">
        <div class="completion-modal-content">
            <button type="button" class="completion-modal-close" onclick="closeCompletionModal()">&times;</button>
            <h3 id="modal-mission-title" class="completion-modal-title"></h3>
            <form id="completion-form" method="POST" class="completion-modal-form">
                @csrf
                <div class="completion-modal-form-group">
                    <label for="reflection" class="completion-modal-label">Bagaimana kamu menyelesaikan misi ini?</label>
                    <textarea name="reflection" id="reflection" rows="5" class="completion-modal-textarea reflection" required></textarea>
                </div>
                <div class="completion-modal-form-group">
                    <label for="feeling" class="completion-modal-label">Bagaimana perasaanmu setelahnya?</label>
                    <textarea name="feeling" id="feeling" rows="2" class="completion-modal-textarea feeling" required></textarea>
                </div>
                <button type="submit" class="completion-modal-submit" id="completion-submit-btn">
                    <span class="btn-content">Kirim</span>
                </button>
            </form>
        </div>
    </div>

    <!-- XP Notification -->
    @if(session('success'))
        @php
            $message = session('success');
            $xpAwarded = 0;
            if (strpos($message, '+') !== false && strpos($message, 'XP') !== false) {
                preg_match('/\+(\d+)\s*XP/', $message, $matches);
                $xpAwarded = $matches[1] ?? 0;
            }
        @endphp
        <x-xp-notification :xp-awarded="$xpAwarded" :message="$message" />
    @endif

    <script>
        function switchTab(difficulty) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            document.querySelector(`.tab.${difficulty}`).classList.add('active');
            document.getElementById(`${difficulty}-content`).classList.add('active');
        }

        // Modal logic
        let currentMissionId = null;
        function openCompletionModal(btn) {
            currentMissionId = btn.getAttribute('data-mission-id');
            const missionTitle = btn.getAttribute('data-mission-title');
            document.getElementById('modal-mission-title').textContent = missionTitle;
            document.getElementById('completion-modal').style.display = 'flex';
            document.getElementById('completion-form').setAttribute('action', `/missions-of-the-day/${currentMissionId}/complete`);
            document.getElementById('reflection').value = '';
            document.getElementById('feeling').value = '';
        }
        function closeCompletionModal() {
            document.getElementById('completion-modal').style.display = 'none';
        }
        // Close modal on outside click
        document.getElementById('completion-modal').addEventListener('click', function(e) {
            if (e.target === this) closeCompletionModal();
        });

        // Handle form submission
        document.getElementById('completion-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const missionId = currentMissionId;
            const submitBtn = document.getElementById('completion-submit-btn');
            const originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.classList.add('button-loading');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner"></span><span>Mengirim...</span>';
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async (response) => {
                if (!response.ok) {
                    const err = await response.json().catch(() => ({}));
                    throw new Error(err.message || 'Gagal menyelesaikan misi');
                }
                return response.json();
            })
            .then(data => {
                // Close modal
                closeCompletionModal();
                
                // Update UI without full reload
                const btn = document.querySelector(`.complete-btn[data-mission-id="${missionId}"]`);
                if (btn) {
                    const footer = btn.closest('.mission-footer');
                    if (footer) {
                        const toggle = footer.querySelector('.completion-toggle');
                        if (toggle) {
                            toggle.innerHTML = '<span class="badge badge-success">Selesai</span>';
                        }
                    }
                }
                const card = document.querySelector(`.mission-card .complete-btn[data-mission-id="${missionId}"]`);
                if (card) {
                    const missionCard = card.closest('.mission-card');
                    missionCard && missionCard.classList.add('completed');
                }

                // Simple toast
                const toast = document.createElement('div');
                toast.id = 'toast-success';
                toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:9999;background:#10b981;color:#fff;padding:12px 16px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.15)';
                toast.textContent = data.message || 'Misi selesai!';
                document.body.appendChild(toast);
                setTimeout(() => { toast.remove(); }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyelesaikan misi. Silakan coba lagi.');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.classList.remove('button-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml || '<span class="btn-content">Kirim</span>';
                }
            });
        });
    </script>
</body>
</html>

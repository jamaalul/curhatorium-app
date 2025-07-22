<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misssions of The Day | Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/missions.css') }}">
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
                        $totalPoints = $missionsList->sum('points');
                        $progress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
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
                                {{ $totalPoints }}
                            </div>
                        <div class="stat-label">Total Poin</div>
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
                                        {{ $mission->points }} poin
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
                <button type="submit" class="completion-modal-submit">Kirim</button>
            </form>
        </div>
    </div>

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
    </script>
</body>
</html>

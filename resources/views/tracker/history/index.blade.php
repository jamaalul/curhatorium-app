<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Mood - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        :root {
            --primary-color: #8ecbcf;
            --primary-light: #9acbd0;
            --primary-dark: #7ab8bd;
            --secondary-color: #ffcd2d;
            --secondary-light: #ffd84d;
            --secondary-dark: #f0c020;
            --text-primary: #333333;
            --text-secondary: #595959;
            --text-tertiary: #6b7280;
            --bg-primary: #f5f2eb;
            --bg-secondary: #f3f4f6;
            --bg-tertiary: #e5e7eb;
            --white: #ffffff;
            --success: #f59e0b;
            --error: #ef4444;
            --warning: #f59e0b;
            --info: #f59e0b;
            --border-radius-sm: 0.25rem;
            --border-radius: 0.5rem;
            --border-radius-lg: 0.75rem;
            --border-radius-xl: 1rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s ease;
            --container-width: 1000px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            padding-top: 70px;
        }

        .container {
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 1.5rem;
        }

        .page-header {
            text-align: center;
            padding: 3rem 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            margin-bottom: 3rem;
            border-radius: var(--border-radius-lg);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            pointer-events: none;
        }

        .page-header-content {
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.125rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .view-toggle {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 3rem;
            background: var(--white);
            padding: 0.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        .toggle-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .toggle-btn.active {
            background: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .toggle-btn:hover:not(.active) {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .history-container {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(142, 203, 207, 0.1);
            margin-bottom: 2rem;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .track-card {
            background: var(--bg-secondary);
            border-radius: var(--border-radius-xl);
            padding: 1.5rem;
            transition: var(--transition);
            border: 1px solid var(--bg-tertiary);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .track-card:hover {
            background: var(--white);
            border-color: var(--primary-light);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .track-card:active {
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .card-date {
            font-size: 0.875rem;
            color: var(--text-tertiary);
        }

        .card-mood {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mood-emoji {
            font-size: 1.75rem;
        }

        .mood-score {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .card-content {
            margin-bottom: 1rem;
        }

        .card-summary {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .card-stats {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            padding: 0.75rem;
            background: var(--white);
            border-radius: var(--border-radius);
        }

        .stat-icon {
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-tertiary);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--bg-tertiary);
        }

        .card-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .type-icon {
            font-size: 1rem;
        }

        .card-arrow {
            color: var(--text-tertiary);
            font-size: 1.25rem;
            transition: var(--transition);
        }

        .track-card:hover .card-arrow {
            color: var(--primary-color);
            transform: translateX(4px);
        }

        .daily-card {
            border-left: 4px solid var(--info);
        }

        .daily-card .card-type {
            color: var(--info);
        }

        .weekly-card {
            border-left: 4px solid var(--warning);
        }

        .weekly-card .card-type {
            color: var(--warning);
        }

        .monthly-card {
            border-left: 4px solid var(--success);
        }

        .monthly-card .card-type {
            color: var(--success);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .empty-state p {
            margin-bottom: 1.5rem;
        }

        .empty-state-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-lg);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .empty-state-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .pagination-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1.5rem;
            gap: 0.5rem;
        }

        .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--primary-light);
            background: var(--white);
            color: var(--primary-color);
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .pagination-btn:hover:not(:disabled) {
            background: var(--primary-light);
            color: var(--white);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: var(--bg-tertiary);
            color: var(--text-tertiary);
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .page-header p {
                font-size: 1rem;
            }

            .view-toggle {
                width: 100%;
                justify-content: center;
            }

            .toggle-btn {
                flex: 1;
                text-align: center;
            }

            .history-container {
                padding: 1.5rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .card-stats {
                flex-direction: column;
                gap: 0.75rem;
            }

            .stat-item {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .container {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                padding: 2rem 0;
            }

            .page-header h1 {
                font-size: 1.75rem;
            }

            .track-card {
                padding: 1rem;
            }

            .mood-emoji {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Riwayat Mood</h1>
                <p>Jelajahi catatan pelacakan mood Anda dalam entri harian, ringkasan mingguan, dan gambaran bulanan.</p>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="toggle-btn active" data-view="daily">Harian</button>
            @if(Auth::user()->hasActiveInnerPeaceMembership())
                <button class="toggle-btn" data-view="weekly">Mingguan</button>
                <button class="toggle-btn" data-view="monthly">Bulanan</button>
            @endif
        </div>

        <!-- History Container -->
        <div class="history-container" id="historyContainer">
            <!-- Konten akan diisi oleh JavaScript -->
        </div>
    </div>

    <script>
        // Variabel global
        let currentView = 'daily';

        // Pemetaan emoji mood (sama seperti tracker/result.blade.php)
        function getMoodEmoji(score) {
            const moods = {
                1: '😢',
                2: '😞',
                3: '😔',
                4: '😐',
                5: '🙂',
                6: '😊',
                7: '😄',
                8: '😁',
                9: '🤩',
                10: '🥳',
            };
            return moods[score] || '';
        }

        // Setup event listener
        function setupEventListeners() {
            // Tombol toggle tampilan
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentView = this.dataset.view;
                    updateView();
                });
            });
        }

        // State untuk data dan paginasi
        let dailyTracks = [];
        let weeklyTracks = [];
        let monthlyTracks = [];
        let dailyPage = 1;
        let weeklyPage = 1;
        let monthlyPage = 1;
        let dailyLastPage = 1;
        let weeklyLastPage = 1;
        let monthlyLastPage = 1;

        // Ambil data dari API
        async function fetchDailyTracks(page = 1) {
            const res = await fetch(`/api/tracker/stats?page=${page}`);
            const data = await res.json();
            dailyTracks = data.data;
            dailyPage = data.current_page;
            dailyLastPage = data.last_page;
        }
        async function fetchWeeklyTracks(page = 1) {
            try {
                const res = await fetch(`/api/tracker/weekly-stats?page=${page}`);
                if (res.status === 403) {
                    // User doesn't have Inner Peace membership
                    weeklyTracks = [];
                    weeklyPage = 1;
                    weeklyLastPage = 1;
                    return;
                }
                const data = await res.json();
                weeklyTracks = data.data;
                weeklyPage = data.current_page;
                weeklyLastPage = data.last_page;
            } catch (error) {
                weeklyTracks = [];
                weeklyPage = 1;
                weeklyLastPage = 1;
            }
        }
        async function fetchMonthlyTracks(page = 1) {
            try {
                const res = await fetch(`/api/tracker/monthly-stats?page=${page}`);
                if (res.status === 403) {
                    // User doesn't have Inner Peace membership
                    monthlyTracks = [];
                    monthlyPage = 1;
                    monthlyLastPage = 1;
                    return;
                }
                const data = await res.json();
                monthlyTracks = data.data;
                monthlyPage = data.current_page;
                monthlyLastPage = data.last_page;
            } catch (error) {
                monthlyTracks = [];
                monthlyPage = 1;
                monthlyLastPage = 1;
            }
        }

        // Inisialisasi halaman
        document.addEventListener('DOMContentLoaded', async function() {
            setupEventListeners();
            await fetchDailyTracks();
            await fetchWeeklyTracks();
            await fetchMonthlyTracks();
            updateView();
        });

        // Update tampilan berdasarkan pilihan saat ini
        function updateView() {
            const historyContainer = document.getElementById('historyContainer');
            switch (currentView) {
                case 'daily':
                    historyContainer.innerHTML = renderDailyCards();
                    break;
                case 'weekly':
                    historyContainer.innerHTML = renderWeeklyCards();
                    break;
                case 'monthly':
                    historyContainer.innerHTML = renderMonthlyCards();
                    break;
            }
            addCardClickListeners();
        }

        // Render kartu harian
        function renderDailyCards() {
            if (dailyTracks.length === 0) {
                return `
                    <div class="empty-state">
                        <div class="empty-state-icon">📅</div>
                        <h3>Tidak Ada Catatan Harian</h3>
                        <p>Anda belum pernah melacak mood harian.</p>
                        <a href="mood-tracker.html" class="empty-state-btn">Lacak Mood Anda</a>
                    </div>
                `;
            }
            return `
                <div class="cards-grid">
                    ${dailyTracks.map(track => {
                        const moods = {
                            1: {emoji: '😢', label: 'Sangat sedih'},
                            2: {emoji: '😞', label: 'Sedih'},
                            3: {emoji: '😔', label: 'Murung'},
                            4: {emoji: '😐', label: 'Biasa'},
                            5: {emoji: '🙂', label: 'Netral'},
                            6: {emoji: '😊', label: 'Positif'},
                            7: {emoji: '😄', label: 'Senang'},
                            8: {emoji: '😁', label: 'Sangat senang'},
                            9: {emoji: '🤩', label: 'Bahagia'},
                            10: {emoji: '🥳', label: 'Gembira'},
                        };
                        const mood = moods[track.mood] || {emoji: '', label: ''};
                        return `
                        <div class="track-card daily-card" data-type="daily" data-id="${track.id}">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">${formatDate(track.created_at)}</div>
                                    <div class="card-date">${mood.label}</div>
                                </div>
                                <div class="card-mood">
                                    <span class="mood-emoji">${mood.emoji}</span>
                                    <span class="mood-score">${track.mood}/10</span>
                                </div>
                            </div>
                            <div class="card-content"></div>
                            <div class="card-footer">
                                <div class="card-type">
                                    <span class="type-icon"></span>
                                    <span>Catatan Harian</span>
                                </div>
                                <div class="card-arrow">→</div>
                            </div>
                        </div>
                        `;
                    }).join('')}
                </div>
                ${renderPagination('daily', dailyPage, dailyLastPage)}
            `;
        }

        // Render kartu mingguan
        function renderWeeklyCards() {
            if (weeklyTracks.length === 0) {
                return `
                    <div class="empty-state">
                        <div class="empty-state-icon">📊</div>
                        <h3>Fitur Mingguan Hanya untuk Inner Peace</h3>
                        <p>Upgrade ke membership Inner Peace untuk mengakses laporan mingguan dan analisis mendalam.</p>
                        <a href="/membership" class="empty-state-btn">Lihat Membership</a>
                    </div>
                `;
            }
            return `
                <div class="cards-grid">
                    ${weeklyTracks.map(track => {
                        const roundedMood = Math.round(track.avg_mood);
                        const emoji = getMoodEmoji(roundedMood);
                        return `
                        <div class="track-card weekly-card" data-type="weekly" data-id="${track.id}">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">${formatWeekRange(track.week_start, track.week_end)}</div>
                                    <div class="card-date">${track.total_entries} entri</div>
                                </div>
                                <div class="card-mood">
                                    <span class="mood-emoji">${emoji}</span>
                                    <span class="mood-score">${track.avg_mood.toFixed(1)}/10</span>
                                </div>
                            </div>
                            <div class="card-content"></div>
                            <div class="card-footer">
                                <div class="card-type">
                                    <span class="type-icon"></span>
                                    <span>Ringkasan Mingguan</span>
                                </div>
                                <div class="card-arrow">→</div>
                            </div>
                        </div>
                        `;
                    }).join('')}
                </div>
                ${renderPagination('weekly', weeklyPage, weeklyLastPage)}
            `;
        }

        // Render kartu bulanan
        function renderMonthlyCards() {
            if (monthlyTracks.length === 0) {
                return `
                    <div class="empty-state">
                        <div class="empty-state-icon">📈</div>
                        <h3>Fitur Bulanan Hanya untuk Inner Peace</h3>
                        <p>Upgrade ke membership Inner Peace untuk mengakses laporan bulanan dan analisis mendalam.</p>
                        <a href="/membership" class="empty-state-btn">Lihat Membership</a>
                    </div>
                `;
            }
            return `
                <div class="cards-grid">
                    ${monthlyTracks.map(track => {
                        const roundedMood = Math.round(track.avg_mood);
                        const emoji = getMoodEmoji(roundedMood);
                        return `
                        <div class="track-card monthly-card" data-type="monthly" data-id="${track.id}">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">${formatMonth(track.month)}</div>
                                    <div class="card-date">${track.total_entries} entri</div>
                                </div>
                                <div class="card-mood">
                                    <span class="mood-emoji">${emoji}</span>
                                    <span class="mood-score">${track.avg_mood.toFixed(1)}/10</span>
                                </div>
                            </div>
                            <div class="card-content"></div>
                            <div class="card-footer">
                                <div class="card-type">
                                    <span class="type-icon"></span>
                                    <span>Gambaran Bulanan</span>
                                </div>
                                <div class="card-arrow">→</div>
                            </div>
                        </div>
                        `;
                    }).join('')}
                </div>
                ${renderPagination('monthly', monthlyPage, monthlyLastPage)}
            `;
        }

        // Render paginasi
        function renderPagination(type, currentPage, lastPage) {
            if (lastPage <= 1) return '';
            let prevDisabled = currentPage <= 1 ? 'disabled' : '';
            let nextDisabled = currentPage >= lastPage ? 'disabled' : '';
            return `
                <div class="pagination-controls" style="text-align:center;margin-top:1.5rem;">
                    <button class="pagination-btn" data-type="${type}" data-action="prev" ${prevDisabled}>Sebelumnya</button>
                    <span style="margin:0 1rem;">Halaman ${currentPage} dari ${lastPage}</span>
                    <button class="pagination-btn" data-type="${type}" data-action="next" ${nextDisabled}>Berikutnya</button>
                </div>
            `;
        }

        // Event listener paginasi
        document.addEventListener('click', async function(e) {
            if (e.target.classList.contains('pagination-btn')) {
                const type = e.target.getAttribute('data-type');
                const action = e.target.getAttribute('data-action');
                if (type === 'daily') {
                    if (action === 'prev' && dailyPage > 1) {
                        await fetchDailyTracks(dailyPage - 1);
                        updateView();
                    } else if (action === 'next' && dailyPage < dailyLastPage) {
                        await fetchDailyTracks(dailyPage + 1);
                        updateView();
                    }
                } else if (type === 'weekly') {
                    if (action === 'prev' && weeklyPage > 1) {
                        await fetchWeeklyTracks(weeklyPage - 1);
                        updateView();
                    } else if (action === 'next' && weeklyPage < weeklyLastPage) {
                        await fetchWeeklyTracks(weeklyPage + 1);
                        updateView();
                    }
                } else if (type === 'monthly') {
                    if (action === 'prev' && monthlyPage > 1) {
                        await fetchMonthlyTracks(monthlyPage - 1);
                        updateView();
                    } else if (action === 'next' && monthlyPage < monthlyLastPage) {
                        await fetchMonthlyTracks(monthlyPage + 1);
                        updateView();
                    }
                }
            }
        });

        // Tambahkan event click ke kartu
        function addCardClickListeners() {
            document.querySelectorAll('.track-card').forEach(card => {
                card.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const id = this.dataset.id;
                    
                    // Redirect ke halaman detail stat
                    if (type === 'daily') {
                        window.location.href = `/tracker/stat/${id}`;
                    } else if (type === 'weekly') {
                        window.location.href = `/tracker/weekly-stat/${id}`;
                    } else if (type === 'monthly') {
                        window.location.href = `/tracker/monthly-stat/${id}`;
                    }
                });
            });
        }

        // Fungsi bantu
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        }

        function formatWeekRange(start, end) {
            const startDate = new Date(start);
            const endDate = new Date(end);

            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };

            const formattedStart = startDate.toLocaleDateString('id-ID', options);
            const formattedEnd = endDate.toLocaleDateString('id-ID', options);

            return `${formattedStart} - ${formattedEnd}`;
        }

        function formatMonth(monthString) {
            // Pastikan ada hari agar bisa dibuat objek Date
            const date = new Date(`${monthString}-01`);
            
            return date.toLocaleDateString('id-ID', {
                month: 'long',
                year: 'numeric',
            });
        }
    </script>
</body>
</html>

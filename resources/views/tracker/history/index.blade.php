<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood History - Curhatorium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            padding-top: 70px;
        }

        /* Navbar styles */
        nav {
            width: 100%;
            height: 70px;
            position: fixed;
            top: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            z-index: 1000;
            justify-content: space-between;
            box-shadow: var(--shadow);
            border-bottom: 1px solid rgba(142, 203, 207, 0.1);
        }

        nav #logo-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        nav h1 {
            color: var(--text-secondary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        nav #mini-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Container */
        .container {
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Header */
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

        /* View Toggle */
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

        /* History Container */
        .history-container {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(142, 203, 207, 0.1);
            margin-bottom: 2rem;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        /* Track Card */
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

        /* Card Header */
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

        /* Card Content */
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

        /* Card Footer */
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

        /* Daily Card Specific */
        .daily-card {
            border-left: 4px solid var(--info);
        }

        .daily-card .card-type {
            color: var(--info);
        }

        /* Weekly Card Specific */
        .weekly-card {
            border-left: 4px solid var(--warning);
        }

        .weekly-card .card-type {
            color: var(--warning);
        }

        /* Monthly Card Specific */
        .monthly-card {
            border-left: 4px solid var(--success);
        }

        .monthly-card .card-type {
            color: var(--success);
        }

        /* Empty State */
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

        /* Responsive Design */
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
    <nav>
        <div id="logo-box">
            <img src="https://via.placeholder.com/40/8ecbcf/ffffff?text=C" alt="mini_logo" id="mini-logo">
            <h1>curhatorium</h1>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Mood History</h1>
                <p>Browse your mood tracking records across daily entries, weekly summaries, and monthly overviews.</p>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="toggle-btn active" data-view="daily">📅 Daily</button>
            <button class="toggle-btn" data-view="weekly">📊 Weekly</button>
            <button class="toggle-btn" data-view="monthly">📈 Monthly</button>
        </div>

        <!-- History Container -->
        <div class="history-container" id="historyContainer">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>

    <script>
        // Global variables
        let currentView = 'daily';

        // Mock data for demonstration
        const mockDailyTracks = [
            {
                id: 1,
                date: '2024-01-18',
                mood_score: 8,
                mood_emoji: '😁',
                summary: 'Great workout session, feeling energized and accomplished'
            },
            {
                id: 2,
                date: '2024-01-17',
                mood_score: 6,
                mood_emoji: '😊',
                summary: 'Productive day at work, completed important tasks'
            },
            {
                id: 3,
                date: '2024-01-16',
                mood_score: 5,
                mood_emoji: '🙂',
            },
            {
                id: 4,
                date: '2024-01-15',
                mood_score: 7,
                mood_emoji: '😄',
            },
            {
                id: 5,
                date: '2024-01-14',
                mood_score: 4,
                mood_emoji: '😐',
            }
        ];

        const mockWeeklyTracks = [
            {
                id: 1,
                week_start: '2024-01-15',
                week_end: '2024-01-21',
                avg_mood: 6.4,
                mood_emoji: '😊',
                total_entries: 5,
            },
            {
                id: 2,
                week_start: '2024-01-08',
                week_end: '2024-01-14',
                avg_mood: 7.1,
                mood_emoji: '😄',
                total_entries: 6,
            },
            {
                id: 3,
                week_start: '2024-01-01',
                week_end: '2024-01-07',
                avg_mood: 5.8,
                mood_emoji: '🙂',
                total_entries: 4,
            }
        ];

        const mockMonthlyTracks = [
            {
                id: 1,
                month: 'January 2024',
                avg_mood: 6.8,
                mood_emoji: '😊',
                total_entries: 18,
            },
            {
                id: 2,
                month: 'December 2023',
                avg_mood: 7.2,
                mood_emoji: '😄',
                total_entries: 22,
            },
            {
                id: 3,
                month: 'November 2023',
                avg_mood: 6.1,
                mood_emoji: '😊',
                total_entries: 20,
            }
        ];

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            updateView();
        });

        // Setup event listeners
        function setupEventListeners() {
            // View toggle buttons
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentView = this.dataset.view;
                    updateView();
                });
            });
        }

        // Update view based on current selection
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

            // Add click event listeners to cards
            addCardClickListeners();
        }

        // Render daily cards
        function renderDailyCards() {
            if (mockDailyTracks.length === 0) {
                return `
                    <div class="empty-state">
                        <div class="empty-state-icon">📅</div>
                        <h3>No Daily Records</h3>
                        <p>You haven't tracked any daily moods yet.</p>
                        <a href="mood-tracker.html" class="empty-state-btn">Track Your Mood</a>
                    </div>
                `;
            }

            return `
                <div class="cards-grid">
                    ${mockDailyTracks.map(track => `
                        <div class="track-card daily-card" data-type="daily" data-id="${track.id}">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">${formatDate(track.date)}</div>
                                    <div class="card-date">${track.date}</div>
                                </div>
                                <div class="card-mood">
                                    <span class="mood-emoji">${track.mood_emoji}</span>
                                    <span class="mood-score">${track.mood_score}/10</span>
                                </div>
                            </div>
                            <div class="card-content">
                            </div>
                            <div class="card-footer">
                                <div class="card-type">
                                    <span class="type-icon">📅</span>
                                    <span>Daily Track</span>
                                </div>
                                <div class="card-arrow">→</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Render weekly cards
        function renderWeeklyCards() {
            if (mockWeeklyTracks.length === 0) {
                return `
                    <div class="empty-state">
                        <div class="empty-state-icon">📊</div>
                        <h3>No Weekly Records</h3>
                        <p>No weekly summaries available yet.</p>
                        <a href="mood-tracker.html" class="empty-state-btn">Track Your Mood</a>
                    </div>
                `;
            }

            return `
                <div class="cards-grid">
                    ${mockWeeklyTracks.map(track => `
                        <div class="track-card weekly-card" data-type="weekly" data-id="${track.id}">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">${formatWeekRange(track.week_start, track.week_end)}</div>
                                    <div class="card-date">${track.total_entries} entries</div>
                                </div>
                                <div class="card-mood">
                                    <span class="mood-emoji">${track.mood_emoji}</span>
                                    <span class="mood-score">${track.avg_mood.toFixed(1)}/10</span>
                                </div>
                            </div>
                            <div class="card-content">
                                
                            </div>
                            <div class="card-footer">
                                <div class="card-type">
                                    <span class="type-icon">📊</span>
                                    <span>Weekly Summary</span>
                                </div>
                                <div class="card-arrow">→</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Render monthly cards
        function renderMonthlyCards() {
            if (mockMonthlyTracks.length === 0) {
                return `
                    <div class="empty-state">
                        <div class="empty-state-icon">📈</div>
                        <h3>No Monthly Records</h3>
                        <p>No monthly summaries available yet.</p>
                        <a href="mood-tracker.html" class="empty-state-btn">Track Your Mood</a>
                    </div>
                `;
            }

            return `
                <div class="cards-grid">
                    ${mockMonthlyTracks.map(track => `
                        <div class="track-card monthly-card" data-type="monthly" data-id="${track.id}">
                            <div class="card-header">
                                <div>
                                    <div class="card-title">${track.month}</div>
                                    <div class="card-date">${track.total_entries} entries</div>
                                </div>
                                <div class="card-mood">
                                    <span class="mood-emoji">${track.mood_emoji}</span>
                                    <span class="mood-score">${track.avg_mood.toFixed(1)}/10</span>
                                </div>
                            </div>
                            <div class="card-content">
                                
                            </div>
                            <div class="card-footer">
                                <div class="card-type">
                                    <span class="type-icon">📈</span>
                                    <span>Monthly Overview</span>
                                </div>
                                <div class="card-arrow">→</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Add click event listeners to cards
        function addCardClickListeners() {
            document.querySelectorAll('.track-card').forEach(card => {
                card.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const id = this.dataset.id;
                    
                    // For now, just log the click - later this will navigate to detail page
                    console.log(`Clicked ${type} track with ID: ${id}`);
                    
                    // Future implementation:
                    // window.location.href = `${type}-detail.html?id=${id}`;
                });
            });
        }

        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date(today.getTime() - 24 * 60 * 60 * 1000);
            
            if (date.toDateString() === today.toDateString()) {
                return 'Today';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            } else {
                return date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }
        }

        function formatWeekRange(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            const startMonth = start.toLocaleDateString('en-US', { month: 'short' });
            const endMonth = end.toLocaleDateString('en-US', { month: 'short' });
            
            if (startMonth === endMonth) {
                return `${startMonth} ${start.getDate()}-${end.getDate()}`;
            } else {
                return `${startMonth} ${start.getDate()} - ${endMonth} ${end.getDate()}`;
            }
        }
    </script>
</body>
</html>

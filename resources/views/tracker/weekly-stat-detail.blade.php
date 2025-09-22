<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Mingguan - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker/result.css') }}">
    <style>
        .weekly-summary {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .summary-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #222;
        }
        
        .summary-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .summary-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        
        .metric-card {
            text-align: center;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #48A6A6 !important;
            margin-bottom: 4px;
        }
        
        .metric-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .daily-entries {
            margin-top: 32px;
        }
        
        .entries-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
        }
        
        .entries-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #222;
        }
        
        .entry-item {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .entry-item:hover {
            transform: translateY(-2px);
        }
        
        .entry-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .entry-date {
            font-weight: 500;
            color: #222;
        }
        
        .entry-mood {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .entry-activity {
            color: #666;
            font-size: 0.9rem;
        }
        
        .empty-entries {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .empty-entries-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }
        
        .weekly-chart {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .chart-header {
            margin-bottom: 20px;
        }
        
        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 4px;
        }
        
        .chart-subtitle {
            color: #666;
            font-size: 0.9rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        /* Analysis Sections */
        .analysis-section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .analysis-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .mood-rating-card, .mood-range-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #48A6A6;
        }
        
        .mood-rating-card h3, .mood-range-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 15px;
        }
        
        .rating-score {
            font-size: 2.5rem;
            font-weight: 700;
            color: #48A6A6;
            margin-bottom: 10px;
        }
        
        .rating-description {
            color: #666;
            line-height: 1.6;
        }
        
        .range-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #48A6A6;
            margin-bottom: 8px;
        }
        
        .range-description {
            color: #666;
            line-height: 1.5;
        }
        
        /* Desktop/Mobile Display Classes */
        .desktop-only {
            display: block;
        }
        
        .mobile-only {
            display: none !important;
        }
        
        /* Goal Table (Desktop) */
        .goal-table {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .goal-table table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        
        .goal-table th {
            background: #48A6A6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .goal-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            vertical-align: top;
            line-height: 1.5;
        }
        
        .goal-table tr:last-child td {
            border-bottom: none;
        }
        
        .goal-table .user-row {
            background: linear-gradient(135deg, #48A6A6 0%, #3a8a8a 100%);
            color: white;
        }
        
        .goal-table .user-row td {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .goal-table .user-row:last-child td {
            border-bottom: none;
        }
        
        /* Goal Cards (Mobile) */
        .goal-cards {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .goal-main-card {
            background: linear-gradient(135deg, #48A6A6 0%, #3a8a8a 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .goal-info {
            display: grid;
            gap: 16px;
        }
        
        .goal-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .goal-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        
        .goal-value {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
        }
        
        .goal-theory-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #48A6A6;
        }
        
        .theory-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .theory-item:last-child {
            margin-bottom: 0;
        }
        
        .theory-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #48A6A6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .theory-value {
            font-size: 0.95rem;
            color: #222;
            line-height: 1.5;
        }
        
        /* Activity Analysis */
        .activity-analysis-content {
            display: grid;
            gap: 20px;
        }
        
        .dominant-activity-card, .varied-activities-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #FFCD2D;
        }
        
        .dominant-activity-card h3, .varied-activities-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 15px;
        }
        
        .activity-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #48A6A6;
            margin-bottom: 10px;
        }
        
        .activity-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .stat-item {
            background: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #666;
            border: 1px solid #e0e0e0;
        }
        
        .activity-impact {
            background: white;
            padding: 15px;
            border-radius: 6px;
            color: #666;
            line-height: 1.6;
            border-left: 3px solid #FFCD2D;
        }
        
        .varied-activities {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .best-activity, .worst-activity {
            background: white;
            padding: 15px;
            border-radius: 6px;
        }
        
        .best-activity h4, .worst-activity h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .best-activity h4 {
            color: #10b981;
        }
        
        .worst-activity h4 {
            color: #ef4444;
        }
        
        /* Productivity Analysis */
        .productivity-analysis-content {
            display: grid;
            gap: 20px;
        }
        
        .productivity-overview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #10b981;
        }
        
        .productivity-overview h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 15px;
        }
        
        .productivity-score {
            font-size: 2.5rem;
            font-weight: 700;
            color: #10b981;
            margin-bottom: 10px;
        }
        
        .productivity-description {
            color: #666;
            font-size: 1rem;
        }
        
        .productivity-days {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .most-productive, .least-productive {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .most-productive {
            border-left: 4px solid #10b981;
        }
        
        .least-productive {
            border-left: 4px solid #ef4444;
        }
        
        .most-productive h4, .least-productive h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .most-productive h4 {
            color: #10b981;
        }
        
        .least-productive h4 {
            color: #ef4444;
        }
        
        .day-info {
            background: white;
            padding: 15px;
            border-radius: 6px;
        }
        
        .day-date {
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
        }
        
        .day-score, .day-mood {
            color: #666;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .day-activities {
            color: #666;
            font-size: 0.85rem;
            line-height: 1.4;
            margin-top: 8px;
        }
        
        /* Best Mood Activity Analysis */
        .best-mood-activity-content {
            display: grid;
            gap: 20px;
        }
        
        .best-mood-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #FFD700;
        }
        
        .best-mood-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .best-mood-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #222;
            margin: 0;
        }
        
        .best-mood-score {
            font-size: 2rem;
            font-weight: 700;
            color: #FFD700;
        }
        
        .best-mood-details {
            background: white;
            padding: 15px;
            border-radius: 6px;
        }
        
        .activity-info {
            margin-bottom: 12px;
        }
        
        .activity-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #48A6A6;
            margin-bottom: 5px;
        }
        
        .activity-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .activity-metrics {
            display: flex;
            gap: 20px;
            margin-bottom: 12px;
        }
        
        .metric {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .metric-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .metric-value {
            font-weight: 600;
            color: #222;
        }
        
        .activity-explanation {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }
            
            .weekly-summary,
            .analysis-section,
            .weekly-chart,
            .ai-analysis {
                padding: 16px;
                margin-bottom: 16px;
            }
            
            .summary-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .summary-metrics {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            
            .metric-card {
                padding: 12px;
            }
            
            .metric-value {
                font-size: 1.5rem;
            }
            
            .analysis-content {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .varied-activities {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .productivity-days {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .activity-stats {
                flex-direction: column;
                gap: 8px;
            }
            
            .activity-metrics {
                flex-direction: column;
                gap: 8px;
            }
            
            .best-mood-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .best-mood-score {
                font-size: 1.5rem;
            }
            
            .entries-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .entry-item {
                padding: 12px;
            }
            
            .entry-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .section-title {
                font-size: 1.25rem;
                margin-bottom: 16px;
            }
            
            .chart-container {
                height: 250px;
            }
            
            .daily-entries {
                margin-top: 24px;
            }
            
            .entries-title {
                font-size: 1.1rem;
                margin-bottom: 12px;
            }
            
            /* Show mobile cards, hide desktop table */
            .desktop-only {
                display: none !important;
            }
            
            .mobile-only {
                display: block !important;
            }
            
            /* Goal cards responsive */
            .goal-cards {
                gap: 20px;
                margin-top: 16px;
            }
            
            .goal-main-card,
            .goal-theory-card {
                padding: 16px;
                margin-bottom: 8px;
            }
            
            .goal-info {
                gap: 12px;
            }
            
            .theory-item {
                gap: 6px;
                margin-bottom: 12px;
            }
            
            .goal-label,
            .theory-label {
                font-size: 0.7rem;
            }
            
            .goal-value,
            .theory-value {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 12px;
            }
            
            .weekly-summary,
            .analysis-section,
            .weekly-chart,
            .ai-analysis {
                padding: 12px;
                margin-bottom: 12px;
            }
            
            .summary-metrics {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .metric-card {
                padding: 10px;
            }
            
            .metric-value {
                font-size: 1.25rem;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            .chart-container {
                height: 200px;
            }
            
            .daily-entries {
                margin-top: 20px;
            }
            
            .entries-title {
                font-size: 1rem;
                margin-bottom: 10px;
            }
            
            .entry-item {
                padding: 10px;
            }
            
            .mood-display {
                font-size: 2.5rem;
            }
            
            .goal-main-card,
            .goal-theory-card {
                padding: 12px;
                margin-bottom: 6px;
            }
            
            .goal-info {
                gap: 10px;
            }
            
            .theory-item {
                gap: 4px;
                margin-bottom: 10px;
            }
            
            .goal-label,
            .theory-label {
                font-size: 0.65rem;
            }
            
            .goal-value,
            .theory-value {
                font-size: 0.85rem;
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
                <h1>Ringkasan Mingguan</h1>
                <p>Berikut ringkasan tracking mental kamu untuk minggu ini.</p>
                <div class="date-display">
                    {{ \Carbon\Carbon::parse($weeklyStat->week_start)->format('j M Y') }} - 
                    {{ \Carbon\Carbon::parse($weeklyStat->week_end)->format('j M Y') }}
                </div>
            </div>
        </div>

        <!-- Weekly Summary Card -->
        <div class="weekly-summary">
            <div class="summary-header">
                <div>
                    <div class="summary-title">Ringkasan Mingguan</div>
                    <div class="summary-date">
                        {{ \Carbon\Carbon::parse($weeklyStat->week_start)->translatedFormat('l, j F Y') }} - 
                        {{ \Carbon\Carbon::parse($weeklyStat->week_end)->translatedFormat('l, j F Y') }}
                    </div>
                </div>
                <div class="mood-display">
                    @php
                        $moods = [
                            1 => ['emoji' => '😢', 'label' => 'Sangat sedih'],
                            2 => ['emoji' => '😞', 'label' => 'Sedih'],
                            3 => ['emoji' => '😔', 'label' => 'Murung'],
                            4 => ['emoji' => '😐', 'label' => 'Biasa'],
                            5 => ['emoji' => '🙂', 'label' => 'Netral'],
                            6 => ['emoji' => '😊', 'label' => 'Positif'],
                            7 => ['emoji' => '😄', 'label' => 'Senang'],
                            8 => ['emoji' => '😁', 'label' => 'Sangat senang'],
                            9 => ['emoji' => '🤩', 'label' => 'Bahagia'],
                            10 => ['emoji' => '🥳', 'label' => 'Gembira'],
                        ];
                        $avgMood = round($weeklyStat->avg_mood);
                        $mood = $moods[$avgMood] ?? ['emoji' => '😐', 'label' => 'Netral'];
                    @endphp
                    {{ $mood['emoji'] }}
                </div>
            </div>

            <div class="summary-metrics">
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($weeklyStat->avg_mood, 1) }}</div>
                    <div class="metric-label">Rata-rata Mood</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($weeklyStat->avg_productivity, 1) }}</div>
                    <div class="metric-label">Rata-rata Produktivitas</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $weeklyStat->total_entries }}</div>
                    <div class="metric-label">Total Entri</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($weeklyStat->best_mood, 1) }}</div>
                    <div class="metric-label">Mood Terbaik</div>
                </div>
            </div>
        </div>

        <!-- Weekly Chart Section -->
        <div class="weekly-chart">
            <div class="chart-header">
                <h2 class="chart-title">Grafik Mingguan</h2>
                <p class="chart-subtitle">Perkembangan mood dan produktivitas selama minggu ini</p>
            </div>
            <div class="chart-container">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

        @if($analysis)
        <!-- Mood Rating Analysis -->
        <div class="analysis-section">
            <h2 class="section-title">📊 Analisis Mood Rating</h2>
            <div class="analysis-content">
                <div class="mood-rating-card">
                    <h3>Rata-rata Mood Mingguan</h3>
                    <div class="mood-rating-display">
                        <div class="rating-score">{{ number_format($analysis['mood_rating']['average'], 1) }}</div>
                        <div class="rating-description">
                            Rata-rata skor moodmu selama minggu ini adalah {{ number_format($analysis['mood_rating']['average'], 1) }} 
                            menunjukkan minggu ini mood anda <strong>{{ $analysis['mood_rating']['description'] }}</strong> 
                            berikut adalah aktivitas yang dapat <strong>{{ $analysis['mood_rating']['recommendation'] }}</strong>
                        </div>
                    </div>
                </div>
                
                <div class="mood-range-card">
                    <h3>Jangkauan Mood</h3>
                    <div class="range-info">
                        <div class="range-value">{{ $analysis['mood_rating']['range'] }} poin</div>
                        <div class="range-description">
                            Jangkauan nilai mood dalam 1 minggu: <strong>{{ $analysis['mood_rating']['range_description'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Analysis -->
        <div class="analysis-section">
            <h2 class="section-title">📝 Analisis Aktivitas</h2>
            <div class="activity-analysis-content">
                @if(isset($analysis['activity_analysis']['dominant']))
                    <div class="dominant-activity-card">
                        <h3>🏆 Aktivitas Dominan</h3>
                        <div class="activity-info">
                            <div class="activity-name">{{ $analysis['activity_analysis']['dominant']['activity'] }}</div>
                            <div class="activity-stats">
                                <span class="stat-item">Dilakukan {{ $analysis['activity_analysis']['dominant']['count'] }}x</span>
                                <span class="stat-item">Mood: {{ $analysis['activity_analysis']['dominant']['avg_mood'] }}/10</span>
                                <span class="stat-item">Produktivitas: {{ $analysis['activity_analysis']['dominant']['avg_productivity'] }}/10</span>
                            </div>
                            <div class="activity-impact">
                                Kegiatan {{ $analysis['activity_analysis']['dominant']['activity'] }} 
                                @if($analysis['activity_analysis']['dominant']['mood_contribution'] > 0)
                                    menaikkan
                                @else
                                    menurunkan
                                @endif
                                rata-rata mood mingguanmu sebanyak {{ abs($analysis['activity_analysis']['dominant']['mood_contribution']) }} poin! 
                                Produktivitasmu juga {{ $analysis['activity_analysis']['dominant']['productivity_status'] }} saat melakukan aktivitas ini!
                            </div>
                        </div>
                    </div>
                @else
                    <div class="varied-activities-card">
                        <h3>🔄 Aktivitas Bervariasi</h3>
                        <div class="varied-activities">
                            <div class="best-activity">
                                <h4>🎯 Aktivitas Terbaik</h4>
                                <div class="activity-name">{{ $analysis['activity_analysis']['varied']['highest']['activity'] }}</div>
                                <div class="activity-stats">
                                    <span>Mood: {{ $analysis['activity_analysis']['varied']['highest']['avg_mood'] }}/10</span>
                                    <span>Produktivitas: {{ $analysis['activity_analysis']['varied']['highest']['avg_productivity'] }}/10</span>
                                </div>
                            </div>
                            <div class="worst-activity">
                                <h4>⚠️ Aktivitas Terendah</h4>
                                <div class="activity-name">{{ $analysis['activity_analysis']['varied']['lowest']['activity'] }}</div>
                                <div class="activity-stats">
                                    <span>Mood: {{ $analysis['activity_analysis']['varied']['lowest']['avg_mood'] }}/10</span>
                                    <span>Produktivitas: {{ $analysis['activity_analysis']['varied']['lowest']['avg_productivity'] }}/10</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Goal and Recommendation Table/Cards -->
        <div class="analysis-section">
            <h2 class="section-title">🎯 Tujuan & Rekomendasi</h2>
            
            <!-- Desktop Table -->
            <div class="goal-table desktop-only">
                <table>
                    <thead>
                        <tr>
                            <th>Weekly Avg Score</th>
                            <th>Mood Pattern</th>
                            <th>Goal</th>
                            <th>Theoretical Backing</th>
                            <th>How It Helps</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($analysis['mood_rating']['average'] <= 5.0)
                        <tr class="user-row">
                            <td rowspan="3">≤ 5</td>
                            <td rowspan="3">Low Mood Week</td>
                            <td rowspan="3">Increase Emotional Support & Energy</td>
                            <td>Behavioral Activation (BA)</td>
                            <td>Builds structure and reintroduces behavioral engagement</td>
                        </tr>
                        <tr class="user-row">
                            <td>Social Support Theory</td>
                            <td>Increases perceived support and emotional validation</td>
                        </tr>
                        <tr class="user-row">
                            <td>CBT, Self-Monitoring</td>
                            <td>Enhances self-awareness of triggers and mood patterns</td>
                        </tr>
                        @elseif($analysis['mood_rating']['average'] <= 6.0)
                        <tr class="user-row">
                            <td rowspan="3">5–6</td>
                            <td rowspan="3">Fluctuating Mood</td>
                            <td rowspan="3">Create Routine, Reduce Variability</td>
                            <td>Chronobiology, Habit Formation</td>
                            <td>Stabilizes circadian rhythm and reduces emotional lability</td>
                        </tr>
                        <tr class="user-row">
                            <td>Affective Activation Theory</td>
                            <td>Enhances positive affect and physiological arousal</td>
                        </tr>
                        <tr class="user-row">
                            <td>Expressive Writing Theory</td>
                            <td>Improves emotional regulation and resilience</td>
                        </tr>
                        @else
                        <tr class="user-row">
                            <td rowspan="3">> 6</td>
                            <td rowspan="3">Good/Stable Mood</td>
                            <td rowspan="3">Reinforce Success, Growth Focus</td>
                            <td>Self-Monitoring, Positive Psychology</td>
                            <td>Reinforces adaptive habits and builds intentional living</td>
                        </tr>
                        <tr class="user-row">
                            <td>Self-Determination Theory (SDT), Flow Theory</td>
                            <td>Increases purpose and engagement through mastery</td>
                        </tr>
                        <tr class="user-row">
                            <td>Altruism Theory, Broaden-and-Build</td>
                            <td>Boosts sense of purpose and social bonding</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="goal-cards mobile-only">
                @if($analysis['mood_rating']['average'] <= 5.0)
                    <!-- Main Info Card -->
                    <div class="goal-main-card">
                        <div class="goal-info">
                            <div class="goal-item">
                                <span class="goal-label">Weekly Avg Score</span>
                                <span class="goal-value">≤ 5</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-label">Mood Pattern</span>
                                <span class="goal-value">Low Mood Week</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-label">Goal</span>
                                <span class="goal-value">Increase Emotional Support & Energy</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Behavioral Activation (BA)</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Builds structure and reintroduces behavioral engagement</span>
                        </div>
                    </div>
                    
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Social Support Theory</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Increases perceived support and emotional validation</span>
                        </div>
                    </div>
                    
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">CBT, Self-Monitoring</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Enhances self-awareness of triggers and mood patterns</span>
                        </div>
                    </div>
                    
                @elseif($analysis['mood_rating']['average'] <= 6.0)
                    <!-- Main Info Card -->
                    <div class="goal-main-card">
                        <div class="goal-info">
                            <div class="goal-item">
                                <span class="goal-label">Weekly Avg Score</span>
                                <span class="goal-value">5–6</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-label">Mood Pattern</span>
                                <span class="goal-value">Fluctuating Mood</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-label">Goal</span>
                                <span class="goal-value">Create Routine, Reduce Variability</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Chronobiology, Habit Formation</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Stabilizes circadian rhythm and reduces emotional lability</span>
                        </div>
                    </div>
                    
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Affective Activation Theory</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Enhances positive affect and physiological arousal</span>
                        </div>
                    </div>
                    
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Expressive Writing Theory</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Improves emotional regulation and resilience</span>
                        </div>
                    </div>
                    
                @else
                    <!-- Main Info Card -->
                    <div class="goal-main-card">
                        <div class="goal-info">
                            <div class="goal-item">
                                <span class="goal-label">Weekly Avg Score</span>
                                <span class="goal-value">> 6</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-label">Mood Pattern</span>
                                <span class="goal-value">Good/Stable Mood</span>
                            </div>
                            <div class="goal-item">
                                <span class="goal-label">Goal</span>
                                <span class="goal-value">Reinforce Success, Growth Focus</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Self-Monitoring, Positive Psychology</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Reinforces adaptive habits and builds intentional living</span>
                        </div>
                    </div>
                    
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Self-Determination Theory (SDT), Flow Theory</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Increases purpose and engagement through mastery</span>
                        </div>
                    </div>
                    
                    <div class="goal-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Theoretical Backing</span>
                            <span class="theory-value">Altruism Theory, Broaden-and-Build</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Boosts sense of purpose and social bonding</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Productivity Analysis -->
        <div class="analysis-section">
            <h2 class="section-title">⚡ Analisis Produktivitas</h2>
            <div class="productivity-analysis-content">
                <div class="productivity-overview">
                    <h3>Rata-rata Produktivitas Mingguan</h3>
                    <div class="productivity-score">{{ $analysis['productivity_analysis']['average'] }}/10</div>
                    <div class="productivity-description">
                        Rata-rata produktivitasmu minggu ini {{ $analysis['productivity_analysis']['average'] }}!
                    </div>
                </div>
                
                <div class="productivity-days">
                    <div class="most-productive">
                        <h4>🚀 Hari Paling Produktif</h4>
                        <div class="day-info">
                            <div class="day-date">{{ \Carbon\Carbon::parse($analysis['productivity_analysis']['most_productive']['date'])->translatedFormat('l, j F Y') }}</div>
                            <div class="day-score">Produktivitas: {{ $analysis['productivity_analysis']['most_productive']['productivity'] }}/10</div>
                            <div class="day-mood">Mood: {{ $analysis['productivity_analysis']['most_productive']['mood'] }}/10</div>
                            <div class="day-activities">
                                Aktivitas: {{ implode(', ', array_map(function($activity) {
                                    $activities = [
                                        'work' => 'Pekerjaan & Karir',
                                        'exercise' => 'Aktivitas Fisik',
                                        'social' => 'Sosialisasi',
                                        'hobbies' => 'Kreativitas & Hobi',
                                        'rest' => 'Hiburan & Santai',
                                        'entertainment' => 'Perawatan Diri',
                                        'nature' => 'Aktivitas Luar Ruangan',
                                        'food' => 'Rumah Tangga',
                                        'health' => 'Kesehatan Mental',
                                        'study' => 'Belajar & Produktivitas',
                                        'spiritual' => 'Spiritual',
                                        'romance' => 'Hubungan Romantis',
                                        'finance' => 'Finansial & Mandiri',
                                        'other' => 'Lainnya',
                                    ];
                                    return $activities[$activity] ?? $activity;
                                }, $analysis['productivity_analysis']['most_productive']['activities'])) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="least-productive">
                        <h4>😴 Hari Paling Tidak Produktif</h4>
                        <div class="day-info">
                            <div class="day-date">{{ \Carbon\Carbon::parse($analysis['productivity_analysis']['least_productive']['date'])->translatedFormat('l, j F Y') }}</div>
                            <div class="day-score">Produktivitas: {{ $analysis['productivity_analysis']['least_productive']['productivity'] }}/10</div>
                            <div class="day-mood">Mood: {{ $analysis['productivity_analysis']['least_productive']['mood'] }}/10</div>
                            <div class="day-activities">
                                Aktivitas: {{ implode(', ', array_map(function($activity) {
                                    $activities = [
                                        'work' => 'Pekerjaan & Karir',
                                        'exercise' => 'Aktivitas Fisik',
                                        'social' => 'Sosialisasi',
                                        'hobbies' => 'Kreativitas & Hobi',
                                        'rest' => 'Hiburan & Santai',
                                        'entertainment' => 'Perawatan Diri',
                                        'nature' => 'Aktivitas Luar Ruangan',
                                        'food' => 'Rumah Tangga',
                                        'health' => 'Kesehatan Mental',
                                        'study' => 'Belajar & Produktivitas',
                                        'spiritual' => 'Spiritual',
                                        'romance' => 'Hubungan Romantis',
                                        'finance' => 'Finansial & Mandiri',
                                        'other' => 'Lainnya',
                                    ];
                                    return $activities[$activity] ?? $activity;
                                }, $analysis['productivity_analysis']['least_productive']['activities'])) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Mood Activity Analysis -->
        @if($analysis['best_mood_activity'])
        <div class="analysis-section">
            <h2 class="section-title">🌟 Aktivitas Mood Terbaik</h2>
            <div class="best-mood-activity-content">
                <div class="best-mood-card">
                    <div class="best-mood-header">
                        <h3>🏆 Aktivitas dengan Mood Tertinggi</h3>
                        <div class="best-mood-score">{{ $analysis['best_mood_activity']['mood_score'] }}/10</div>
                    </div>
                    <div class="best-mood-details">
                        <div class="activity-info">
                            <div class="activity-name">{{ $analysis['best_mood_activity']['activity'] }}</div>
                            <div class="activity-date">{{ \Carbon\Carbon::parse($analysis['best_mood_activity']['date'])->translatedFormat('l, j F Y') }}</div>
                        </div>
                        <div class="activity-metrics">
                            <div class="metric">
                                <span class="metric-label">Mood:</span>
                                <span class="metric-value">{{ $analysis['best_mood_activity']['mood_score'] }}/10</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Produktivitas:</span>
                                <span class="metric-value">{{ $analysis['best_mood_activity']['productivity'] }}/10</span>
                            </div>
                        </div>
                        @if($analysis['best_mood_activity']['explanation'])
                        <div class="activity-explanation">
                            <strong>Catatan:</strong> {{ $analysis['best_mood_activity']['explanation'] }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        <!-- AI Feedback Section -->
        @if($weeklyStat->feedback)
        <div class="ai-analysis">
            <div class="ai-header">
                <div class="ai-icon">🤖</div>
                <div>
                    <h2 class="ai-title">Umpan Balik & Insight AI Mingguan</h2>
                    <p class="ai-subtitle">Umpan balik personal dari Ment-AI untuk minggu ini</p>
                </div>
            </div>
            <div class="ai-content">
                <div class="ai-section-content">
                    @php
                        // Konversi Markdown sederhana ke HTML aman (tanpa tag HTML)
                        $text = $weeklyStat->feedback ?? '';
                        // Escape all HTML
                        $text = e($text);

                        // Bold: **text** or __text__
                        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
                        $text = preg_replace('/\_\_(.*?)\_\_/s', '<strong>$1</strong>', $text);

                        // Italic: *text* or _text_
                        $text = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $text);
                        $text = preg_replace('/\_(.*?)\_/s', '<em>$1</em>', $text);

                        // Inline code: `code`
                        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

                        // Headings: #, ##, ###
                        $text = preg_replace('/^### (.*)$/m', '<b>$1</b>', $text);
                        $text = preg_replace('/^## (.*)$/m', '<b>$1</b>', $text);
                        $text = preg_replace('/^# (.*)$/m', '<b>$1</b>', $text);

                        // Unordered lists: - item or * item
                        $text = preg_replace('/^(\s*)[-\*] (.*)$/m', '$1• $2', $text);

                        // Numbered lists: 1. item
                        $text = preg_replace('/^(\s*)\d+\.\s(.*)$/m', '$1$2', $text);

                        // Convert newlines to <br>
                        $text = nl2br($text);
                    @endphp
                    {!! $text !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Entries Section -->
        <div class="daily-entries">
            <div class="entries-title">Entri Harian Minggu Ini</div>
            
            @if($stats->count() > 0)
                <div class="entries-grid">
                    @foreach($stats as $stat)
                        <div class="entry-item" onclick="window.location.href = '{{ route('tracker.stat.detail', $stat->id) }}'">
                            <div class="entry-header">
                                <div class="entry-date">
                                    {{ \Carbon\Carbon::parse($stat->created_at)->translatedFormat('l, j F Y') }}
                                </div>
                                <div class="entry-mood">
                                    @php
                                        $dayMood = $moods[$stat->mood] ?? ['emoji' => '😐', 'label' => 'Netral'];
                                    @endphp
                                    <span class="mood-emoji">{{ $dayMood['emoji'] }}</span>
                                    <span class="mood-score">{{ $stat->mood }}/10</span>
                                </div>
                            </div>
                            <div class="entry-activity">
                                @php
                                    $activities = [
                                        'work' => '💼 Pekerjaan & Karir',
                                        'exercise' => '🏃‍♂️ Aktivitas Fisik',
                                        'social' => '💬 Sosialisasi',
                                        'hobbies' => '🎨 Kreativitas & Hobi',
                                        'rest' => '🎧 Hiburan & Santai',
                                        'entertainment' => '🛁 Perawatan Diri',
                                        'nature' => '🌳 Aktivitas Luar Ruangan',
                                        'food' => '🏠 Rumah Tangga',
                                        'health' => '🧘 Kesehatan Mental',
                                        'study' => '📖 Belajar & Produktivitas',
                                        'spiritual' => '🙏 Spiritual',
                                        'romance' => '💖 Hubungan Romantis',
                                        'finance' => '📊 Finansial & Mandiri',
                                        'other' => '🧩 Lainnya',
                                    ];
                                    $activityName = $activities[$stat->activity] ?? $stat->activity;
                                @endphp
                                {{ $activityName }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-entries">
                    <div class="empty-entries-icon">📝</div>
                    <h3>Tidak Ada Entri</h3>
                    <p>Belum ada catatan harian untuk minggu ini.</p>
                </div>
            @endif
        </div>


        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('tracker.history') }}" class="action-btn btn-secondary">
                Kembali ke Riwayat
            </a>
            <a href="{{ route('dashboard') }}" class="action-btn btn-primary">
                Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Prepare chart data
        @php
            $chartLabels = [];
            $moodData = [];
            $productivityData = [];
            
            foreach($stats as $stat) {
                $chartLabels[] = \Carbon\Carbon::parse($stat->created_at)->format('D');
                $moodData[] = $stat->mood;
                $productivityData[] = $stat->productivity;
            }
        @endphp

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('weeklyChart');
            ctx.height = 250;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Mood',
                            data: {!! json_encode($moodData) !!},
                            borderWidth: 2,
                            tension: 0.3,
                            borderColor: '#48A6A6',
                            backgroundColor: 'rgba(54,162,235,0.08)',
                            pointBackgroundColor: '#48A6A6',
                            pointRadius: 4,
                            fill: true,
                        },
                        {
                            label: 'Productivity',
                            data: {!! json_encode($productivityData) !!},
                            borderWidth: 2,
                            tension: 0.3,
                            borderColor: '#FFCD2D',
                            backgroundColor: 'rgba(253,215,91,0.08)',
                            pointBackgroundColor: '#FFCD2D',
                            pointRadius: 4,
                            fill: true,
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#222',
                                font: {
                                    size: 14,
                                    family: "'Inter', 'Arial', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#fff',
                            titleColor: '#222',
                            bodyColor: '#222',
                            borderColor: '#e9e7e4',
                            borderWidth: 1,
                            padding: 12,
                            caretSize: 6,
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 10,
                            bottom: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 13,
                                    family: "'Inter', 'Arial', sans-serif"
                                }
                            }
                        },
                        y: {
                            min: 0,
                            max: 10,
                            grid: {
                                color: '#e9e7e4'
                            },
                            ticks: {
                                color: '#888',
                                font: {
                                    size: 13,
                                    family: "'Inter', 'Arial', sans-serif"
                                },
                                stepSize: 2
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html> 
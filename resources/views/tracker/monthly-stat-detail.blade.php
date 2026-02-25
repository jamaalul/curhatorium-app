<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Bulanan - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker/result.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .monthly-summary {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            color: #48A6A6;
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
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
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
        
        .monthly-chart {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        
        /* Mood Description */
        .mood-description {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #48A6A6;
        }
        
        .mood-description p {
            margin: 0 0 8px 0;
            color: #222;
            line-height: 1.5;
        }
        
        .mood-description p:last-child {
            margin-bottom: 0;
        }
        
        /* Mood Days Summary */
        .mood-days-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .mood-days-card {
            text-align: center;
            padding: 16px;
            border-radius: 8px;
            color: white;
        }
        
        .mood-days-card.good-mood {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .mood-days-card.low-mood {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .mood-days-count {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .mood-days-label {
            font-size: 0.9rem;
            opacity: 0.9;
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
        
        /* Desktop/Mobile Display Classes */
        .desktop-only {
            display: block;
        }
        
        .mobile-only {
            display: none !important;
        }
        
        /* Monthly Pattern Table */
        .pattern-table {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .pattern-table table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        
        .pattern-table th {
            background: #48A6A6;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .pattern-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            vertical-align: top;
            line-height: 1.5;
        }
        
        .pattern-table tr:last-child td {
            border-bottom: none;
        }
        
        .pattern-table .user-row {
            background: linear-gradient(135deg, #48A6A6 0%, #3a8a8a 100%);
            color: white;
        }
        
        .pattern-table .user-row td {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .pattern-table .user-row:last-child td {
            border-bottom: none;
        }
        
        /* Pattern Cards (Mobile) */
        .pattern-cards {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .pattern-main-card {
            background: linear-gradient(135deg, #48A6A6 0%, #3a8a8a 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .pattern-info {
            display: grid;
            gap: 16px;
        }
        
        .pattern-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .pattern-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        
        .pattern-value {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
        }
        
        .pattern-theory-card {
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
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
        }
        
        .theory-value {
            font-size: 0.9rem;
            color: #222;
            line-height: 1.4;
        }
        
        /* Activity Analysis */
        .activity-analysis-content {
            display: grid;
            gap: 20px;
        }
        
        .activity-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #FFCD2D;
        }
        
        .activity-card h3 {
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
        
        .activity-description {
            background: white;
            padding: 15px;
            border-radius: 6px;
            color: #666;
            line-height: 1.6;
            border-left: 3px solid #FFCD2D;
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }
            
            .desktop-only {
                display: none !important;
            }
            
            .mobile-only {
                display: block !important;
            }
            
            .monthly-summary,
            .analysis-section,
            .monthly-chart,
            .ai-analysis {
                padding: 16px;
                margin-bottom: 16px;
            }
            
            .summary-header {
                flex-direction: row;
                align-items: center;
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
            
            .mood-days-summary {
                gap: 12px;
            }
            
            .mood-days-card {
                padding: 12px;
            }
            
            .mood-days-count {
                font-size: 1.5rem;
            }
            
            .activity-analysis-content {
                gap: 16px;
            }
            
            .activity-card {
                padding: 16px;
            }
            
            .activity-stats {
                flex-direction: column;
                gap: 8px;
            }
            
            .productivity-days {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .section-title {
                font-size: 1.25rem;
                margin-bottom: 16px;
            }
            
            .pattern-cards {
                gap: 20px;
            }
            
            .pattern-main-card {
                padding: 16px;
                margin-bottom: 8px;
            }
            
            .pattern-theory-card {
                padding: 16px;
                margin-bottom: 8px;
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
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 12px;
            }
            
            .monthly-summary,
            .analysis-section,
            .monthly-chart,
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
            
            .mood-days-card {
                padding: 10px;
            }
            
            .mood-days-count {
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
                <h1>Ringkasan Bulanan</h1>
                <p>Berikut ringkasan tracking mental kamu untuk bulan ini.</p>
                <div class="date-display">
                    {{ \Carbon\Carbon::parse($monthlyStat->month)->translatedFormat('F Y') }}
                </div>
            </div>
        </div>

        <!-- Monthly Summary Card -->
        <div class="monthly-summary">
            <div class="summary-header">
                <div>
                    <div class="summary-title">Ringkasan Bulanan</div>
                    <div class="summary-date">
                        {{ \Carbon\Carbon::parse($monthlyStat->month)->translatedFormat('F Y') }}
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
                        
                        // Use stored data from database
                        $avgWeeklyMood = $monthlyStat->avg_weekly_mood ?? $monthlyStat->avg_mood;
                        $avgMood = round($avgWeeklyMood);
                        $mood = $moods[$avgMood] ?? ['emoji' => '😐', 'label' => 'Netral'];
                        
                        // Use stored mood days data
                        $goodMoodDays = $monthlyStat->good_mood_days ?? 0;
                        $lowMoodDays = $monthlyStat->low_mood_days ?? 0;
                        
                        // Use stored mood fluctuation
                        $moodSD = $monthlyStat->mood_fluctuation ?? 0;
                        
                        // Use stored most frequent mood
                        $mostFrequentMood = $monthlyStat->most_frequent_mood ?? 5;
                        $mostFrequentMoodLabel = $moods[$mostFrequentMood] ?? ['label' => 'Netral'];
                    @endphp
                    {{ $mood['emoji'] }}
                </div>
            </div>

            <div class="summary-metrics">
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($avgWeeklyMood, 1) }}</div>
                    <div class="metric-label">Rata-rata Mood Mingguan</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($monthlyStat->avg_productivity, 1) }}</div>
                    <div class="metric-label">Rata-rata Produktivitas</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $monthlyStat->total_entries }}</div>
                    <div class="metric-label">Total Hari</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ number_format($moodSD, 1) }}</div>
                    <div class="metric-label">Fluktuasi Mood</div>
                </div>
            </div>
            
            <!-- Mood Description -->
            <div class="mood-description">
                <p>Dalam bulan ini mood anda rata-rata <strong>{{ $mood['label'] }}</strong> dengan nilai rata-rata {{ number_format($avgWeeklyMood, 1) }}.</p>
                <p>Dalam bulan ini kamu sering merasa <strong>{{ $mostFrequentMoodLabel['label'] }}</strong>.</p>
            </div>
            
            <!-- Mood Days Summary -->
            <div class="mood-days-summary">
                <div class="mood-days-card good-mood">
                    <div class="mood-days-count">{{ $goodMoodDays }}</div>
                    <div class="mood-days-label">Good Mood Days</div>
                </div>
                <div class="mood-days-card low-mood">
                    <div class="mood-days-count">{{ $lowMoodDays }}</div>
                    <div class="mood-days-label">Low Mood Days</div>
                </div>
            </div>
        </div>

        <!-- Monthly Chart Section -->
        <div class="monthly-chart">
            <div class="chart-header">
                <h2 class="chart-title">Grafik Bulanan</h2>
                <p class="chart-subtitle">Perkembangan mood dan produktivitas selama bulan ini</p>
            </div>
            <div class="chart-container">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Monthly Pattern Analysis -->
        <div class="analysis-section">
            <h2 class="section-title">📊 Analisis Pola Bulanan</h2>
            
            <!-- Desktop Table -->
            <div class="pattern-table desktop-only">
                <table>
                    <thead>
                        <tr>
                            <th>Pattern</th>
                            <th>Insight/Deskripsi</th>
                            <th>Contoh Aktivitas</th>
                            <th>Description</th>
                            <th>How It Helps</th>
                            <th>Theory</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Use stored pattern analysis
                            $patternAnalysis = $monthlyStat->pattern_analysis ?? [];
                            $lowMoodPercentage = $patternAnalysis['low_mood_percentage'] ?? 0;
                            $isHighFluctuation = $patternAnalysis['is_high_fluctuation'] ?? false;
                            $isMostlyPositive = $patternAnalysis['is_mostly_positive'] ?? false;
                        @endphp
                        
                        @if($lowMoodPercentage > 50)
                        <tr class="user-row">
                            <td rowspan="3">>50% Low Mood Days</td>
                            <td rowspan="3">Chronic stress, low energy, or burnout</td>
                            <td>1-Month Reset Plan</td>
                            <td>Choose 1–2 life areas (sleep, diet, time online, social) to intentionally reset for 4 weeks</td>
                            <td>Breaks negative feedback loops and fosters control</td>
                            <td>Behavioral Activation (BA), CBT</td>
                        </tr>
                        <tr class="user-row">
                            <td>Professional Support Engagement</td>
                            <td>Begin or continue mental health support (e.g., therapist, group)</td>
                            <td>Promotes accountability, skill-building, and external support</td>
                            <td>Social Support Theory, Psychoeducation</td>
                        </tr>
                        <tr class="user-row">
                            <td>Anchor Habit Development</td>
                            <td>Commit to one core routine habit (e.g., morning stretch or evening shutdown)</td>
                            <td>Builds predictability, sense of mastery, and emotional stability</td>
                            <td>Habit Loop Theory, BA</td>
                        </tr>
                        @elseif($isHighFluctuation)
                        <tr class="user-row">
                            <td rowspan="3">High Mood Fluctuation</td>
                            <td rowspan="3">Emotional instability, low regulation</td>
                            <td>Trigger & Response Log</td>
                            <td>Track what situations, people, or environments cause sudden mood shifts</td>
                            <td>Increases emotional awareness and helps design avoidance or coping strategies</td>
                            <td>CBT, Emotional Regulation</td>
                        </tr>
                        <tr class="user-row">
                            <td>Digital Detox Plan</td>
                            <td>Choose 2–3 days/week to limit digital input (e.g., social media, notifications)</td>
                            <td>Reduces stimulation, improves self-reflection and attention</td>
                            <td>Attention Restoration Theory</td>
                        </tr>
                        <tr class="user-row">
                            <td>Emotional Toolkit Practice</td>
                            <td>Use grounding techniques, emotion wheels, or distress tolerance tools weekly</td>
                            <td>Strengthens capacity to handle emotional surges</td>
                            <td>DBT, Emotional Regulation Theory</td>
                        </tr>
                        @elseif($isMostlyPositive)
                        <tr class="user-row">
                            <td rowspan="3">Mostly Positive Mood</td>
                            <td rowspan="3">High engagement, strong resilience</td>
                            <td>1-Month Growth Challenge</td>
                            <td>Pick a creative, skill-building, or social challenge and track your experience</td>
                            <td>Fuels personal growth, maintains dopamine stability</td>
                            <td>Self-Determination Theory, Flow</td>
                        </tr>
                        <tr class="user-row">
                            <td>Reflective Journaling (End of Month)</td>
                            <td>Write about lessons learned, wins, and strategies to maintain momentum</td>
                            <td>Increases meta-cognition and reinforcement of emotional regulation</td>
                            <td>Self-Monitoring, Positive Psychology</td>
                        </tr>
                        <tr class="user-row">
                            <td>Mentoring or Group Support</td>
                            <td>Offer advice, feedback, or leadership in a peer or support group</td>
                            <td>Enhances meaning, gratitude, and community connection</td>
                            <td>Eudaimonic Wellbeing, Altruism</td>
                        </tr>
                        @else
                        <tr class="user-row">
                            <td rowspan="3">Balanced Mood Pattern</td>
                            <td rowspan="3">Stable emotional baseline with normal fluctuations</td>
                            <td>Maintenance & Growth Focus</td>
                            <td>Continue current positive habits while exploring new activities</td>
                            <td>Maintains stability while preventing stagnation</td>
                            <td>Positive Psychology, Growth Mindset</td>
                        </tr>
                        <tr class="user-row">
                            <td>Mindful Awareness Practice</td>
                            <td>Regular mindfulness or meditation to enhance emotional awareness</td>
                            <td>Strengthens emotional regulation and stress resilience</td>
                            <td>Mindfulness-Based Stress Reduction</td>
                        </tr>
                        <tr class="user-row">
                            <td>Social Connection Enhancement</td>
                            <td>Strengthen existing relationships and build new meaningful connections</td>
                            <td>Increases social support and emotional wellbeing</td>
                            <td>Social Connection Theory</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Cards -->
            <div class="pattern-cards mobile-only">
                @if($lowMoodPercentage > 50)
                    <!-- Main Info Card -->
                    <div class="pattern-main-card">
                        <div class="pattern-info">
                            <div class="pattern-item">
                                <span class="pattern-label">Pattern</span>
                                <span class="pattern-value">>50% Low Mood Days</span>
                            </div>
                            <div class="pattern-item">
                                <span class="pattern-label">Insight/Deskripsi</span>
                                <span class="pattern-value">Chronic stress, low energy, or burnout</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">1-Month Reset Plan</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Choose 1–2 life areas (sleep, diet, time online, social) to intentionally reset for 4 weeks</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Breaks negative feedback loops and fosters control</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Behavioral Activation (BA), CBT</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Professional Support Engagement</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Begin or continue mental health support (e.g., therapist, group)</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Promotes accountability, skill-building, and external support</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Social Support Theory, Psychoeducation</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Anchor Habit Development</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Commit to one core routine habit (e.g., morning stretch or evening shutdown)</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Builds predictability, sense of mastery, and emotional stability</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Habit Loop Theory, BA</span>
                        </div>
                    </div>
                    
                @elseif($isHighFluctuation)
                    <!-- Main Info Card -->
                    <div class="pattern-main-card">
                        <div class="pattern-info">
                            <div class="pattern-item">
                                <span class="pattern-label">Pattern</span>
                                <span class="pattern-value">High Mood Fluctuation</span>
                            </div>
                            <div class="pattern-item">
                                <span class="pattern-label">Insight/Deskripsi</span>
                                <span class="pattern-value">Emotional instability, low regulation</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Trigger & Response Log</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Track what situations, people, or environments cause sudden mood shifts</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Increases emotional awareness and helps design avoidance or coping strategies</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">CBT, Emotional Regulation</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Digital Detox Plan</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Choose 2–3 days/week to limit digital input (e.g., social media, notifications)</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Reduces stimulation, improves self-reflection and attention</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Attention Restoration Theory</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Emotional Toolkit Practice</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Use grounding techniques, emotion wheels, or distress tolerance tools weekly</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Strengthens capacity to handle emotional surges</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">DBT, Emotional Regulation Theory</span>
                        </div>
                    </div>
                    
                @elseif($isMostlyPositive)
                    <!-- Main Info Card -->
                    <div class="pattern-main-card">
                        <div class="pattern-info">
                            <div class="pattern-item">
                                <span class="pattern-label">Pattern</span>
                                <span class="pattern-value">Mostly Positive Mood</span>
                            </div>
                            <div class="pattern-item">
                                <span class="pattern-label">Insight/Deskripsi</span>
                                <span class="pattern-value">High engagement, strong resilience</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">1-Month Growth Challenge</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Pick a creative, skill-building, or social challenge and track your experience</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Fuels personal growth, maintains dopamine stability</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Self-Determination Theory, Flow</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Reflective Journaling (End of Month)</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Write about lessons learned, wins, and strategies to maintain momentum</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Increases meta-cognition and reinforcement of emotional regulation</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Self-Monitoring, Positive Psychology</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Mentoring or Group Support</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Offer advice, feedback, or leadership in a peer or support group</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Enhances meaning, gratitude, and community connection</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Eudaimonic Wellbeing, Altruism</span>
                        </div>
                    </div>
                    
                @else
                    <!-- Main Info Card -->
                    <div class="pattern-main-card">
                        <div class="pattern-info">
                            <div class="pattern-item">
                                <span class="pattern-label">Pattern</span>
                                <span class="pattern-value">Balanced Mood Pattern</span>
                            </div>
                            <div class="pattern-item">
                                <span class="pattern-label">Insight/Deskripsi</span>
                                <span class="pattern-value">Stable emotional baseline with normal fluctuations</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theory Cards -->
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Maintenance & Growth Focus</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Continue current positive habits while exploring new activities</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Maintains stability while preventing stagnation</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Positive Psychology, Growth Mindset</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Mindful Awareness Practice</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Regular mindfulness or meditation to enhance emotional awareness</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Strengthens emotional regulation and stress resilience</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Mindfulness-Based Stress Reduction</span>
                        </div>
                    </div>
                    
                    <div class="pattern-theory-card">
                        <div class="theory-item">
                            <span class="theory-label">Contoh Aktivitas</span>
                            <span class="theory-value">Social Connection Enhancement</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Description</span>
                            <span class="theory-value">Strengthen existing relationships and build new meaningful connections</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">How It Helps</span>
                            <span class="theory-value">Increases social support and emotional wellbeing</span>
                        </div>
                        <div class="theory-item">
                            <span class="theory-label">Theory</span>
                            <span class="theory-value">Social Connection Theory</span>
                        </div>
                    </div>
                    
                @endif
            </div>
        </div>

        <!-- Activity Analysis -->
        <div class="analysis-section">
            <h2 class="section-title">📝 Analisis Aktivitas</h2>
            <div class="activity-analysis-content">
                @php
                    // Use stored activity analysis
                    $activityAnalysis = $monthlyStat->activity_analysis ?? [];
                    $mostFrequentActivity = $activityAnalysis['most_frequent']['activity'] ?? 'other';
                    $mostFrequentStats = $activityAnalysis['most_frequent']['stats'] ?? ['count' => 0, 'avg_mood' => 5, 'avg_productivity' => 5];
                    $bestMoodActivity = $activityAnalysis['best_mood']['activity'] ?? 'other';
                    $bestMoodStats = $activityAnalysis['best_mood']['stats'] ?? ['count' => 0, 'avg_mood' => 5, 'avg_productivity' => 5];
                    $worstMoodActivity = $activityAnalysis['worst_mood']['activity'] ?? 'other';
                    $worstMoodStats = $activityAnalysis['worst_mood']['stats'] ?? ['count' => 0, 'avg_mood' => 5, 'avg_productivity' => 5];
                    
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
                @endphp
                
                <!-- Most Frequent Activity -->
                <div class="activity-card">
                    <h3>🏆 Aktivitas Paling Sering</h3>
                    <div class="activity-info">
                        <div class="activity-name">{{ $activities[$mostFrequentActivity] ?? $mostFrequentActivity }}</div>
                        <div class="activity-stats">
                            <span class="stat-item">Dilakukan {{ $mostFrequentStats['count'] }}x</span>
                            <span class="stat-item">Mood: {{ number_format($mostFrequentStats['avg_mood'], 1) }}/10</span>
                            <span class="stat-item">Produktivitas: {{ number_format($mostFrequentStats['avg_productivity'], 1) }}/10</span>
                        </div>
                        <div class="activity-description">
                            Kegiatan yang paling sering anda lakukan pada bulan ini adalah <strong>{{ $activities[$mostFrequentActivity] ?? $mostFrequentActivity }}</strong>.
                        </div>
                    </div>
                </div>
                
                <!-- Best Mood Activity -->
                <div class="activity-card">
                    <h3>🌟 Aktivitas Paling Mendukung Mood</h3>
                    <div class="activity-info">
                        <div class="activity-name">{{ $activities[$bestMoodActivity] ?? $bestMoodActivity }}</div>
                        <div class="activity-stats">
                            <span class="stat-item">Dilakukan {{ $bestMoodStats['count'] }}x</span>
                            <span class="stat-item">Mood: {{ number_format($bestMoodStats['avg_mood'], 1) }}/10</span>
                            <span class="stat-item">Produktivitas: {{ number_format($bestMoodStats['avg_productivity'], 1) }}/10</span>
                        </div>
                        <div class="activity-description">
                            <strong>{{ $activities[$bestMoodActivity] ?? $bestMoodActivity }}</strong> yang kamu lakuin selama bulan ini ngeningkatin moodmu!
                        </div>
                    </div>
                </div>
                
                <!-- Worst Mood Activity -->
                <div class="activity-card">
                    <h3>⚠️ Aktivitas Paling Mengurangi Mood</h3>
                    <div class="activity-info">
                        <div class="activity-name">{{ $activities[$worstMoodActivity] ?? $worstMoodActivity }}</div>
                        <div class="activity-stats">
                            <span class="stat-item">Dilakukan {{ $worstMoodStats['count'] }}x</span>
                            <span class="stat-item">Mood: {{ number_format($worstMoodStats['avg_mood'], 1) }}/10</span>
                            <span class="stat-item">Produktivitas: {{ number_format($worstMoodStats['avg_productivity'], 1) }}/10</span>
                        </div>
                        <div class="activity-description">
                            <strong>{{ $activities[$worstMoodActivity] ?? $worstMoodActivity }}</strong> yang kamu lakukan di bulan ini ngurangin moodmu banget!
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productivity Analysis -->
        <div class="analysis-section">
            <h2 class="section-title">⚡ Analisis Produktivitas</h2>
            <div class="productivity-analysis-content">
                @php
                    // Use stored productivity analysis
                    $productivityAnalysis = $monthlyStat->productivity_analysis ?? [];
                    $highestProductivityDay = $productivityAnalysis['highest_day'] ?? null;
                    $lowestProductivityDay = $productivityAnalysis['lowest_day'] ?? null;
                    $highestProductivityWeek = $productivityAnalysis['highest_week'] ?? 0;
                    $lowestProductivityWeek = $productivityAnalysis['lowest_week'] ?? 0;
                @endphp
                
                <div class="productivity-overview">
                    <h3>Rata-rata Produktivitas Bulanan</h3>
                    <div class="productivity-score">{{ number_format($monthlyStat->avg_productivity, 1) }}/10</div>
                    <div class="productivity-description">
                        Rata-rata produktivitasmu bulan ini {{ number_format($monthlyStat->avg_productivity, 1) }}!
                    </div>
                </div>
                
                <div class="productivity-days">
                    @if($highestProductivityDay)
                    <div class="most-productive">
                        <h4>🚀 Hari Paling Produktif</h4>
                        <div class="day-info">
                            <div class="day-date">{{ \Carbon\Carbon::parse($highestProductivityDay['date'])->translatedFormat('l, j F Y') }}</div>
                            <div class="day-score">Produktivitas: {{ $highestProductivityDay['productivity'] }}/10</div>
                            <div class="day-mood">Mood: {{ $highestProductivityDay['mood'] }}/10</div>
                            <div class="day-activities">
                                Aktivitas: {{ $activities[$highestProductivityDay['activity']] ?? $highestProductivityDay['activity'] }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($lowestProductivityDay)
                    <div class="least-productive">
                        <h4>😴 Hari Paling Tidak Produktif</h4>
                        <div class="day-info">
                            <div class="day-date">{{ \Carbon\Carbon::parse($lowestProductivityDay['date'])->translatedFormat('l, j F Y') }}</div>
                            <div class="day-score">Produktivitas: {{ $lowestProductivityDay['productivity'] }}/10</div>
                            <div class="day-mood">Mood: {{ $lowestProductivityDay['mood'] }}/10</div>
                            <div class="day-activities">
                                Aktivitas: {{ $activities[$lowestProductivityDay['activity']] ?? $lowestProductivityDay['activity'] }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="productivity-days">
                    <div class="most-productive">
                        <h4>📈 Minggu Paling Produktif</h4>
                        <div class="day-info">
                            <div class="day-score">Produktivitas: {{ number_format($highestProductivityWeek, 1) }}/10</div>
                        </div>
                    </div>
                    
                    <div class="least-productive">
                        <h4>📉 Minggu Paling Tidak Produktif</h4>
                        <div class="day-info">
                            <div class="day-score">Produktivitas: {{ number_format($lowestProductivityWeek, 1) }}/10</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Feedback Section -->
        @if($monthlyStat->feedback)
        <div class="ai-analysis">
            <div class="ai-header">
                <div class="ai-icon">🤖</div>
                <div>
                    <h2 class="ai-title">Umpan Balik & Insight AI Bulanan</h2>
                    <p class="ai-subtitle">Umpan balik personal dari Ment-AI untuk bulan ini</p>
                </div>
            </div>
            <div class="ai-content">
                <div class="ai-section-content prose prose-lg" style="max-width: none;">
                    {!! Illuminate\Support\Str::markdown($monthlyStat->feedback) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Daily Entries Section -->
        <div class="daily-entries">
            <div class="entries-title">Entri Harian Bulan Ini</div>
            
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
                    <p>Belum ada catatan harian untuk bulan ini.</p>
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
                $chartLabels[] = \Carbon\Carbon::parse($stat->created_at)->format('j M');
                $moodData[] = $stat->mood;
                $productivityData[] = $stat->productivity;
            }
        @endphp

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('monthlyChart');
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
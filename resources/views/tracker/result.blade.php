<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Mood Results - Curhatorium</title>
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
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --profile-bg: #222222;
            --border-radius-sm: 0.25rem;
            --border-radius: 0.5rem;
            --border-radius-lg: 0.75rem;
            --border-radius-xl: 1rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition: all 0.3s ease;
            --container-width: 800px;
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

        .date-display {
            font-size: 1rem;
            opacity: 0.8;
            margin-top: 0.5rem;
        }

        /* Today's Entry Card */
        .entry-card {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            padding: 2.5rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid rgba(142, 203, 207, 0.1);
            margin-bottom: 2rem;
        }

        .entry-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .entry-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--bg-tertiary);
        }

        .mood-display {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .mood-score {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .mood-label {
            font-size: 1.125rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Entry Details */
        .entry-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .detail-section {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
        }

        .detail-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .activity-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .activity-icon {
            font-size: 2rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            border-radius: 50%;
            box-shadow: var(--shadow-sm);
        }

        .activity-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Energy Metrics */
        .energy-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .metric-item {
            text-align: center;
            padding: 1rem;
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .metric-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.25rem;
        }

        .metric-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        /* Explanation Section */
        .explanation-section {
            grid-column: 1 / -1;
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
        }

        .explanation-text {
            color: var(--text-secondary);
            line-height: 1.6;
            font-style: italic;
            background: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }

        /* AI Analysis Section */
        .ai-analysis {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(142, 203, 207, 0.1);
            margin-bottom: 2rem;
        }

        .ai-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--bg-tertiary);
        }

        .ai-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .ai-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .ai-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .ai-content {
            max-width: none;
        }

        .ai-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            border: 1px solid var(--bg-tertiary);
            background: var(--bg-secondary);
        }

        .ai-section:last-child {
            margin-bottom: 0;
        }

        .ai-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ai-section-content {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        .ai-section-content p {
            margin-bottom: 1rem;
        }

        .ai-section-content p:last-child {
            margin-bottom: 0;
        }

        .ai-section-content ul {
            list-style: none;
            padding-left: 0;
        }

        .ai-section-content li {
            margin-bottom: 0.75rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .ai-section-content li::before {
            content: '•';
            color: var(--primary-color);
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .ai-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .ai-loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--bg-tertiary);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-lg);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-primary);
            border: 1px solid var(--bg-tertiary);
        }

        .btn-secondary:hover {
            background: var(--bg-secondary);
            border-color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .page-header p {
                font-size: 1rem;
            }

            .entry-details {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .energy-metrics {
                grid-template-columns: 1fr;
            }

            .entry-card,
            .ai-analysis {
                padding: 1.5rem;
            }

            .container {
                padding: 0 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .mood-display {
                font-size: 3rem;
            }

            .mood-score {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                padding: 2rem 0;
            }

            .page-header h1 {
                font-size: 1.75rem;
            }

            .entry-card,
            .ai-analysis {
                padding: 1rem;
            }

            .ai-section {
                padding: 1rem;
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
                <h1>Today's Mood Entry</h1>
                <p>Here's your mood tracking result for today with personalized AI insights.</p>
                <div class="date-display">{{ \Carbon\Carbon::parse($stat->created_at)->format('l, d F Y') }}</div>
            </div>
        </div>

        <!-- Entry Card -->
        <div class="entry-card">
            <div class="entry-header" style="text-align:center;">
                <div class="mood-display" style="font-size:4rem;">
                    @php
                        $moods = [
                            1 => ['emoji' => '😢', 'label' => 'Very Sad'],
                            2 => ['emoji' => '😞', 'label' => 'Sad'],
                            3 => ['emoji' => '😔', 'label' => 'Down'],
                            4 => ['emoji' => '😐', 'label' => 'Neutral'],
                            5 => ['emoji' => '🙂', 'label' => 'Okay'],
                            6 => ['emoji' => '😊', 'label' => 'Good'],
                            7 => ['emoji' => '😄', 'label' => 'Happy'],
                            8 => ['emoji' => '😁', 'label' => 'Very Happy'],
                            9 => ['emoji' => '🤩', 'label' => 'Excited'],
                            10 => ['emoji' => '🥳', 'label' => 'Euphoric'],
                        ];
                        $mood = $moods[$stat->mood] ?? ['emoji' => '', 'label' => ''];
                    @endphp
                    {{ $mood['emoji'] }}
                </div>
                <div class="mood-score" style="font-size:2rem;font-weight:700;color:var(--primary-dark);">{{ $stat->mood }}/10</div>
                <div class="mood-label" style="font-size:1.125rem;color:var(--text-secondary);font-weight:500;">{{ $mood['label'] }}</div>
            </div>

            <div class="entry-details" style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem;">
                <div class="detail-section" style="background:var(--bg-secondary);padding:1.5rem;border-radius:var(--border-radius-lg);">
                    <div class="detail-title" style="font-size:1rem;font-weight:600;color:var(--text-primary);margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        🎯 Main Activity
                    </div>
                    <div class="activity-display" style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                        @php
                            $activities = [
                                'work' => ['icon' => '💼', 'name' => 'Work/Study'],
                                'exercise' => ['icon' => '🏃‍♂️', 'name' => 'Exercise'],
                                'social' => ['icon' => '👥', 'name' => 'Social Time'],
                                'hobbies' => ['icon' => '🎨', 'name' => 'Hobbies'],
                                'rest' => ['icon' => '😴', 'name' => 'Rest/Sleep'],
                                'entertainment' => ['icon' => '📺', 'name' => 'Entertainment'],
                                'nature' => ['icon' => '🌳', 'name' => 'Nature/Outdoors'],
                                'food' => ['icon' => '🍽️', 'name' => 'Food/Cooking'],
                                'health' => ['icon' => '🏥', 'name' => 'Health/Medical'],
                                'other' => ['icon' => '❓', 'name' => 'Other'],
                            ];
                            $activity = $activities[$stat->activity] ?? ['icon' => '', 'name' => $stat->activity];
                        @endphp
                        <div class="activity-icon" style="font-size:2rem;width:50px;height:50px;display:flex;align-items:center;justify-content:center;background:var(--white);border-radius:50%;box-shadow:var(--shadow-sm);">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="activity-name" style="font-size:1.125rem;font-weight:600;color:var(--text-primary);">
                            {{ $activity['name'] }}
                        </div>
                    </div>
                </div>

                <div class="detail-section" style="background:var(--bg-secondary);padding:1.5rem;border-radius:var(--border-radius-lg);">
                    <div class="detail-title" style="font-size:1rem;font-weight:600;color:var(--text-primary);margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        ⚡ Energy & Productivity
                    </div>
                    <div class="energy-metrics" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                        <div class="metric-item" style="text-align:center;padding:1rem;background:var(--white);border-radius:var(--border-radius-lg);box-shadow:var(--shadow-sm);">
                            <div class="metric-icon" style="font-size:1.5rem;margin-bottom:0.5rem;">⚡</div>
                            <div class="metric-value" style="font-size:1.5rem;font-weight:700;color:var(--primary-dark);margin-bottom:0.25rem;">{{ $stat->energy }}</div>
                            <div class="metric-label" style="font-size:0.875rem;color:var(--text-secondary);">Energy</div>
                        </div>
                        <div class="metric-item" style="text-align:center;padding:1rem;background:var(--white);border-radius:var(--border-radius-lg);box-shadow:var(--shadow-sm);">
                            <div class="metric-icon" style="font-size:1.5rem;margin-bottom:0.5rem;">🎯</div>
                            <div class="metric-value" style="font-size:1.5rem;font-weight:700;color:var(--primary-dark);margin-bottom:0.25rem;">{{ $stat->productivity }}</div>
                            <div class="metric-label" style="font-size:0.875rem;color:var(--text-secondary);">Productivity</div>
                        </div>
                    </div>
                </div>

                @if($stat->explanation)
                <div class="explanation-section" style="grid-column:1/-1;background:var(--bg-secondary);padding:1.5rem;border-radius:var(--border-radius-lg);">
                    <div class="detail-title" style="font-size:1rem;font-weight:600;color:var(--text-primary);margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;">
                        💭 Your Thoughts
                    </div>
                    <div class="explanation-text" style="color:var(--text-secondary);line-height:1.6;font-style:italic;background:var(--white);padding:1rem;border-radius:var(--border-radius);border-left:4px solid var(--primary-color);">
                        "{{ $stat->explanation }}"
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- AI Feedback Section -->
        <div class="ai-analysis">
            <div class="ai-header" style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;padding-bottom:1rem;border-bottom:1px solid var(--bg-tertiary);">
                <div class="ai-icon" style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary-color),var(--primary-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:white;">🤖</div>
                <div>
                    <h2 class="ai-title" style="font-size:1.5rem;font-weight:700;color:var(--text-primary);">AI Feedback & Insights</h2>
                    <p class="ai-subtitle" style="color:var(--text-secondary);font-size:0.95rem;">Personalized feedback from Ment-AI</p>
                </div>
            </div>
            <div class="ai-content" style="max-width:none;">
                @if($stat->feedback)
                    <div class="ai-section-content" style="color:var(--text-secondary);line-height:1.7;">
                        @php
                            // A very basic Markdown to safe HTML converter (no HTML tags allowed)
                            $text = $stat->feedback ?? '';
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
                @else
                    <div class="ai-section-content" style="color:var(--text-tertiary);font-style:italic;">No AI feedback available for this entry.</div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons" style="display:flex;gap:1rem;justify-content:center;margin-top:2rem;">
            <a href="{{ route('dashboard') }}" class="action-btn btn-primary" style="background:var(--primary-color);color:white;border:none;padding:0.75rem 1.5rem;border-radius:var(--border-radius-lg);font-weight:600;cursor:pointer;transition:var(--transition);text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;">
                Kembali
            </a>
        </div>
    </div>
</body>
</html>


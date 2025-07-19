<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missions of the Day - Curhatorium</title>
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

        /* Main Content */
        .missions-container {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(142, 203, 207, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Tabs */
        .tabs {
            display: flex;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--bg-tertiary);
        }

        .tab {
            flex: 1;
            padding: 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            background: transparent;
            font-size: 1.125rem;
            font-weight: 600;
            position: relative;
            color: var(--text-secondary);
        }

        .tab.easy {
            color: var(--success);
        }

        .tab.medium {
            color: var(--warning);
        }

        .tab.hard {
            color: var(--error);
        }

        .tab.active {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: currentColor;
        }

        .tab:hover:not(.active) {
            background: var(--bg-tertiary);
        }

        /* Tab Content */
        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        .difficulty-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .difficulty-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .difficulty-title.easy {
            color: var(--success);
        }

        .difficulty-title.medium {
            color: var(--warning);
        }

        .difficulty-title.hard {
            color: var(--error);
        }

        .difficulty-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Stats */
        .stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            padding: 1rem 1.5rem;
            background: var(--bg-secondary);
            border-radius: var(--border-radius-lg);
            border: 1px solid var(--bg-tertiary);
            transition: var(--transition);
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Progress Bar */
        .progress-bar {
            background: var(--bg-tertiary);
            height: 8px;
            border-radius: 4px;
            margin: 1.5rem 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-fill.easy {
            background: var(--success);
        }

        .progress-fill.medium {
            background: var(--warning);
        }

        .progress-fill.hard {
            background: var(--error);
        }

        /* Missions Grid */
        .missions-grid {
            display: grid;
            gap: 1.5rem;
        }

        .mission-card {
            background: var(--bg-secondary);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            transition: var(--transition);
            border-left: 4px solid;
            border: 1px solid var(--bg-tertiary);
            position: relative;
            overflow: hidden;
        }

        .mission-card.easy {
            border-left-color: var(--success);
        }

        .mission-card.medium {
            border-left-color: var(--warning);
        }

        .mission-card.hard {
            border-left-color: var(--error);
        }

        .mission-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: var(--white);
        }

        .mission-card.completed {
            background: #f0f9ff;
            opacity: 0.8;
        }

        .mission-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .mission-info {
            flex: 1;
        }

        .mission-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .mission-description {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .mission-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mission-points {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-lg);
            font-size: 0.875rem;
        }

        .mission-points.easy {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .mission-points.medium {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .mission-points.hard {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
        }

        /* Completion Toggle */
        .completion-toggle {
            position: relative;
        }

        .completion-checkbox {
            appearance: none;
            width: 24px;
            height: 24px;
            border: 2px solid var(--bg-tertiary);
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .completion-checkbox:checked {
            background: var(--success);
            border-color: var(--success);
        }

        .completion-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        .completion-checkbox:hover {
            border-color: var(--primary-color);
        }

        /* Floating Action Button */
        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: none;
        }

        .floating-action:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .page-header p {
                font-size: 1rem;
            }

            .tabs {
                flex-direction: column;
            }

            .tab {
                padding: 1rem;
            }

            .tab-content {
                padding: 1.5rem;
            }

            .mission-card {
                padding: 1rem;
            }

            .stats {
                gap: 1rem;
            }

            .stat-item {
                padding: 0.75rem 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            .floating-action {
                bottom: 1rem;
                right: 1rem;
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                padding: 2rem 0;
            }

            .page-header h1 {
                font-size: 1.75rem;
            }

            .tab-content {
                padding: 1rem;
            }

            .mission-card {
                padding: 1rem;
            }

            .mission-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .stats {
                flex-direction: column;
                gap: 0.75rem;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .completion-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-lg);
            text-align: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
            border: 1px solid rgba(142, 203, 207, 0.1);
        }

        .completion-message h3 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .completion-message p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .completion-message button {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: var(--border-radius-lg);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .completion-message button:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
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
                <h1>🎯 Missions of the Day</h1>
                <p>Complete daily challenges to improve your mental well-being and build healthy habits</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="missions-container">
            <div class="tabs">
                <button class="tab easy active" onclick="switchTab('easy')">
                    Easy
                </button>
                <button class="tab medium" onclick="switchTab('medium')">
                    Medium
                </button>
                <button class="tab hard" onclick="switchTab('hard')">
                    Hard
                </button>
            </div>

            <!-- Easy Missions -->
            <div id="easy-content" class="tab-content active">
                <div class="difficulty-header">
                    <h2 class="difficulty-title easy">Easy Missions</h2>
                    <p class="difficulty-subtitle">Simple daily habits to start your wellness journey</p>
                </div>

                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--success);">0/5</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--success);">50</div>
                        <div class="stat-label">Total Points</div>
                    </div>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill easy" style="width: 0%"></div>
                </div>

                <div class="missions-grid">
                    <div class="mission-card easy">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Take 5 Deep Breaths</h3>
                                <p class="mission-description">Practice mindful breathing for 2 minutes to reduce stress and center yourself.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points easy">
                                2 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('easy')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card easy">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Drink 8 Glasses of Water</h3>
                                <p class="mission-description">Stay hydrated throughout the day to maintain physical and mental energy.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points easy">
                                2 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('easy')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card easy">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Write 3 Things You're Grateful For</h3>
                                <p class="mission-description">Practice gratitude by noting three positive things from your day.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points easy">
                                2 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('easy')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card easy">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Take a 10-Minute Walk</h3>
                                <p class="mission-description">Get some fresh air and light exercise to boost your mood and energy.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points easy">
                                2 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('easy')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card easy">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Compliment Someone</h3>
                                <p class="mission-description">Spread positivity by giving a genuine compliment to someone today.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points easy">
                                2 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('easy')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medium Missions -->
            <div id="medium-content" class="tab-content">
                <div class="difficulty-header">
                    <h2 class="difficulty-title medium">Medium Missions</h2>
                    <p class="difficulty-subtitle">Moderate challenges to build healthy habits</p>
                </div>

                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--warning);">0/5</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--warning);">100</div>
                        <div class="stat-label">Total Points</div>
                    </div>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill medium" style="width: 0%"></div>
                </div>

                <div class="missions-grid">
                    <div class="mission-card medium">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">15-Minute Meditation</h3>
                                <p class="mission-description">Practice guided meditation to improve focus and reduce anxiety.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points medium">
                                5 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('medium')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card medium">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Journal for 10 Minutes</h3>
                                <p class="mission-description">Reflect on your thoughts and emotions through written expression.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points medium">
                                5 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('medium')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card medium">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">30-Minute Exercise</h3>
                                <p class="mission-description">Engage in physical activity to release endorphins and improve mood.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points medium">
                                5 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('medium')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card medium">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Call a Friend or Family Member</h3>
                                <p class="mission-description">Strengthen social connections by reaching out to someone you care about.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points medium">
                                5 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('medium')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card medium">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Learn Something New</h3>
                                <p class="mission-description">Spend 20 minutes learning a new skill or reading about a topic of interest.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points medium">
                                5 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('medium')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hard Missions -->
            <div id="hard-content" class="tab-content">
                <div class="difficulty-header">
                    <h2 class="difficulty-title hard">Hard Missions</h2>
                    <p class="difficulty-subtitle">Challenging tasks for significant personal growth</p>
                </div>

                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--error);">0/5</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" style="color: var(--error);">200</div>
                        <div class="stat-label">Total Points</div>
                    </div>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill hard" style="width: 0%"></div>
                </div>

                <div class="missions-grid">
                    <div class="mission-card hard">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Face a Fear</h3>
                                <p class="mission-description">Take a small step towards overcoming something that makes you anxious.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points hard">
                                10 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('hard')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card hard">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Have a Difficult Conversation</h3>
                                <p class="mission-description">Address an important issue you've been avoiding with someone close to you.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points hard">
                                10 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('hard')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card hard">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Digital Detox for 3 Hours</h3>
                                <p class="mission-description">Disconnect from all digital devices and focus on offline activities.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points hard">
                                10 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('hard')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card hard">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Practice Vulnerability</h3>
                                <p class="mission-description">Share something personal or meaningful with someone you trust.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points hard">
                                10 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('hard')">
                            </div>
                        </div>
                    </div>

                    <div class="mission-card hard">
                        <div class="mission-header">
                            <div class="mission-info">
                                <h3 class="mission-title">Set a Challenging Goal</h3>
                                <p class="mission-description">Define a specific, measurable goal that pushes you out of your comfort zone.</p>
                            </div>
                        </div>
                        <div class="mission-footer">
                            <div class="mission-points hard">
                                10 points
                            </div>
                            <div class="completion-toggle">
                                <input type="checkbox" class="completion-checkbox" onchange="updateProgress('hard')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mission data structure for easy backend integration
        const missionsData = {
            easy: [
                { id: 1, title: "Take 5 Deep Breaths", description: "Practice mindful breathing for 2 minutes to reduce stress and center yourself.", points: 10, completed: false },
                { id: 2, title: "Drink 8 Glasses of Water", description: "Stay hydrated throughout the day to maintain physical and mental energy.", points: 10, completed: false },
                { id: 3, title: "Write 3 Things You're Grateful For", description: "Practice gratitude by noting three positive things from your day.", points: 10, completed: false },
                { id: 4, title: "Take a 10-Minute Walk", description: "Get some fresh air and light exercise to boost your mood and energy.", points: 10, completed: false },
                { id: 5, title: "Compliment Someone", description: "Spread positivity by giving a genuine compliment to someone today.", points: 10, completed: false }
            ],
            medium: [
                { id: 6, title: "15-Minute Meditation", description: "Practice guided meditation to improve focus and reduce anxiety.", points: 20, completed: false },
                { id: 7, title: "Journal for 10 Minutes", description: "Reflect on your thoughts and emotions through written expression.", points: 20, completed: false },
                { id: 8, title: "30-Minute Exercise", description: "Engage in physical activity to release endorphins and improve mood.", points: 20, completed: false },
                { id: 9, title: "Call a Friend or Family Member", description: "Strengthen social connections by reaching out to someone you care about.", points: 20, completed: false },
                { id: 10, title: "Learn Something New", description: "Spend 20 minutes learning a new skill or reading about a topic of interest.", points: 20, completed: false }
            ],
            hard: [
                { id: 11, title: "Face a Fear", description: "Take a small step towards overcoming something that makes you anxious.", points: 40, completed: false },
                { id: 12, title: "Have a Difficult Conversation", description: "Address an important issue you've been avoiding with someone close to you.", points: 40, completed: false },
                { id: 13, title: "Digital Detox for 3 Hours", description: "Disconnect from all digital devices and focus on offline activities.", points: 40, completed: false },
                { id: 14, title: "Practice Vulnerability", description: "Share something personal or meaningful with someone you trust.", points: 40, completed: false },
                { id: 15, title: "Set a Challenging Goal", description: "Define a specific, measurable goal that pushes you out of your comfort zone.", points: 40, completed: false }
            ]
        };

        function switchTab(difficulty) {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            document.querySelector(`.tab.${difficulty}`).classList.add('active');
            document.getElementById(`${difficulty}-content`).classList.add('active');
        }

        function updateProgress(difficulty) {
            const checkboxes = document.querySelectorAll(`#${difficulty}-content .completion-checkbox`);
            const completedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            const totalCount = checkboxes.length;
            const percentage = (completedCount / totalCount) * 100;
            
            // Update progress bar
            const progressBar = document.querySelector(`#${difficulty}-content .progress-fill`);
            progressBar.style.width = `${percentage}%`;
            
            // Update stats
            const statNumber = document.querySelector(`#${difficulty}-content .stat-number`);
            statNumber.textContent = `${completedCount}/${totalCount}`;
            
            // Update mission card appearance
            checkboxes.forEach((checkbox, index) => {
                const missionCard = checkbox.closest('.mission-card');
                if (checkbox.checked) {
                    missionCard.classList.add('completed');
                } else {
                    missionCard.classList.remove('completed');
                }
            });
            
            // Show completion celebration
            if (completedCount === totalCount) {
                showCompletionMessage(difficulty);
            }
        }

        function showCompletionMessage(difficulty) {
            const message = document.createElement('div');
            message.className = 'completion-message';
            
            const difficultyColors = {
                easy: 'var(--success)',
                medium: 'var(--warning)',
                hard: 'var(--error)'
            };
            
            message.innerHTML = `
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎉</div>
                <h3 style="color: ${difficultyColors[difficulty]};">Congratulations!</h3>
                <p>You've completed all ${difficulty} missions for today!</p>
                <button onclick="this.parentElement.remove()">Awesome!</button>
            `;
            
            document.body.appendChild(message);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (message.parentElement) {
                    message.remove();
                }
            }, 5000);
        }

        function refreshMissions() {
            // Reset all checkboxes and progress
            document.querySelectorAll('.completion-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            document.querySelectorAll('.mission-card').forEach(card => {
                card.classList.remove('completed');
            });
            
            document.querySelectorAll('.progress-fill').forEach(bar => {
                bar.style.width = '0%';
            });
            
            document.querySelectorAll('.stat-number').forEach(stat => {
                if (stat.textContent.includes('/')) {
                    stat.textContent = stat.textContent.replace(/^\d+/, '0');
                }
            });
            
            // Show refresh animation
            const floatingAction = document.querySelector('.floating-action');
            floatingAction.style.animation = 'spin 1s linear';
            setTimeout(() => {
                floatingAction.style.animation = '';
            }, 1000);
        }

        // Initialize progress on page load
        document.addEventListener('DOMContentLoaded', function() {
            ['easy', 'medium', 'hard'].forEach(difficulty => {
                updateProgress(difficulty);
            });
        });
    </script>
</body>
</html>

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
                <div class="date-display" id="currentDate"></div>
            </div>
        </div>

        <!-- Today's Entry Display -->
        <div id="entryDisplay">
            <!-- Content will be populated by JavaScript -->
        </div>

        <!-- AI Analysis Section -->
        <div class="ai-analysis">
            <div class="ai-header">
                <div class="ai-icon">🤖</div>
                <div>
                    <h2 class="ai-title">AI Analysis & Insights</h2>
                    <p class="ai-subtitle">Personalized analysis of your today's mood entry</p>
                </div>
            </div>
            
            <div id="aiContent" class="ai-content">
                <!-- AI analysis will be populated here -->
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="mood-tracker.html" class="action-btn btn-primary">
                📝 Track Another Entry
            </a>
            <a href="#" class="action-btn btn-secondary" onclick="shareResults()">
                📤 Share Results
            </a>
        </div>
    </div>

    <script>
        // Activity metadata
        const activityMeta = {
            work: { icon: '💼', name: 'Work/Study' },
            exercise: { icon: '🏃‍♂️', name: 'Exercise' },
            social: { icon: '👥', name: 'Social Time' },
            hobbies: { icon: '🎨', name: 'Hobbies' },
            rest: { icon: '😴', name: 'Rest/Sleep' },
            entertainment: { icon: '📺', name: 'Entertainment' },
            nature: { icon: '🌳', name: 'Nature/Outdoors' },
            food: { icon: '🍽️', name: 'Food/Cooking' },
            health: { icon: '🏥', name: 'Health/Medical' },
            other: { icon: '❓', name: 'Other' }
        };

        // Mood emojis and labels
        const moodData = {
            1: { emoji: '😢', label: 'Very Sad' },
            2: { emoji: '😞', label: 'Sad' },
            3: { emoji: '😔', label: 'Down' },
            4: { emoji: '😐', label: 'Neutral' },
            5: { emoji: '🙂', label: 'Okay' },
            6: { emoji: '😊', label: 'Good' },
            7: { emoji: '😄', label: 'Happy' },
            8: { emoji: '😁', label: 'Very Happy' },
            9: { emoji: '🤩', label: 'Excited' },
            10: { emoji: '🥳', label: 'Euphoric' }
        };

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            displayCurrentDate();
            loadTodaysEntry();
        });

        // Display current date
        function displayCurrentDate() {
            const today = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            document.getElementById('currentDate').textContent = today.toLocaleDateString('en-US', options);
        }

        // Load today's entry
        function loadTodaysEntry() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];
            
            // Get all entries from localStorage
            const allEntries = JSON.parse(localStorage.getItem('moodEntries') || '[]');
            
            // Find today's entry
            const todaysEntry = allEntries.find(entry => {
                const entryDate = new Date(entry.timestamp).toISOString().split('T')[0];
                return entryDate === today;
            });

            if (todaysEntry) {
                displayEntry(todaysEntry);
                generateAIAnalysis(todaysEntry);
            } else {
                showEmptyState();
            }
        }

        // Display the entry
        function displayEntry(entry) {
            const entryDisplay = document.getElementById('entryDisplay');
            const mood = moodData[entry.mood];
            const activity = activityMeta[entry.activity];

            entryDisplay.innerHTML = `
                <div class="entry-card">
                    <div class="entry-header">
                        <div class="mood-display">${mood.emoji}</div>
                        <div class="mood-score">${entry.mood}/10</div>
                        <div class="mood-label">${mood.label}</div>
                    </div>

                    <div class="entry-details">
                        <div class="detail-section">
                            <div class="detail-title">
                                🎯 Main Activity
                            </div>
                            <div class="activity-display">
                                <div class="activity-icon">${activity.icon}</div>
                                <div class="activity-name">${activity.name}</div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <div class="detail-title">
                                ⚡ Energy Levels
                            </div>
                            <div class="energy-metrics">
                                <div class="metric-item">
                                    <div class="metric-icon">⚡</div>
                                    <div class="metric-value">${entry.energy}</div>
                                    <div class="metric-label">Energy</div>
                                </div>
                                <div class="metric-item">
                                    <div class="metric-icon">🎯</div>
                                    <div class="metric-value">${entry.productivity}</div>
                                    <div class="metric-label">Productivity</div>
                                </div>
                            </div>
                        </div>

                        ${entry.explanation ? `
                        <div class="explanation-section">
                            <div class="detail-title">
                                💭 Your Thoughts
                            </div>
                            <div class="explanation-text">
                                "${entry.explanation}"
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        // Generate AI Analysis
        function generateAIAnalysis(entry) {
            const aiContent = document.getElementById('aiContent');
            
            // Show loading state
            aiContent.innerHTML = `
                <div class="ai-loading">
                    <div class="ai-loading-spinner"></div>
                    <span>Analyzing your mood entry...</span>
                </div>
            `;

            // Simulate AI processing delay
            setTimeout(() => {
                const analysis = performTodaysAnalysis(entry);
                displayAIAnalysis(analysis);
            }, 2000);
        }

        // Perform analysis on today's entry
        function performTodaysAnalysis(entry) {
            const mood = moodData[entry.mood];
            const activity = activityMeta[entry.activity];
            
            return {
                entry,
                mood,
                activity,
                moodLevel: entry.mood >= 7 ? 'positive' : entry.mood >= 5 ? 'neutral' : 'concerning',
                energyLevel: entry.energy >= 7 ? 'high' : entry.energy >= 5 ? 'moderate' : 'low',
                productivityLevel: entry.productivity >= 7 ? 'high' : entry.productivity >= 5 ? 'moderate' : 'low',
                energyProductivityBalance: Math.abs(entry.energy - entry.productivity)
            };
        }

        // Display AI analysis
        function displayAIAnalysis(analysis) {
            const aiContent = document.getElementById('aiContent');
            
            const summary = generateTodaysSummary(analysis);
            const insights = generateTodaysInsights(analysis);
            const suggestions = generateTodaysSuggestions(analysis);

            aiContent.innerHTML = `
                <div class="ai-section">
                    <h3 class="ai-section-title">
                        📊 Today's Summary
                    </h3>
                    <div class="ai-section-content">
                        <p>${summary}</p>
                    </div>
                </div>
                
                <div class="ai-section">
                    <h3 class="ai-section-title">
                        🔍 Key Insights
                    </h3>
                    <div class="ai-section-content">
                        ${insights}
                    </div>
                </div>
                
                <div class="ai-section">
                    <h3 class="ai-section-title">
                        💡 Personalized Suggestions
                    </h3>
                    <div class="ai-section-content">
                        ${suggestions}
                    </div>
                </div>
            `;
        }

        // Generate today's summary
        function generateTodaysSummary(analysis) {
            const { entry, mood, activity, moodLevel, energyLevel, productivityLevel } = analysis;
            
            let summaryText = `Today you're feeling ${mood.label.toLowerCase()} with a mood score of ${entry.mood}/10, which indicates a ${moodLevel} emotional state. `;
            
            summaryText += `Your main activity was ${activity.name.toLowerCase()}, and you experienced ${energyLevel} energy levels (${entry.energy}/10) `;
            summaryText += `with ${productivityLevel} productivity (${entry.productivity}/10). `;

            if (analysis.energyProductivityBalance <= 1) {
                summaryText += `Your energy and productivity levels are well-balanced today.`;
            } else if (entry.energy > entry.productivity) {
                summaryText += `You had more energy than productivity today, which might indicate untapped potential.`;
            } else {
                summaryText += `Your productivity exceeded your energy levels, which might suggest you pushed through despite feeling tired.`;
            }

            return summaryText;
        }

        // Generate today's insights
        function generateTodaysInsights(analysis) {
            const { entry, activity, moodLevel, energyLevel, productivityLevel } = analysis;
            const insights = [];

            // Mood-activity correlation
            if (entry.mood >= 7) {
                insights.push(`${activity.name} had a positive impact on your mood today, contributing to your ${moodLevel} emotional state.`);
            } else if (entry.mood <= 4) {
                insights.push(`${activity.name} may have contributed to your lower mood today. Consider what aspects of this activity affected you.`);
            } else {
                insights.push(`${activity.name} resulted in a neutral mood impact today. This activity neither significantly boosted nor lowered your emotional state.`);
            }

            // Energy analysis
            if (entry.energy >= 8) {
                insights.push(`Your high energy levels today (${entry.energy}/10) suggest you're well-rested and physically prepared for activities.`);
            } else if (entry.energy <= 4) {
                insights.push(`Your low energy levels (${entry.energy}/10) might be affecting your overall well-being. Consider factors like sleep, nutrition, or stress.`);
            }

            // Productivity analysis
            if (entry.productivity >= 8) {
                insights.push(`Excellent productivity today (${entry.productivity}/10)! You accomplished a lot and should feel proud of your achievements.`);
            } else if (entry.productivity <= 4) {
                insights.push(`Lower productivity today (${entry.productivity}/10) is normal and happens to everyone. Don't be too hard on yourself.`);
            }

            // Energy-productivity relationship
            if (entry.energy > entry.productivity + 2) {
                insights.push(`You had high energy but lower productivity today. This might indicate distractions or lack of focus rather than physical limitations.`);
            } else if (entry.productivity > entry.energy + 2) {
                insights.push(`You were highly productive despite lower energy levels. This shows great determination, but be mindful not to burn out.`);
            }

            return insights.map(insight => `<p>${insight}</p>`).join('');
        }

        // Generate today's suggestions
        function generateTodaysSuggestions(analysis) {
            const { entry, activity, moodLevel, energyLevel, productivityLevel } = analysis;
            const suggestions = [];

            // Mood-based suggestions
            if (entry.mood <= 4) {
                suggestions.push(`Since you're feeling ${moodLevel} today, consider doing something that usually lifts your spirits - perhaps calling a friend, listening to music, or taking a short walk.`);
                suggestions.push(`If low mood persists, don't hesitate to reach out to a mental health professional or trusted person in your life.`);
            } else if (entry.mood >= 7) {
                suggestions.push(`You're in a great mood today! This is an excellent time to tackle challenging tasks or engage in activities you've been putting off.`);
                suggestions.push(`Consider sharing your positive energy with others - it might brighten their day too.`);
            }

            // Activity-specific suggestions
            if (entry.mood >= 6) {
                suggestions.push(`${activity.name} seems to work well for you today. Consider incorporating more of this activity into your routine when you need a mood boost.`);
            } else if (entry.mood <= 5) {
                suggestions.push(`If ${activity.name.toLowerCase()} contributed to your lower mood, think about ways to make this activity more enjoyable or consider alternative approaches.`);
            }

            // Energy suggestions
            if (entry.energy <= 4) {
                suggestions.push(`Your low energy today suggests you might benefit from prioritizing rest, staying hydrated, and eating nutritious foods.`);
                suggestions.push(`Consider gentle activities like stretching, meditation, or a short walk to naturally boost your energy levels.`);
            } else if (entry.energy >= 8) {
                suggestions.push(`With your high energy levels today, this would be a great time for physical activities or tackling demanding tasks.`);
            }

            // Productivity suggestions
            if (entry.productivity <= 4) {
                suggestions.push(`Lower productivity is completely normal. Focus on small, achievable tasks and celebrate small wins to build momentum.`);
                suggestions.push(`Consider breaking larger tasks into smaller, more manageable pieces to make progress feel more attainable.`);
            }

            // Balance suggestions
            if (analysis.energyProductivityBalance > 2) {
                if (entry.energy > entry.productivity) {
                    suggestions.push(`You have energy to spare today. Consider channeling it into activities that align with your goals or bring you joy.`);
                } else {
                    suggestions.push(`You've been productive despite lower energy. Make sure to rest and recharge to maintain this level of output.`);
                }
            }

            // General suggestions
            suggestions.push(`Continue tracking your mood daily to identify patterns and understand what activities and circumstances affect your well-being most.`);
            
            if (entry.explanation && entry.explanation.trim()) {
                suggestions.push(`Your reflection on today's experience shows good self-awareness. Keep noting these details as they help identify what works best for you.`);
            }

            return suggestions.map(suggestion => `<p>${suggestion}</p>`).join('');
        }

        // Show empty state
        function showEmptyState() {
            const entryDisplay = document.getElementById('entryDisplay');
            entryDisplay.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📝</div>
                    <h3>No Entry for Today</h3>
                    <p>You haven't tracked your mood today yet. Start by recording how you're feeling!</p>
                    <a href="mood-tracker.html" class="empty-state-btn">Track Your Mood</a>
                </div>
            `;

            // Hide AI analysis section
            document.querySelector('.ai-analysis').style.display = 'none';
        }

        // Share results function
        function shareResults() {
            if (navigator.share) {
                navigator.share({
                    title: 'My Mood Tracking Results',
                    text: 'Check out my mood tracking results from Curhatorium!',
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Results link copied to clipboard!');
                });
            }
        }
    </script>
</body>
</html>

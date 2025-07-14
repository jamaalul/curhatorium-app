<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Health Consultation - Curhatorium</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        /* Reset and base styles */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Original color scheme */
            --primary: #8ecbcf;
            --primary-light: #9acbd0;
            --primary-dark: #7ab8bd;
            --secondary: #ffcd2d;
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
            --container-width: 1200px;
        }

        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            color: var(--text-primary);
            line-height: 1.5;
            padding-top: 70px;
        }

        /* Container */
        .container {
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            line-height: 1.2;
        }

        /* Navbar styles (same as before) */
        nav {
            width: 100%;
            height: 70px;
            position: fixed;
            top: 0;
            background-color: var(--white);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            z-index: 50;
            justify-content: space-between;
            box-shadow: var(--shadow);
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

        nav #profile-box {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            background-color: var(--profile-bg);
            height: 46px;
            border-radius: 23px;
            padding-right: 0.25rem;
            color: var(--white);
            font-weight: 500;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
            transition: var(--transition);
        }

        nav #profile-box img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        nav #profile-box #xp-box {
            height: 100%;
            border-radius: 23px;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding-right: 1.25rem;
        }

        nav #profile-box #xp-box #badge-box {
            height: 100%;
            border-radius: 23px;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0 1.25rem 0 0.625rem;
            background-color: var(--secondary);
        }

        /* Hero section */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--text-primary);
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: var(--border-radius-lg);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
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

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
            text-align: center;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        /* Main content */
        .main-content {
            display: block;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Consultation types section */
        .consultation-types {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 2rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .section-title::before,
        .section-title::after {
            content: '';
            display: block;
            width: 4px;
            height: 1.5rem;
            background-color: var(--secondary);
            border-radius: var(--border-radius-sm);
        }

        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .type-card {
            background-color: var(--white);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .type-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-light);
        }

        .type-card.psychiatrist {
            border-left: 4px solid var(--primary);
        }

        .type-card.partner {
            border-left: 4px solid var(--secondary);
        }

        .type-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .type-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .psychiatrist .type-icon {
            background-color: var(--primary-light);
            color: var(--text-primary);
        }

        .partner .type-icon {
            background-color: var(--secondary-light);
            color: var(--text-primary);
        }

        .type-info h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .type-info .subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .type-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .consultation-options {
            margin-bottom: 1.5rem;
        }

        .options-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .option-list {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .option-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .option-badge.chat {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .option-badge.video {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .option-badge.disabled {
            background-color: var(--bg-tertiary);
            color: var(--text-tertiary);
            opacity: 0.6;
        }

        .select-button {
            width: 100%;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .psychiatrist .select-button {
            background-color: var(--primary);
            color: var(--text-primary);
        }

        .psychiatrist .select-button:hover {
            background-color: var(--primary-dark);
        }

        .partner .select-button {
            background-color: var(--secondary);
            color: var(--text-primary);
        }

        .partner .select-button:hover {
            background-color: var(--secondary-dark);
        }

        /* Professionals grid */
        .professionals-section {
            margin-top: 3rem;
        }

        .professionals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .professional-card {
            background-color: var(--white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .professional-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .professional-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid var(--bg-tertiary);
        }

        .professional-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .professional-info h4 {
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
        }

        .professional-title {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .professional-rating {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            color: var(--warning);
        }

        .professional-body {
            padding: 1.5rem;
        }

        .specialties {
            margin-bottom: 1rem;
        }

        .specialties-title {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .specialty-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
        }

        .specialty-tag {
            padding: 0.25rem 0.5rem;
            background-color: var(--bg-secondary);
            border-radius: var(--border-radius-sm);
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .availability {
            margin-bottom: 1.5rem;
        }

        .availability-title {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .availability-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-indicator.online {
            background-color: var(--success);
        }

        .status-indicator.busy {
            background-color: var(--warning);
        }

        .status-indicator.offline {
            background-color: var(--error);
        }

        .consultation-actions {
            display: flex;
            gap: 0.75rem;
        }

        .action-button {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
        }

        .action-button.chat {
            border-color: var(--info);
            color: var(--info);
            background-color: transparent;
        }

        .action-button.chat:hover {
            background-color: var(--info);
            color: var(--white);
        }

        .action-button.video {
            border-color: var(--success);
            color: var(--success);
            background-color: transparent;
        }

        .action-button.video:hover {
            background-color: var(--success);
            color: var(--white);
        }

        .action-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Filter section */
        .filter-section {
            background-color: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .filter-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .filter-options {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .filter-select {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--bg-tertiary);
            border-radius: var(--border-radius);
            background-color: var(--bg-secondary);
            font-size: 0.875rem;
            min-width: 150px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(142, 203, 207, 0.2);
        }

        /* Toast notification */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .toast {
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            background-color: var(--white);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 300px;
            max-width: 400px;
            transform: translateX(120%);
            animation: slide-in 0.3s forwards;
        }

        .toast.hide {
            animation: slide-out 0.3s forwards;
        }

        @keyframes slide-in {
            100% { transform: translateX(0); }
        }

        @keyframes slide-out {
            100% { transform: translateX(120%); }
        }

        .toast-success {
            border-left: 4px solid var(--success);
        }

        .toast-error {
            border-left: 4px solid var(--error);
        }

        .toast-info {
            border-left: 4px solid var(--info);
        }

        .toast-icon {
            width: 1.5rem;
            height: 1.5rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }

        .toast-message {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-tertiary);
            font-size: 1.25rem;
            line-height: 1;
            padding: 0.25rem;
        }

        /* Responsive styles */
        @media (max-width: 767px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .types-grid {
                grid-template-columns: 1fr;
            }
            
            .professionals-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-options {
                flex-direction: column;
            }
            
            .consultation-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.75rem;
            }
            
            .container {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Toast container for notifications -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-content">
                <h1>Mental Health Consultation</h1>
                <p>Connect with licensed psychiatrists or trained mental health partners for professional support and guidance.</p>
            </div>
        </div>

        <div class="main-content">
            <!-- Consultation Types -->
            <div class="consultation-types">
                <h2 class="section-title">Choose Your Consultation Type</h2>
                
                <div class="types-grid">
                    <!-- Psychiatrist Card -->
                    <div class="type-card psychiatrist">
                        <div class="type-header">
                            <div class="type-icon">🩺</div>
                            <div class="type-info">
                                <h3>Licensed Psychiatrist</h3>
                                <p class="subtitle">Medical professionals with specialized training</p>
                            </div>
                        </div>
                        <p class="type-description">
                            Get professional medical consultation from licensed psychiatrists who can provide diagnosis, 
                            treatment plans, and medication management for various mental health conditions.
                        </p>
                        <div class="consultation-options">
                            <p class="options-title">Available Options:</p>
                            <div class="option-list">
                                <span class="option-badge chat">
                                    💬 Text Chat
                                </span>
                                <span class="option-badge video">
                                    📹 Video Call
                                </span>
                            </div>
                        </div>
                        <button class="select-button" onclick="showProfessionals('psychiatrist')">
                            Select Psychiatrist
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Trained Partner Card -->
                    <div class="type-card partner">
                        <div class="type-header">
                            <div class="type-icon">🤝</div>
                            <div class="type-info">
                                <h3>Trained Mental Health Partner</h3>
                                <p class="subtitle">Certified counselors and therapists</p>
                            </div>
                        </div>
                        <p class="type-description">
                            Connect with trained mental health partners who provide supportive counseling, 
                            emotional guidance, and therapeutic conversations to help you navigate life's challenges.
                        </p>
                        <div class="consultation-options">
                            <p class="options-title">Available Options:</p>
                            <div class="option-list">
                                <span class="option-badge chat disabled">
                                    💬 Text Chat (Not Available)
                                </span>
                                <span class="option-badge video">
                                    📹 Video Call
                                </span>
                            </div>
                        </div>
                        <button class="select-button" onclick="showProfessionals('partner')">
                            Select Partner
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Professionals Section -->
            <div class="professionals-section" id="professionals-section" style="display: none;">
                <h2 class="section-title" id="professionals-title">Available Professionals</h2>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <h3 class="filter-title">Filter Professionals</h3>
                    <div class="filter-options">
                        <div class="filter-group">
                            <label class="filter-label">Specialty</label>
                            <select class="filter-select" id="specialty-filter">
                                <option value="">All Specialties</option>
                                <option value="anxiety">Anxiety Disorders</option>
                                <option value="depression">Depression</option>
                                <option value="trauma">Trauma & PTSD</option>
                                <option value="relationships">Relationship Issues</option>
                                <option value="addiction">Addiction</option>
                                <option value="eating">Eating Disorders</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Availability</label>
                            <select class="filter-select" id="availability-filter">
                                <option value="">All</option>
                                <option value="online">Available Now</option>
                                <option value="busy">Busy</option>
                                <option value="offline">Offline</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Rating</label>
                            <select class="filter-select" id="rating-filter">
                                <option value="">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3">3+ Stars</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Professionals Grid -->
                <div class="professionals-grid" id="professionals-grid">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mock data for professionals
        const mockProfessionals = {
            psychiatrist: [
                {
                    id: 1,
                    name: "Dr. Sarah Johnson",
                    title: "Licensed Psychiatrist",
                    avatar: "https://via.placeholder.com/60/8ecbcf/ffffff?text=SJ",
                    rating: 4.9,
                    reviews: 127,
                    specialties: ["Anxiety Disorders", "Depression", "ADHD"],
                    availability: "online",
                    availabilityText: "Available Now",
                    type: "psychiatrist"
                },
                {
                    id: 2,
                    name: "Dr. Michael Chen",
                    title: "Clinical Psychiatrist",
                    avatar: "https://via.placeholder.com/60/8ecbcf/ffffff?text=MC",
                    rating: 4.8,
                    reviews: 89,
                    specialties: ["Trauma & PTSD", "Bipolar Disorder", "Anxiety"],
                    availability: "busy",
                    availabilityText: "Busy - Next available in 2 hours",
                    type: "psychiatrist"
                },
                {
                    id: 3,
                    name: "Dr. Emily Rodriguez",
                    title: "Child & Adult Psychiatrist",
                    avatar: "https://via.placeholder.com/60/8ecbcf/ffffff?text=ER",
                    rating: 4.7,
                    reviews: 156,
                    specialties: ["Depression", "Eating Disorders", "Family Therapy"],
                    availability: "online",
                    availabilityText: "Available Now",
                    type: "psychiatrist"
                }
            ],
            partner: [
                {
                    id: 4,
                    name: "Lisa Thompson",
                    title: "Licensed Counselor",
                    avatar: "https://via.placeholder.com/60/ffcd2d/333333?text=LT",
                    rating: 4.6,
                    reviews: 94,
                    specialties: ["Relationship Issues", "Stress Management", "Life Coaching"],
                    availability: "online",
                    availabilityText: "Available Now",
                    type: "partner"
                },
                {
                    id: 5,
                    name: "James Wilson",
                    title: "Mental Health Therapist",
                    avatar: "https://via.placeholder.com/60/ffcd2d/333333?text=JW",
                    rating: 4.5,
                    reviews: 73,
                    specialties: ["Anxiety", "Career Counseling", "Mindfulness"],
                    availability: "offline",
                    availabilityText: "Offline - Available tomorrow at 9 AM",
                    type: "partner"
                },
                {
                    id: 6,
                    name: "Maria Garcia",
                    title: "Certified Therapist",
                    avatar: "https://via.placeholder.com/60/ffcd2d/333333?text=MG",
                    rating: 4.8,
                    reviews: 112,
                    specialties: ["Trauma Recovery", "Women's Issues", "Self-Esteem"],
                    availability: "busy",
                    availabilityText: "Busy - Next available in 1 hour",
                    type: "partner"
                }
            ]
        };

        let currentProfessionalType = '';
        let filteredProfessionals = [];

        // Toast notification system
        const Toast = {
            create: function(type, title, message, duration = 5000) {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                
                let iconSvg = '';
                if (type === 'success') {
                    iconSvg = `<svg class="toast-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10b981"><path d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"/></svg>`;
                } else if (type === 'error') {
                    iconSvg = `<svg class="toast-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ef4444"><path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"/></svg>`;
                } else if (type === 'info') {
                    iconSvg = `<svg class="toast-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#3b82f6"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>`;
                }
                
                toast.innerHTML = `
                    ${iconSvg}
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button class="toast-close">&times;</button>
                `;
                
                document.getElementById('toast-container').appendChild(toast);
                
                // Add event listener to close button
                toast.querySelector('.toast-close').addEventListener('click', function() {
                    toast.classList.add('hide');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                });
                
                // Auto remove after duration
                setTimeout(() => {
                    toast.classList.add('hide');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, duration);
                
                return toast;
            },
            success: function(title, message, duration) {
                return this.create('success', title, message, duration);
            },
            error: function(title, message, duration) {
                return this.create('error', title, message, duration);
            },
            info: function(title, message, duration) {
                return this.create('info', title, message, duration);
            }
        };

        // Show professionals based on type
        function showProfessionals(type) {
            currentProfessionalType = type;
            filteredProfessionals = mockProfessionals[type];
            
            const section = document.getElementById('professionals-section');
            const title = document.getElementById('professionals-title');
            
            if (type === 'psychiatrist') {
                title.textContent = 'Available Psychiatrists';
            } else {
                title.textContent = 'Available Mental Health Partners';
            }
            
            section.style.display = 'block';
            renderProfessionals(filteredProfessionals);
            
            // Smooth scroll to professionals section
            section.scrollIntoView({ behavior: 'smooth' });
        }

        // Render professionals grid
        function renderProfessionals(professionals) {
            const grid = document.getElementById('professionals-grid');
            
            if (professionals.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--text-secondary);">
                        No professionals found matching your criteria.
                    </div>
                `;
                return;
            }
            
            grid.innerHTML = professionals.map(professional => `
                <div class="professional-card">
                    <div class="professional-header">
                        <img src="${professional.avatar}" alt="${professional.name}" class="professional-avatar">
                        <div class="professional-info">
                            <h4>${professional.name}</h4>
                            <p class="professional-title">${professional.title}</p>
                            <div class="professional-rating">
                                ${'★'.repeat(Math.floor(professional.rating))} ${professional.rating} (${professional.reviews} reviews)
                            </div>
                        </div>
                    </div>
                    <div class="professional-body">
                        <div class="specialties">
                            <p class="specialties-title">Specialties:</p>
                            <div class="specialty-tags">
                                ${professional.specialties.map(specialty => `<span class="specialty-tag">${specialty}</span>`).join('')}
                            </div>
                        </div>
                        <div class="availability">
                            <p class="availability-title">Availability:</p>
                            <div class="availability-status">
                                <span class="status-indicator ${professional.availability}"></span>
                                ${professional.availabilityText}
                            </div>
                        </div>
                        <div class="consultation-actions">
                            ${professional.type === 'psychiatrist' ? `
                                <button class="action-button chat" onclick="startConsultation(${professional.id}, 'chat')" ${professional.availability !== 'online' ? 'disabled' : ''}>
                                    💬 Chat
                                </button>
                            ` : ''}
                            <button class="action-button video" onclick="startConsultation(${professional.id}, 'video')" ${professional.availability !== 'online' ? 'disabled' : ''}>
                                📹 Video Call
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Start consultation
        function startConsultation(professionalId, type) {
            const professional = [...mockProfessionals.psychiatrist, ...mockProfessionals.partner]
                .find(p => p.id === professionalId);
            
            if (!professional) {
                Toast.error('Error', 'Professional not found.');
                return;
            }
            
            if (professional.availability !== 'online') {
                Toast.error('Unavailable', `${professional.name} is currently not available.`);
                return;
            }
            
            // Simulate starting consultation
            const consultationType = type === 'chat' ? 'chat session' : 'video call';
            Toast.success('Consultation Started', `Starting ${consultationType} with ${professional.name}...`);
            
            // In a real application, this would redirect to the consultation interface
            setTimeout(() => {
                Toast.info('Connecting', 'Please wait while we connect you...');
            }, 2000);
        }

        // Filter functionality
        function applyFilters() {
            const specialtyFilter = document.getElementById('specialty-filter').value;
            const availabilityFilter = document.getElementById('availability-filter').value;
            const ratingFilter = document.getElementById('rating-filter').value;
            
            let filtered = mockProfessionals[currentProfessionalType];
            
            if (specialtyFilter) {
                filtered = filtered.filter(p => 
                    p.specialties.some(s => s.toLowerCase().includes(specialtyFilter.toLowerCase()))
                );
            }
            
            if (availabilityFilter) {
                filtered = filtered.filter(p => p.availability === availabilityFilter);
            }
            
            if (ratingFilter) {
                const minRating = parseFloat(ratingFilter);
                filtered = filtered.filter(p => p.rating >= minRating);
            }
            
            filteredProfessionals = filtered;
            renderProfessionals(filtered);
        }

        // Event listeners for filters
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('specialty-filter').addEventListener('change', applyFilters);
            document.getElementById('availability-filter').addEventListener('change', applyFilters);
            document.getElementById('rating-filter').addEventListener('change', applyFilters);
        });
    </script>
</body>
</html>

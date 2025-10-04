<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tes Kesehatan Mental - Curhatorium</title>
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

        /* Container */
        .container {
            max-width: var(--container-width);
            margin: 0 auto;
            padding: 1.5rem;
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
        .main-content {
            background: var(--white);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(142, 203, 207, 0.1);
            margin-bottom: 2rem;
        }

        /* Intro Section */
        .intro-section {
            background: var(--bg-secondary);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        .intro-section h3 {
            color: var(--primary-dark);
            margin-bottom: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .intro-section p {
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .intro-section ul {
            margin: 1rem 0 1rem 1.5rem;
            color: var(--text-secondary);
        }

        .intro-section li {
            margin-bottom: 0.5rem;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 1.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Question Styles */
        .question {
            background: var(--bg-secondary);
            border: 2px solid var(--bg-tertiary);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .question:hover {
            border-color: var(--primary-light);
            background: var(--white);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .question-text {
            font-size: 1rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .question-number {
            color: var(--primary-dark);
            font-weight: 700;
            margin-right: 0.5rem;
        }

        /* Likert Scale */
        .likert-scale {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .likert-option {
            position: relative;
        }

        .likert-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .likert-label {
            display: block;
            padding: 0.875rem 0.5rem;
            background: var(--white);
            border: 2px solid var(--bg-tertiary);
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .likert-option input[type="radio"]:checked + .likert-label {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .likert-label:hover {
            border-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .scale-value {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .scale-text {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        /* Submit Button */
        .submit-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: var(--border-radius-lg);
            cursor: pointer;
            transition: var(--transition);
            display: block;
            margin: 2rem auto;
            min-width: 200px;
            box-shadow: var(--shadow);
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Results Section */
        .results {
            display: none;
            background: var(--white);
            border-radius: var(--border-radius-xl);
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(142, 203, 207, 0.1);
        }

        .results.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .results h3 {
            color: var(--primary-dark);
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 700;
        }

        /* Score Grid */
        .score-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .score-card {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            text-align: center;
            border: 1px solid var(--bg-tertiary);
            transition: var(--transition);
        }

        .score-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .score-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .score-label {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .score-max {
            color: var(--text-tertiary);
            font-size: 0.875rem;
        }

        /* Category Result */
        .category-result {
            background: var(--bg-secondary);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid var(--bg-tertiary);
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .category-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .flourishing {
            border-left: 4px solid var(--success);
        }

        .flourishing .category-title {
            color: var(--success);
        }

        .moderate {
            border-left: 4px solid var(--warning);
        }

        .moderate .category-title {
            color: var(--warning);
        }

        .languishing {
            border-left: 4px solid var(--error);
        }

        .languishing .category-title {
            color: var(--error);
        }

        /* Suggestions */
        .suggestion {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            margin-top: 1rem;
            border: 1px solid var(--bg-tertiary);
        }

        .suggestion h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .suggestion ul {
            margin-left: 1.5rem;
            color: var(--text-secondary);
        }

        .suggestion li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        /* Reset Button */
        .reset-btn {
            background: var(--text-tertiary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-lg);
            cursor: pointer;
            margin-top: 1rem;
            transition: var(--transition);
            font-weight: 600;
        }

        .reset-btn:hover {
            background: var(--text-secondary);
            transform: translateY(-1px);
        }

        /* Disclaimer */
        .disclaimer {
            background: var(--white);
            border: 1px solid var(--secondary-light);
            border-left: 4px solid var(--secondary-color);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .disclaimer h4 {
            color: var(--secondary-dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .disclaimer p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
            line-height: 1.6;
        }

        .disclaimer p:last-child {
            margin-bottom: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .page-header p {
                font-size: 1rem;
            }

            .main-content {
                padding: 1.5rem;
            }

            .intro-section {
                padding: 1.5rem;
            }

            .likert-scale {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            .likert-label {
                padding: 0.75rem 0.25rem;
                font-size: 0.8rem;
            }

            .score-grid {
                grid-template-columns: 1fr;
            }

            .question {
                padding: 1rem;
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

            .main-content {
                padding: 1rem;
            }

            .intro-section {
                padding: 1rem;
            }

            .section-title {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }

            .submit-btn {
                width: 100%;
                min-width: auto;
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
                <h1>Skala Kesejahteraan Mental</h1>
                <p>Evaluasi kesejahteraan mental Anda berdasarkan<br>Health Continuum - Short Form (MHC-SF)</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Intro Section -->
            <div class="intro-section">
                <h3>📋 Tentang Tes Ini</h3>
                <p><strong>Mental Health Continuum - Short Form (MHC-SF)</strong> adalah alat penilaian yang dikembangkan oleh Keyes (2009) untuk mengukur kesejahteraan mental dalam tiga aspek:</p>
                <ul>
                    <li><strong>Kesejahteraan Emosional</strong> - Perasaan bahagia dan kepuasan hidup</li>
                    <li><strong>Kesejahteraan Sosial</strong> - Hubungan dengan masyarakat dan kontribusi sosial</li>
                    <li><strong>Kesejahteraan Psikologis</strong> - Pertumbuhan pribadi dan makna hidup</li>
                </ul>
                <p>Tes ini terdiri dari 14 pertanyaan yang akan membantu Anda memahami kondisi kesehatan mental saat ini.</p>
            </div>

            <form id="mhcForm">
                <!-- Emotional Well-being Section -->
                <div class="form-section">
                    <div class="section-title">
                        😊 Kesejahteraan Emosional
                    </div>
                    
                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">1.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa <strong>bahagia</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q1_0" name="q1" value="0" required>
                                <label for="q1_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q1_1" name="q1" value="1">
                                <label for="q1_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q1_2" name="q1" value="2">
                                <label for="q1_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q1_3" name="q1" value="3">
                                <label for="q1_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q1_4" name="q1" value="4">
                                <label for="q1_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q1_5" name="q1" value="5">
                                <label for="q1_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">2.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa <strong>tertarik pada kehidupan</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q2_0" name="q2" value="0" required>
                                <label for="q2_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q2_1" name="q2" value="1">
                                <label for="q2_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q2_2" name="q2" value="2">
                                <label for="q2_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q2_3" name="q2" value="3">
                                <label for="q2_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q2_4" name="q2" value="4">
                                <label for="q2_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q2_5" name="q2" value="5">
                                <label for="q2_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">3.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa <strong>puas terhadap hidup</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q3_0" name="q3" value="0" required>
                                <label for="q3_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q3_1" name="q3" value="1">
                                <label for="q3_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q3_2" name="q3" value="2">
                                <label for="q3_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q3_3" name="q3" value="3">
                                <label for="q3_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q3_4" name="q3" value="4">
                                <label for="q3_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q3_5" name="q3" value="5">
                                <label for="q3_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Well-being Section -->
                <div class="form-section">
                    <div class="section-title">
                        🤝 Kesejahteraan Sosial
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">4.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>memiliki sesuatu yang penting untuk dikontribusikan kepada masyarakat</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q4_0" name="q4" value="0" required>
                                <label for="q4_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q4_1" name="q4" value="1">
                                <label for="q4_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q4_2" name="q4" value="2">
                                <label for="q4_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q4_3" name="q4" value="3">
                                <label for="q4_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q4_4" name="q4" value="4">
                                <label for="q4_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q4_5" name="q4" value="5">
                                <label for="q4_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">5.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>adalah bagian dari komunitas</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q5_0" name="q5" value="0" required>
                                <label for="q5_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q5_1" name="q5" value="1">
                                <label for="q5_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q5_2" name="q5" value="2">
                                <label for="q5_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q5_3" name="q5" value="3">
                                <label for="q5_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q5_4" name="q5" value="4">
                                <label for="q5_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q5_5" name="q5" value="5">
                                <label for="q5_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">6.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa <strong>masyarakat menjadi tempat yang lebih baik berkat Anda</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q6_0" name="q6" value="0" required>
                                <label for="q6_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q6_1" name="q6" value="1">
                                <label for="q6_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q6_2" name="q6" value="2">
                                <label for="q6_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q6_3" name="q6" value="3">
                                <label for="q6_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q6_4" name="q6" value="4">
                                <label for="q6_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q6_5" name="q6" value="5">
                                <label for="q6_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">7.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa <strong>percaya kepada orang lain di masyarakat</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q7_0" name="q7" value="0" required>
                                <label for="q7_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q7_1" name="q7" value="1">
                                <label for="q7_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q7_2" name="q7" value="2">
                                <label for="q7_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q7_3" name="q7" value="3">
                                <label for="q7_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q7_4" name="q7" value="4">
                                <label for="q7_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q7_5" name="q7" value="5">
                                <label for="q7_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">8.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa <strong>masyarakat memiliki arah atau tujuan yang jelas</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q8_0" name="q8" value="0" required>
                                <label for="q8_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q8_1" name="q8" value="1">
                                <label for="q8_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q8_2" name="q8" value="2">
                                <label for="q8_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q8_3" name="q8" value="3">
                                <label for="q8_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q8_4" name="q8" value="4">
                                <label for="q8_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q8_5" name="q8" value="5">
                                <label for="q8_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Psychological Well-being Section -->
                <div class="form-section">
                    <div class="section-title">
                        🌱 Kesejahteraan Psikologis
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">9.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>tumbuh dan berkembang sebagai pribadi</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q9_0" name="q9" value="0" required>
                                <label for="q9_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q9_1" name="q9" value="1">
                                <label for="q9_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q9_2" name="q9" value="2">
                                <label for="q9_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q9_3" name="q9" value="3">
                                <label for="q9_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q9_4" name="q9" value="4">
                                <label for="q9_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q9_5" name="q9" value="5">
                                <label for="q9_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">10.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>memiliki hubungan hangat dan penuh kasih</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q10_0" name="q10" value="0" required>
                                <label for="q10_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q10_1" name="q10" value="1">
                                <label for="q10_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q10_2" name="q10" value="2">
                                <label for="q10_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q10_3" name="q10" value="3">
                                <label for="q10_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q10_4" name="q10" value="4">
                                <label for="q10_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q10_5" name="q10" value="5">
                                <label for="q10_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">11.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>memiliki kehidupan yang bermakna</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q11_0" name="q11" value="0" required>
                                <label for="q11_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q11_1" name="q11" value="1">
                                <label for="q11_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q11_2" name="q11" value="2">
                                <label for="q11_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q11_3" name="q11" value="3">
                                <label for="q11_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q11_4" name="q11" value="4">
                                <label for="q11_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q11_5" name="q11" value="5">
                                <label for="q11_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">12.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>bisa menangani tanggung jawab sehari-hari</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q12_0" name="q12" value="0" required>
                                <label for="q12_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q12_1" name="q12" value="1">
                                <label for="q12_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q12_2" name="q12" value="2">
                                <label for="q12_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q12_3" name="q12" value="3">
                                <label for="q12_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q12_4" name="q12" value="4">
                                <label for="q12_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q12_5" name="q12" value="5">
                                <label for="q12_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">13.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>mampu mengelola waktu dan tantangan hidup</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q13_0" name="q13" value="0" required>
                                <label for="q13_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q13_1" name="q13" value="1">
                                <label for="q13_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q13_2" name="q13" value="2">
                                <label for="q13_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q13_3" name="q13" value="3">
                                <label for="q13_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q13_4" name="q13" value="4">
                                <label for="q13_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q13_5" name="q13" value="5">
                                <label for="q13_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-text">
                            <span class="question-number">14.</span>
                            Dalam sebulan terakhir, seberapa sering Anda merasa bahwa Anda <strong>percaya pada diri sendiri</strong>?
                        </div>
                        <div class="likert-scale">
                            <div class="likert-option">
                                <input type="radio" id="q14_0" name="q14" value="0" required>
                                <label for="q14_0" class="likert-label">
                                    <div class="scale-value">0</div>
                                    <div class="scale-text">Tidak Pernah</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q14_1" name="q14" value="1">
                                <label for="q14_1" class="likert-label">
                                    <div class="scale-value">1</div>
                                    <div class="scale-text">1-2 kali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q14_2" name="q14" value="2">
                                <label for="q14_2" class="likert-label">
                                    <div class="scale-value">2</div>
                                    <div class="scale-text">Seminggu sekali</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q14_3" name="q14" value="3">
                                <label for="q14_3" class="likert-label">
                                    <div class="scale-value">3</div>
                                    <div class="scale-text">2-3x seminggu</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q14_4" name="q14" value="4">
                                <label for="q14_4" class="likert-label">
                                    <div class="scale-value">4</div>
                                    <div class="scale-text">Hampir setiap hari</div>
                                </label>
                            </div>
                            <div class="likert-option">
                                <input type="radio" id="q14_5" name="q14" value="5">
                                <label for="q14_5" class="likert-label">
                                    <div class="scale-value">5</div>
                                    <div class="scale-text">Setiap hari</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Analisis Hasil Tes
                </button>
            </form>
        </div>

        <!-- Results Section -->
        <div id="results" class="results">
            <h3>🎯 Hasil Tes Kesehatan Mental Anda</h3>
            
            <div class="score-grid">
                <div class="score-card">
                    <div class="score-value" id="totalScore">0</div>
                    <div class="score-label">Skor Total</div>
                    <div class="score-max">(Maksimal: 70)</div>
                </div>
                <div class="score-card">
                    <div class="score-value" id="emotionalScore">0</div>
                    <div class="score-label">Kesejahteraan Emosional</div>
                    <div class="score-max">(Maksimal: 15)</div>
                </div>
                <div class="score-card">
                    <div class="score-value" id="socialScore">0</div>
                    <div class="score-label">Kesejahteraan Sosial</div>
                    <div class="score-max">(Maksimal: 25)</div>
                </div>
                <div class="score-card">
                    <div class="score-value" id="psychologicalScore">0</div>
                    <div class="score-label">Kesejahteraan Psikologis</div>
                    <div class="score-max">(Maksimal: 30)</div>
                </div>
            </div>

            <div id="categoryResult" class="category-result">
                <div class="category-title" id="categoryTitle">Moderate Mental Health</div>
                <p class="category-description" id="categoryDescription">Deskripsi kategori akan muncul di sini.</p>
            </div>

            <div class="suggestion">
                <h4>💡 Saran untuk Anda:</h4>
                <div id="suggestions">
                    <p>Saran akan muncul berdasarkan hasil tes Anda.</p>
                </div>
            </div>

            <button type="button" class="reset-btn" onclick="resetTest()">
                Ulangi Tes
            </button>
        </div>

        <!-- Disclaimer -->
        <div class="disclaimer">
            <h4>⚠️ Penting untuk Diketahui</h4>
            <p><strong>Tes ini bukan alat diagnosis medis.</strong> Ini adalah self-assessment berdasarkan Mental Health Continuum - Short Form (MHC-SF) yang dikembangkan oleh Keyes (2009). Hasil tes ini hanya memberikan gambaran umum tentang kesejahteraan mental Anda saat ini.</p>
            <p>Jika Anda mengalami masalah kesehatan mental yang serius atau berkelanjutan, sangat disarankan untuk berkonsultasi dengan profesional kesehatan mental seperti psikolog atau psikiater.</p>
        </div>
    </div>

    <script>
        document.getElementById('mhcForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitTestResult();
        });

        async function submitTestResult() {
            const formData = new FormData(document.getElementById('mhcForm'));
            // Prepare answers array
            let answers = {};
            for (let i = 1; i <= 14; i++) {
                answers[`q${i}`] = formData.get(`q${i}`);
            }

            // Calculate scores
            let totalScore = 0;
            let emotionalScore = 0;
            let socialScore = 0;
            let psychologicalScore = 0;

            for (let i = 1; i <= 3; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                emotionalScore += value;
                totalScore += value;
            }
            for (let i = 4; i <= 8; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                socialScore += value;
                totalScore += value;
            }
            for (let i = 9; i <= 14; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                psychologicalScore += value;
                totalScore += value;
            }

            // Determine category
            const category = determineCategory(formData);

            // Prepare payload
            const payload = {
                answers: answers,
                total_score: totalScore,
                emotional_score: emotionalScore,
                social_score: socialScore,
                psychological_score: psychologicalScore,
                category: category
            };

            try {
                const response = await fetch('/mental-test/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.success) {
                    calculateResults(); // Show results as before
                } else {
                    alert('Gagal menyimpan hasil tes. Silakan coba lagi.');
                }
            } catch (error) {
                alert('Terjadi kesalahan saat mengirim hasil tes. Silakan coba lagi.');
            }
        }

        function calculateResults() {
            const formData = new FormData(document.getElementById('mhcForm'));
            
            // Calculate scores
            let totalScore = 0;
            let emotionalScore = 0;
            let socialScore = 0;
            let psychologicalScore = 0;
            
            // Emotional well-being (questions 1-3)
            for (let i = 1; i <= 3; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                emotionalScore += value;
                totalScore += value;
            }
            
            // Social well-being (questions 4-8)
            for (let i = 4; i <= 8; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                socialScore += value;
                totalScore += value;
            }
            
            // Psychological well-being (questions 9-14)
            for (let i = 9; i <= 14; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                psychologicalScore += value;
                totalScore += value;
            }
            
            // Determine category
            const category = determineCategory(formData);
            
            // Display results
            displayResults(totalScore, emotionalScore, socialScore, psychologicalScore, category);
        }

        function determineCategory(formData) {
            // Check for Flourishing
            let emotionalHigh = 0;
            for (let i = 1; i <= 3; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                if (value >= 4) emotionalHigh++;
            }
            
            let socialPsychHigh = 0;
            for (let i = 4; i <= 14; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                if (value >= 4) socialPsychHigh++;
            }
            
            if (emotionalHigh >= 1 && socialPsychHigh >= 6) {
                return 'flourishing';
            }
            
            // Check for Languishing
            let emotionalLow = 0;
            for (let i = 1; i <= 3; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                if (value <= 1) emotionalLow++;
            }
            
            let socialPsychLow = 0;
            for (let i = 4; i <= 14; i++) {
                const value = parseInt(formData.get(`q${i}`)) || 0;
                if (value <= 1) socialPsychLow++;
            }
            
            if (emotionalLow >= 1 && socialPsychLow >= 6) {
                return 'languishing';
            }
            
            return 'moderate';
        }

        function displayResults(total, emotional, social, psychological, category) {
            // Update scores
            document.getElementById('totalScore').textContent = total;
            document.getElementById('emotionalScore').textContent = emotional;
            document.getElementById('socialScore').textContent = social;
            document.getElementById('psychologicalScore').textContent = psychological;
            
            // Update category
            const categoryResult = document.getElementById('categoryResult');
            const categoryTitle = document.getElementById('categoryTitle');
            const categoryDescription = document.getElementById('categoryDescription');
            const suggestions = document.getElementById('suggestions');
            
            // Remove existing classes
            categoryResult.classList.remove('flourishing', 'moderate', 'languishing');
            
            if (category === 'flourishing') {
                categoryResult.classList.add('flourishing');
                categoryTitle.textContent = '🌟 Flourishing (Berkembang Optimal)';
                categoryDescription.textContent = 'Selamat! Anda berada dalam kondisi kesehatan mental yang sangat baik. Anda merasakan tingkat kebahagiaan, kepuasan hidup, dan fungsi psikologis yang tinggi.';
                suggestions.innerHTML = `
                    <ul>
                        <li>Pertahankan rutinitas positif yang sudah Anda jalani</li>
                        <li>Berbagi pengalaman positif dengan orang lain</li>
                        <li>Tetap terbuka untuk pertumbuhan dan tantangan baru</li>
                        <li>Jadilah mentor atau dukungan bagi orang lain</li>
                        <li>Lanjutkan aktivitas yang memberikan makna dalam hidup</li>
                    </ul>
                `;
            } else if (category === 'languishing') {
                categoryResult.classList.add('languishing');
                categoryTitle.textContent = '🥀 Languishing (Kurang Berkembang)';
                categoryDescription.textContent = 'Anda mungkin merasa "kosong" atau kurang bersemangat dalam hidup. Ini bukan depresi, tetapi juga bukan kondisi mental yang optimal.';
                suggestions.innerHTML = `
                    <ul>
                        <li>Mulai dengan aktivitas kecil yang memberikan kepuasan</li>
                        <li>Cari dukungan dari keluarga, teman, atau profesional</li>
                        <li>Tetapkan tujuan kecil yang dapat dicapai setiap hari</li>
                        <li>Lakukan aktivitas fisik ringan secara teratur</li>
                        <li>Pertimbangkan untuk berkonsultasi dengan psikolog</li>
                        <li>Fokus pada membangun satu hubungan yang bermakna</li>
                    </ul>
                `;
            } else {
                categoryResult.classList.add('moderate');
                categoryTitle.textContent = '⚖️ Moderate Mental Health (Kesehatan Mental Sedang)';
                categoryDescription.textContent = 'Anda berada dalam kondisi kesehatan mental yang cukup stabil, namun masih ada ruang untuk perbaikan dalam beberapa aspek kehidupan.';
                suggestions.innerHTML = `
                    <ul>
                        <li>Identifikasi area yang ingin Anda tingkatkan</li>
                        <li>Bangun rutinitas harian yang konsisten</li>
                        <li>Perkuat hubungan sosial yang sudah ada</li>
                        <li>Cari aktivitas yang memberikan rasa pencapaian</li>
                        <li>Praktikkan mindfulness atau meditasi</li>
                        <li>Tetap aktif secara fisik dan sosial</li>
                    </ul>
                `;
            }
            
            // Show results
            document.getElementById('results').classList.add('show');
            document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
        }

        function resetTest() {
            document.getElementById('mhcForm').reset();
            document.getElementById('results').classList.remove('show');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Add form validation
        document.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', function() {
                // Add visual feedback when option is selected
                const question = this.closest('.question');
                question.style.borderColor = 'var(--primary-color)';
                question.style.backgroundColor = 'var(--white)';
                
                setTimeout(() => {
                    question.style.backgroundColor = 'var(--bg-secondary)';
                }, 300);
            });
        });

        // Progress tracking
        function updateProgress() {
            const totalQuestions = 14;
            let answeredQuestions = 0;
            
            for (let i = 1; i <= totalQuestions; i++) {
                const radios = document.querySelectorAll(`input[name="q${i}"]`);
                const isAnswered = Array.from(radios).some(radio => radio.checked);
                if (isAnswered) answeredQuestions++;
            }
            
            const progress = (answeredQuestions / totalQuestions) * 100;
            const submitBtn = document.querySelector('.submit-btn');
            
            if (answeredQuestions === totalQuestions) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Analisis Hasil Tes';
            } else {
                submitBtn.disabled = true;
                submitBtn.textContent = `Jawab ${totalQuestions - answeredQuestions} pertanyaan lagi`;
            }
        }

        // Add progress tracking to all radio buttons
        document.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener('change', updateProgress);
        });

        // Initialize progress on page load
        updateProgress();
    </script>
</body>
</html>

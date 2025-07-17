<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood & Productivity Tracker - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tracker/index.css') }}">
    <style>
    </style>
</head>
<body>
    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Page Header -->
        <div class="page-header" style="padding: 3.5rem 0;">
            <div class="page-header-content" style="padding: 0 2rem;">
                <h1 style="font-size: 2.25rem;">Mood & Productivity Tracker</h1>
                <p style="font-size: 1.125rem;">
                    Track your daily mood, activities, and energy levels to better understand your mental health patterns and improve your well-being.
                </p>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="progress-step active" id="step1"></div>
            <div class="progress-step" id="step2"></div>
            <div class="progress-step" id="step3"></div>
            <div class="progress-step" id="step4"></div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="form-card">
                <form id="moodTrackingForm" method="POST" action="{{ route('tracker.entry') }}">
                    @csrf
                    <!-- 1. Mood Scale -->
                    <div class="form-section">
                        <h2 class="section-title">How are you feeling today?</h2>
                        <p class="section-description">
                            Select the emoji that best represents your overall mood today. This helps us understand your emotional state.
                        </p>
                        <div class="mood-scale">
                            @for ($i = 1; $i <= 10; $i++)
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
                                @endphp
                                <label class="mood-option" data-mood="{{ $i }}" style="cursor:pointer;">
                                    <input type="radio" name="mood" value="{{ $i }}" style="display:none;">
                                    <div class="mood-emoji">{{ $moods[$i]['emoji'] }}</div>
                                    <div class="mood-label">{{ $moods[$i]['label'] }}</div>
                                    <div class="mood-number">{{ $i }}</div>
                                </label>
                            @endfor
                        </div>
                    </div>

                    <!-- 2. Activity Selection -->
                    <div class="form-section">
                        <h2 class="section-title">What activity contributed most to this mood?</h2>
                        <p class="section-description">
                            Choose the main activity or experience that influenced your mood today. This helps identify patterns in your daily life.
                        </p>
                        <div class="activity-grid">
                            @php
                                $activities = [
                                    'work' => ['icon' => '💼', 'name' => 'Work/Study', 'desc' => 'Job tasks, meetings, studying'],
                                    'exercise' => ['icon' => '🏃‍♂️', 'name' => 'Exercise', 'desc' => 'Gym, running, sports, yoga'],
                                    'social' => ['icon' => '👥', 'name' => 'Social Time', 'desc' => 'Friends, family, social events'],
                                    'hobbies' => ['icon' => '🎨', 'name' => 'Hobbies', 'desc' => 'Creative activities, interests'],
                                    'rest' => ['icon' => '😴', 'name' => 'Rest/Sleep', 'desc' => 'Relaxation, napping, sleeping'],
                                    'entertainment' => ['icon' => '📺', 'name' => 'Entertainment', 'desc' => 'Movies, games, reading'],
                                    'nature' => ['icon' => '🌳', 'name' => 'Nature/Outdoors', 'desc' => 'Walking, hiking, fresh air'],
                                    'food' => ['icon' => '🍽️', 'name' => 'Food/Cooking', 'desc' => 'Meals, cooking, dining out'],
                                    'health' => ['icon' => '🏥', 'name' => 'Health/Medical', 'desc' => 'Doctor visits, health care'],
                                    'other' => ['icon' => '❓', 'name' => 'Other', 'desc' => 'Something else entirely'],
                                ];
                            @endphp
                            @foreach ($activities as $key => $activity)
                                <label class="activity-option" data-activity="{{ $key }}" style="cursor:pointer;">
                                    <input type="radio" name="activity" value="{{ $key }}" style="display:none;">
                                    <div class="activity-icon">{{ $activity['icon'] }}</div>
                                    <div class="activity-info">
                                        <div class="activity-name">{{ $activity['name'] }}</div>
                                        <div class="activity-description">{{ $activity['desc'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Activity Explanation -->
                    <div class="form-section">
                        <h2 class="section-title">Tell us more about this activity</h2>
                        <p class="section-description">
                            Describe what specifically happened during this activity that influenced your mood. Be as detailed as you'd like.
                        </p>
                        <div class="form-group">
                            <label for="activityExplanation" class="form-label">Activity Details</label>
                            <textarea 
                                id="activityExplanation" 
                                name="activityExplanation" 
                                class="form-textarea"
                                placeholder="Describe what happened during this activity that affected your mood. For example: 'Had a great workout session at the gym, felt energized and accomplished after completing my routine' or 'Difficult meeting at work with challenging feedback, felt stressed and overwhelmed afterwards'..."
                                maxlength="500"
                            ></textarea>
                            <div style="text-align: right; font-size: 0.875rem; color: var(--text-tertiary); margin-top: 0.5rem;">
                                <span id="charCount">0</span>/500 characters
                            </div>
                        </div>
                    </div>

                    <!-- 4. Energy Sliders -->
                    <div class="form-section">
                        <h2 class="section-title">How did this activity affect your energy?</h2>
                        <p class="section-description">
                            Rate your energy and productivity levels during and after this activity. This helps us understand how different activities impact your well-being.
                        </p>
                        <div class="slider-container">
                            <div class="slider-group">
                                <div class="slider-label">
                                    <div class="slider-title">
                                        ⚡ Energy Level
                                        <span style="font-size: 0.875rem; font-weight: 400; color: var(--text-secondary);">(How energetic did you feel?)</span>
                                    </div>
                                    <div class="slider-value" id="energyValue">5</div>
                                </div>
                                <div class="slider-wrapper">
                                    <input type="range" id="energySlider" class="slider" min="1" max="10" value="5" name="energy">
                                    <div class="slider-track" id="energyTrack"></div>
                                </div>
                                <div class="slider-labels">
                                    <span>Very Low</span>
                                    <span>Low</span>
                                    <span>Moderate</span>
                                    <span>High</span>
                                    <span>Very High</span>
                                </div>
                            </div>

                            <div class="slider-group">
                                <div class="slider-label">
                                    <div class="slider-title">
                                        🎯 Productivity Level
                                        <span style="font-size: 0.875rem; font-weight: 400; color: var(--text-secondary);">(How productive were you?)</span>
                                    </div>
                                    <div class="slider-value" id="productivityValue">5</div>
                                </div>
                                <div class="slider-wrapper">
                                    <input type="range" id="productivitySlider" class="slider" min="1" max="10" value="5" name="productivity">
                                    <div class="slider-track" id="productivityTrack"></div>
                                </div>
                                <div class="slider-labels">
                                    <span>Very Low</span>
                                    <span>Low</span>
                                    <span>Moderate</span>
                                    <span>High</span>
                                    <span>Very High</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="submit-section">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        // Form state
        let formData = {
            mood: null,
            activity: null,
            explanation: '',
            energy: 5,
            productivity: 5,
            timestamp: null
        };

        // DOM elements
        const moodOptions = document.querySelectorAll('.mood-option');
        const activityOptions = document.querySelectorAll('.activity-option');
        const explanationTextarea = document.getElementById('activityExplanation');
        const charCount = document.getElementById('charCount');
        const energySlider = document.getElementById('energySlider');
        const productivitySlider = document.getElementById('productivitySlider');
        const energyValue = document.getElementById('energyValue');
        const productivityValue = document.getElementById('productivityValue');
        const energyTrack = document.getElementById('energyTrack');
        const productivityTrack = document.getElementById('productivityTrack');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('moodTrackingForm');

        // Progress steps
        const progressSteps = {
            step1: document.getElementById('step1'),
            step2: document.getElementById('step2'),
            step3: document.getElementById('step3'),
            step4: document.getElementById('step4')
        };

        // Mood selection
        moodOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove previous selection
                moodOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selection to clicked option
                this.classList.add('selected');
                
                // Select the radio button
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
                
                // Update form data
                formData.mood = parseInt(this.dataset.mood);
                
                // Update progress
                updateProgress();
            });
        });

        // Activity selection
        activityOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove previous selection
                activityOptions.forEach(opt => opt.classList.remove('selected'));
                
                // Add selection to clicked option
                this.classList.add('selected');
                
                // Select the radio button
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
                
                // Update form data
                formData.activity = this.dataset.activity;
                
                // Update progress
                updateProgress();
            });
        });

        // Explanation textarea
        explanationTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Update form data
            formData.explanation = this.value;
            
            // Update progress
            updateProgress();
        });

        // Energy slider
        energySlider.addEventListener('input', function() {
            const value = this.value;
            energyValue.textContent = value;
            energyTrack.style.width = ((value - 1) / 9) * 100 + '%';
            
            // Update form data
            formData.energy = parseInt(value);
            
            // Update progress
            updateProgress();
        });

        // Productivity slider
        productivitySlider.addEventListener('input', function() {
            const value = this.value;
            productivityValue.textContent = value;
            productivityTrack.style.width = ((value - 1) / 9) * 100 + '%';
            
            // Update form data
            formData.productivity = parseInt(value);
            
            // Update progress
            updateProgress();
        });

        // Update progress indicator
        function updateProgress() {
            // Reset all steps
            Object.values(progressSteps).forEach(step => {
                step.classList.remove('active', 'completed');
            });

            // Step 1: Mood selected
            if (formData.mood !== null) {
                progressSteps.step1.classList.add('completed');
                progressSteps.step2.classList.add('active');
            } else {
                progressSteps.step1.classList.add('active');
                return;
            }

            // Step 2: Activity selected
            if (formData.activity !== null) {
                progressSteps.step2.classList.add('completed');
                progressSteps.step3.classList.add('active');
            } else {
                return;
            }

            // Step 3: Explanation provided (optional but encouraged)
            if (formData.explanation.trim().length > 0) {
                progressSteps.step3.classList.add('completed');
                progressSteps.step4.classList.add('active');
            } else {
                return;
            }

            // Step 4: Sliders (always have default values)
            progressSteps.step4.classList.add('completed');

            // Enable submit button when minimum requirements are met
            updateSubmitButton();
        }

        // Update submit button state
        function updateSubmitButton() {
            const isValid = formData.mood !== null && formData.activity !== null;
            submitBtn.disabled = !isValid;
        }

        // Form submission is now handled by Laravel
        // The form will submit to the server and redirect back with success/error messages



        // Form reset is now handled by Laravel redirect

        // Initialize sliders
        function initializeSliders() {
            energyTrack.style.width = '44.44%'; // 5/10 * 100% adjusted for 1-10 scale
            productivityTrack.style.width = '44.44%';
        }

        // Initialize the form
        document.addEventListener('DOMContentLoaded', function() {
            initializeSliders();
            updateSubmitButton();
        });

        // Auto-save draft functionality removed - using server-side storage instead
    </script>
</body>
</html>

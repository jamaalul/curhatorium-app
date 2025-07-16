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
                <form id="moodTrackingForm">
                    <!-- 1. Mood Scale -->
                    <div class="form-section">
                        <h2 class="section-title">How are you feeling today?</h2>
                        <p class="section-description">
                            Select the emoji that best represents your overall mood today. This helps us understand your emotional state.
                        </p>
                        
                        <div class="mood-scale">
                            <div class="mood-option" data-mood="1">
                                <div class="mood-emoji">😢</div>
                                <div class="mood-label">Very Sad</div>
                                <div class="mood-number">1</div>
                            </div>
                            <div class="mood-option" data-mood="2">
                                <div class="mood-emoji">😞</div>
                                <div class="mood-label">Sad</div>
                                <div class="mood-number">2</div>
                            </div>
                            <div class="mood-option" data-mood="3">
                                <div class="mood-emoji">😔</div>
                                <div class="mood-label">Down</div>
                                <div class="mood-number">3</div>
                            </div>
                            <div class="mood-option" data-mood="4">
                                <div class="mood-emoji">😐</div>
                                <div class="mood-label">Neutral</div>
                                <div class="mood-number">4</div>
                            </div>
                            <div class="mood-option" data-mood="5">
                                <div class="mood-emoji">🙂</div>
                                <div class="mood-label">Okay</div>
                                <div class="mood-number">5</div>
                            </div>
                            <div class="mood-option" data-mood="6">
                                <div class="mood-emoji">😊</div>
                                <div class="mood-label">Good</div>
                                <div class="mood-number">6</div>
                            </div>
                            <div class="mood-option" data-mood="7">
                                <div class="mood-emoji">😄</div>
                                <div class="mood-label">Happy</div>
                                <div class="mood-number">7</div>
                            </div>
                            <div class="mood-option" data-mood="8">
                                <div class="mood-emoji">😁</div>
                                <div class="mood-label">Very Happy</div>
                                <div class="mood-number">8</div>
                            </div>
                            <div class="mood-option" data-mood="9">
                                <div class="mood-emoji">🤩</div>
                                <div class="mood-label">Excited</div>
                                <div class="mood-number">9</div>
                            </div>
                            <div class="mood-option" data-mood="10">
                                <div class="mood-emoji">🥳</div>
                                <div class="mood-label">Euphoric</div>
                                <div class="mood-number">10</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Activity Selection -->
                    <div class="form-section">
                        <h2 class="section-title">What activity contributed most to this mood?</h2>
                        <p class="section-description">
                            Choose the main activity or experience that influenced your mood today. This helps identify patterns in your daily life.
                        </p>
                        
                        <div class="activity-grid">
                            <div class="activity-option" data-activity="work">
                                <div class="activity-icon">💼</div>
                                <div class="activity-info">
                                    <div class="activity-name">Work/Study</div>
                                    <div class="activity-description">Job tasks, meetings, studying</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="exercise">
                                <div class="activity-icon">🏃‍♂️</div>
                                <div class="activity-info">
                                    <div class="activity-name">Exercise</div>
                                    <div class="activity-description">Gym, running, sports, yoga</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="social">
                                <div class="activity-icon">👥</div>
                                <div class="activity-info">
                                    <div class="activity-name">Social Time</div>
                                    <div class="activity-description">Friends, family, social events</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="hobbies">
                                <div class="activity-icon">🎨</div>
                                <div class="activity-info">
                                    <div class="activity-name">Hobbies</div>
                                    <div class="activity-description">Creative activities, interests</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="rest">
                                <div class="activity-icon">😴</div>
                                <div class="activity-info">
                                    <div class="activity-name">Rest/Sleep</div>
                                    <div class="activity-description">Relaxation, napping, sleeping</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="entertainment">
                                <div class="activity-icon">📺</div>
                                <div class="activity-info">
                                    <div class="activity-name">Entertainment</div>
                                    <div class="activity-description">Movies, games, reading</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="nature">
                                <div class="activity-icon">🌳</div>
                                <div class="activity-info">
                                    <div class="activity-name">Nature/Outdoors</div>
                                    <div class="activity-description">Walking, hiking, fresh air</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="food">
                                <div class="activity-icon">🍽️</div>
                                <div class="activity-info">
                                    <div class="activity-name">Food/Cooking</div>
                                    <div class="activity-description">Meals, cooking, dining out</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="health">
                                <div class="activity-icon">🏥</div>
                                <div class="activity-info">
                                    <div class="activity-name">Health/Medical</div>
                                    <div class="activity-description">Doctor visits, health care</div>
                                </div>
                            </div>
                            <div class="activity-option" data-activity="other">
                                <div class="activity-icon">❓</div>
                                <div class="activity-info">
                                    <div class="activity-name">Other</div>
                                    <div class="activity-description">Something else entirely</div>
                                </div>
                            </div>
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
                                    <input type="range" id="energySlider" class="slider" min="1" max="10" value="5">
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
                                    <input type="range" id="productivitySlider" class="slider" min="1" max="10" value="5">
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
                            Save Mood Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span>✅</span>
            <span>Mood entry saved successfully!</span>
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
        const toast = document.getElementById('toast');
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

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (formData.mood === null || formData.activity === null) {
                alert('Please select your mood and activity before submitting.');
                return;
            }

            // Add timestamp
            formData.timestamp = new Date().toISOString();

            // Simulate saving data (in real app, this would be an API call)
            console.log('Saving mood entry:', formData);
            
            // Save to localStorage for demo purposes
            const existingEntries = JSON.parse(localStorage.getItem('moodEntries') || '[]');
            existingEntries.push(formData);
            localStorage.setItem('moodEntries', JSON.stringify(existingEntries));

            // Show success toast
            showToast();

            // Reset form after a delay
            setTimeout(() => {
                resetForm();
            }, 2000);
        });

        // Show toast notification
        function showToast() {
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Reset form
        function resetForm() {
            // Reset form data
            formData = {
                mood: null,
                activity: null,
                explanation: '',
                energy: 5,
                productivity: 5,
                timestamp: null
            };

            // Reset UI
            moodOptions.forEach(opt => opt.classList.remove('selected'));
            activityOptions.forEach(opt => opt.classList.remove('selected'));
            explanationTextarea.value = '';
            charCount.textContent = '0';
            energySlider.value = 5;
            productivitySlider.value = 5;
            energyValue.textContent = '5';
            productivityValue.textContent = '5';
            energyTrack.style.width = '44.44%';
            productivityTrack.style.width = '44.44%';

            // Reset progress
            Object.values(progressSteps).forEach(step => {
                step.classList.remove('active', 'completed');
            });
            progressSteps.step1.classList.add('active');

            // Disable submit button
            submitBtn.disabled = true;
        }

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

        // Auto-save draft (optional feature)
        function saveDraft() {
            localStorage.setItem('moodTrackerDraft', JSON.stringify(formData));
        }

        // Load draft on page load
        function loadDraft() {
            const draft = localStorage.getItem('moodTrackerDraft');
            if (draft) {
                const draftData = JSON.parse(draft);
                // You could implement draft loading here if desired
            }
        }

        // Save draft periodically
        setInterval(saveDraft, 30000); // Save every 30 seconds
    </script>
</body>
</html>

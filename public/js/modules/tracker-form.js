/**
 * Tracker Form Module
 * Handles the mood tracker form interactions and validation
 */

class TrackerForm {
    constructor() {
        this.formData = {
            mood: null,
            activity: null,
            explanation: '',
            energy: 5,
            productivity: 5
        };
        
        // DOM elements
        this.moodButtons = document.querySelectorAll('.mood-btn');
        this.activityButtons = document.querySelectorAll('.activity-btn');
        this.explanationInput = document.getElementById('explanation');
        this.energySlider = document.getElementById('energy-slider');
        this.energyValue = document.getElementById('energy-value');
        this.energyTrack = document.getElementById('energy-track');
        this.productivitySlider = document.getElementById('productivity-slider');
        this.productivityValue = document.getElementById('productivity-value');
        this.productivityTrack = document.getElementById('productivity-track');
        this.progressSteps = {
            step1: document.getElementById('step-1'),
            step2: document.getElementById('step-2'),
            step3: document.getElementById('step-3'),
            step4: document.getElementById('step-4')
        };
        this.submitBtn = document.getElementById('submit-btn');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeSliders();
        this.updateSubmitButton();
    }

    setupEventListeners() {
        // Mood buttons
        this.moodButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.selectMood(btn);
            });
        });

        // Activity buttons
        this.activityButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.selectActivity(btn);
            });
        });

        // Explanation input
        if (this.explanationInput) {
            this.explanationInput.addEventListener('input', (e) => {
                this.formData.explanation = e.target.value;
                this.updateProgress();
            });
        }

        // Energy slider
        if (this.energySlider) {
            this.energySlider.addEventListener('input', (e) => {
                const value = e.target.value;
                this.energyValue.textContent = value;
                this.energyTrack.style.width = ((value - 1) / 9) * 100 + '%';
                this.formData.energy = parseInt(value);
                this.updateProgress();
            });
        }

        // Productivity slider
        if (this.productivitySlider) {
            this.productivitySlider.addEventListener('input', (e) => {
                const value = e.target.value;
                this.productivityValue.textContent = value;
                this.productivityTrack.style.width = ((value - 1) / 9) * 100 + '%';
                this.formData.productivity = parseInt(value);
                this.updateProgress();
            });
        }
    }

    selectMood(selectedBtn) {
        // Remove active class from all mood buttons
        this.moodButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to selected button
        selectedBtn.classList.add('active');
        
        // Update form data
        this.formData.mood = selectedBtn.getAttribute('data-mood');
        
        // Update progress
        this.updateProgress();
    }

    selectActivity(selectedBtn) {
        // Remove active class from all activity buttons
        this.activityButtons.forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to selected button
        selectedBtn.classList.add('active');
        
        // Update form data
        this.formData.activity = selectedBtn.getAttribute('data-activity');
        
        // Update progress
        this.updateProgress();
    }

    updateProgress() {
        // Reset all steps
        Object.values(this.progressSteps).forEach(step => {
            if (step) {
                step.classList.remove('active', 'completed');
            }
        });

        // Step 1: Mood selected
        if (this.formData.mood !== null) {
            if (this.progressSteps.step1) this.progressSteps.step1.classList.add('completed');
            if (this.progressSteps.step2) this.progressSteps.step2.classList.add('active');
        } else {
            if (this.progressSteps.step1) this.progressSteps.step1.classList.add('active');
            return;
        }

        // Step 2: Activity selected
        if (this.formData.activity !== null) {
            if (this.progressSteps.step2) this.progressSteps.step2.classList.add('completed');
            if (this.progressSteps.step3) this.progressSteps.step3.classList.add('active');
        } else {
            return;
        }

        // Step 3: Explanation filled (optional but recommended)
        if (this.formData.explanation.trim().length > 0) {
            if (this.progressSteps.step3) this.progressSteps.step3.classList.add('completed');
            if (this.progressSteps.step4) this.progressSteps.step4.classList.add('active');
        } else {
            return;
        }

        // Step 4: Sliders (always have default values)
        if (this.progressSteps.step4) this.progressSteps.step4.classList.add('completed');

        // Enable submit button if minimum requirements are met
        this.updateSubmitButton();
    }

    updateSubmitButton() {
        const isValid = this.formData.mood !== null && this.formData.activity !== null;
        if (this.submitBtn) {
            this.submitBtn.disabled = !isValid;
        }
    }

    initializeSliders() {
        if (this.energyTrack) {
            this.energyTrack.style.width = '44.44%'; // 5/10 * 100% adjusted for 1-10 scale
        }
        if (this.productivityTrack) {
            this.productivityTrack.style.width = '44.44%';
        }
    }

    // Method to get form data for submission
    getFormData() {
        return this.formData;
    }

    // Method to validate form data
    validate() {
        return this.formData.mood !== null && this.formData.activity !== null;
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the tracker page
    if (document.getElementById('tracker-form')) {
        new TrackerForm();
    }
});

// Make available globally for debugging
window.TrackerForm = TrackerForm; 
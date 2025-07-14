<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Share and Talk</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/share-and-talk.css') }}">
    <style>
        .action-button:disabled,
        .action-button:disabled:hover,
        .action-button:disabled:focus {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
            background-color: inherit !important;
            color: inherit !important;
            box-shadow: none !important;
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
                <h1>Share and Talk</h1>
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
                                <h3>Psikolog Profesional</h3>
                                <p class="subtitle">Certified medical profesional</p>
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
                                    Chat
                                </span>
                                <span class="option-badge video">
                                    Video Call
                                </span>
                            </div>
                        </div>
                        <button class="select-button" onclick="showProfessionals('psychiatrist')">
                            Select Professionals
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Trained Partner Card -->
                    <div class="type-card partner">
                        <div class="type-header">
                            <div class="type-icon">🤝</div>
                            <div class="type-info">
                                <h3>Rangers</h3>
                                <p class="subtitle">Trained Curhatorium partners</p>
                            </div>
                        </div>
                        <p class="type-description">
                            Connect with trained mental health partners who provide supportive counseling, 
                            emotional guidance, and therapeutic conversations to help you navigate life's challenges.
                        </p>
                        <div class="consultation-options">
                            <p class="options-title">Available Options:</p>
                            <div class="option-list">
                                <span class="option-badge video">
                                    Video Call
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
                
                {{-- <!-- Filter Section -->
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
                </div> --}}

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
                title.textContent = 'Available Professionals';
            } else {
                title.textContent = 'Available Rangers';
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
                            <button class="action-button video" onclick="startConsultation(${professional.id}, 'reserve')" ${professional.availability !== 'online' ? 'disabled' : ''}>
                                Reserve
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
                return;
            }
            
            if (professional.availability !== 'online') {
                return;
            }
            
            // In a real application, this would redirect to the consultation interface
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

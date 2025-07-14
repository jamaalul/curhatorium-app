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
        // Remove mockProfessionals
        // let mockProfessionals = ...
        let allProfessionals = [];
        let currentProfessionalType = '';
        let filteredProfessionals = [];

        // Fetch professionals from backend
        async function fetchProfessionals(type = '') {
            let url = '/share-and-talk/professionals';
            if (type) {
                url += `?type=${encodeURIComponent(type)}`;
            }
            const response = await fetch(url);
            if (!response.ok) {
                Toast.error('Error', 'Failed to load professionals');
                return [];
            }
            return await response.json();
        }

        // Show professionals based on type
        async function showProfessionals(type) {
            currentProfessionalType = type;
            allProfessionals = await fetchProfessionals(type);
            filteredProfessionals = allProfessionals;

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

        // Helper to ensure specialties is always an array
        function getSpecialtiesArray(specialties) {
            if (Array.isArray(specialties)) return specialties;
            if (typeof specialties === 'string') {
                try { return JSON.parse(specialties); } catch { return []; }
            }
            return [];
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
                        <img src="/storage/${professional.avatar}" alt="${professional.name}" class="professional-avatar">
                        <div class="professional-info">
                            <h4>${professional.name}</h4>
                            <p class="professional-title">${professional.title}</p>
                        </div>
                    </div>
                    <div class="professional-body">
                        <div class="specialties">
                            <p class="specialties-title">Specialties:</p>
                            <div class="specialty-tags">
                                ${getSpecialtiesArray(professional.specialties).map(specialty => `<span class="specialty-tag">${specialty}</span>`).join('')}
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
            const professional = allProfessionals.find(p => p.id === professionalId);
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

            let filtered = allProfessionals;

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
                filtered = filtered.filter(p => (p.rating || 0) >= minRating);
            }

            filteredProfessionals = filtered;
            renderProfessionals(filtered);
        }

        // Event listeners for filters
        document.addEventListener('DOMContentLoaded', function() {
            const specialty = document.getElementById('specialty-filter');
            const availability = document.getElementById('availability-filter');
            const rating = document.getElementById('rating-filter');
            if (specialty && availability && rating) {
                specialty.addEventListener('change', applyFilters);
                availability.addEventListener('change', applyFilters);
                rating.addEventListener('change', applyFilters);
            }
        });
    </script>
</body>
</html>

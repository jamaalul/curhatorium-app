<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Share and Talk</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/share-and-talk/index.css') }}">
</head>
<body>
    <!-- Toast container for notifications -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Main Content -->
    <div class="container">
        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->has('msg'))
            <div class="alert alert-error">{{ $errors->first('msg') }}</div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="alert alert-error">{{ $error }}</div>
            @endforeach
        @endif

        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-content">
                <h1>Share and Talk</h1>
                <p>Terhubung dengan psikolog berlisensi atau mitra kesehatan mental terlatih untuk dukungan dan bimbingan profesional.</p>
            </div>
        </div>

        <div class="main-content">
            <!-- Consultation Types -->
            <div class="consultation-types">
                <h2 class="section-title">Pilih Jenis Konsultasi Anda</h2>
                
                <div class="types-grid">
                    <!-- Psychiatrist Card -->
                    <div class="type-card psychiatrist">
                        <div class="type-header">
                            <div class="type-icon">🩺</div>
                            <div class="type-info">
                                <h3>Psikolog Profesional</h3>
                                <p class="subtitle">Profesional medis bersertifikat</p>
                            </div>
                        </div>
                        <p class="type-description">
                            Dapatkan layanan konsultasi dari psikolog berlisensi yang siap membantumu memahami kondisi emosional, memberikan pendampingan psikologis, serta menyusun strategi pemulihan dan perawatan non-medis sesuai kebutuhanmu.
                        </p>
                        <div class="consultation-options">
                            <p class="options-title">Pilihan Tersedia:</p>
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
                            Pilih Profesional
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Trained Partner Card -->
                    <div class="type-card partner">
                        <div class="type-header">
                            <div class="type-icon">🤝</div>
                            <div class="type-info">
                                <h3>Rangers</h3>
                                <p class="subtitle">Mitra Curhatorium terlatih</p>
                            </div>
                        </div>
                        <p class="type-description">
                            Terhubung dengan mitra kesehatan mental terlatih yang memberikan peer support, bimbingan emosional, dan percakapan saling mendukung untuk membantu Anda menghadapi tantangan hidup.
                        </p>
                        <div class="consultation-options">
                            <p class="options-title">Pilihan Tersedia:</p>
                            <div class="option-list">
                                <span class="option-badge chat">
                                    Chat
                                </span>
                            </div>
                        </div>
                        <button class="select-button" onclick="showProfessionals('partner')">
                            Pilih Ranger
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Professionals Section -->
            <div class="professionals-section" id="professionals-section" style="display: none;">
                <h2 class="section-title" id="professionals-title">Profesional Tersedia</h2>
                
                {{-- <!-- Filter Section -->
                <div class="filter-section">
                    <h3 class="filter-title">Filter Profesional</h3>
                    <div class="filter-options">
                        <div class="filter-group">
                            <label class="filter-label">Spesialisasi</label>
                            <select class="filter-select" id="specialty-filter">
                                <option value="">Semua Spesialisasi</option>
                                <option value="anxiety">Gangguan Kecemasan</option>
                                <option value="depression">Depresi</option>
                                <option value="trauma">Trauma & PTSD</option>
                                <option value="relationships">Masalah Hubungan</option>
                                <option value="addiction">Adiksi</option>
                                <option value="eating">Gangguan Makan</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Ketersediaan</label>
                            <select class="filter-select" id="availability-filter">
                                <option value="">Semua</option>
                                <option value="online">Tersedia Sekarang</option>
                                <option value="busy">Sibuk</option>
                                <option value="offline">Offline</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label">Rating</label>
                            <select class="filter-select" id="rating-filter">
                                <option value="">Semua Rating</option>
                                <option value="5">5 Bintang</option>
                                <option value="4">4+ Bintang</option>
                                <option value="3">3+ Bintang</option>
                            </select>
                        </div>
                    </div>
                </div> --}}

                <!-- Professionals Grid -->
                <div class="professionals-grid" id="professionals-grid">
                    <!-- Akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <button class="close" id="close-modal" aria-label="Tutup">&times;</button>
            <div class="modal-header">
                <img id="modal-avatar" src="" alt="" class="modal-avatar">
                <div>
                    <h2 id="modal-title">Pesan Konsultasi</h2>
                    <div id="modal-professional-title" class="modal-professional-title"></div>
                </div>
            </div>
            <form id="checkout-form" class="modal-form">
                <div id="consultation-type-section">
                    <div id="consultation-type-options" class="consultation-type-options"></div>
                </div>
                <button type="submit" class="action-button modal-confirm">Konfirmasi Pemesanan</button>
            </form>
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
                Toast.error('Kesalahan', 'Gagal memuat profesional');
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
                title.textContent = 'Profesional Tersedia';
            } else {
                title.textContent = 'Ranger Tersedia';
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
                        Tidak ada profesional yang sesuai dengan kriteria Anda.
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
                            <p class="specialties-title">Spesialisasi:</p>
                            <div class="specialty-tags">
                                ${getSpecialtiesArray(professional.specialties).map(specialty => `<span class="specialty-tag">${specialty}</span>`).join('')}
                            </div>
                        </div>
                        <div class="availability">
                            <p class="availability-title">Availability:</p>
                            <div class="availability-status">
                                <div style="display: flex; align-items: center; gap: 0.5em;">
                                    <span class="status-indicator ${professional.status}"></span>
                                    <span class="status-label">${professional.status.charAt(0).toUpperCase() + professional.status.slice(1)}</span>
                                </div>
                                <div class="availability-text" style="font-size:1em;color:var(--text-tertiary);">
                                    ${professional.statusText}
                                </div>
                            </div>
                        </div>
                        <div class="consultation-actions">
                            <button class="action-button video" onclick="startConsultation(${professional.id}, 'reserve')" ${professional.status !== 'online' ? 'disabled' : ''}>
                                Pesan
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
            if (professional.status !== 'online') {
                return;
            }
            // Add type info for modal logic
            professional.type = currentProfessionalType;
            openCheckoutModal(professional);
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
                filtered = filtered.filter(p => p.status === availabilityFilter);
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
    <script>
    // Modal logic
    const modal = document.getElementById('checkout-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const modalProfessionalInfo = document.getElementById('modal-professional-info'); // unused, replaced by header
    const modalAvatar = document.getElementById('modal-avatar');
    const modalProfessionalTitle = document.getElementById('modal-professional-title');
    const consultationTypeOptions = document.getElementById('consultation-type-options');
    let selectedProfessional = null;
    let selectedConsultationType = null;
    function openCheckoutModal(professional) {
        selectedProfessional = professional;
        document.getElementById('modal-title').textContent = `Pesan Sesi dengan ${professional.name}`;
        modalAvatar.src = `/storage/${professional.avatar}`;
        modalAvatar.alt = professional.name;
        modalProfessionalTitle.textContent = professional.title;
        // Consultation type options as cards
        let options = '';
        if (professional.type === 'psychiatrist') {
            options += `
                <label class="consultation-option-card selected">
                    <input type='radio' name='consultation_type' value='chat' checked>
                    <span class="consultation-option-label">Chat</span>
                    <span class="consultation-option-desc">Konsultasi berbasis teks</span>
                </label>
                <label class="consultation-option-card">
                    <input type='radio' name='consultation_type' value='video'>
                    <span class="consultation-option-label">Video Call</span>
                    <span class="consultation-option-desc">Sesi online tatap muka</span>
                </label>
            `;
        } else {
            options += `
                <label class="consultation-option-card selected">
                    <input type='radio' name='consultation_type' value='chat' checked>
                    <span class="consultation-option-label">Chat</span>
                    <span class="consultation-option-desc">Konsultasi berbasis teks</span>
                </label>
            `;
        }
        consultationTypeOptions.innerHTML = options;
        // Card selection logic
        Array.from(consultationTypeOptions.querySelectorAll('input[type="radio"]')).forEach(input => {
            input.addEventListener('change', function() {
                Array.from(consultationTypeOptions.children).forEach(card => card.classList.remove('selected'));
                this.closest('.consultation-option-card').classList.add('selected');
            });
        });
        modal.style.display = 'flex';
        setTimeout(() => { modal.querySelector('.modal-content').focus(); }, 100);
    }
    function closeCheckoutModal() {
        modal.style.display = 'none';
        selectedProfessional = null;
        selectedConsultationType = null;
    }
    closeModalBtn.onclick = closeCheckoutModal;
    window.onclick = function(event) {
        if (event.target === modal) closeCheckoutModal();
    };
    // Intercept form submit
    document.getElementById('checkout-form').onsubmit = function(e) {
        e.preventDefault();
        
        const consultationType = document.querySelector('input[name="consultation_type"]:checked').value;
        const professionalId = selectedProfessional.id;
        
        // Route based on consultation type
        if (consultationType === 'video') {
            window.location.href = `/share-and-talk/video/${professionalId}`;
        } else {
            window.location.href = `/share-and-talk/chat/${professionalId}`;
        }
    };
    // Patch startConsultation to open modal
    function startConsultation(professionalId, reserveType) {
        const professional = allProfessionals.find(p => p.id === professionalId);
        if (!professional || professional.status !== 'online') return;
        professional.type = currentProfessionalType;
        openCheckoutModal(professional);
    }

    function chatConsultation() {
        window.location.href = `/share-and-talk/chat/${selectedProfessional.id}`;
    }

    function videoConsultation() {
        console.log('Konsultasi video');
    }
    </script>
</body>
</html>

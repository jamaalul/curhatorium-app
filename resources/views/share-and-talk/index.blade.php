<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Share and Talk</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        /* Custom styles can be added here if needed */
        .professional-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .professional-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="pt-16 w-full overflow-x-hidden bg-gray-50">
    @include('components.navbar')

    <!-- Toast container for notifications -->
    <div class="toast-container fixed top-20 right-5 z-50" id="toast-container"></div>

    <!-- Hero Section -->
    <section class="w-full h-fit px-4 py-12 flex flex-col gap-2 items-center justify-center bg-cover shadow-inner relative text-white" style="background-image: url('{{ asset('images/background.jpg') }}');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold">Share and Talk</h1>
            <p class="text-base mt-2">Terhubung dengan psikolog berlisensi atau mitra kesehatan mental terlatih untuk dukungan dan bimbingan profesional.</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">{{ $error }}</div>
            @endforeach
        @endif

        <div class="main-content">
            <!-- Consultation Types -->
            <div class="consultation-types mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Pilih Jenis Konsultasi Anda</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Psychologist Card -->
                    <div class="type-card bg-white p-6 rounded-lg shadow-md border border-gray-200 flex flex-col">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="text-4xl">🩺</div>
                            <div>
                                <h3 class="text-xl font-bold">Psikolog Profesional</h3>
                                <p class="text-gray-500">Profesional medis bersertifikat</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4 flex-grow">
                            Dapatkan layanan konsultasi dari psikolog berlisensi yang siap membantumu memahami kondisi emosional, memberikan pendampingan psikologis, serta menyusun strategi pemulihan dan perawatan non-medis sesuai kebutuhanmu.
                        </p>
                        <div class="mb-4">
                            <p class="font-semibold mb-2">Pilihan Tersedia:</p>
                            <div class="flex gap-2">
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">Chat</span>
                                <span class="bg-purple-100 text-purple-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">Video Call</span>
                            </div>
                        </div>
                        <button class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979] transition-colors duration-200 flex items-center justify-center gap-2" onclick="showProfessionals('psychiatrist')">
                            Pilih Profesional
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Trained Partner Card -->
                    <div class="type-card bg-white p-6 rounded-lg shadow-md border border-gray-200 flex flex-col">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="text-4xl">🤝</div>
                            <div>
                                <h3 class="text-xl font-bold">Rangers</h3>
                                <p class="text-gray-500">Mitra Curhatorium terlatih</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4 flex-grow">
                            Terhubung dengan mitra kesehatan mental terlatih yang memberikan peer support, bimbingan emosional, dan percakapan saling mendukung untuk membantu Anda menghadapi tantangan hidup.
                        </p>
                        <div class="mb-4">
                            <p class="font-semibold mb-2">Pilihan Tersedia:</p>
                            <div class="flex gap-2">
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">Chat</span>
                            </div>
                        </div>
                        <button class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979] transition-colors duration-200 flex items-center justify-center gap-2" onclick="showProfessionals('partner')">
                            Pilih Ranger
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Professionals Section -->
            <div class="professionals-section" id="professionals-section" style="display: none;">
                <h2 class="text-2xl md:text-3xl font-bold text-center mb-8" id="professionals-title">Profesional Tersedia</h2>
                
                <!-- Professionals Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="professionals-grid">
                    <!-- Akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50" style="display:none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md m-4 p-6 relative">
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-800" id="close-modal" aria-label="Tutup">&times;</button>
            <div class="flex items-center gap-4 mb-4">
                <img id="modal-avatar" src="" alt="" class="w-16 h-16 rounded-full object-cover">
                <div>
                    <h2 id="modal-title" class="text-xl font-bold">Pesan Konsultasi</h2>
                    <p id="modal-professional-title" class="text-gray-500"></p>
                </div>
            </div>
            <form id="checkout-form">
                <div id="consultation-type-section" class="mb-6">
                    <div id="consultation-type-options" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>
                <button type="submit" class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979] transition-colors duration-200">Konfirmasi Pemesanan</button>
            </form>
        </div>
    </div>

    @include('components.footer')

    <script>
        let allProfessionals = [];
        let currentProfessionalType = '';

        async function fetchProfessionals(type = '') {
            let url = '/share-and-talk/professionals';
            if (type) {
                url += `?type=${encodeURIComponent(type)}`;
            }
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    console.error('Failed to load professionals');
                    return [];
                }
                return await response.json();
            } catch (error) {
                console.error('Error fetching professionals:', error);
                return [];
            }
        }

        async function showProfessionals(type) {
            currentProfessionalType = type;
            allProfessionals = await fetchProfessionals(type);
            
            const section = document.getElementById('professionals-section');
            const title = document.getElementById('professionals-title');

            title.textContent = type === 'psychiatrist' ? 'Profesional Tersedia' : 'Ranger Tersedia';
            
            section.style.display = 'block';
            renderProfessionals(allProfessionals);

            section.scrollIntoView({ behavior: 'smooth' });
        }

        function getSpecialtiesArray(specialties) {
            if (Array.isArray(specialties)) return specialties;
            if (typeof specialties === 'string') {
                try { return JSON.parse(specialties); } catch { return []; }
            }
            return [];
        }

        function renderProfessionals(professionals) {
            const grid = document.getElementById('professionals-grid');
            
            if (professionals.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-8 text-gray-500">Tidak ada profesional yang tersedia saat ini.</div>`;
                return;
            }
            
            grid.innerHTML = professionals.map(p => `
                <div class="professional-card bg-white p-5 rounded-lg shadow-md border border-gray-200">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="/storage/${p.avatar}" alt="${p.name}" class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h4 class="font-bold text-lg">${p.name}</h4>
                            <p class="text-gray-500">${p.title}</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="font-semibold text-sm mb-2">Spesialisasi:</p>
                        <div class="flex flex-wrap gap-2">
                            ${getSpecialtiesArray(p.specialties).map(s => `<span class="bg-gray-200 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">${s}</span>`).join('')}
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <p class="font-semibold text-sm">Ketersediaan:</p>
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full ${p.status === 'online' ? 'bg-green-500' : 'bg-gray-400'}"></span>
                            <span class="capitalize text-sm">${p.status}</span>
                        </div>
                    </div>
                    <button class="w-full text-white py-2 px-4 rounded-md transition-colors duration-200 ${p.status === 'online' ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 cursor-not-allowed'}" onclick="startConsultation(${p.id})" ${p.status !== 'online' ? 'disabled' : ''}>
                        Pesan Sesi
                    </button>
                </div>
            `).join('');
        }

        function startConsultation(professionalId) {
            const professional = allProfessionals.find(p => p.id === professionalId);
            if (!professional || professional.status !== 'online') return;
            
            professional.type = currentProfessionalType;
            openCheckoutModal(professional);
        }

        const modal = document.getElementById('checkout-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalAvatar = document.getElementById('modal-avatar');
        const modalProfessionalTitle = document.getElementById('modal-professional-title');
        const consultationTypeOptions = document.getElementById('consultation-type-options');
        let selectedProfessional = null;

        function openCheckoutModal(professional) {
            selectedProfessional = professional;
            document.getElementById('modal-title').textContent = `Pesan Sesi dengan ${professional.name}`;
            modalAvatar.src = `/storage/${professional.avatar}`;
            modalAvatar.alt = professional.name;
            modalProfessionalTitle.textContent = professional.title;

            let options = '';
            if (professional.type === 'psychiatrist') {
                options = `
                    <label class="consultation-option-card border rounded-lg p-4 cursor-pointer hover:border-blue-500 has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                        <input type='radio' name='consultation_type' value='chat' class="hidden" checked>
                        <span class="font-bold">Chat</span>
                        <span class="text-sm text-gray-500 block">Konsultasi berbasis teks</span>
                    </label>
                    <label class="consultation-option-card border rounded-lg p-4 cursor-pointer hover:border-blue-500 has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                        <input type='radio' name='consultation_type' value='video' class="hidden">
                        <span class="font-bold">Video Call</span>
                        <span class="text-sm text-gray-500 block">Sesi online tatap muka</span>
                    </label>
                `;
            } else {
                options = `
                    <label class="consultation-option-card border rounded-lg p-4 cursor-pointer hover:border-blue-500 has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                        <input type='radio' name='consultation_type' value='chat' class="hidden" checked>
                        <span class="font-bold">Chat</span>
                        <span class="text-sm text-gray-500 block">Konsultasi berbasis teks</span>
                    </label>
                `;
            }
            consultationTypeOptions.innerHTML = options;
            
            modal.style.display = 'flex';
        }

        function closeCheckoutModal() {
            modal.style.display = 'none';
            selectedProfessional = null;
        }

        closeModalBtn.onclick = closeCheckoutModal;
        window.onclick = function(event) {
            if (event.target === modal) closeCheckoutModal();
        };

        document.getElementById('checkout-form').onsubmit = function(e) {
            e.preventDefault();
            
            const consultationType = document.querySelector('input[name="consultation_type"]:checked').value;
            const professionalId = selectedProfessional.id;
            
            if (consultationType === 'video') {
                window.location.href = `/share-and-talk/video/${professionalId}`;
            } else {
                window.location.href = `/share-and-talk/chat/${professionalId}`;
            }
        };
    </script>
</body>
</html>

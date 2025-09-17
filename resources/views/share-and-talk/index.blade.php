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
        <div class="absolute inset-0 bg-none"></div>
        <div class="relative z-10 text-center text-[#222222]">
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
                        <div class="flex items-center gap-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="md:size-12 size-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                            <div>
                                <h3 class="text-xl font-bold">Psikolog Profesional</h3>
                                <p class="text-gray-500">Profesional klinis bersertifikat</p>
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
                        <div class="flex items-center gap-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="md:size-12 size-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
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

            title.textContent = type === 'psychiatrist' ? 'Daftar Psikolog' : 'Daftar Rangers';
            
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
            window.location.href = `/share-and-talk/checkout/${professionalId}`;
        }
    </script>
</body>
</html>

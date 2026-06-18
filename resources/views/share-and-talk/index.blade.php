@extends('layouts.dashboard')

@section('title', 'Curhatorium | Share and Talk')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-gray-50')

@section('head')
        @vite('resources/css/app.css')
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('dashboard-content')
    <!-- Toast container for notifications -->
    <div class="top-20 right-5 z-50 fixed toast-container" id="toast-container"></div>

    <!-- Hero Section -->
    <section
        class="relative flex flex-col justify-center items-center gap-2 bg-cover shadow-inner px-4 py-12 w-full h-fit text-white"
        style="background-image: url('{{ asset('images/background.webp') }}');">
        <div class="absolute inset-0 bg-none"></div>
        <div class="z-10 relative text-[#222222] text-center">
            <h1 class="font-bold text-3xl md:text-5xl">Share and Talk</h1>
            <p class="mt-2 text-base">Terhubung dengan psikolog profesional atau mitra kesehatan mental terlatih untuk
                dukungan dan konsultasi profesional.</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="mx-auto px-4 py-8 container">
        <!-- Success and Error Messages -->
        @if (session('success'))
            <div class="relative bg-green-100 mb-4 px-4 py-3 border border-green-400 rounded text-green-700"
                role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="relative bg-red-100 mb-4 px-4 py-3 border border-red-400 rounded text-red-700" role="alert">
                {{ session('error') }}</div>
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="relative bg-red-100 mb-4 px-4 py-3 border border-red-400 rounded text-red-700"
                    role="alert">{{ $error }}</div>
            @endforeach
        @endif

        <div class="main-content">
            <!-- Upcoming Consultations -->
            @if (isset($upcomingConsultations) && $upcomingConsultations->isNotEmpty())
                <div class="mb-12 upcoming-consultations">
                    <h2 class="mb-8 font-bold text-2xl md:text-3xl text-center">Jadwal Konsultasi Anda</h2>
                    <div class="bg-white shadow-md p-6 border border-gray-200 rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="divide-y divide-gray-200 min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">
                                            Fasilitator</th>
                                        <th scope="col"
                                            class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">
                                            Jadwal</th>
                                        <th scope="col"
                                            class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">
                                            Tipe</th>
                                        <th scope="col"
                                            class="px-6 py-3 font-medium text-gray-500 text-xs text-left uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col" class="relative px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($upcomingConsultations as $consultation)
                                        <tr id="consultation-{{ $consultation->id }}">
                                            <td class="px-6 py-4 font-medium text-gray-900 text-sm whitespace-nowrap">
                                                {{ $consultation->professional->name }}</td>
                                            <td class="px-6 py-4 text-gray-500 text-sm whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($consultation->start)->format('d M Y, H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 text-sm whitespace-nowrap">
                                                {{ $consultation->consultation_type }}</td>
                                            <td class="px-6 py-4 text-gray-500 text-sm whitespace-nowrap">
                                                @if ($consultation->status == 'waiting')
                                                    <span class="inline-flex bg-yellow-100 px-2 rounded-full font-semibold text-yellow-800 text-xs leading-5">Menunggu</span>
                                                @elseif($consultation->status == 'pending')
                                                    <span class="inline-flex bg-blue-100 px-2 rounded-full font-semibold text-blue-800 text-xs leading-5">Pending</span>
                                                @elseif($consultation->status == 'active')
                                                    <span class="inline-flex bg-green-100 px-2 rounded-full font-semibold text-green-800 text-xs leading-5">Aktif</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 font-medium text-sm text-right whitespace-nowrap">
                                                @php
                                                    $slotStart = \Carbon\Carbon::parse($consultation->start);
                                                    $minutesUntil = now()->diffInMinutes($slotStart, false);
                                                    $disabled = $minutesUntil > 5;
                                                    $isVideo = stripos($consultation->consultation_type, 'video') !== false;
                                                @endphp

                                                <button
                                                    @if ($isVideo) onclick="if(!this.disabled) window.location.href='/share-and-talk/video/{{ $consultation->room }}'"
                                                    @else onclick="if(!this.disabled) window.location.href='/share-and-talk/chat/{{ $consultation->room }}'" @endif
                                                    class="bg-[#48a6a6] hover:bg-[#357979] disabled:bg-gray-400 px-4 py-2 rounded-md text-white transition-colors duration-200 disabled:cursor-not-allowed goto-room-btn"
                                                    data-schedule-time="{{ $consultation->start }}"
                                                    @if ($disabled) disabled @endif>
                                                    Masuk Ruangan
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="text-red-400 text-sm text-right">Anda bisa masuk ke ruangan 5 menit sebelum jadwal
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Consultation Types -->
            <div class="mb-12 consultation-types">
                <h2 class="mb-8 font-bold text-2xl md:text-3xl text-center">Pilih Jenis Konsultasi Anda</h2>

                <div class="gap-8 grid grid-cols-1 md:grid-cols-2">
                    <!-- Psychologist Card -->
                    <div class="flex flex-col bg-white shadow-md p-6 border border-gray-200 rounded-lg type-card">
                        <div class="flex items-center gap-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-10 md:size-12">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                            <div>
                                <h3 class="font-bold text-xl">Psikolog Profesional</h3>
                                <p class="text-gray-500">Psikolog klinis berlisensi</p>
                            </div>
                        </div>
                        <p class="flex-grow mb-4 text-gray-600">
                            Dapatkan layanan konsultasi dari psikolog berlisensi yang siap membantumu memahami kondisi
                            emosional, memberikan saran psikologis, serta merekomendasikan strategi pemulihan
                            sesuai kebutuhanmu
                        </p>
                        <div class="mb-4">
                            <p class="mb-2 font-semibold">Pilihan Tersedia:</p>
                            <div class="flex gap-2">
                                <span
                                    class="bg-blue-100 mr-2 px-2.5 py-0.5 rounded font-medium text-blue-800 text-sm">Chat</span>
                                <span
                                    class="bg-purple-100 mr-2 px-2.5 py-0.5 rounded font-medium text-purple-800 text-sm">Video
                                    Call</span>
                            </div>
                        </div>
                        <button
                            class="flex justify-center items-center gap-2 bg-[#48A6A6] hover:bg-[#357979] px-4 py-2 rounded-md w-full text-white transition-colors duration-200"
                            onclick="showProfessionals('psychiatrist')">
                            Pilih Profesional
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Trained Partner Card -->
                    <div class="flex flex-col bg-white shadow-md p-6 border border-gray-200 rounded-lg type-card">
                        <div class="flex items-center gap-4 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-10 md:size-12">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                            <div>
                                <h3 class="font-bold text-xl">Rangers</h3>
                                <p class="text-gray-500">Mitra Curhatorium terlatih</p>
                            </div>
                        </div>
                        <p class="flex-grow mb-4 text-gray-600">
                            Terhubung dengan mitra kesehatan mental terlatih yang memberikan peer support, dukungan
                            emosional, dan percakapan supportif untuk membantu Anda dengan apapun.
                        </p>
                        <div class="mb-4">
                            <p class="mb-2 font-semibold">Pilihan Tersedia:</p>
                            <div class="flex gap-2">
                                <span
                                    class="bg-blue-100 mr-2 px-2.5 py-0.5 rounded font-medium text-blue-800 text-sm">Chat</span>
                            </div>
                        </div>
                        <button
                            class="flex justify-center items-center gap-2 bg-[#48A6A6] hover:bg-[#357979] px-4 py-2 rounded-md w-full text-white transition-colors duration-200"
                            onclick="showProfessionals('partner')">
                            Pilih Ranger
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Professionals Section -->
            <div class="professionals-section" id="professionals-section" style="display: none;">
                <div class="flex md:flex-row flex-col justify-between items-center mb-8">
                    <h2 class="font-bold text-2xl md:text-3xl text-center" id="professionals-title">Profesional
                        Tersedia</h2>
                    <div class="relative mt-4 md:mt-0">
                        <input type="text" id="date-filter" class="px-4 py-2 border border-gray-300 rounded-md"
                            placeholder="Filter by date...">
                    </div>
                </div>

                <!-- Professionals Grid -->
                <div class="gap-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3" id="professionals-grid">
                    <!-- Akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            let allProfessionals = [];
            let currentProfessionalType = '';

            async function fetchProfessionals(type = '', date = '') {
                let url = new URL(window.location.origin + '/share-and-talk/professionals');
                if (type) url.searchParams.append('type', type);
                if (date) url.searchParams.append('date', date);

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

            async function showProfessionals(type, date = '') {
                currentProfessionalType = type;
                allProfessionals = await fetchProfessionals(type, date);

                const section = document.getElementById('professionals-section');
                const title = document.getElementById('professionals-title');

                title.textContent = type === 'psychiatrist' ? 'Daftar Psikolog' : 'Daftar Rangers';

                section.style.display = 'block';
                renderProfessionals(allProfessionals);

                if (!date) { // Only scroll into view on the initial click
                    section.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }

            function getSpecialtiesArray(specialties) {
                if (Array.isArray(specialties)) return specialties;
                if (typeof specialties === 'string') {
                    try {
                        return JSON.parse(specialties);
                    } catch {
                        return [];
                    }
                }
                return [];
            }

            function renderProfessionals(professionals) {
                const grid = document.getElementById('professionals-grid');

                if (professionals.length === 0) {
                    grid.innerHTML =
                        `<div class="col-span-full py-8 text-gray-500 text-center">Tidak ada profesional yang tersedia saat ini.</div>`;
                    return;
                }

                grid.innerHTML = professionals.map(p => `
                    <div class="bg-white shadow-md p-5 border border-gray-200 rounded-lg professional-card">
                        <div class="flex items-center gap-4 mb-4">
                            <div>
                                <h4 class="font-bold text-lg">${p.name}</h4>
                                <p class="text-gray-500">${p.title}</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="mb-2 font-semibold text-sm">Spesialisasi:</p>
                            <div class="flex flex-wrap gap-2">
                                ${getSpecialtiesArray(p.specialties).map(s => `<span class="bg-gray-200 px-2 py-1 rounded-full font-medium text-gray-800 text-xs">${s}</span>`).join('')}
                            </div>
                        </div>
                        <div class="flex justify-between items-center mb-4 text-sm">
                            <p class="font-semibold">Jadwal terdekat:</p>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-700">${p.next_availability_formatted}</span>
                            </div>
                        </div>
                        <button class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-md w-full text-white transition-colors duration-200" onclick="startConsultation(${p.id})">
                            Pesan Sesi
                        </button>
                    </div>
                `).join('');
            }

            function startConsultation(professionalId) {
                window.location.href = `/share-and-talk/checkout/${professionalId}`;
            }

            document.addEventListener('DOMContentLoaded', function() {
                flatpickr("#date-filter", {
                    dateFormat: "Y-m-d",
                    onChange: function(selectedDates, dateStr, instance) {
                        if (currentProfessionalType) {
                            showProfessionals(currentProfessionalType, dateStr);
                        }
                    }
                });
            });

            function checkConsultationSchedule() {
                const now = new Date();
                document.querySelectorAll('.goto-room-btn').forEach(button => {
                    const scheduleTime = new Date(button.dataset.scheduleTime);
                    const diffMinutes = (scheduleTime.getTime() - now.getTime()) / 60000;

                    if (diffMinutes <= 5 && diffMinutes > -60) { // -60 to allow access for 1 hour after start
                        button.disabled = false;
                        button.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                    } else {
                        button.disabled = true;
                        button.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                checkConsultationSchedule();
                setInterval(checkConsultationSchedule, 60000); // Check every minute
            });
        </script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | {{ $professional->name }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        /* Custom styles for FullCalendar */
        :root {
            --fc-border-color: #e5e7eb;
            --fc-daygrid-event-dot-width: 8px;
            --fc-list-event-hover-bg-color: #f3f4f6;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .fc .fc-button-primary {
            background-color: #48A6A6;
            border-color: #48A6A6;
        }
        .fc .fc-button-primary:hover {
            background-color: #357979;
            border-color: #357979;
        }
        .fc .fc-daygrid-day.fc-day-today {
            background-color: #f0fdfa;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800">Curhatorium</h1>
                <p class="text-sm text-gray-500">Fasilitator Panel</p>
            </div>
            <nav class="mt-6">
                <a href="#schedule" id="nav-schedule" class="block px-6 py-3 text-gray-700 font-semibold bg-gray-200">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Jadwal Saya
                </a>
                <a href="#profile" id="nav-profile" class="block px-6 py-3 text-gray-600 hover:bg-gray-100">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('professional.dashboard.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-6 py-3 text-gray-600 hover:bg-gray-100">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                <header class="flex justify-between items-center mb-8">
                    <div>
                        <h2 id="page-title" class="text-3xl font-bold text-gray-800">Jadwal Saya</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="font-semibold">{{ $professional->name }}</p>
                            <p class="text-sm text-gray-500">{{ $professional->title }}</p>
                        </div>
                        <img src="{{ asset('assets/profile_pict.svg') }}" alt="Avatar" class="w-12 h-12 rounded-full">
                    </div>
                </header>

                <!-- Schedule Section -->
                <div id="schedule-section">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                            <div id='calendar'></div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-4">Atur Ketersediaan</h3>
                            <p class="text-sm text-gray-600 mb-6">Pilih hari dan jam berulang untuk membuka slot jadwal.</p>
                            <form id="scheduleForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hari</label>
                                    <div class="grid grid-cols-3 gap-2 text-sm">
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="1" class="mr-2 rounded">Sen</label></div>
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="2" class="mr-2 rounded">Sel</label></div>
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="3" class="mr-2 rounded">Rab</label></div>
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="4" class="mr-2 rounded">Kam</label></div>
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="5" class="mr-2 rounded">Jum</label></div>
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="6" class="mr-2 rounded">Sab</label></div>
                                        <div><label class="flex items-center"><input type="checkbox" name="days[]" value="0" class="mr-2 rounded">Min</label></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="start_time" class="block text-sm font-medium text-gray-700">Dari Jam</label>
                                        <input type="time" id="start_time" name="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    </div>
                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700">Sampai Jam</label>
                                        <input type="time" id="end_time" name="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Terapkan dari</label>
                                    <input type="date" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai</label>
                                    <input type="date" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <button type="submit" class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979]">Terapkan Jadwal</button>
                            </form>
                            <div id="scheduleMessage" class="mt-4"></div>
                        </div>
                    </div>
                    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold mb-4">Sesi Terbaru</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Tanggal</th>
                                        <th scope="col" class="px-6 py-3">Tipe</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Durasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSessions as $session)
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">{{ $session->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4">{{ ucfirst($session->type) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($session->status === 'active') bg-green-100 text-green-800
                                                @elseif($session->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($session->start && $session->end)
                                                {{ $session->start->diffInMinutes($session->end) }} min
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada sesi terbaru.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div id="profile-section" class="hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-4">Status & Profil</h3>
                            <form id="availabilityForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status Saat Ini</label>
                                    <p class="mt-1 text-lg font-semibold
                                        @if($professional->getEffectiveAvailability() === 'online') text-green-600
                                        @elseif($professional->getEffectiveAvailability() === 'busy') text-yellow-600
                                        @else text-gray-600 @endif">
                                        {{ $professional->getEffectiveAvailabilityText() }}
                                    </p>
                                </div>
                                <div>
                                    <label for="availability" class="block text-sm font-medium text-gray-700">Ubah Status</label>
                                    <select id="availability" name="availability" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="online" {{ $professional->availability === 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="offline" {{ $professional->availability === 'offline' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="availabilityText" class="block text-sm font-medium text-gray-700">Pesan Ketersediaan</label>
                                    <input type="text" id="availabilityText" name="availabilityText" value="{{ $professional->availabilityText }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <button type="submit" class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979]">Update Status</button>
                            </form>
                            <div id="updateMessage" class="mt-4"></div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-4">Ubah Password</h3>
                            <form id="passwordForm" class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                    <input type="password" id="new_password" name="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <div>
                                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                </div>
                                <button type="submit" class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979]">Ubah Password</button>
                            </form>
                            <div id="passwordMessage" class="mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navSchedule = document.getElementById('nav-schedule');
            const navProfile = document.getElementById('nav-profile');
            const scheduleSection = document.getElementById('schedule-section');
            const profileSection = document.getElementById('profile-section');
            const pageTitle = document.getElementById('page-title');

            function showSection(sectionToShow) {
                scheduleSection.classList.add('hidden');
                profileSection.classList.add('hidden');
                navSchedule.classList.remove('bg-gray-200');
                navProfile.classList.remove('bg-gray-200');

                if (sectionToShow === 'schedule') {
                    scheduleSection.classList.remove('hidden');
                    navSchedule.classList.add('bg-gray-200');
                    pageTitle.textContent = 'Jadwal Saya';
                } else {
                    profileSection.classList.remove('hidden');
                    navProfile.classList.add('bg-gray-200');
                    pageTitle.textContent = 'Profil & Pengaturan';
                }
            }

            navSchedule.addEventListener('click', (e) => { e.preventDefault(); showSection('schedule'); });
            navProfile.addEventListener('click', (e) => { e.preventDefault(); showSection('profile'); });

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: `/api/professionals/{{ $professional->id }}/schedule`,
                eventColor: '#378006'
            });
            calendar.render();

            // Availability form submission
            document.getElementById('availabilityForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = {
                    availability: formData.get('availability'),
                    availabilityText: formData.get('availabilityText')
                };
                fetch(`/professional/{{ $professional->id }}/update-availability`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify(data)
                }).then(res => res.json()).then(data => {
                    const msgDiv = document.getElementById('updateMessage');
                    msgDiv.innerHTML = `<p class="${data.success ? 'text-green-600' : 'text-red-600'}">${data.message}</p>`;
                    if(data.success) setTimeout(() => window.location.reload(), 1500);
                }).catch(err => console.error(err));
            });

            // Schedule form submission
            document.getElementById('scheduleForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = {
                    days: formData.getAll('days[]'),
                    start_time: formData.get('start_time'),
                    end_time: formData.get('end_time'),
                    start_date: formData.get('start_date'),
                    end_date: formData.get('end_date'),
                };
                fetch(`/professional/availability/set`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify(data)
                }).then(res => res.json()).then(data => {
                    const msgDiv = document.getElementById('scheduleMessage');
                    msgDiv.innerHTML = `<p class="${data.success ? 'text-green-600' : 'text-red-600'}">${data.message}</p>`;
                    if(data.success) calendar.refetchEvents();
                }).catch(err => console.error(err));
            });

            // Password Change Form
            document.getElementById('passwordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = {
                    current_password: formData.get('current_password'),
                    new_password: formData.get('new_password'),
                    new_password_confirmation: formData.get('new_password_confirmation')
                };
                fetch(`/professional/{{ $professional->id }}/change-password`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify(data)
                }).then(res => res.json()).then(data => {
                    const msgDiv = document.getElementById('passwordMessage');
                    msgDiv.innerHTML = `<p class="${data.success ? 'text-green-600' : 'text-red-600'}">${data.message}</p>`;
                    if(data.success) this.reset();
                }).catch(err => console.error(err));
            });
        });
    </script>
</body>
</html>
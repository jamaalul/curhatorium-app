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
            font-size: 1.1rem; /* Smaller for mobile */
            font-weight: 600;
        }
        @media (min-width: 768px) {
            .fc .fc-toolbar-title {
                font-size: 1.25rem;
            }
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
    <div class="flex flex-col md:flex-row h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white shadow-md flex-shrink-0">
            <div class="p-4 md:p-6 flex justify-between md:block">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Curhatorium</h1>
                    <p class="text-sm text-gray-500">Fasilitator Panel</p>
                </div>
                <button id="mobile-menu-button" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
            <nav id="mobile-menu" class="mt-2 md:mt-6 hidden md:block">
                <a href="#schedule" id="nav-schedule" class="block px-4 py-2 md:px-6 md:py-3 text-gray-700 font-semibold bg-gray-200">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Jadwal Saya
                </a>
                <a href="#profile" id="nav-profile" class="block px-4 py-2 md:px-6 md:py-3 text-gray-600 hover:bg-gray-100">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('professional.dashboard.logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 md:px-6 md:py-3 text-gray-600 hover:bg-gray-100">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                <header class="flex flex-col md:flex-row justify-between md:items-center mb-6 md:mb-8">
                    <div>
                        <h2 id="page-title" class="text-2xl md:text-3xl font-bold text-gray-800">Jadwal Saya</h2>
                    </div>
                    <div class="flex items-center gap-4 mt-4 md:mt-0">
                        <div class="text-right">
                            <p class="font-semibold">{{ $professional->name }}</p>
                            <p class="text-sm text-gray-500">{{ $professional->title }}</p>
                        </div>
                        <img src="{{ asset('assets/profile_pict.svg') }}" alt="Avatar" class="w-12 h-12 rounded-full">
                    </div>
                </header>

                <!-- Schedule Section -->
                <div id="schedule-section">
                    <div class="mt-6 md:mt-8 bg-white p-4 md:p-6 rounded-lg shadow-md">
                        <h3 class="text-lg md:text-xl font-bold mb-4">Permintaan Booking</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Klien</th>
                                        <th scope="col" class="px-4 py-3">Waktu Slot</th>
                                        <th scope="col" class="px-4 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingBookings as $booking)
                                    <tr class="bg-white border-b">
                                        <td class="px-4 py-4">{{ $booking->bookedBy->username ?? 'N/A' }}</td>
                                        <td class="px-4 py-4">{{ \Carbon\Carbon::parse($booking->slot_start_time)->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-4 flex flex-col sm:flex-row gap-2">
                                            <form action="{{ route('professional.booking.accept', $booking) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-xs font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700">Accept</button>
                                            </form>
                                            <form action="{{ route('professional.booking.decline', $booking) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 text-xs font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700">Decline</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">Tidak ada permintaan booking.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-lg shadow-md relative">
                            <div id="calendar-management-overlay" class="absolute inset-0 bg-red-500 bg-opacity-10 border-2 border-red-500 rounded-lg hidden items-center justify-center">
                                <p class="text-red-700 font-semibold text-base md:text-lg w-full text-center pt-4">Mode Hapus Aktif: Klik slot untuk menghapus</p>
                            </div>
                            <div class="flex flex-col md:flex-row justify-between md:items-center mb-4">
                                <h3 class="text-lg md:text-xl font-bold mb-2 md:mb-0">Kalender Jadwal</h3>
                                <div class="flex items-center">
                                    <label for="manage-schedule-toggle" class="mr-2 text-xs md:text-sm font-medium text-gray-900">Hapus Jadwal</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="manage-schedule-toggle" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                    </label>
                                </div>
                            </div>
                            <div id='calendar'></div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
                            <h3 class="text-lg md:text-xl font-bold mb-4">Atur Ketersediaan</h3>
                            <p class="text-xs md:text-sm text-gray-600 mb-6">Pilih hari dan jam berulang untuk membuka slot jadwal.</p>
                            <form id="scheduleForm" class="space-y-4">
                                <div>
                                    <label for="start_date" class="block text-xs md:text-sm font-medium text-gray-700">Terapkan dari</label>
                                    <input type="date" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                </div>
                                <div>
                                    <label for="end_date" class="block text-xs md:text-sm font-medium text-gray-700">Sampai</label>
                                    <input type="date" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Pilih Hari</label>
                                    <div class="grid grid-cols-3 gap-2 text-xs md:text-sm">
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
                                        <label for="start_time" class="block text-xs md:text-sm font-medium text-gray-700">Dari Jam</label>
                                        <input type="time" id="start_time" name="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                    </div>
                                    <div>
                                        <label for="end_time" class="block text-xs md:text-sm font-medium text-gray-700">Sampai Jam</label>
                                        <input type="time" id="end_time" name="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-[#48A6A6] text-white py-2 px-4 rounded-md hover:bg-[#357979] text-sm">Buat Slot Jadwal</button>
                            </form>
                            <div id="scheduleMessage" class="mt-4"></div>
                        </div>
                    </div>
                    <div class="mt-4 bg-white p-4 md:p-6 rounded-lg shadow-md">
                        <h3 class="text-lg md:text-xl font-bold mb-4">Sesi Terakhir</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Klien</th>
                                        <th scope="col" class="px-4 py-3">Waktu Slot</th>
                                        <th scope="col" class="px-4 py-3">Status</th>
                                        <th scope="col" class="px-4 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSessions as $session)
                                    <tr class="bg-white border-b">
                                        <td class="px-4 py-4">{{ $session->bookedBy->username ?? 'N/A' }}</td>
                                        <td class="px-4 py-4">{{ \Carbon\Carbon::parse($session->slot_start_time)->format('d M Y, H:i') }}</td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($session->status === 'booked') bg-green-100 text-green-800
                                                @elseif($session->status === 'pending_confirmation') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @php
                                                    $statusText = match($session->status) {
                                                        'pending_confirmation' => 'Menunggu Konfirmasi',
                                                        'booked' => 'Mendatang',
                                                        'completed' => 'Selesai',
                                                        default => ucfirst($session->status),
                                                    };
                                                @endphp
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($session->status === 'booked')
                                                @php
                                                    $slotStart = \Carbon\Carbon::parse($session->slot_start_time);
                                                    $minutesUntil = now()->diffInMinutes($slotStart, false);
                                                    $disabled = $minutesUntil > 5;
                                                    $isVideo = stripos($session->consultation->consultation_type, 'video') !== false;
                                                @endphp
                                                <button
                                                    @if ($isVideo)
                                                        onclick="if(!this.disabled) window.location.href='/video/{{ $session->consultation->room }}'"
                                                    @else
                                                        onclick="if(!this.disabled) window.location.href='/professional/chat/{{ $session->consultation->room }}'"
                                                    @endif
                                                    class="goto-room-btn bg-[#48a6a6] hover:bg-[#357979] text-white py-2 px-4 rounded-md transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                                    data-schedule-time="{{ $session->slot_start_time }}"
                                                    @if($disabled) disabled @endif
                                                >
                                                    Masuk Ruangan
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada sesi terbaru.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div id="profile-section" class="hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                        <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
                            <h3 class="text-lg md:text-xl font-bold mb-4">Ubah Password</h3>
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

    <!-- Deletion Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md m-4 p-4 md:p-6">
            <h2 class="text-lg md:text-xl font-bold mb-4">Konfirmasi Hapus Jadwal</h2>
            <p id="delete-modal-text" class="mb-6">Apakah Anda yakin ingin menghapus slot jadwal yang tersedia ini?</p>
            <div class="flex justify-end gap-4">
                <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button>
                <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
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

            const calendarEl = document.getElementById('calendar');
            const manageToggle = document.getElementById('manage-schedule-toggle');
            const calendarOverlay = document.getElementById('calendar-management-overlay');
            const deleteModal = document.getElementById('delete-modal');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            const deleteModalText = document.getElementById('delete-modal-text');
            let slotToDelete = null;
            let manageMode = false;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 768 ? 'timeGridDay' : 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: `/api/professionals/{{ $professional->id }}/schedule`,
                windowResize: function(view) {
                    if (window.innerWidth < 768) {
                        calendar.changeView('timeGridDay');
                    } else {
                        calendar.changeView('dayGridMonth');
                    }
                },
                eventClick: function(info) {
                    if (!manageMode || info.event.title !== 'Available') {
                        return;
                    }
                    
                    slotToDelete = info.event;
                    deleteModalText.innerHTML = `Apakah Anda yakin ingin menghapus slot jadwal pada <br> <span class="font-semibold">${slotToDelete.start.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' })}</span>?`;
                    deleteModal.classList.remove('hidden');
                }
            });
            calendar.render();


            // Toggle Manage Mode
            manageToggle.addEventListener('change', function() {
                manageMode = this.checked;
                calendarOverlay.classList.toggle('hidden', !manageMode);
            });

            // Modal Controls
            cancelDeleteBtn.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
                slotToDelete = null;
            });

            confirmDeleteBtn.addEventListener('click', () => {
                if (!slotToDelete) return;

                fetch(`/professional/slots/${slotToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calendar.refetchEvents();
                    } else {
                        alert(data.message); // Or a more elegant notification
                    }
                    deleteModal.classList.add('hidden');
                    slotToDelete = null;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                    deleteModal.classList.add('hidden');
                    slotToDelete = null;
                });
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

        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Checkout</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
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
        .fc-event {
            cursor: pointer;
        }
    </style>
</head>
<body class="pt-16 w-full overflow-x-hidden bg-gray-100">
    @include('components.navbar')

    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('share-and-talk.book') }}" method="POST">
            @csrf
            <input type="hidden" name="professional_id" value="{{ $professional->id }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Left Column: Booking Details -->
                <div class="lg:col-span-2 bg-white p-8 rounded-lg shadow-md">
                    <h1 class="text-2xl font-bold mb-6">Informasi Pemesanan</h1>

                    <!-- User Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                            <input type="tel" id="whatsapp_number" name="whatsapp_number" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 081234567890" required>
                        </div>
                    </div>

                    <!-- Consultation Type -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-3">Jenis Konsultasi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="consultation-option-card border rounded-lg p-4 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                                <input type="radio" name="consultation_type" value="chat" class="hidden" checked onchange="updateSummary()" required>
                                <span class="font-bold">Chat</span>
                                <span class="text-sm text-gray-500 block">Konsultasi via chat</span>
                            </label>
                            @if ($professional->type === 'psychiatrist')
                            <label class="consultation-option-card border rounded-lg p-4 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-200">
                                <input type="radio" name="consultation_type" value="video" class="hidden" onchange="updateSummary()">
                                <span class="font-bold">Video Call</span>
                                <span class="text-sm text-gray-500 block">Konsultasi via video</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <!-- Schedule Appointment -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Jadwalkan Sesi</h3>
                        <div id='calendar'></div>
                        <input type="hidden" id="date" name="date" required>
                        <input type="hidden" id="time" name="time" required>
                        <p id="selected-slot-text" class="mt-4 text-center font-semibold text-gray-700"></p>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="bg-white p-8 rounded-lg shadow-md h-fit">
                    <h2 class="text-2xl font-bold mb-6">Ringkasan Pesanan</h2>
                    <div class="space-y-3" id="order-summary">
                        <div class="flex items-center gap-4 pb-4 border-b">
                            <div>
                                <h3 class="font-semibold">{{ $professional->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $professional->title }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jenis Tiket</span>
                            <span class="font-semibold" id="summary-ticket-type"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tiket Tersedia</span>
                            <span class="font-semibold" id="summary-available-tickets"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tiket Digunakan</span>
                            <span class="font-semibold" id="summary-consumed-tickets">1 Tiket</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t mt-3">
                            <span class="font-bold">Sisa Tiket</span>
                            <span class="font-bold" id="summary-remaining-tickets"></span>
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-6 bg-[#48A6A6] text-white py-3 px-4 rounded-md hover:bg-[#357979] transition-colors duration-200 font-semibold">Reservasi</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const userTickets = @json($tickets);
        const professionalType = @json($professional->type);

        function updateSummary() {
            const selectedType = document.querySelector('input[name="consultation_type"]:checked').value;
            const availableTickets = selectedType === 'chat' ? userTickets.chat : userTickets.video;
            const consumedAmount = 1;
            const remainingTickets = availableTickets - consumedAmount;

            let ticketTypeName = '';
            if (selectedType === 'chat') {
                ticketTypeName = professionalType === 'psychiatrist' ? 'Psikolog - Chat' : 'Ranger - Chat';
            } else {
                ticketTypeName = 'Psikolog - Video';
            }

            document.getElementById('summary-ticket-type').textContent = ticketTypeName;
            document.getElementById('summary-available-tickets').textContent = `${availableTickets} Tiket`;
            document.getElementById('summary-remaining-tickets').textContent = `${remainingTickets} Tiket`;
            
            const submitButton = document.querySelector('button[type="submit"]');
            if (remainingTickets < 0) {
                document.getElementById('summary-remaining-tickets').classList.add('text-red-500');
                submitButton.disabled = true;
                submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.remove('bg-[#48A6A6]', 'hover:bg-[#357979]');

            } else {
                document.getElementById('summary-remaining-tickets').classList.remove('text-red-500');
                submitButton.disabled = false;
                submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitButton.classList.add('bg-[#48A6A6]', 'hover:bg-[#357979]');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('time');
            const selectedSlotText = document.getElementById('selected-slot-text');
            let selectedEvent = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: `/api/professionals/{{ $professional->id }}/schedule`,
                slotMinTime: '08:00:00',
                slotMaxTime: '18:00:00',
                allDaySlot: false,
                eventDataTransform: function(eventData) {
                    const now = new Date();
                    const eventStart = new Date(eventData.start);
                    if (eventStart < now) {
                        eventData.color = '#d1d5db'; // Gray for past events
                        eventData.extendedProps = { isPast: true };
                    }
                    return eventData;
                },
                eventClick: function(info) {
                    if (info.event.extendedProps.isPast || info.event.title !== 'Available') {
                        return;
                    }

                    // Revert previous event color if one was selected
                    if (selectedEvent) {
                        selectedEvent.setProp('backgroundColor', '#10b981');
                    }

                    // Set new selected event and change its color
                    selectedEvent = info.event;
                    selectedEvent.setProp('backgroundColor', '#3b82f6'); // Blue for active

                    const startTime = new Date(info.event.start);
                    const dateStr = startTime.toISOString().split('T')[0];
                    const timeStr = startTime.toTimeString().split(' ')[0].substring(0, 5);
                    
                    dateInput.value = dateStr;
                    timeInput.value = timeStr;

                    selectedSlotText.textContent = `Jadwal Terpilih: ${info.event.start.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' })}`;
                }
            });
            calendar.render();

            updateSummary();
        });
    </script>
</body>
</html>
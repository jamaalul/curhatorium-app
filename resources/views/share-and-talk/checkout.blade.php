@extends('layouts.dashboard')

@section('title', 'Curhatorium | Checkout')

@section('bodyClass', 'pt-16 w-full overflow-x-hidden bg-gray-100')

@section('head')
    @vite('resources/css/app.css')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <style>
        :root {
            --fc-border-color: #e5e7eb;
            --fc-daygrid-event-dot-width: 8px;
            --fc-list-event-hover-bg-color: #f3f4f6;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem;
            /* Smaller for mobile */
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

        .fc-event {
            cursor: pointer;
        }

        .fc-popover {
            max-width: 90vw;
            max-height: 60vh;
            overflow-y: auto;
        }

        @media (min-width: 768px) {
            .fc-popover {
                max-width: 400px;
                max-height: 400px;
            }
        }
    </style>
@endsection

@section('dashboard-content')
    @include('components.error', ['msg' => $errors])
    <div class="mx-auto px-4 py-4 md:py-8 container">
        <form action="{{ route('share-and-talk.book') }}" method="POST">
            @csrf
            <input type="hidden" name="professional_id" value="{{ $professional->id }}">

            <div class="gap-6 grid grid-cols-1 lg:grid-cols-3">
                <!-- Left Column: Booking Details -->
                <div class="lg:col-span-2 bg-white shadow-md p-4 md:p-8 rounded-lg">
                    <h1 class="mb-6 font-bold text-xl md:text-2xl">Informasi Pemesanan</h1>

                    <!-- User Information -->
                    <div class="gap-4 md:gap-6 grid grid-cols-1 md:grid-cols-2 mb-6 md:mb-8">
                        <div>
                            <label for="whatsapp_number"
                                class="block mb-1 font-medium text-gray-700 text-xs md:text-sm">Nomor WhatsApp</label>
                            <input type="tel" id="whatsapp_number" name="whatsapp_number"
                                class="shadow-sm border-gray-300 rounded-md w-full text-sm"
                                placeholder="Contoh: 081234567890" required>
                        </div>
                        <div>
                            <p class="mt-2 md:mt-0 text-stone-500 text-xs md:text-sm">Silakan masukkan nomor WhatsApp
                                Anda agar kami dapat memberikan informasi terkait booking Anda.</p>
                        </div>
                    </div>

                    <!-- Consultation Type -->
                    <div class="mb-6 md:mb-8">
                        <h3 class="mb-3 font-semibold text-base md:text-lg">Jenis Konsultasi</h3>
                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2 text-sm">
                            <label
                                class="p-4 border has-[:checked]:border-blue-500 rounded-lg has-[:checked]:ring-2 has-[:checked]:ring-blue-200 cursor-pointer consultation-option-card">
                                <input type="radio" name="consultation_type" value="chat" class="hidden" checked
                                    onchange="updateSummary()" required>
                                <span class="font-bold text-sm md:text-base">Chat</span>
                                <span class="block text-gray-500 text-xs md:text-sm">Konsultasi via chat</span>
                            </label>
                            @if ($professional->type === 'psychiatrist')
                                <label
                                    class="p-4 border has-[:checked]:border-blue-500 rounded-lg has-[:checked]:ring-2 has-[:checked]:ring-blue-200 cursor-pointer consultation-option-card">
                                    <input type="radio" name="consultation_type" value="video" class="hidden"
                                        onchange="updateSummary()">
                                    <span class="font-bold text-sm md:text-base">Video Call</span>
                                    <span class="block text-gray-500 text-xs md:text-sm">Konsultasi via video</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- Schedule Appointment -->
                    <div>
                        <h3 class="mb-3 font-semibold text-base md:text-lg">Jadwalkan Sesi</h3>
                        <div id='calendar' class="text-sm"></div>
                        <input type="hidden" id="date" name="date" required>
                        <input type="hidden" id="time" name="time" required>
                        <p id="selected-slot-text"
                            class="mt-4 font-semibold text-gray-700 text-sm md:text-base text-center"></p>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="bg-white shadow-md p-4 md:p-8 rounded-lg h-fit text-sm">
                    <h2 class="mb-6 font-bold text-xl md:text-2xl">Ringkasan Pesanan</h2>
                    <div class="space-y-3" id="order-summary">
                        <div class="flex items-center gap-4 pb-4 border-b">
                            <div>
                                <h3 class="font-semibold text-sm md:text-base">{{ $professional->name }}</h3>
                                <p class="text-gray-500 text-xs md:text-sm">{{ $professional->title }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-600">Jenis Tiket</span>
                            <span class="font-semibold" id="summary-ticket-type"></span>
                        </div>
                        <div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-600">Tiket Tersedia</span>
                            <span class="font-semibold" id="summary-available-tickets"></span>
                        </div>
                        <div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-600">Tiket Digunakan</span>
                            <span class="font-semibold" id="summary-consumed-tickets">1 Tiket</span>
                        </div>
                        <div class="flex justify-between mt-3 pt-3 border-t text-sm md:text-base">
                            <span class="font-bold">Sisa Tiket</span>
                            <span class="font-bold" id="summary-remaining-tickets"></span>
                        </div>
                    </div>
                    <button type="submit"
                        class="bg-[#48A6A6] hover:bg-[#357979] mt-6 px-4 py-3 rounded-md w-full font-semibold text-white text-sm md:text-base transition-colors duration-200">Reservasi</button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script>
        const userTickets = @json($tickets);
        const professionalType = @json($professional->type);

        function updateSummary() {
            const selectedType = document.querySelector('input[name="consultation_type"]:checked').value;
            const availableTickets = selectedType === 'chat' ? userTickets.chat : userTickets.video;
            const consumedAmount = 1;

            let ticketTypeName = '';
            if (selectedType === 'chat') {
                ticketTypeName = professionalType === 'psychiatrist' ? 'Psikolog - Chat' : 'Ranger - Chat';
            } else {
                ticketTypeName = 'Psikolog - Video';
            }

            document.getElementById('summary-ticket-type').textContent = ticketTypeName;

            let isUnlimited = availableTickets === 'Tak Terbatas';
            let remainingTickets = isUnlimited ? 'Tak Terbatas' : (availableTickets - consumedAmount);
            let hasEnoughTickets = isUnlimited || (remainingTickets >= 0);

            document.getElementById('summary-available-tickets').textContent = isUnlimited ? 'Tak Terbatas' : `${availableTickets} Tiket`;
            document.getElementById('summary-remaining-tickets').textContent = isUnlimited ? 'Tak Terbatas' : `${remainingTickets} Tiket`;

            const submitButton = document.querySelector('button[type="submit"]');
            if (!hasEnoughTickets) {
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

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const dateInput = document.getElementById('date');
            const timeInput = document.getElementById('time');
            const selectedSlotText = document.getElementById('selected-slot-text');
            let selectedEvent = null;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: `/api/professionals/{{ $professional->id }}/schedule`,
                dayMaxEventRows: 2,
                dayMaxEvents: 2,
                moreLinkClick: 'popover',
                dayPopoverFormat: {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                },
                slotMinTime: '00:00:00',
                slotMaxTime: '23:59:00',
                allDaySlot: false,
                eventDataTransform: function (eventData) {
                    const now = new Date();
                    const eventStart = new Date(eventData.start);
                    if (eventStart < now) {
                        eventData.color = '#d1d5db'; // Gray for past events
                        eventData.extendedProps = {
                            isPast: true
                        };
                    }
                    return eventData;
                },
                eventClick: function (info) {
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
                    // Use local date/time instead of UTC to match database storage
                    const dateStr = startTime.getFullYear() + '-' +
                        String(startTime.getMonth() + 1).padStart(2, '0') + '-' +
                        String(startTime.getDate()).padStart(2, '0');
                    const timeStr = String(startTime.getHours()).padStart(2, '0') + ':' +
                        String(startTime.getMinutes()).padStart(2, '0');

                    dateInput.value = dateStr;
                    timeInput.value = timeStr;

                    selectedSlotText.textContent =
                        `Jadwal Terpilih: ${info.event.start.toLocaleString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' })}`;
                }
            });
            calendar.render();

            updateSummary();
        });
    </script>
@endsection
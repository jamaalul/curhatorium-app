<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curhatorium | Checkout</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            box-shadow: none;
            border: 1px solid #e5e7eb;
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
                            <input type="text" id="name" name="name" class="w-full border-gray-300 rounded-md shadow-sm" readonly>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm" readonly>
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
                                <input type="radio" name="consultation_type" value="chat" class="hidden" checked onchange="updateSummary()">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                                <div id="datepicker-container"></div>
                                <input type="hidden" id="date" name="date">
                            </div>
                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-2">Waktu</label>
                                <div id="time-slots-container" class="grid grid-cols-2 gap-2">
                                    <p class="text-sm text-gray-500 col-span-2">Pilih tanggal untuk melihat waktu yang tersedia.</p>
                                </div>
                            </div>
                        </div>
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
                    <button type="submit" class="w-full mt-6 bg-[#48A6A6] text-white py-3 px-4 rounded-md hover:bg-[#357979] transition-colors duration-200 font-semibold">Konfirmasi Pesanan</button>
                </div>
            </div>
        </form>
    </div>

    @include('components.footer')

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
            const professionalId = {{ $professional->id }};
            const timeSlotsContainer = document.getElementById('time-slots-container');

            flatpickr("#datepicker-container", {
                inline: true,
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: async function(selectedDates, dateStr, instance) {
                    document.getElementById('date').value = dateStr;
                    timeSlotsContainer.innerHTML = '<p class="text-sm text-gray-500 col-span-2">Memuat...</p>';

                    try {
                        const response = await fetch(`/api/professionals/${professionalId}/availability?date=${dateStr}`);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const slots = await response.json();

                        if (slots.length > 0) {
                            timeSlotsContainer.innerHTML = slots.map(time => `
                                <label class="border rounded-md p-2 text-center cursor-pointer has-[:checked]:bg-blue-500 has-[:checked]:text-white has-[:checked]:border-blue-500">
                                    <input type="radio" name="time" value="${time}" class="hidden">
                                    <span>${time}</span>
                                </label>
                            `).join('');
                        } else {
                            timeSlotsContainer.innerHTML = '<p class="text-sm text-gray-500 col-span-2">Tidak ada waktu yang tersedia pada tanggal ini.</p>';
                        }
                    } catch (error) {
                        console.error('Error fetching time slots:', error);
                        timeSlotsContainer.innerHTML = '<p class="text-sm text-red-500 col-span-2">Gagal memuat waktu.</p>';
                    }
                }
            });

            updateSummary();
        });
    </script>
</body>
</html>
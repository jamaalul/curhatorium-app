<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Consultation - {{ $originalSlot->professional->name ?? 'Professional' }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="max-w-xl mx-auto p-4">
        <div class="text-center mb-8 py-5">
            <div class="text-2xl font-bold text-[#48A6A6] mb-2">Curhatorium</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">Reschedule Consultation</h1>
            <p class="text-gray-600 text-sm">{{ $originalSlot->professional->name ?? 'Professional' }}</p>
        </div>

        <div class="bg-white rounded-lg p-5 shadow-md mb-5">
            <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Original Booking</h2>
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="font-bold text-gray-600">Date:</div>
                <div class="col-span-2">{{ $originalDate }}</div>
            </div>
            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="font-bold text-gray-600">Time:</div>
                <div class="col-span-2">{{ $originalTime }}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="font-bold text-gray-600">Type:</div>
                <div class="col-span-2">{{ $consultation->consultation_type ?? 'Consultation' }}</div>
            </div>
        </div>

        <div class="bg-yellow-50 text-yellow-800 p-3 rounded-md mb-5 flex items-center">
            <span class="mr-2 text-xl">⏰</span>
            <span>Please select a new time within {{ $timeRemaining }} or your booking will remain as scheduled.</span>
        </div>

        <form id="reschedule-form" action="{{ route('reschedule.select', $reschedule->token) }}" method="POST">
            @csrf
            <input type="hidden" name="action" id="action-input" value="">
            <input type="hidden" name="slot_id" id="slot-id-input" value="">

            <div class="bg-white rounded-lg p-5 shadow-md mb-5">
                <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Available Time Slots</h2>
                <p class="text-gray-600 mb-5">Please select one of the following available time slots:</p>

                @if (count($offeredSlots) > 0)
                    @foreach ($offeredSlots as $slot)
                        <div class="slot-card border border-gray-200 rounded-md p-4 mb-4 transition-all duration-200 cursor-pointer hover:border-[#48A6A6] hover:shadow-sm"
                            data-slot-id="{{ $slot->slot->id }}" onclick="selectSlot({{ $slot->slot->id }})">
                            <div class="font-bold text-gray-800 mb-1">{{ $slot->formatted_date }}</div>
                            <div class="text-gray-600 mb-2">{{ $slot->formatted_time }}</div>
                            <div class="text-[#48A6A6] text-sm">
                                {{ $consultation->consultation_type ?? 'Consultation' }}</div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-600">No available time slots to display.</p>
                @endif
            </div>

            <div class="mt-8">
                <button type="button"
                    class="w-full py-3 px-4 bg-[#48A6A6] text-white font-bold rounded-md mb-3 hover:bg-[#357979] transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    id="confirm-button" onclick="confirmSelection()" disabled>
                    Confirm New Time
                </button>
                <button type="button"
                    class="w-full py-3 px-4 bg-white text-gray-600 border border-gray-200 font-bold rounded-md hover:bg-gray-50 transition-colors duration-200"
                    onclick="cancelBooking()">
                    Cancel Booking
                </button>
            </div>
        </form>

        <div class="text-center text-gray-500 text-xs mt-8 pt-5 border-t border-gray-200">
            <p class="mb-1">If you have any questions, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} Curhatorium. All rights reserved.</p>
        </div>
    </div>

    <script>
        let selectedSlotId = null;

        function selectSlot(slotId) {
            // Remove selection from all slots
            document.querySelectorAll('.slot-card').forEach(card => {
                card.classList.remove('selected', 'border-[#48A6A6]', 'bg-teal-50');
            });

            // Add selection to the clicked slot
            const selectedCard = document.querySelector(`.slot-card[data-slot-id="${slotId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected', 'border-[#48A6A6]', 'bg-teal-50');
                selectedSlotId = slotId;

                // Enable the confirm button
                document.getElementById('confirm-button').disabled = false;
            }
        }

        function confirmSelection() {
            if (!selectedSlotId) {
                alert('Please select a time slot first.');
                return;
            }

            // Set the form action and values
            document.getElementById('action-input').value = 'accept';
            document.getElementById('slot-id-input').value = selectedSlotId;

            // Submit the form
            document.getElementById('reschedule-form').submit();
        }

        function cancelBooking() {
            // Set the form action to cancel
            document.getElementById('action-input').value = 'cancel';

            // Submit the form
            document.getElementById('reschedule-form').submit();
        }
    </script>
</body>

</html>

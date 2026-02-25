<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Offer Reschedule Slots | {{ $professional->name }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .back-button {
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .back-button:hover {
            background-color: #5a6268;
        }

        .booking-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #48A6A6;
        }

        .booking-info h3 {
            margin-top: 0;
            color: #48A6A6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #48A6A6;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(72, 166, 166, 0.25);
        }

        .slots-container {
            margin-bottom: 30px;
        }

        .slot-item {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .slot-item:hover {
            border-color: #48A6A6;
            box-shadow: 0 2px 8px rgba(72, 166, 166, 0.15);
        }

        .slot-date {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .slot-time {
            color: #6c757d;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: #48A6A6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #357979;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .info-message {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">Offer Reschedule Slots</h1>
            <a href="{{ route('professional.dashboard', $professional->id) }}" class="back-button">Back to Dashboard</a>
        </div>

        @if ($errors->any())
            <div class="error-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="booking-info">
            <h3>Original Booking</h3>
            <p>
                <strong>Client:</strong>
                {{ $originalSlot->bookedBy->username ?? 'N/A' }}<br>
                <strong>Date:</strong>
                {{ \Carbon\Carbon::parse($originalSlot->slot_start_time)->format('d M Y') }}<br>
                <strong>Time:</strong>
                {{ \Carbon\Carbon::parse($originalSlot->slot_start_time)->format('H:i') }} -
                {{ \Carbon\Carbon::parse($originalSlot->slot_end_time)->format('H:i') }}
            </p>
        </div>

        <form
            action="{{ route('professional.reschedule.offer-slots.save', ['professionalId' => $professional->id, 'rescheduleId' => $reschedule->id]) }}"
            method="POST">
            @csrf
            <div class="form-group">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea id="notes" name="notes" class="form-control" rows="3"
                    placeholder="Add a message to the client about the reschedule options..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Available Slots to Offer</label>
                <p class="info-message">
                    Select at least one slot to offer to the client. They will be able to choose from these options.
                </p>
                <div class="slots-container">
                    @if ($availableSlots->count() > 0)
                        @foreach ($availableSlots as $slot)
                            <div class="slot-item">
                                <div>
                                    <div class="slot-date">
                                        {{ \Carbon\Carbon::parse($slot->slot_start_time)->format('d M Y') }}
                                    </div>
                                    <div class="slot-time">
                                        {{ \Carbon\Carbon::parse($slot->slot_start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($slot->slot_end_time)->format('H:i') }}
                                    </div>
                                </div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="slots[]" value="{{ $slot->id }}"
                                        class="form-check-input">
                                    <span class="ml-2">Offer this slot</span>
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p>No available slots to offer. You need to create more schedule slots first.</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('professional.dashboard', $professional->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" {{ $availableSlots->count() == 0 ? 'disabled' : '' }}>
                    Send Reschedule Offer
                </button>
            </div>
        </form>
    </div>
</body>

</html>

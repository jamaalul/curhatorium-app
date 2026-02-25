# Reschedule Functionality Implementation Plan

## Overview
Implementation of a complete reschedule system where professionals can offer alternative slots to clients through WhatsApp notifications, and clients can select from offered options or cancel.

## Business Logic
- **Offer Validity**: 48 hours from creation
- **Slot Selection**: Clients can only choose from offered slots
- **Original Booking**: Held until client makes decision
- **Payment**: Same ticket value, no changes
- **Security**: Token-based secure links

## Database Schema

### Reschedules Table Structure
```php
// Update app/Models/Reschedule.php
protected $fillable = [
    'consultation_id',
    'original_slot_id',
    'status', // 'pending', 'accepted', 'cancelled', 'expired'
    'token',
    'expires_at',
    'client_response_at',
    'notes',
    'created_at',
    'updated_at'
];

// Relationships
public function consultation() { return $this->belongsTo(Consultation::class); }
public function originalSlot() { return $this->belongsTo(ProfessionalScheduleSlot::class, 'original_slot_id'); }
public function rescheduleSlots() { return $this->hasMany(RescheduleSlot::class); }
```

### RescheduleSlots Table (New)
```php
// Create new migration
protected $fillable = [
    'reschedule_id',
    'professional_schedule_slot_id',
    'is_selected',
    'created_at',
    'updated_at'
];

// Relationship
public function reschedule() { return $this->belongsTo(Reschedule::class); }
public function slot() { return $this->belongsTo(ProfessionalScheduleSlot::class, 'professional_schedule_slot_id'); }
```

## Backend Implementation

### 1. Controller Methods (ProfessionalDashboardController)

#### `rescheduleBooking(Request $request, ProfessionalScheduleSlot $slot)`
- Validate slot ownership and status
- Create reschedule record with token
- Generate unique secure token
- Set expiration to 48 hours from now
- Return form for slot selection

#### `offerRescheduleSlots(Request $request, Reschedule $reschedule)`
- Validate reschedule ownership
- Store offered slots in RescheduleSlots table
- Update reschedule status to 'pending'
- Send WhatsApp notification to client
- Redirect with success message

#### `rescheduleClientResponse(Reschedule $reschedule, $token)`
- Validate token and expiration
- Show client interface with offered slots
- Handle slot selection or cancellation

#### `selectRescheduleSlot(Request $request, Reschedule $reschedule, $token)`
- Validate token and slot selection
- Update consultation with new slot
- Update original slot to available
- Update reschedule status to 'accepted'
- Send notifications to both parties

#### `cancelReschedule(Reschedule $reschedule, $token)`
- Validate token
- Keep original booking as is
- Update reschedule status to 'cancelled'
- Send notification to professional

### 2. Service Classes

#### RescheduleService
```php
class RescheduleService
{
    public function createRescheduleOffer(ProfessionalScheduleSlot $originalSlot, array $offeredSlots, ?string $notes = null)
    public function generateSecureToken()
    public function isTokenValid(Reschedule $reschedule, string $token)
    public function handleClientResponse(Reschedule $reschedule, string $selectedSlotId, string $action)
    public function expireOldReschedules()
}
```

#### FonnteService Extensions
```php
// Add to existing FonnteService
public function sendRescheduleNotification(User $client, Professional $professional, Reschedule $reschedule)
public function sendRescheduleConfirmation(User $client, Professional $professional, ProfessionalScheduleSlot $newSlot)
public function sendRescheduleCancellation(User $client, Professional $professional)
```

## Routes

### Professional Routes
```php
// Existing route already defined
Route::post('/slots/{slot}/reschedule', [ProfessionalDashboardController::class, 'rescheduleBooking'])->name('booking.reschedule');

// New routes needed
Route::get('/reschedule/{reschedule}/offer-slots', [ProfessionalDashboardController::class, 'showOfferSlotsForm'])->name('reschedule.offer-slots');
Route::post('/reschedule/{reschedule}/offer-slots', [ProfessionalDashboardController::class, 'offerRescheduleSlots'])->name('reschedule.offer-slots.save');
Route::get('/reschedules', [ProfessionalDashboardController::class, 'listReschedules'])->name('reschedule.list');
```

### Client Public Routes (No Auth Required)
```php
Route::get('/reschedule/{token}', [RescheduleController::class, 'clientInterface'])->name('reschedule.client');
Route::post('/reschedule/{token}/select', [RescheduleController::class, 'selectSlot'])->name('reschedule.select');
Route::post('/reschedule/{token}/cancel', [RescheduleController::class, 'cancelReschedule'])->name('reschedule.cancel');
```

## Frontend Implementation

### 1. Professional Dashboard Enhancements

#### Reschedule Button Action (Existing - Line 173-179)
```html
<!-- Already implemented in dashboard.blade.php -->
<form action="{{ route('professional.booking.reschedule', $booking->id) }}" method="POST">
    @csrf
    <button type="submit" class="px-3 py-1 text-xs font-medium text-center text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">Reschedule</button>
</form>
```

#### New Reschedule Management Interface
- **Reschedule List Page**: Show all pending/completed reschedules
- **Offer Slots Form**: Modal or separate page to select available slots
- **Status Tracking**: Visual indicators for reschedule progress

#### JavaScript Enhancements
```javascript
// Add to existing dashboard JavaScript
function initRescheduleFunctionality() {
    // Handle reschedule button clicks
    // Show slot selection interface
    // Manage modal dialogs
    // Update calendar with reschedule status
}
```

### 2. Client Interface (Public Page)

#### Responsive Design Features
- **Mobile-First**: Optimized for WhatsApp link opening
- **Clear Interface**: Easy slot selection with date/time display
- **Token Validation**: Show expiration countdown
- **Professional Info**: Display professional details
- **Simple Actions**: Large buttons for selection/cancellation

#### Page Structure
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Consultation - {{ $professional->name }}</title>
</head>
<body>
    <div class="reschedule-container">
        <!-- Professional Info -->
        <!-- Original Booking Details -->
        <!-- Offered Slots Grid -->
        <!-- Action Buttons -->
        <!-- Expiration Warning -->
    </div>
</body>
</html>
```

## WhatsApp Message Templates

### Initial Reschedule Notification
```
Halo {{ $client->name }},

{{ $professional->name }} ingin menukar jadwal konsultasi Anda.

🗓️ Jadwal Lama: {{ $originalSlot->formatted_date }}
🕐 Waktu: {{ $originalSlot->formatted_time }}

Silakan pilih jadwal baru melalui link berikut:
{{ $rescheduleLink }}

Pilihan harus dilakukan dalam 48 jam.

Terima kasih,
Tim Curhatorium
```

### Reschedule Confirmation
```
Halo {{ $client->name }},

Jadwal konsultasi Anda telah berhasil diubah!

🗓️ Jadwal Baru: {{ $newSlot->formatted_date }}
🕐 Waktu: {{ $newSlot->formatted_time }}

{{ $professional->name }} akan menghubungi Anda sesuai jadwal baru.

Terima kasih,
Tim Curhatorium
```

### Reschedule Cancellation
```
Halo {{ $client->name }),

Jadwal konsultasi Anda tetap sesuai rencana awal.

🗓️ Jadwal: {{ $originalSlot->formatted_date }}
🕐 Waktu: {{ $originalSlot->formatted_time }}

{{ $professional->name }} akan menghubungi Anda sesuai jadwal yang telah ditentukan.

Terima kasih,
Tim Curhatorium
```

## Security Considerations

### Token Management
- **Generation**: Cryptographically secure random tokens
- **Validation**: Check token existence and expiration
- **One-Time Use**: Tokens become invalid after use
- **Cleanup**: Automatic cleanup of expired tokens

### Access Control
- **Professional Validation**: Ensure only slot owner can create offers
- **Token Verification**: Validate tokens for client access
- **Expiration Handling**: Automatic token invalidation after 48 hours

## Error Handling

### Professional Side
- Invalid slot selection
- Expired offers
- Network failures during notification
- Concurrent booking conflicts

### Client Side
- Invalid/expired links
- No available slots
- Network timeouts
- Double-submission protection

## Testing Strategy

### Unit Tests
- RescheduleService methods
- Token generation and validation
- Database operations
- WhatsApp service integration

### Integration Tests
- Complete reschedule flow
- Database transactions
- API endpoints
- Frontend interactions

### Manual Testing
- Professional workflow
- Client interface
- WhatsApp notifications
- Edge cases (expiration, invalid tokens)

## Deployment Considerations

### Database Migrations
1. Update reschedules table schema
2. Create reschedule_slots table
3. Add indexes for performance
4. Seed any required data

### Environment Configuration
- WhatsApp API keys
- Token expiration settings
- Notification templates
- Debug logging

## Future Enhancements

### Phase 2 Features
- Bulk reschedule offers
- Calendar integration
- Email notifications
- Automated reminders
- Reschedule analytics
- Client feedback system

This implementation provides a robust, secure, and user-friendly reschedule system that integrates seamlessly with your existing infrastructure.
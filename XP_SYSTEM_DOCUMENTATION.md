# XP System Documentation

## Overview

The XP (Experience Points) system has been implemented based on the specifications from the image. Users can earn XP through various activities, with different amounts based on their membership type.

## XP Constants

-   **Total XP for Psychologist Access**: 10,000 XP
-   **Max Daily XP (Paid Membership)**: 333 XP
-   **Max Daily XP (Calm Starter)**: 222 XP

## XP Values by Activity

### Free Users (Calm Starter)

-   Mental Health Test: 10 XP
-   Share and Talk (Rangers): 5 XP
-   Share and Talk (Psychiatrist): 0 XP (not available)
-   Mission Easy: 30 XP
-   Mission Medium: 40 XP
-   Mission Hard: 80 XP
-   Ment-AI Chatbot: 10 XP
-   Deep Cards: 5 XP
-   Support Group Discussion: 27 XP
-   Mood Tracker: 15 XP

### Subscription Users (Paid Memberships)

-   Mental Health Test: 10 XP
-   Share and Talk (Rangers): 25 XP
-   Share and Talk (Psychiatrist): 80 XP
-   Mission Easy: 40 XP
-   Mission Medium: 50 XP
-   Mission Hard: 90 XP
-   Ment-AI Chatbot: 10 XP
-   Deep Cards: 5 XP
-   Support Group Discussion: 28 XP
-   Mood Tracker: 25 XP

## Implementation Details

### Database Structure

-   `users.total_xp`: Stores the user's total accumulated XP
-   `daily_xp_logs`: Tracks daily XP gains for enforcing daily limits
-   `missions`: Contains mission data (points field removed, XP calculated by difficulty)

### Key Files

-   `app/Services/XpService.php`: Core XP logic and calculations
-   `app/Models/DailyXpLog.php`: Model for daily XP tracking
-   `app/Http/Controllers/XpController.php`: API endpoints for XP operations
-   `app/Models/User.php`: XP-related methods added to User model

### Integration Points

The XP system is automatically integrated into existing features:

1. **Mental Health Test** (`MentalTestController`): Awards XP when test is completed
2. **Missions** (`MissionController`): Awards XP based on mission difficulty
3. **Mood Tracker** (`TrackerController`): Awards XP when daily tracking is completed
4. **Support Group Discussion** (`SgdController`): Awards XP when joining a group
5. **Deep Cards** (`CardController`): Awards XP when accessing cards
6. **Ment-AI Chatbot** (`ChatbotController`): Awards XP when using chatbot
7. **Share and Talk** (`ShareAndTalkController`): Awards XP when session ends

### API Endpoints

-   `POST /api/xp/award`: Award XP for an activity
-   `GET /api/xp/progress`: Get XP progress towards psychologist access
-   `GET /api/xp/daily-summary`: Get daily XP summary
-   `GET /api/xp/breakdown`: Get XP breakdown for all activities
-   `GET /api/xp/can-access-psychologist`: Check if user can access psychologist
-   `GET /api/xp/history`: Get XP history

### Blade Components

-   `<x-xp-display :user="$user" />`: Displays XP progress and daily summary
-   `<x-xp-notification :xp-awarded="$xpAwarded" :message="$message" />`: Shows XP notification

## Usage Examples

### Awarding XP

```php
$user = auth()->user();
$result = $user->awardXp('mental_test');
```

### Getting XP Progress

```php
$progress = $user->getXpProgress();
$dailySummary = $user->getDailyXpSummary();
$canAccess = $user->canAccessPsychologist();
```

### Checking Membership Type

```php
$xpService = app(\App\Services\XpService::class);
$membershipType = $xpService->getUserMembershipType($user); // 'free' or 'subscription'
```

## Daily Limits

The system enforces daily XP limits based on membership type:

-   Users cannot exceed their daily XP limit
-   If an activity would exceed the limit, only partial XP is awarded
-   Daily limits reset at midnight

## Testing

Use the test route `/test-xp` (when authenticated) to view current XP status and system information.

## Notes

-   XP is awarded automatically when users complete activities
-   Daily limits prevent XP farming
-   The system tracks all XP gains for audit purposes
-   Users with 10,000+ XP can access psychologist consultations
-   Mission XP is calculated based on difficulty level (easy: 30/40 XP, medium: 40/50 XP, hard: 80/90 XP)
-   The `points` field has been removed from the missions table as XP is now handled by the XP system

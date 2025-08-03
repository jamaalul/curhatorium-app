# XP System - Reorganized Architecture

## Overview

The XP (Experience Points) system has been completely reorganized to improve maintainability, scalability, and separation of concerns. This document outlines the new architecture and how to work with it.

## Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Controllers   │    │     Services    │    │   Repositories  │
│                 │    │                 │    │                 │
│  XpController   │───▶│   XpService     │───▶│  XpRepository   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │     Events      │
                       │                 │
                       │  XpAwarded      │
                       └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │    Listeners    │
                       │                 │
                       │ HandleXpAwarded │
                       └─────────────────┘
```

## Key Components

### 1. Configuration (`config/xp.php`)

All XP-related constants and values are now centralized in a configuration file:

```php
// XP targets
'targets' => [
    'psychologist_access' => env('XP_PSYCHOLOGIST_TARGET', 10000),
],

// Daily limits
'daily_limits' => [
    'free' => env('XP_DAILY_LIMIT_FREE', 222),
    'subscription' => env('XP_DAILY_LIMIT_SUBSCRIPTION', 333),
],

// Activity rewards
'activities' => [
    'mental_test' => [
        'free' => 10,
        'subscription' => 10,
        'description' => 'Mental Health Test',
    ],
    // ... more activities
],
```

**Benefits:**
- Easy to modify XP values without touching code
- Environment-specific configuration
- Clear documentation of all XP-related values

### 2. Service Layer (`app/Services/XpService.php`)

The service layer handles all business logic:

```php
class XpService
{
    public function __construct(
        private XpRepository $xpRepository
    ) {}

    public function awardXp(User $user, string $activity, int $quantity = 1): array
    {
        // Business logic for awarding XP
    }
}
```

**Key Features:**
- Dependency injection for better testability
- Comprehensive error handling
- Event dispatching for decoupled notifications
- Transaction safety

### 3. Repository Pattern (`app/Repositories/XpRepository.php`)

Data access is abstracted through repositories:

```php
class XpRepository
{
    public function getDailyXpGained(User $user, Carbon $date = null): int
    {
        // Database queries
    }
    
    public function getXpHistory(User $user, int $days = 30): Collection
    {
        // Complex queries
    }
}
```

**Benefits:**
- Separation of data access from business logic
- Easier to test and mock
- Reusable data access methods
- Better query optimization

### 4. Event-Driven Architecture

Events are dispatched when XP is awarded:

```php
// Event
class XpAwarded implements ShouldBroadcast
{
    public function __construct(
        public User $user,
        public int $xpAwarded,
        public string $activity,
        public array $progress
    ) {}
}

// Listener
class HandleXpAwarded implements ShouldQueue
{
    public function handle(XpAwarded $event): void
    {
        // Handle XP award (logging, notifications, etc.)
    }
}
```

**Benefits:**
- Decoupled notifications and side effects
- Real-time updates via broadcasting
- Queue-able listeners for performance
- Easy to add new XP-related features

### 5. Controller Layer (`app/Http/Controllers/XpController.php`)

Controllers are now thin and focused on HTTP concerns:

```php
class XpController extends Controller
{
    public function __construct(
        private XpService $xpService
    ) {}

    public function awardXp(Request $request): JsonResponse
    {
        // Validation and HTTP response handling
        $result = $this->xpService->awardXp($user, $activity, $quantity);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
```

## Usage Examples

### Awarding XP

```php
// In a controller or service
$xpService = app(XpService::class);
$result = $xpService->awardXp($user, 'mental_test', 1);

if ($result['success']) {
    // XP awarded successfully
    $xpAwarded = $result['xp_awarded'];
    $newTotal = $result['new_total_xp'];
} else {
    // Handle error
    $errorMessage = $result['message'];
}
```

### Getting XP Progress

```php
$progress = $xpService->getXpProgress($user);
// Returns: ['current_xp' => 5000, 'target_xp' => 10000, 'progress_percentage' => 50]
```

### Getting XP History

```php
$history = $xpService->getXpHistory($user, 30); // Last 30 days
// Returns grouped data by date with activity breakdown
```

## Configuration Management

### Environment Variables

Add these to your `.env` file for customization:

```env
XP_PSYCHOLOGIST_TARGET=10000
XP_DAILY_LIMIT_FREE=222
XP_DAILY_LIMIT_SUBSCRIPTION=333
```

### Adding New Activities

To add a new activity, simply update the configuration:

```php
// In config/xp.php
'activities' => [
    'new_activity' => [
        'free' => 15,
        'subscription' => 30,
        'description' => 'New Activity Description',
    ],
],
```

## Testing

### Unit Testing Services

```php
class XpServiceTest extends TestCase
{
    public function test_award_xp_successfully()
    {
        $user = User::factory()->create(['total_xp' => 0]);
        $xpService = app(XpService::class);
        
        $result = $xpService->awardXp($user, 'mental_test');
        
        $this->assertTrue($result['success']);
        $this->assertEquals(10, $result['xp_awarded']);
    }
}
```

### Testing with Repository Mock

```php
public function test_get_daily_xp_gained()
{
    $user = User::factory()->create();
    $mockRepository = Mockery::mock(XpRepository::class);
    $mockRepository->shouldReceive('getDailyXpGained')
        ->once()
        ->andReturn(50);
    
    $xpService = new XpService($mockRepository);
    $result = $xpService->getDailyXpGained($user);
    
    $this->assertEquals(50, $result);
}
```

## Migration Guide

### From Old System

1. **Update Service Calls**: Replace direct model calls with service calls
2. **Update Configuration**: Move hardcoded values to `config/xp.php`
3. **Update Event Handling**: Use the new event system for notifications
4. **Update Tests**: Refactor tests to use the new architecture

### Breaking Changes

- `XpService::TOTAL_XP_FOR_PSYCHOLOGIST` → `config('xp.targets.psychologist_access')`
- Direct model queries → Repository methods
- Manual event handling → Event/listener system

## Performance Considerations

1. **Caching**: Consider caching frequently accessed XP data
2. **Queue Processing**: XP events are queued for better performance
3. **Database Indexing**: Ensure proper indexes on `daily_xp_logs` table
4. **Batch Operations**: Use repository methods for bulk operations

## Monitoring and Analytics

The new system provides better monitoring capabilities:

- XP award events are logged with detailed context
- Repository methods support analytics queries
- Event listeners can integrate with external monitoring systems

## Future Enhancements

1. **XP Multipliers**: Add time-based or event-based XP multipliers
2. **Achievement System**: Build on top of the XP system
3. **Leaderboards**: Use repository methods for ranking queries
4. **Analytics Dashboard**: Leverage the new data access patterns 
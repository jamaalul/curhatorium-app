<?php

return [
    'enabled' => env('MAINTENANCE_MODE_ENABLED', false),
    'message' => env('MAINTENANCE_MODE_MESSAGE', 'We are currently undergoing scheduled maintenance. Please check back later.'),
    'estimated_downtime' => env('MAINTENANCE_MODE_ESTIMATED_DOWNTIME', null),
    'scheduled_at' => env('MAINTENANCE_MODE_SCHEDULED_AT', null),
    'allow' => env('MAINTENANCE_MODE_ALLOWED_IPS', ''),
];
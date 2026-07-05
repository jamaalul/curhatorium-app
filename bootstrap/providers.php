<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\Filament\AdminprivilegePanelProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    AdminprivilegePanelProvider::class,
];

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Domyślny adres po zalogowaniu.
     */
    public const HOME = '/dashboard';

    // Inne metody i właściwości, jeśli są potrzebne...
}

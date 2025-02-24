<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Domyślny adres po zalogowaniu.
     */
    public const HOME = '/dashboard';

    /**
     * Definiuje powiązania modeli, wzorce oraz inne filtry.
     */
    
    public function boot(): void
    {
        $this->configureRateLimiting();
    
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
    
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    
        // Ręczne powiązanie dla parametru "question"
        Route::bind('question', function($value) {
            return \App\Models\Question::findOrFail($value);
        });
    }
    


    /**
     * Konfiguracja limitowania żądań.
     */
    protected function configureRateLimiting(): void
    {
        // Możesz tutaj skonfigurować rate limiter, np.:
        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        // });
    }
}

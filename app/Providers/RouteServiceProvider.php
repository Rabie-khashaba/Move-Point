<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        // Override the default storage route to prevent Laravel from intercepting storage requests
        Route::get('storage/{path}', function () {
            abort(404);
        })->where('path', '.*')->name('storage.local');
    }
}

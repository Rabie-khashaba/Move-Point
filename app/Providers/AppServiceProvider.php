<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enable query logging in development
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        // Override Laravel's default storage route to prevent interception
        Route::get('storage/{path}', function () {
            abort(404);
        })->where('path', '.*')->name('storage.local');
    }
}

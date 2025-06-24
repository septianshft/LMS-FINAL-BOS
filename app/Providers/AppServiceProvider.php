<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;

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
        // Set timezone for the application
        $timezone = config('app.timezone');
        date_default_timezone_set($timezone);

        // For consistency across all date operations
        if (function_exists('ini_set')) {
            ini_set('date.timezone', $timezone);
        }

        // Set Carbon timezone and locale
        Carbon::setLocale('id'); // Indonesian locale
        // Carbon will use the application timezone set by date_default_timezone_set() above

        View::composer('*', function ($view) {
            $count = 0;
            if (Auth::check()) {
                $count = CartItem::where('user_id', Auth::id())->count();
            }
            $view->with('cartCount', $count);
        });
    }
}

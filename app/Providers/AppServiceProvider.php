<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Services\WeatherService;

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
        $this->app->singleton(WeatherService::class, function () {
            return new WeatherService();
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $cartCount = 0;
            if (auth()->check()) {
                $cart = auth()->user()->cart;
                $cartCount = $cart
                    ? $cart->items()->sum('quantity')
                    : 0;
            }
            $view->with('cartCount', $cartCount);
        });
    }
}

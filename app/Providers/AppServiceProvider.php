<?php

namespace App\Providers;

use App\Models\Period;
use App\Observers\PeriodObserver;
use Illuminate\Support\ServiceProvider;

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
        Period::observe(PeriodObserver::class);
    }
}

<?php

namespace App\Providers;

use App\Models\Curriculum;
use App\Models\Period;
use App\Observers\PeriodObserver;
use App\Models\Subject;
use App\Observers\SubjectObserver;
use App\Observers\CurriculumObsever;
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
        Subject::observe(SubjectObserver::class);
        Curriculum::observe(CurriculumObsever::class);
    }
}

<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Period;
use App\Observers\PeriodObserver;
use App\Models\Subject;
use App\Observers\SubjectObserver;
use App\Observers\StudentObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Curriculum;
use App\Observers\CurriculumObserver;

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
        if (str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Period::observe(PeriodObserver::class);
        Subject::observe(SubjectObserver::class);
        Student::observe(StudentObserver::class);
        Curriculum::observe(CurriculumObserver::class);
    }
}

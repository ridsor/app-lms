<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Teacher;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use App\Models\Schedule;
use App\Policies\SchedulePolicy;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Policies\AttendancePolicy;
use App\Policies\MeetingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The model to policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    Student::class => StudentPolicy::class,
    Teacher::class => TeacherPolicy::class,
    Schedule::class => SchedulePolicy::class,
    Attendance::class => AttendancePolicy::class,
    Meeting::class => MeetingPolicy::class,
  ];

  /**
   * Register any authentication / authorization services.
   */
  public function boot(): void
  {
    $this->registerPolicies();
  }
}

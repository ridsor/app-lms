<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Teacher;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use App\Models\Schedule;
use App\Policies\SchedulePolicy;
use App\Models\Attendance;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\MeetingText;
use App\Policies\AttendancePolicy;
use App\Policies\MaterialPolicy;
use App\Policies\MeetingPolicy;
use App\Policies\MeetingTextPolicy;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Policies\TaskPolicy;
use App\Models\Exam;
use App\Models\UKK;
use App\Policies\ExamPolicy;
use App\Policies\TaskSubmissionPolicy;
use App\Policies\UKKPolicy;
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
    Material::class => MaterialPolicy::class,
    MeetingText::class => MeetingTextPolicy::class,
    Task::class => TaskPolicy::class,
    TaskSubmission::class => TaskSubmissionPolicy::class,
    Exam::class => ExamPolicy::class,
    UKK::class => UKKPolicy::class,
  ];

  /**
   * Register any authentication / authorization services.
   */
  public function boot(): void
  {
    $this->registerPolicies();
  }
}

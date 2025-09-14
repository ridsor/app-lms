<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
  /**
   * Determine whether the user can view the schedule.
   */
  public function viewPossession(User $user)
  {
    if ($user->can('attendance.view')) {
      if ($user->hasRole('teacher')) {
        return true;
      }
      if ($user->hasRole('student')) {
        return true;
      }
      if ($user->hasRole('parent')) {
        return true;
      }
    }
    return false;
  }

  public function viewAny(User $user)
  {
    if ($user->can('attendance.view')) {
      return true;
    }
    return false;
  }

  public function view(User $user, Attendance $attendance)
  {
    if ($user->can('attendance.view')) {
      if ($user->hasRole('teacher')) {
        return $attendance->schedule->teacher_id == $user->teacher->id;
      } else if ($user->hasRole('student')) {
        $student = $user->student;
        return in_array($attendance->schedule->id, optional($student->schedules)->schedule_ids ?? []);
      } else if ($user->hasRole('parent')) {
        $student = $user->parent;
        return in_array($attendance->schedule->id, optional($student->schedules)->schedule_ids ?? []);
      } else {
        return true;
      }
    }
    return false;
  }

  /**
   * Determine whether the user can update the schedule.
   */
  public function update(User $user, Attendance $attendance)
  {
    if ($user->can('attendance.edit')) {
      if ($user->hasRole('teacher')) {
        return $attendance->schedule->teacher_id == $user->teacher->id;
      } else {
        return true;
      }
    }
    return false;
  }
}

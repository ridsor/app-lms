<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class SchedulePolicy
{
  /**
   * Determine whether the user can view the schedule.
   */
  public function viewAny(User $user)
  {
    if ($user->can('schedule.*')) {
      return true;
    }
    if ($user->can('schedule.view')) {
      return true;
    }
    return false;
  }

  public function viewPossession(User $user)
  {
    if ($user->can('schedule.view')) {
      if ($user->hasRole('teacher')) {
        return true;
      }
      if ($user->hasRole('student')) {
        return true;
      }
    }
    return false;
  }

  public function view(User $user, Schedule $schedule)
  {
    if ($user->can('schedule.*')) {
      return true;
    }
    if ($user->can('schedule.view')) {
      if ($user->hasRole('teacher')) {
        return $schedule->teacher_id === $user->id;
      } else if ($user->hasRole('student')) {
        return $user->student->class_id === $schedule->class_id;
      }
    }
    return false;
  }

  /**
   * Determine whether the user can update the schedule.
   */
  public function update(User $user)
  {
    return $user->can('schedule.*');
  }

  /**
   * Determine whether the user can delete the schedule.
   */
  public function delete(User $user)
  {
    return $user->can('schedule.*');
  }

  /**
   * Determine whether the user can create a schedule.
   */
  public function create(User $user)
  {
    return $user->can('schedule.*');
  }
}

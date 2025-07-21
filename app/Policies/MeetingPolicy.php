<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
  /**
   * Determine whether the user can view the schedule.
   */

  public function viewAny(User $user)
  {
    if ($user->can('meeting.view')) {
      return true;
    }
    return false;
  }

  public function viewPossession(User $user, Meeting $meeting)
  {
    if ($user->can('meeting.view')) {
      if ($user->hasRole('teacher')) {
        return $meeting->schedule->teacher_id == $user->teacher->id;
      } else if ($user->hasRole('student')) {
        return $user->student->class_id == $meeting->schedule->class_id;
      } else if ($user->hasRole('parent')) {
        return $user->parent->class_id == $meeting->schedule->class_id;
      }
    }
    return false;
  }

  public function view(User $user, Meeting $meeting)
  {
    if ($user->can('meeting.view')) {
      if ($user->hasRole('teacher')) {
        return $meeting->schedule->teacher_id == $user->teacher->id;
      } else if ($user->hasRole('student')) {
        return $user->student->class_id == $meeting->schedule->class_id;
      } else if ($user->hasRole('parent')) {
        return $user->parent->class_id == $meeting->schedule->class_id;
      } else {
        return true;
      }
    }
    return false;
  }

  /**
   * Determine whether the user can update the schedule.
   */
  public function update(User $user, Meeting $meeting)
  {
    if ($user->can('meeting.edit')) {
      if ($user->hasRole('teacher')) {
        return $meeting->schedule->teacher_id == $user->teacher->id;
      }
      return true;
    }
    return false;
  }
}

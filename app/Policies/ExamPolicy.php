<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ExamPolicy
{
  /**
   * Determine whether the user can view the schedule.
   */
  public function viewPossession(User $user)
  {
    if ($user->can('exam.view')) {
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
    if ($user->can('exam.view')) {
      return true;
    }
    return false;
  }

  public function view(User $user, Exam $exam)
  {
    if ($user->can('exam.view')) {
      if ($user->hasRole('teacher')) {
        return $exam->schedule->teacher_id == $user->teacher->id;
      } else if ($user->hasRole('student')) {
        $student = $user->student;
        return in_array($exam->schedule->id, optional($student->schedules)->schedule_ids ?? []);
      } else if ($user->hasRole('parent')) {
        $student = $user->parent;
        return in_array($exam->schedule->id, optional($student->schedules)->schedule_ids ?? []);
      } else if ($user->hasRole('operator')) {
        return true;
      }
    }
    return false;
  }

  /**
   * Determine whether the user can update the schedule.
   */
  public function update(User $user, Exam $exam)
  {
    if ($user->can('exam.edit')) {
      if ($user->hasRole('teacher')) {
        return $exam->schedule->teacher_id == $user->teacher->id;
      } else if ($user->hasRole('operator')) {
        return true;
      }
    }
    return false;
  }

  public function create(User $user)
  {
    return $user->can('exam.create');
  }

  public function delete(User $user)
  {
    return $user->can('exam.delete');
  }
}

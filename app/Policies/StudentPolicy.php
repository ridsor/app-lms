<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class StudentPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view any students.
   */
  public function viewAny(User $user): bool
  {
    // Wakasek dan Admin dapat melihat semua siswa
    if ($user->can('student.*')) {
      return true;
    }

    // Teacher hanya dapat melihat siswa yang diwalinya
    if ($user->can('student.view.homeroomteacher')) {
      return true;
    }

    return false;
  }
  /**
   * Determine whether the user can view the student.
   */
  public function view(User $user, Student $student): bool
  {
    // Wakasek dan Admin dapat melihat semua siswa
    if ($user->can('student.*')) {
      return true;
    }

    // Teacher hanya dapat melihat siswa yang diwalinya
    if ($user->can('student.view.homeroomteacher')) {
      return $user->teacher && $student->class->homeroom_teacher_id && $user->teacher->id === $student->class->homeroom_teacher_id;
    }

    return false;
  }

  /**
   * Determine whether the user can create students.
   */
  public function create(User $user): bool
  {
    return $user->can('student.*');
  }

  /**
   * Determine whether the user can update the student.
   */
  public function update(User $user, Student $student): bool
  {
    // Wakasek dan Admin dapat mengedit semua siswa
    if ($user->can('student.*')) {
      return true;
    }

    if ($user->can('student.edit.homeroomteacher')) {
      return $user->teacher && $student->class->homeroom_teacher_id && $user->teacher->id === $student->class->homeroom_teacher_id;
    }

    return false;
  }

  /**
   * Determine whether the user can delete the student.
   */
  public function delete(User $user): bool
  {
    return $user->can('student.*');
  }
}

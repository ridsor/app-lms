<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Student;

class StudentAuthorizationHelper
{
  /**
   * Cek apakah user dapat melihat semua siswa
   */
  public static function canViewAllStudents(User $user): bool
  {
    return $user->hasPermissionTo('student.view.all');
  }

  /**
   * Cek apakah user dapat melihat siswa yang diwalinya
   */
  public static function canViewHomeroomStudents(User $user): bool
  {
    return $user->hasPermissionTo('student.view.homeroom');
  }

  /**
   * Cek apakah user dapat membuat siswa baru
   */
  public static function canCreateStudent(User $user): bool
  {
    return $user->hasPermissionTo('student.create');
  }

  /**
   * Cek apakah user dapat mengedit semua siswa
   */
  public static function canEditAllStudents(User $user): bool
  {
    return $user->hasPermissionTo('student.edit.all');
  }

  /**
   * Cek apakah user dapat mengedit kelas siswa yang diwalinya
   */
  public static function canEditHomeroomClass(User $user): bool
  {
    return $user->hasPermissionTo('student.edit.homeroom.class');
  }

  /**
   * Cek apakah user dapat menghapus siswa
   */
  public static function canDeleteStudent(User $user): bool
  {
    return $user->hasPermissionTo('student.delete');
  }

  /**
   * Cek apakah user dapat melakukan bulk edit
   */
  public static function canBulkEditStudents(User $user): bool
  {
    return $user->hasPermissionTo('student.bulk.edit');
  }

  /**
   * Cek apakah user dapat melakukan bulk delete
   */
  public static function canBulkDeleteStudents(User $user): bool
  {
    return $user->hasPermissionTo('student.bulk.delete');
  }

  /**
   * Cek apakah user dapat export data siswa
   */
  public static function canExportStudents(User $user): bool
  {
    return $user->hasPermissionTo('student.export');
  }

  /**
   * Cek apakah user adalah homeroom teacher dari student tertentu
   */
  public static function isHomeroomTeacher(User $user, Student $student): bool
  {
    if ($user->teacher && $student->homeroom_teacher_id) {
      return $user->teacher->id === $student->homeroom_teacher_id;
    }

    return false;
  }

  /**
   * Cek apakah user dapat melihat student tertentu
   */
  public static function canViewStudent(User $user, Student $student): bool
  {
    // Wakasek dan Admin dapat melihat semua siswa
    if (self::canViewAllStudents($user)) {
      return true;
    }

    // Teacher hanya dapat melihat siswa yang diwalinya
    if (self::canViewHomeroomStudents($user)) {
      return self::isHomeroomTeacher($user, $student);
    }

    return false;
  }

  /**
   * Cek apakah user dapat mengedit student tertentu
   */
  public static function canEditStudent(User $user, Student $student): bool
  {
    // Wakasek dan Admin dapat mengedit semua siswa
    if (self::canEditAllStudents($user)) {
      return true;
    }

    // Teacher hanya dapat mengedit siswa yang diwalinya
    if (self::canEditHomeroomClass($user)) {
      return self::isHomeroomTeacher($user, $student);
    }

    return false;
  }

  /**
   * Get permission level untuk user
   */
  public static function getPermissionLevel(User $user): string
  {
    if (self::canViewAllStudents($user)) {
      return 'full';
    }

    if (self::canViewHomeroomStudents($user)) {
      return 'homeroom';
    }

    return 'none';
  }

  /**
   * Get available actions untuk user pada student tertentu
   */
  public static function getAvailableActions(User $user, Student $student): array
  {
    $actions = [];

    // View action - semua yang punya akses view bisa lihat
    if (self::canViewStudent($user, $student)) {
      $actions[] = 'view';
    }

    // Edit action
    if (self::canEditStudent($user, $student)) {
      $actions[] = 'edit';
    }

    // Delete action - hanya yang punya permission delete
    if (self::canDeleteStudent($user)) {
      $actions[] = 'delete';
    }

    return $actions;
  }

  /**
   * Get permission summary untuk user
   */
  public static function getPermissionSummary(User $user): array
  {
    return [
      'view_all' => self::canViewAllStudents($user),
      'view_homeroom' => self::canViewHomeroomStudents($user),
      'create' => self::canCreateStudent($user),
      'edit_all' => self::canEditAllStudents($user),
      'edit_homeroom_class' => self::canEditHomeroomClass($user),
      'delete' => self::canDeleteStudent($user),
      'bulk_edit' => self::canBulkEditStudents($user),
      'bulk_delete' => self::canBulkDeleteStudents($user),
      'export' => self::canExportStudents($user),
      'level' => self::getPermissionLevel($user),
    ];
  }
}

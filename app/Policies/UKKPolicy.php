<?php

namespace App\Policies;

use App\Models\UKK;
use App\Models\User;

class UKKPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}


    public function viewAny(User $user)
    {
        if ($user->hasRole('operator') && $user->can('ukk.view')) {
            return true;
        }

        if ($user->can('ukk.evaluation')) {
            return true;
        }

        if ($user->can('ukk.view')) {
            $student = null;
            if ($user->hasRole('student')) {
                $student = $user->student;
            } else if ($user->hasRole('parent')) {
                $student = $user->parent;
            }

            if ($student) {
                // Biasanya UKK untuk kelas tingkat akhir
                $currentStudentLevel = $student->class->level;
                $maxLevelInMajor = $student->class->major->classes()->max('level');

                return $currentStudentLevel === $maxLevelInMajor;
            }
        }

        return false;
    }

    public function view(User $user, UKK $ukk)
    {
        if ($user->hasRole('operator') && $user->can('ukk.view')) {
            return true;
        }

        if ($user->can('ukk.evaluation')) {
            return $ukk->operator_id === $user->id;
        }

        if ($user->can('ukk.view')) {
            $student = null;
            if ($user->hasRole('student')) {
                $student = $user->student;
            } else if ($user->hasRole('parent')) {
                $student = $user->parent;
            }

            if ($student) {
                $currentStudentLevel = $student->class->level;
                $maxLevelInMajor = $student->class->major->classes()->max('level');

                return $currentStudentLevel === $maxLevelInMajor && $ukk->major === $student->class->major->name;
            }
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->can('ukk.create');
    }

    public function update(User $user, UKK $ukk)
    {
        return $user->can('ukk.edit');
    }

    public function delete(User $user, UKK $ukk)
    {
        return $user->can('ukk.delete');
    }

    public function evaluate(User $user, UKK $ukk)
    {
        return $ukk->operator_id === $user->id;
    }
}

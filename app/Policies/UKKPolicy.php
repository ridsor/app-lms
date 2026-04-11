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
        if ($user->can('ukk.view')) {
            if ($user->hasRole('operator')) {
                return true;
            }

            $student = null;
            if ($user->hasRole('student')) {
                $student = $user->student;
            } else if ($user->hasRole('parent')) {
                $student = $user->parent;
            }

            if ($student) {
                $currentStudentLevel = $student->class->level;

                $maxLevelInMajor = $student->class()->major()->classes()->max('level');

                return $currentStudentLevel === $maxLevelInMajor;
            }
        }

        return false;
    }
}

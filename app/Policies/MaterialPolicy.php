<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MaterialPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('material.view');
    }

    public function view(User $user, $material)
    {
        if ($user->can('material.view')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $material->meeting->schedule->teacher_id;
            } elseif ($user->hasRole('student')) {
                return $user->student->class_id === $material->meeting->schedule->class_id;
            } elseif ($user->hasRole('parent')) {
                return $user->student->class_id === $material->meeting->schedule->class_id;
            }
        }
        return false;
    }

    public function create(User $user)
    {
        return $user->can('material.*');
    }

    public function update(User $user, $material)
    {
        if ($user->can('material.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $material->meeting->schedule->teacher_id;
            }
        }
    }

    public function delete(User $user, $material)
    {
        if ($user->can('material.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $material->meeting->schedule->teacher_id;
            }
        }
    }
}

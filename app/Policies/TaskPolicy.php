<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class TaskPolicy
{
    public function viewPossession(User $user, $task)
    {
        if ($user->can('task.view')) {
            if ($user->hasRole('student')) {
                return $user->student->class_id === $task->meeting->schedule->class_id;
            } elseif ($user->hasRole('parent')) {
                return $user->parent->class_id === $task->meeting->schedule->class_id;
            }
        }
        return false;
    }

    public function viewAny(User $user)
    {
        return $user->can('task.view');
    }

    public function view(User $user, $task)
    {
        if ($user->can('task.view')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $task->meeting->schedule->teacher_id;
            } elseif ($user->hasRole('student')) {
                return $user->student->class_id === $task->meeting->schedule->class_id;
            } elseif ($user->hasRole('parent')) {
                return $user->parent->class_id === $task->meeting->schedule->class_id;
            }
        }
        return false;
    }

    public function create(User $user)
    {
        return $user->can('task.*');
    }

    public function update(User $user, $task)
    {
        if ($user->can('task.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $task->meeting->schedule->teacher_id;
            }
        }
    }

    public function delete(User $user, $task)
    {
        if ($user->can('task.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $task->meeting->schedule->teacher_id;
            }
        }
    }
}

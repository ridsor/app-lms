<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MeetingTextPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return $user->can('meeting_text.view') || $user->can('meeting_text.*');
    }

    public function view(User $user, $meeting_text)
    {
        if ($user->can('meeting_text.view') || $user->can('meeting_text.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $meeting_text->meeting->schedule->teacher_id;
            } elseif ($user->hasRole('student')) {
                return $user->student->class_id === $meeting_text->meeting->schedule->class_id;
            } elseif ($user->hasRole('parent')) {
                return $user->student->class_id === $meeting_text->meeting->schedule->class_id;
            }
        }
        return false;
    }

    public function create(User $user)
    {
        return $user->can('meeting_text.*');
    }

    public function update(User $user, $material)
    {
        if ($user->can('meeting_text.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $material->meeting->schedule->teacher_id;
            }
        }
    }

    public function delete(User $user, $material)
    {
        if ($user->can('meeting_text.*')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $material->meeting->schedule->teacher_id;
            }
        }
    }
}

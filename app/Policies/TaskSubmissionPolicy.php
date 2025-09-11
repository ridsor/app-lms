<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class TaskSubmissionPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('task_submission.view');
    }

    public function view(User $user, $task_submission)
    {
        if ($user->can('task_submission.view')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $task_submission->task->meeting->schedule->teacher_id;
            } elseif ($user->hasRole('student')) {
                return $user->student->class_id === $task_submission->task->meeting->schedule->class_id;
            } elseif ($user->hasRole('parent')) {
                return $user->parent->class_id === $task_submission->task->meeting->schedule->class_id;
            }
        }
        return false;
    }

    public function update(User $user, $task_submission)
    {
        if ($user->can('task_submission.edit')) {
            if ($user->hasRole('teacher')) {
                return $user->teacher->id === $task_submission->task->meeting->schedule->teacher_id;
            } elseif ($user->hasRole('student')) {
                return $user->student->class_id === $task_submission->task->meeting->schedule->class_id;
            }
        };
        return false;
    }
}

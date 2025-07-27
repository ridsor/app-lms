<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    public function view(User $user, User $profileUser)
    {
        return $user->id === $profileUser->id;
    }

    public function update(User $user, User $profileUser)
    {
        return $user->id === $profileUser->id;
    }

    public function delete(User $user, User $profileUser)
    {
        return $user->id === $profileUser->id;
    }

    public function viewForSchedule(User $user, $schedule_id)
    {
        Log::info("Checking if user {$user->username} can view schedule with ID {$schedule_id}");
        $schedule = Schedule::findOrFail($schedule_id);
        if ($user->hasRole('teacher')) {
            return $schedule->teacher_id === $user->teacher->id;
        } elseif ($user->hasRole('student')) {
            Log::info("Checking if student {$user->student->class_id} is in class {$schedule->class_id}");
            return $user->student->class_id === $schedule->class_id;
        } elseif ($user->hasRole('parent')) {
            return $user->parent->class_id === $schedule->class_id;
        }
        return false;
    }
}

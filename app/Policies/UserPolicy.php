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
}

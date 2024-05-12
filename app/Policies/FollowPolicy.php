<?php

namespace App\Policies;

use App\Models\Follow;
use App\Models\User;

class FollowPolicy
{
    public function delete(User $user, Follow $follow): bool
    {
        return $user->id === $follow->follower_id || $user->id === $follow->followee_id;
    }
}

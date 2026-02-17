<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

final class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageMeetings();
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $user->canManageMeetings();
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $user->canManageMeetings();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageMeetings();
    }
}

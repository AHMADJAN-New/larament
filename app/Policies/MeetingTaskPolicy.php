<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MeetingTask;
use App\Models\User;

final class MeetingTaskPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageMeetings();
    }

    public function update(User $user, MeetingTask $meetingTask): bool
    {
        if ($user->canEditAnyTask()) {
            return true;
        }

        if ($user->getRole() !== UserRole::Member) {
            return false;
        }

        return in_array($meetingTask->owner, [$user->name, $user->email], true);
    }

    public function delete(User $user): bool
    {
        return $user->canManageMeetings();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageMeetings();
    }
}

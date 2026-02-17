<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User as UserModel;

final class UserPolicy
{
    public function viewAny(UserModel $user): bool
    {
        return $user->canManageUsers();
    }

    public function view(UserModel $user, UserModel $model): bool
    {
        return $user->canManageUsers();
    }

    public function create(UserModel $user): bool
    {
        return $user->canManageUsers();
    }

    public function update(UserModel $user, UserModel $model): bool
    {
        return $user->canManageUsers();
    }

    public function delete(UserModel $user, UserModel $model): bool
    {
        return $user->canManageUsers();
    }

    public function deleteAny(UserModel $user): bool
    {
        return $user->canManageUsers();
    }
}

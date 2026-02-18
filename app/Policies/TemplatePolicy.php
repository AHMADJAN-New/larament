<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

final class TemplatePolicy
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
        return $user->canManageTemplates();
    }

    public function update(User $user): bool
    {
        return $user->canManageTemplates();
    }

    public function delete(User $user): bool
    {
        return $user->canManageTemplates();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageTemplates();
    }
}

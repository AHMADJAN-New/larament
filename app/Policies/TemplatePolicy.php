<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Template;
use App\Models\User;

final class TemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Template $template): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageTemplates();
    }

    public function update(User $user, Template $template): bool
    {
        return $user->canManageTemplates();
    }

    public function delete(User $user, Template $template): bool
    {
        return $user->canManageTemplates();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageTemplates();
    }
}

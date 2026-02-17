<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Secretary = 'secretary';
    case Member = 'member';
    case Viewer = 'viewer';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'مدیر',
            self::Secretary => 'منشي',
            self::Member => 'غړی',
            self::Viewer => 'کتونکی',
        };
    }

    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }

    public function canManageMeetings(): bool
    {
        return in_array($this, [self::Admin, self::Secretary], true);
    }

    public function canManageTemplates(): bool
    {
        return in_array($this, [self::Admin, self::Secretary], true);
    }

    public function canEditAnyTask(): bool
    {
        return in_array($this, [self::Admin, self::Secretary], true);
    }

    public function canViewReports(): bool
    {
        return true;
    }
}

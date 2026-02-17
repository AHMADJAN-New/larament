<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Blocked = 'blocked';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    public function label(): string
    {
        return match ($this) {
            self::Open => 'پرانیستی',
            self::InProgress => 'روان',
            self::Done => 'بشپړ',
            self::Blocked => 'بند',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskPriority: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $priority) {
            $options[$priority->value] = $priority->label();
        }

        return $options;
    }

    public function label(): string
    {
        return match ($this) {
            self::High => 'لوړ',
            self::Medium => 'منځنی',
            self::Low => 'ټیټ',
        };
    }
}

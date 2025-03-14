<?php

namespace App\Enum;

enum TaskStatus: string
{
    case pending = 'Oczekujące';
    case in_progress = 'W trakcie';
    case completed = 'Zakończone';

    public static function getValueFromName(string $name): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case->value;
            }
        }

        return null;
    }
}

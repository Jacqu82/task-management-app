<?php

namespace App\Enum;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Oczekujące',
            self::InProgress => 'W trakcie',
            self::Completed => 'Zakończone',
        };
    }
}

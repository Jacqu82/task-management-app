<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TaskDTO
{
    #[Assert\NotBlank(message: 'Tytuł jest wymagany.', groups: ['all'])]
    #[Assert\Length(max: 255, maxMessage: 'Tytuł może zawierać max 255 znaków', groups: ['all'])]
    public string $title;
    
    #[Assert\Length(max: 16777215, maxMessage: 'Wprowadzony "Opis" jest za długi', groups: ['all'])]
    public string $description;
    
    #[Assert\Choice(
        choices: ['in_progress', 'pending', 'completed'],
        message: 'Niepoprawny status',
        groups: ['status_only', 'all']
    )]
    public ?string $status = null;
}

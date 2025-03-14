<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TaskDTO
{
    #[Assert\NotBlank(message: 'Tytuł jest wymagany.')]
    #[Assert\Length(max: 255, maxMessage: 'Tytuł może zawierać max 255 znaków')]
    public string $title;

    public string $description;

    public ?string $status = null;
}

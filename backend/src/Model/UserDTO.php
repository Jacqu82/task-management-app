<?php

declare(strict_types=1);

namespace App\Model;

use App\Validator\UniqueEmail;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    #[Assert\NotBlank(message: 'Email jest wymagany.')]
    #[Assert\Email(message: 'Podaj poprawny adres email.')]
    #[UniqueEmail]
    public string $email;

    #[Assert\NotBlank(message: 'Hasło jest wymagane.')]
    #[Assert\Regex(
        pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])([#?!@$%^&*-]*).{6,}$/',
        message: 'min. 6 znaków (w tym min. cyfra, jedna duża i mała litera)'
    )]
    public string $password;
}

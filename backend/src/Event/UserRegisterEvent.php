<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\UserDTO;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    public function __construct(private readonly UserDTO $userDTO)
    {
    }

    public function getUserDTO(): UserDTO
    {
        return $this->userDTO;
    }
}

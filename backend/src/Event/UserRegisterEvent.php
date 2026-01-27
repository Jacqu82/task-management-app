<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\User;
use App\Model\UserDTO;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
	private ?User $user = null;
	
    public function __construct(private readonly UserDTO $userDTO)
    {
    }

    public function getUserDTO(): UserDTO
    {
        return $this->userDTO;
    }
	
	public function setUser(User $user): void
	{
		$this->user = $user;
	}
	
	public function getUser(): ?User
	{
		return $this->user;
	}
}

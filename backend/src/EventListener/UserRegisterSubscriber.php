<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserRegisterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function onUserRegister(UserRegisterEvent $event): void
    {
        $user = new User();
        $userDTO = $event->getUserDTO();
        $user->setEmail($userDTO->email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $userDTO->password));
        $this->userRepository->save($user);
	    
	    $event->setUser($user);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisterEvent::class => 'onUserRegister',
        ];
    }
}

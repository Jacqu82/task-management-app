<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    private const string UPDATE = 'update';
    private const string DELETE = 'delete';
    private const string SHOW = 'show';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::UPDATE, self::DELETE, self::SHOW], true)) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $task = $subject;

        return match ($attribute) {
            self::UPDATE => $this->canUpdate($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            self::SHOW => $this->canShow($task, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canUpdate(Task $task, User $user): bool
    {
        return $task->getUser() === $user;
    }

    private function canDelete(Task $task, User $user): bool
    {
        return $task->getUser() === $user;
    }

    private function canShow(Task $task, User $user): bool
    {
        return $task->getUser() === $user;
    }
}

<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Task;
use App\Event\TaskEvent;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('task_handler')]
readonly class CreateTaskHandler implements TaskHandlerInterface
{
    public function __construct(
        private TaskRepository $taskRepository,
        private Security $security
    ) {
    }

    public function supports(TaskEvent $event): bool
    {
        return null === $event->getTask();
    }

    public function handle(TaskEvent $event): void
    {
        $task = new Task();
        $task->setUser($this->security->getUser());
        $task->setTitle($event->getTaskDTO()->title);
        $task->setDescription($event->getTaskDTO()->description);
        $this->taskRepository->save($task);
    }
}

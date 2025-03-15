<?php

declare(strict_types=1);

namespace App\Handler;

use App\Event\TaskEvent;
use App\Repository\TaskRepository;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('task_handler')]
readonly class UpdateTaskHandler implements TaskHandlerInterface
{
    public function __construct(private TaskRepository $taskRepository)
    {
    }

    public function supports(TaskEvent $event): bool
    {
        return null !== $event->getTask();
    }

    public function handle(TaskEvent $event): void
    {
        $task = $event->getTask();
        $task->setTitle($event->getTaskDTO()->title);
        $task->setDescription($event->getTaskDTO()->description);
        $task->setStatus($event->getTaskDTO()->status);
        $this->taskRepository->flush();
    }
}

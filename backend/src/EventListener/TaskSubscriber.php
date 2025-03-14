<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Task;
use App\Event\TaskEvent;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class TaskSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TaskRepository $taskRepository,
        private Security $security,
    ) {
    }

    public function onTaskSave(TaskEvent $event): void
    {
        if (null === $event->getTask()) {
            $task = new Task();
            $task->setUser($this->security->getUser());
            $task->setTitle($event->getTaskDTO()->title);
            $task->setDescription($event->getTaskDTO()->description);
            $this->taskRepository->save($task);
        } else {
            $task = $event->getTask();
            $task->setTitle($event->getTaskDTO()->title);
            $task->setDescription($event->getTaskDTO()->description);
            $task->setStatus($event->getTaskDTO()->status);
            $this->taskRepository->flush();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TaskEvent::class => 'onTaskSave',
        ];
    }
}

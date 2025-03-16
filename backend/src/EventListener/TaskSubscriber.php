<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\TaskEvent;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class TaskSubscriber implements EventSubscriberInterface
{
    public function __construct(#[AutowireIterator('task_handler')] private iterable $handlers)
    {
    }

    public function onTaskSave(TaskEvent $event): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($event)) {
                $handler->handle($event);
                break;
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TaskEvent::class => 'onTaskSave',
        ];
    }
}

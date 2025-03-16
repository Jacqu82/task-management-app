<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\Event\TaskEvent;
use App\EventListener\TaskSubscriber;
use App\Handler\TaskHandlerInterface;
use PHPUnit\Framework\TestCase;

class TaskSubscriberTest extends TestCase
{
    public function testOnTaskSave_CallsFirstSupportingHandler(): void
    {
        $event = $this->createMock(TaskEvent::class);

        $supportingHandler = $this->createMock(TaskHandlerInterface::class);
        $supportingHandler
            ->expects($this->once())
            ->method('supports')
            ->with($event)
            ->willReturn(true)
        ;

        $supportingHandler
            ->expects($this->once())
            ->method('handle')
            ->with($event)
        ;

        $nonSupportingHandler = $this->createMock(TaskHandlerInterface::class);
        $nonSupportingHandler
            ->expects($this->once())
            ->method('supports')
            ->with($event)
            ->willReturn(false)
        ;

        $nonSupportingHandler
            ->expects($this->never())
            ->method('handle')
        ;

        $handlers = [$nonSupportingHandler, $supportingHandler];

        $subscriber = new TaskSubscriber($handlers);
        $subscriber->onTaskSave($event);
    }

    public function testOnTaskSave_NoHandlerSupportsEvent(): void
    {
        $event = $this->createMock(TaskEvent::class);

        $handler = $this->createMock(TaskHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('supports')
            ->with($event)
            ->willReturn(false)
        ;
        $handler
            ->expects($this->never())
            ->method('handle')
        ;

        $subscriber = new TaskSubscriber([$handler]);
        $subscriber->onTaskSave($event);
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [TaskEvent::class => 'onTaskSave'],
            TaskSubscriber::getSubscribedEvents()
        );
    }
}

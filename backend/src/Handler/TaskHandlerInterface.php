<?php

namespace App\Handler;

use App\Event\TaskEvent;

interface TaskHandlerInterface
{
    public function supports(TaskEvent $event): bool;
    public function handle(TaskEvent $event): void;
}

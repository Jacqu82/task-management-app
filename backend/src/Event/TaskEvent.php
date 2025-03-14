<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Task;
use App\Model\TaskDTO;
use Symfony\Contracts\EventDispatcher\Event;

class TaskEvent extends Event
{
    public function __construct(private readonly TaskDTO $taskDTO, private readonly ?Task $task = null)
    {
    }

    public function getTaskDTO(): TaskDTO
    {
        return $this->taskDTO;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }
}

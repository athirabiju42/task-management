<?php

namespace App\Jobs;

use App\Services\TaskService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTaskAI implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $taskId) {}

    public function handle(TaskService $taskService): void
    {
        $taskService->processAiForTask($this->taskId);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService) {}   // dependency injec

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->listForUser(
            $request->user(),
            $request->only(['status', 'priority', 'search'])
        );

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            $data['assigned_to'] = $request->user()->id;   //assign
        }

        $task = $this->taskService->store($data);

        return (new TaskResource($task->load('assignee')))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): TaskResource
    {
        $updated = $this->taskService->updateStatus(
            $task->id,
            $request->validated('status')
        );

        return new TaskResource($updated->load('assignee'));
    }

    public function aiSummary(Request $request, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task = $this->taskService->findForUser($request->user(), $task->id);

        if (! $task->ai_summary) {
            $task = $this->taskService->regenerateAiSummary($task->id);    ///ai summary sec
        }

        return response()->json([
            'data' => [
                'ai_summary' => $task->ai_summary,
                'ai_priority' => $task->ai_priority?->value,
            ],
        ]);
    }
}

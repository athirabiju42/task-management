<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Jobs\ProcessTaskAI;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $repo,
        protected AIService $aiService,
    ) {}

    public function getAssignableUsers(User $user): Collection
    {
        if ($user->role === UserRole::Admin) {
            return User::orderBy('name')->get();
        }

        return collect([$user]);
    }

    public function listForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        if ($user->role !== UserRole::Admin) {
            $filters['assigned_to'] = $user->id;
        }

        return $this->repo->all($filters);
    }

    public function findForUser(User $user, int $id): Task
    {
        $task = $this->repo->find($id);

        abort_if(! $task, 404);
        abort_if($user->role !== UserRole::Admin && $task->assigned_to !== $user->id, 403);

        return $task;
    }

    public function store(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = $this->repo->create($data);

            if (config('ai.queue_ai')) {
                ProcessTaskAI::dispatch($task->id);

                return $task;
            }

            $aiData = $this->aiService->generateSummary($task);

            return $this->repo->update($task->id, $aiData);
        });
    }

    public function update(int $id, array $data): Task
    {
        return DB::transaction(fn () => $this->repo->update($id, $data));
    }

    public function updateStatus(int $id, string $status): Task
    {
        if (! TaskStatus::tryFrom($status)) {
            abort(422, 'Invalid status');
        }

        return $this->repo->update($id, ['status' => $status]);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function regenerateAiSummary(int $id): Task
    {
        return DB::transaction(function () use ($id) {
            $task = $this->repo->find($id);
            abort_if(! $task, 404);

            $aiData = $this->aiService->generateSummary($task);

            return $this->repo->update($id, $aiData);
        });
    }

    public function processAiForTask(int $taskId): Task
    {
        $task = $this->repo->find($taskId);
        abort_if(! $task, 404);

        $aiData = $this->aiService->generateSummary($task);

        return $this->repo->update($taskId, $aiData);
    }

    public function dashboardStats(User $user): array
    {
        if ($user->role === UserRole::Admin) {
            return [
                'total' => $this->repo->countAll(),
                'completed' => $this->repo->countByStatus(TaskStatus::Completed->value),
                'pending' => $this->repo->countByStatus(TaskStatus::Pending->value),
                'high_priority' => $this->repo->countByPriority('high'),
                'in_progress' => $this->repo->countByStatus(TaskStatus::InProgress->value),
                'by_status' => [
                    'pending' => $this->repo->countByStatus(TaskStatus::Pending->value),
                    'in_progress' => $this->repo->countByStatus(TaskStatus::InProgress->value),
                    'completed' => $this->repo->countByStatus(TaskStatus::Completed->value),
                ],
            ];
        }

        $filters = ['assigned_to' => $user->id];
        $tasks = Task::query()->filter($filters);

        return [
            'total' => (clone $tasks)->count(),
            'completed' => (clone $tasks)->where('status', TaskStatus::Completed)->count(),
            'pending' => (clone $tasks)->where('status', TaskStatus::Pending)->count(),
            'high_priority' => (clone $tasks)->where('priority', 'high')->count(),
            'in_progress' => (clone $tasks)->where('status', TaskStatus::InProgress)->count(),
            'by_status' => [
                'pending' => (clone $tasks)->where('status', TaskStatus::Pending)->count(),
                'in_progress' => (clone $tasks)->where('status', TaskStatus::InProgress)->count(),
                'completed' => (clone $tasks)->where('status', TaskStatus::Completed)->count(),
            ],
        ];
    }
}

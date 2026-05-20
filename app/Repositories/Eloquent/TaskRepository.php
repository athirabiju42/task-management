<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Task::query()
            ->with('assignee')
            ->filter($filters)
            ->latest()
            ->paginate(10);
    }

    public function find(int $id): ?Task
    {
        return Cache::remember(
            "task.{$id}",
            now()->addMinutes(5),
            fn () => Task::with('assignee')->find($id)
        );
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        Cache::forget("task.{$id}");

        return $task->fresh(['assignee']);
    }

    public function delete(int $id): bool
    {
        Cache::forget("task.{$id}");

        return (bool) Task::destroy($id);
    }

    public function countByStatus(?string $status = null): int
    {
        return Task::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->count();
    }

    public function countByPriority(string $priority): int
    {
        return Task::where('priority', $priority)->count();
    }

    public function countAll(): int
    {
        return Task::count();
    }
}

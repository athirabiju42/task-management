<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->listForUser(
            $request->user(),
            $request->only(['status', 'priority', 'search'])
        );

        return view('tasks.index', compact('tasks'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Task::class);

        $users = $this->taskService->getAssignableUsers($request->user());

        return view('tasks.create', compact('users'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            $data['assigned_to'] = $request->user()->id;
        }

        $task = $this->taskService->store($data);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Request $request, Task $task): View
    {
        $this->authorize('view', $task);

        $task = $this->taskService->findForUser($request->user(), $task->id);

        return view('tasks.show', compact('task'));
    }

    public function edit(Request $request, Task $task): View
    {
        $this->authorize('update', $task);

        $users = $this->taskService->getAssignableUsers($request->user());

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();

        if (! $request->user()->isAdmin()) {
            unset($data['assigned_to']);
        }

        $this->taskService->update($task->id, $data);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task->id);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function regenerateAi(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('view', $task);

        $this->taskService->regenerateAiSummary($task->id);

        return back()->with('success', 'AI summary regenerated.');
    }
}

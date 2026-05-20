<x-task-layout title="Task List">
    @if ($tasks->isEmpty())
        <div class="tm-card py-16 text-center">
            <p class="text-lg font-semibold text-slate-800">No tasks found</p>
            <p class="mt-2 text-sm text-slate-500">Create your first task to get started.</p>
            <a href="{{ route('tasks.create') }}" class="tm-btn-primary mt-6 inline-flex">Create Task</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($tasks as $task)
                @include('partials.task-card', ['task' => $task])
            @endforeach
        </div>
        <div class="mt-8 text-slate-300">
            {{ $tasks->withQueryString()->links() }}
        </div>
    @endif
</x-task-layout>

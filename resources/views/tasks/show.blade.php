<x-task-layout title="Task Detail + AI Summary">
    <article class="tm-card mx-auto max-w-3xl">
        <header class="border-b border-slate-100 pb-4">
            <h2 class="text-xl font-bold text-slate-900">{{ $task->title }}</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">Status: {{ $task->status?->label() }}</span>
                <span @class([
                    'rounded-full px-3 py-1 text-xs font-semibold',
                    'tm-priority-high' => $task->priority?->value === 'high',
                    'tm-priority-medium' => $task->priority?->value === 'medium',
                    'tm-priority-low' => $task->priority?->value === 'low',
                ])>Priority: {{ $task->priority?->label() }}</span>
            </div>
        </header>

        <section class="mt-5 space-y-4 text-sm text-slate-700">
            <p><span class="font-semibold text-slate-900">Assigned to:</span> {{ $task->assignee?->name ?? 'Unassigned' }}</p>
            <p><span class="font-semibold text-slate-900">Due Date:</span> {{ $task->due_date?->format('Y-m-d') ?? 'Not set' }}</p>
            <div>
                <p class="font-semibold text-slate-900">Description</p>
                <p class="mt-1 leading-relaxed">{{ $task->description ?: 'No description provided.' }}</p>
            </div>
        </section>

        <section class="mt-6 rounded-xl bg-slate-100 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">AI-Generated Summary</p>
            <p class="mt-2 text-sm leading-relaxed text-slate-800">{{ $task->ai_summary ?? 'AI summary is being generated...' }}</p>
            @if ($task->ai_priority)
                <p class="mt-3 text-sm text-slate-600">
                    <span class="font-semibold">AI Summary</span> — Suggested priority: {{ $task->ai_priority?->label() }}
                </p>
            @endif
        </section>

        <footer class="mt-6 flex flex-wrap items-center justify-center gap-3 border-t border-slate-100 pt-6">
            <form method="POST" action="{{ route('tasks.regenerate-ai', $task) }}">
                @csrf
                <button type="submit" class="tm-btn-secondary inline-flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh AI Summary
                </button>
            </form>
            <a href="{{ route('tasks.edit', $task) }}" class="tm-btn-primary">Edit Task</a>
            @can('delete', $task)
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Delete</button>
                </form>
            @endcan
        </footer>
    </article>
</x-task-layout>

@props(['task'])

<article class="tm-card flex flex-col">
    <header class="mb-3 flex items-start justify-between">
        <span class="flex items-center gap-2 text-xs font-medium text-sky-600">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ $task->status?->label() }}
        </span>
        <span class="text-slate-400" aria-hidden="true">⋯</span>
    </header>

    <h3 class="text-base font-bold text-slate-900">{{ $task->title }}</h3>

    <p class="mt-2">
        <span @class([
            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset',
            'tm-priority-high' => $task->priority?->value === 'high',
            'tm-priority-medium' => $task->priority?->value === 'medium',
            'tm-priority-low' => $task->priority?->value === 'low',
        ])>
            Priority {{ $task->priority?->label() }}
        </span>
    </p>

    <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-500 line-clamp-3">
        {{ $task->description ?: 'No description.' }}
    </p>

    <footer class="mt-4 space-y-1 text-xs text-slate-500">
        <p>Due: {{ $task->due_date?->format('Y-m-d') ?? '—' }}</p>
        @if ($task->ai_priority)
            <p>AI Priority: {{ $task->ai_priority?->label() }}</p>
        @endif
        <p>Assigned: {{ $task->assignee?->name ?? 'Unassigned' }}</p>
        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="tm-btn-secondary">Edit</a>
            @endcan
            <a href="{{ route('tasks.show', $task) }}" class="tm-btn-primary">View</a>
        </div>
    </footer>
</article>

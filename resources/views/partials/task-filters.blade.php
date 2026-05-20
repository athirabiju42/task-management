@php
    use App\Enums\TaskPriority;
    use App\Enums\TaskStatus;
@endphp

<form method="GET" action="{{ request()->url() }}" class="mb-6 space-y-2">
    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search Filter Task" class="tm-input pl-11">
        </div>
        <select name="status" class="tm-select w-full sm:w-36" onchange="this.form.submit()">
            <option value="">Status</option>
            @foreach (TaskStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="priority" class="tm-select w-full sm:w-36" onchange="this.form.submit()">
            <option value="">Priority</option>
            @foreach (TaskPriority::cases() as $priority)
                <option value="{{ $priority->value }}" @selected(request('priority') === $priority->value)>{{ $priority->label() }}</option>
            @endforeach
        </select>
        @if (request()->hasAny(['search', 'status', 'priority']))
            <a href="{{ url()->current() }}" class="tm-btn-secondary text-center">Reset</a>
        @endif
    </div>
    <p class="text-xs text-slate-400">Filter User Task</p>
</form>

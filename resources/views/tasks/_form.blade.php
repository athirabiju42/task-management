@php
    use App\Enums\TaskPriority;
    use App\Enums\TaskStatus;
    $selectedPriority = old('priority', $task->priority?->value ?? 'medium');
    $selectedStatus = old('status', $task->status?->value ?? 'pending');
@endphp

<div class="space-y-5">
    <div>
        <label for="title" class="sr-only">Title</label>
        <input id="title" name="title" type="text" required
            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-lg font-bold text-slate-900 focus:border-sky-500 focus:ring-sky-500"
            value="{{ old('title', $task->title ?? '') }}" placeholder="Task title">
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea id="description" name="description" rows="4"
            class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-800 focus:border-sky-500 focus:ring-sky-500"
            placeholder="Task description">{{ old('description', $task->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <p class="mb-2 text-sm font-medium text-slate-700">Priority</p>
        <div class="flex flex-wrap gap-2">
            @foreach (TaskPriority::cases() as $priority)
                <label class="cursor-pointer">
                    <input type="radio" name="priority" value="{{ $priority->value }}" class="peer sr-only" @checked($selectedPriority === $priority->value) required>
                    <span @class([
                        'inline-flex rounded-full px-4 py-2 text-sm font-medium ring-1 ring-inset transition',
                        'peer-checked:ring-2 peer-checked:ring-sky-500',
                        'tm-priority-high peer-checked:bg-red-500 peer-checked:text-white' => $priority->value === 'high',
                        'tm-priority-medium peer-checked:bg-amber-500 peer-checked:text-white' => $priority->value === 'medium',
                        'tm-priority-low peer-checked:bg-sky-500 peer-checked:text-white' => $priority->value === 'low',
                    ])>{{ $priority->label() }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
    </div>

    <div>
        <p class="mb-2 text-sm font-medium text-slate-700">Status</p>
        <div class="flex flex-wrap gap-2">
            @foreach (TaskStatus::cases() as $status)
                <label class="cursor-pointer">
                    <input type="radio" name="status" value="{{ $status->value }}" class="peer sr-only" @checked($selectedStatus === $status->value)>
                    <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 peer-checked:bg-sky-500 peer-checked:text-white peer-checked:ring-sky-500">{{ $status->label() }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="due_date" class="block text-sm font-medium text-slate-700">Due Date</label>
           <input id="due_date" name="due_date" type="date"
    class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 bg-white focus:border-sky-500 focus:ring-sky-500"
    value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
        </div>

        @if (auth()->user()->isAdmin())
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-slate-700">Assign To</label>
                <select id="assigned_to" name="assigned_to" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 bg-white focus:border-sky-500 focus:ring-sky-500">
                    <option value="">Unassigned</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('assigned_to', $task->assigned_to ?? '') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
            </div>
        @endif
    </div>
</div>

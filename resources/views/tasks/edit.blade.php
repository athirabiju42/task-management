<x-task-layout title="Edit Task" :show-filters="false">
    <form method="POST" action="{{ route('tasks.update', $task) }}" class="tm-card mx-auto max-w-3xl">
        @csrf
        @method('PUT')
        @include('tasks._form')
        <footer class="mt-8 flex justify-center gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('tasks.show', $task) }}" class="tm-btn-secondary">Cancel</a>
            <button type="submit" class="tm-btn-primary min-w-[160px]">Save Changes</button>
        </footer>
    </form>
</x-task-layout>

<x-task-layout title="Create Task" :show-filters="false">
    <form method="POST" action="{{ route('tasks.store') }}" class="tm-card mx-auto max-w-3xl">
        @csrf
        @include('tasks._form', ['task' => null])
        <footer class="mt-8 flex justify-center gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('tasks.index') }}" class="tm-btn-secondary">Cancel</a>
            <button type="submit" class="tm-btn-primary min-w-[160px]">Save Changes</button>
        </footer>
    </form>
</x-task-layout>

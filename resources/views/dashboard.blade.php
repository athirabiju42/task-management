<x-task-layout title="Dashboard" :show-filters="false">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="tm-card">
            <p class="text-sm text-slate-500">Total Tasks</p>
            <p class="mt-1 text-4xl font-bold text-slate-900">{{ $stats['total'] }}</p>
        </div>
        <div class="tm-card">
            <p class="text-sm text-emerald-600">Completed</p>
            <p class="mt-1 text-4xl font-bold text-emerald-700">{{ $stats['completed'] }}</p>
        </div>
        <div class="tm-card">
            <p class="text-sm text-amber-600">Pending</p>
            <p class="mt-1 text-4xl font-bold text-amber-700">{{ $stats['pending'] }}</p>
        </div>
        <div class="tm-card">
            <p class="text-sm text-red-600">High Priority</p>
            <p class="mt-1 text-4xl font-bold text-red-700">{{ $stats['high_priority'] }}</p>
        </div>
    </div>

    <div class="mt-6 tm-card">
        <h3 class="text-lg font-semibold text-slate-900">Tasks by Status</h3>
        <div class="mt-4 h-64">
            <canvas id="dashboardChart"></canvas>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('dashboardChart');
        if (!el || typeof Chart === 'undefined') return;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        {{ $stats['by_status']['pending'] }},
                        {{ $stats['by_status']['in_progress'] }},
                        {{ $stats['by_status']['completed'] }},
                    ],
                    backgroundColor: ['#94a3b8', '#0ea5e9', '#10b981'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
            },
        });
    });
    </script>
    @endpush
</x-task-layout>

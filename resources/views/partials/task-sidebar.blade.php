@php
    $stats = $sidebarStats ?? ['total' => 0, 'completed' => 0, 'pending' => 0, 'by_status' => ['pending' => 0, 'in_progress' => 0, 'completed' => 0]];
@endphp

<div class="space-y-5">
    {{-- user card section ;;;;; --}}
    <div class="tm-card !p-4">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500">{{ auth()->user()->role?->label() }}</p>
            </div>
        </div>

        <nav class="mt-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 {{ request()->routeIs('dashboard') ? '!bg-sky-500 !text-white' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('tasks.index') }}" class="block rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-50 {{ request()->routeIs('tasks.*') ? '!bg-sky-500 !text-white' : '' }}">
                Tasks
            </a>
            @if (auth()->user()->isAdmin())
                <span class="block rounded-lg px-3 py-2 text-slate-400">Users <span class="text-xs">(Only visible to Admin)</span></span>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left font-medium text-slate-600 hover:bg-slate-50">Logout</button>
            </form>
        </nav>
    </div>

    {{-- circular stats section ;;;;; .--}}
    <div class="tm-card !p-4">
        <div class="grid grid-cols-3 gap-2 text-center">
            <div>
                <div class="relative mx-auto h-16 w-16">
                    <canvas id="statTotal" class="h-16 w-16"></canvas>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-slate-800">{{ $stats['total'] }}</span>
                </div>
                <p class="mt-1 text-[10px] font-medium text-slate-500">Total</p>
            </div>
            <div>
                <div class="relative mx-auto h-16 w-16">
                    <canvas id="statCompleted" class="h-16 w-16"></canvas>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-slate-800">{{ $stats['completed'] }}</span>
                </div>
                <p class="mt-1 text-[10px] font-medium text-slate-500">Done</p>
            </div>
            <div>
                <div class="relative mx-auto h-16 w-16">
                    <canvas id="statPending" class="h-16 w-16"></canvas>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-slate-800">{{ $stats['pending'] }}</span>
                </div>
                <p class="mt-1 text-[10px] font-medium text-slate-500">Pending</p>
            </div>
        </div>
        <p class="mt-3 text-center text-xs font-medium text-slate-500">Monthly Task Completion</p>
    </div>

    {{-- bar chart section...--}}
    <div class="rounded-2xl bg-slate-900 p-4 shadow-lg">
        <p class="mb-3 text-sm font-semibold text-white">Monthly Task Completion</p>
        <div class="h-36">
            <canvas id="sidebarBarChart"></canvas>
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const total = {{ $stats['total'] }};
    const completed = {{ $stats['completed'] }};
    const pending = {{ $stats['pending'] }};
    const ring = (id, value, color) => {
        const el = document.getElementById(id);
        if (!el) return;
        const pct = total > 0 ? Math.round((value / total) * 100) : 0;
        new Chart(el, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [pct, 100 - pct],
                    backgroundColor: [color, '#e2e8f0'],
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
            }
        });
    };
    ring('statTotal', total, '#0ea5e9');
    ring('statCompleted', completed, '#10b981');
    ring('statPending', pending, '#f59e0b');

    const barEl = document.getElementById('sidebarBarChart');
    if (barEl) {
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                    data: [
                        {{ $stats['by_status']['pending'] }},
                        {{ $stats['by_status']['in_progress'] }},
                        {{ $stats['completed'] }},
                        {{ $stats['total'] }},
                        {{ $stats['by_status']['completed'] }},
               
                    ],
                    backgroundColor: '#0ea5e9',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } },
                    y: { grid: { color: '#334155' }, ticks: { color: '#94a3b8', font: { size: 10 } }, beginAtZero: true },
                },
            },
        });
    }
});
</script>
@endpush
@endonce

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-800 font-sans text-white antialiased" style="font-family: 'Inter', sans-serif;">
    <div class="flex min-h-screen flex-col lg:flex-row">
        <main class="order-2 flex-1 p-4 sm:p-6 lg:order-1 lg:p-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $title }}</h1>
                <a href="{{ route('tasks.create') }}" class="tm-btn-primary shrink-0 self-start sm:self-auto">
                    + New Task
                </a>
            </div>

            @if ($showFilters)
                @include('partials.task-filters')
            @endif

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-400/30 bg-emerald-500/20 px-4 py-3 text-sm text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <aside class="order-1 w-full shrink-0 border-b border-slate-700 bg-slate-900/50 p-4 lg:order-2 lg:w-80 lg:border-b-0 lg:border-l lg:p-6">
            @include('partials.task-sidebar')
        </aside>
    </div>
    @stack('scripts')
</body>
</html>

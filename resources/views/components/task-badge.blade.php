@props(['type' => 'status', 'value'])

@php
    $colors = match($type) {
        'priority', 'ai_priority' => match($value) {
            'high' => 'bg-red-100 text-red-800 ring-red-200',
            'medium' => 'bg-amber-100 text-amber-800 ring-amber-200',
            'low' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            default => 'bg-gray-100 text-gray-800 ring-gray-200',
        },
        'status' => match($value) {
            'completed' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            'in_progress' => 'bg-blue-100 text-blue-800 ring-blue-200',
            'pending' => 'bg-gray-100 text-gray-800 ring-gray-200',
            default => 'bg-gray-100 text-gray-800 ring-gray-200',
        },
        default => 'bg-gray-100 text-gray-800 ring-gray-200',
    };
    $label = str($value)->replace('_', ' ')->title();
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {$colors}"]) }}>
    {{ $label }}
</span>

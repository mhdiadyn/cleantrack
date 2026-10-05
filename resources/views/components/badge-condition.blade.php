@props(['condition'])

@php
    $classes = match($condition) {
        'Bersih' => 'bg-teal-50 text-teal-700 border-teal-200',
        'Cukup Bersih' => 'bg-sky-50 text-sky-700 border-sky-200',
        'Kotor' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Sangat Kotor' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium border {$classes}"]) }}>
    {{ $condition }}
</span>

@props(['status'])

@php
    $classes = match($status) {
        'Sudah Dibersihkan' => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-emerald-600/20',
        'Sedang Dibersihkan' => 'bg-amber-50 text-amber-700 border-amber-200 ring-amber-600/20',
        'Perlu Dibersihkan' => 'bg-rose-50 text-rose-700 border-rose-200 ring-rose-600/20',
        default => 'bg-slate-50 text-slate-700 border-slate-200 ring-slate-600/20',
    };

    $dotClass = match($status) {
        'Sudah Dibersihkan' => 'bg-emerald-500',
        'Sedang Dibersihkan' => 'bg-amber-500 animate-pulse',
        'Perlu Dibersihkan' => 'bg-rose-500',
        default => 'bg-slate-400',
    };

    $icon = match($status) {
        'Sudah Dibersihkan' => 'fa-circle-check',
        'Sedang Dibersihkan' => 'fa-arrows-rotate',
        'Perlu Dibersihkan' => 'fa-triangle-exclamation',
        default => 'fa-clock',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ring-1 ring-inset shadow-xs {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
    {{ $status }}
</span>

@props(['status'])

@php
    $classes = match($status) {
        'Sudah Dibersihkan' => 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60 ring-emerald-600/20',
        'Sedang Dibersihkan' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/60 ring-amber-600/20',
        'Perlu Dibersihkan' => 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/60 ring-rose-600/20',
        default => 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 ring-slate-600/20',
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

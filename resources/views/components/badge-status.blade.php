@props(['status'])

@php
    $classes = match($status) {
        'Sudah Dibersihkan' => 'bg-[#daffd5] text-[#006622] border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a]',
        'Sedang Dibersihkan' => 'bg-[#e0f2fe] text-[#0369a1] border-[#bae6fd] dark:bg-[#082f49] dark:text-[#7dd3fc] dark:border-[#0284c7]',
        'Perlu Dibersihkan' => 'bg-[#fef3c7] text-[#b45309] border-[#fde68a] dark:bg-[#451a03] dark:text-[#fcd34d] dark:border-[#78350f]',
        'Belum Diperiksa' => 'bg-[#f5f3f3] text-[#5c3f3f] border-[#eae8e7] dark:bg-[#262727] dark:text-[#d1cfce] dark:border-[#3b3d3d]',
        default => 'bg-[#f5f3f3] text-[#5c3f3f] border-[#eae8e7] dark:bg-[#262727] dark:text-[#d1cfce] dark:border-[#3b3d3d]',
    };

    $dotClass = match($status) {
        'Sudah Dibersihkan' => 'bg-[#00822d]',
        'Sedang Dibersihkan' => 'bg-[#0284c7]',
        'Perlu Dibersihkan' => 'bg-[#d97706]',
        default => 'bg-[#916f6e]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {$classes}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
    <span>{{ $status }}</span>
</span>

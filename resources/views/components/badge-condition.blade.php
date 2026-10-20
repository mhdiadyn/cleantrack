@props(['condition'])

@php
    $classes = match($condition) {
        'Bersih' => 'bg-[#daffd5] text-[#006622] border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a]',
        'Cukup Bersih' => 'bg-[#e0f2fe] text-[#0369a1] border-[#bae6fd] dark:bg-[#082f49] dark:text-[#7dd3fc] dark:border-[#0284c7]',
        'Kotor' => 'bg-[#fef3c7] text-[#b45309] border-[#fde68a] dark:bg-[#451a03] dark:text-[#fcd34d] dark:border-[#78350f]',
        'Sangat Kotor' => 'bg-[#ffdad6] text-[#ba1a1a] border-[#ffb4ab] dark:bg-[#410002] dark:text-[#ffb4ab] dark:border-[#93000a]',
        default => 'bg-[#f5f3f3] text-[#5c3f3f] border-[#eae8e7] dark:bg-[#262727] dark:text-[#d1cfce] dark:border-[#3b3d3d]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border {$classes}"]) }}>
    {{ $condition }}
</span>

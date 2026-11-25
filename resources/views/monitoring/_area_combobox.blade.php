{{--
    Area Combobox — Reusable partial for monitoring create & edit forms.

    Props (passed via @include with array):
      $areas          : Collection<Area>  — active areas from controller
      $selectedAreaId : int|null          — currently selected area_id (old input or model value)
      $customAreaName : string|null       — currently entered custom area name (old input or model value)
--}}

{{-- Build the JS-compatible area list from PHP --}}
@php
    $areaList = $areas->map(fn ($a) => [
        'id'    => $a->id,
        'label' => ($a->code ?? '') . ' - ' . $a->name . ' (' . $a->floor_label . ' • ' . $a->location . ')',
        'name'  => $a->name,
    ])->values()->toJson();

    $initAreaId   = old('area_id',          $selectedAreaId ?? '');
    $initCustom   = old('custom_area_name', $customAreaName ?? '');

    // Build initial display label
    $initLabel = '';
    if ($initCustom) {
        $initLabel = $initCustom;
    } elseif ($initAreaId) {
        $found = $areas->firstWhere('id', (int) $initAreaId);
        if ($found) {
            $initLabel = ($found->code ?? '') . ' - ' . $found->name . ' (' . $found->floor_label . ' • ' . $found->location . ')';
        }
    }
@endphp

<div
    x-data="areaCombobox({
        areas:         {{ $areaList }},
        initAreaId:    '{{ $initAreaId }}',
        initCustom:    @js($initCustom),
        initLabel:     @js($initLabel)
    })"
    class="relative"
    @keydown.escape="closeDropdown()"
    @click.outside="closeDropdown()"
>
    {{-- Hidden fields submitted with the form --}}
    <input type="hidden" name="area_id"          :value="selectedAreaId">
    <input type="hidden" name="custom_area_name" :value="customAreaName">

    {{-- Single visible input (combobox) --}}
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#9d8b90] dark:text-[#8c8282]">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </div>

        <input
            type="text"
            id="area_combobox"
            x-model="query"
            @input="onInput()"
            @focus="onFocus()"
            @keydown.arrow-down.prevent="moveDown()"
            @keydown.arrow-up.prevent="moveUp()"
            @keydown.enter.prevent="selectHighlighted()"
            @keydown.tab="closeDropdown()"
            autocomplete="off"
            spellcheck="false"
            placeholder="Cari atau ketik nama area…"
            class="input-clean text-sm pl-10 pr-9 @if($errors->hasAny(['area_id', 'custom_area_name'])) border-[#ba1a1a] @endif"
        >

        {{-- Status icon: check (selected from list) / pen (custom text) / clear --}}
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1.5">
            <span x-show="selectedAreaId && !customAreaName" class="text-[#00822d] dark:text-[#83fc8e]" title="Area dipilih dari daftar">
                <i class="fa-solid fa-circle-check text-xs"></i>
            </span>
            <span x-show="customAreaName && !selectedAreaId" class="text-[#b45309] dark:text-[#fbbf24]" title="Nama area bebas">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
            </span>
            <button
                type="button"
                x-show="query.length > 0"
                @click="clear()"
                class="text-[#9d8b90] hover:text-[#1b1c1c] dark:hover:text-white transition-colors cursor-pointer"
                tabindex="-1"
                title="Hapus"
            >
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    </div>

    {{-- Dropdown list --}}
    <div
        x-show="open && filtered.length > 0"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-[#1f2020] border border-[#eae8e7] dark:border-[#333535] rounded-xl shadow-lg overflow-hidden"
        style="max-height: 272px; overflow-y: auto;"
    >
        <template x-for="(area, idx) in filtered" :key="area.id">
            <button
                type="button"
                @click="selectArea(area)"
                @mouseenter="highlighted = idx"
                class="w-full text-left px-4 py-2.5 text-xs text-[#1b1c1c] dark:text-[#f2f0f0] hover:bg-[#f5f3f3] dark:hover:bg-[#262727] transition-colors flex items-center gap-2 border-b border-[#eae8e7] dark:border-[#2e3030] last:border-0"
                :class="{ 'bg-[#fff1f0] dark:bg-[#3b0b14] text-[#dc143c] dark:text-[#ffb3b3] font-semibold': highlighted === idx }"
            >
                <i class="fa-solid fa-location-dot text-[#dc143c] dark:text-[#ff7070] text-[10px] shrink-0"></i>
                <span x-text="area.label" class="truncate"></span>
            </button>
        </template>
    </div>

    {{-- Hint: "Simpan sebagai nama bebas" when typing something not in list --}}
    <div
        x-show="open && query.length >= 2 && filtered.length === 0"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-[#1f2020] border border-[#eae8e7] dark:border-[#333535] rounded-xl shadow-lg overflow-hidden"
    >
        <button
            type="button"
            @click="useCustom()"
            class="w-full text-left px-4 py-3 text-xs text-[#b45309] dark:text-[#fbbf24] hover:bg-[#fffbeb] dark:hover:bg-[#2a2010] transition-colors flex items-center gap-2"
        >
            <i class="fa-solid fa-pen-to-square text-[10px] shrink-0"></i>
            <span>Simpan sebagai nama bebas: "<span x-text="query" class="font-semibold"></span>"</span>
        </button>
    </div>

    {{-- Validation error --}}
    @if($errors->hasAny(['area_id', 'custom_area_name']))
        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">
            {{ $errors->first('custom_area_name') ?: $errors->first('area_id') }}
        </p>
    @endif

    {{-- Status hint --}}
    <p class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-1.5" x-show="!customAreaName">
        Ketik untuk mencari area dari daftar, atau masukkan nama area bebas jika tidak ada.
    </p>
    <p class="text-[10px] text-[#b45309] dark:text-[#fbbf24] mt-1.5" x-show="customAreaName && !selectedAreaId">
        <i class="fa-solid fa-circle-info text-[9px]"></i>
        Nama bebas — hanya disimpan pada pemeriksaan ini, tidak terhubung ke daftar area.
    </p>
</div>

@once
@push('scripts')
<script>
function areaCombobox({ areas, initAreaId, initCustom, initLabel }) {
    return {
        // State
        areas,
        query:          initLabel || '',
        open:           false,
        highlighted:    -1,
        selectedAreaId: initAreaId || '',
        customAreaName: initCustom || '',

        // Filtered list (max 60 items)
        get filtered() {
            if (!this.query.trim()) return this.areas.slice(0, 60);
            const q = this.query.toLowerCase();
            return this.areas.filter(a => a.label.toLowerCase().includes(q)).slice(0, 60);
        },

        onFocus() {
            this.open = true;
            this.highlighted = -1;
        },

        onInput() {
            this.open = true;
            this.highlighted = -1;
            // If user clears query or modifies it, reset selection
            this.selectedAreaId = '';
            this.customAreaName = '';
        },

        selectArea(area) {
            this.query          = area.label;
            this.selectedAreaId = area.id;
            this.customAreaName = '';
            this.open           = false;
            this.highlighted    = -1;
        },

        useCustom() {
            this.customAreaName = this.query.trim();
            this.selectedAreaId = '';
            this.open           = false;
        },

        selectHighlighted() {
            if (this.highlighted >= 0 && this.filtered[this.highlighted]) {
                this.selectArea(this.filtered[this.highlighted]);
            } else if (this.query.trim().length >= 2 && this.filtered.length === 0) {
                this.useCustom();
            }
        },

        moveDown() {
            if (!this.open) { this.open = true; return; }
            this.highlighted = Math.min(this.highlighted + 1, this.filtered.length - 1);
            this.$nextTick(() => this.scrollToHighlighted());
        },

        moveUp() {
            this.highlighted = Math.max(this.highlighted - 1, -1);
            this.$nextTick(() => this.scrollToHighlighted());
        },

        scrollToHighlighted() {
            const list = this.$el.querySelector('[x-show="open && filtered.length > 0"]');
            if (!list) return;
            const item = list.querySelectorAll('button')[this.highlighted];
            if (item) item.scrollIntoView({ block: 'nearest' });
        },

        closeDropdown() {
            this.open = false;
            // If query doesn't match any selected item, treat as custom
            if (this.query.trim() && !this.selectedAreaId) {
                this.customAreaName = this.query.trim();
            }
        },

        clear() {
            this.query          = '';
            this.selectedAreaId = '';
            this.customAreaName = '';
            this.open           = false;
            this.$nextTick(() => document.getElementById('area_combobox').focus());
        },
    };
}
</script>
@endpush
@endonce

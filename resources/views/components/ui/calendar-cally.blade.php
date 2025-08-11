@props([
    'withMonth' => true,
    'showPrevNext' => true,
])

<calendar-date {{ $attributes->merge(['class' => 'cally']) }}>
    @if($showPrevNext)
        <x-heroicon-o-chevron-left aria-label="Previous" class="fill-current size-4" slot="previous" />
        <x-heroicon-o-chevron-right aria-label="Next" class="fill-current size-4" slot="next" />
    @endif
    @if($withMonth)
        <calendar-month></calendar-month>
    @endif
    {{ $slot }}
</calendar-date>



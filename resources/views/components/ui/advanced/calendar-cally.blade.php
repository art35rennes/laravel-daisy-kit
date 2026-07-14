@props([
    'mode' => 'date',
    'months' => 1,
    'showPrevNext' => true,
    'monthsClass' => 'flex flex-wrap justify-center gap-4',
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'prevIcon' => null,
    'nextIcon' => null,
])

@php
    $tag = match ($mode) {
        'range' => 'calendar-range',
        'multi' => 'calendar-multi',
        default => 'calendar-date',
    };
@endphp

<{{ $tag }}
    {{ $attributes->merge(['class' => 'cally rounded-box border border-base-300 bg-base-100 shadow-lg']) }}
    @if(!is_null($value)) value="{{ $value }}" @endif
    @if(!is_null($min)) min="{{ $min }}" @endif
    @if(!is_null($max)) max="{{ $max }}" @endif
    @if(!is_null($locale)) locale="{{ $locale }}" @endif
    @if($months > 1) months="{{ (int) $months }}" @endif
>
    @if($showPrevNext)
        @isset($previous)
            <span slot="previous">{{ $previous }}</span>
        @elseif(!is_null($prevIcon))
            <span slot="previous">{{ $prevIcon }}</span>
        @else
            <x-bi-chevron-left aria-label="{{ __('daisy::calendar.previous') }}" class="size-4 fill-current" slot="previous" />
        @endisset

        @isset($next)
            <span slot="next">{{ $next }}</span>
        @elseif(!is_null($nextIcon))
            <span slot="next">{{ $nextIcon }}</span>
        @else
            <x-bi-chevron-right aria-label="{{ __('daisy::calendar.next') }}" class="size-4 fill-current" slot="next" />
        @endisset
    @endif

    @isset($heading)
        <span slot="heading">{{ $heading }}</span>
    @endisset

    @if($months > 1)
        <div class="{{ $monthsClass }}">
            @for($i = 0; $i < (int) $months; $i++)
                <calendar-month @if($i > 0) offset="{{ $i }}" @endif></calendar-month>
            @endfor
        </div>
    @else
        <calendar-month></calendar-month>
    @endif

    {{ $slot ?? '' }}
</{{ $tag }}>

@include('daisy::components.partials.assets')

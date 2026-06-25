@props(['panel'])

<div data-reporting-chart="line" role="img" aria-label="{{ $panel['title'] }}">
    <svg viewBox="0 0 260 110" class="h-40 w-full text-secondary">
        <g class="text-base-300">
            <line x1="8" y1="20" x2="252" y2="20" stroke="currentColor" stroke-width="1" />
            <line x1="8" y1="48" x2="252" y2="48" stroke="currentColor" stroke-width="1" />
            <line x1="8" y1="76" x2="252" y2="76" stroke="currentColor" stroke-width="1" />
        </g>
        <polyline points="{{ $panel['points'] }}" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        @foreach(explode(' ', $panel['points']) as $point)
            @php
                [$x, $y] = explode(',', $point);
            @endphp
            <circle cx="{{ $x }}" cy="{{ $y }}" r="3.5" fill="currentColor" />
        @endforeach
    </svg>
    <div class="mt-2 flex justify-between gap-2 text-xs text-base-content/60">
        @foreach($panel['labels'] as $index => $label)
            <span class="text-center">
                <span class="block font-semibold text-base-content">{{ $panel['values'][$index] }}</span>
                {{ $label }}
            </span>
        @endforeach
    </div>
</div>

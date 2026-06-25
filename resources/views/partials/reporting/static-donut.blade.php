@props(['panel'])

<div class="grid gap-4 md:grid-cols-2 md:items-center" data-reporting-chart="donut">
    <div class="relative mx-auto h-40 w-40">
        <svg viewBox="0 0 42 42" class="h-40 w-40 -rotate-90" role="img" aria-label="{{ $panel['title'] }}">
            <circle cx="21" cy="21" r="15.9155" fill="none" stroke="currentColor" stroke-width="6" class="text-base-200" />
            @foreach($panel['segments'] as $segment)
                <circle
                    cx="21"
                    cy="21"
                    r="15.9155"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="6"
                    stroke-dasharray="{{ $segment['dash'] }} {{ 100 - $segment['dash'] }}"
                    stroke-dashoffset="-{{ $segment['offset'] }}"
                    class="{{ $segment['class'] }}"
                />
            @endforeach
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
            <span class="text-2xl font-bold leading-none">{{ $panel['center'] }}</span>
            <span class="mt-1 text-xs text-base-content/60">{{ $panel['centerLabel'] }}</span>
        </div>
    </div>
    <div class="space-y-2">
        @foreach($panel['segments'] as $segment)
            <div class="flex items-center justify-between gap-3 text-xs">
                <span class="inline-flex min-w-0 items-center gap-2">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-current {{ $segment['class'] }}"></span>
                    <span class="truncate">{{ $segment['label'] }}</span>
                </span>
                <span class="shrink-0 font-semibold">{{ $segment['value'] }}</span>
            </div>
        @endforeach
    </div>
</div>

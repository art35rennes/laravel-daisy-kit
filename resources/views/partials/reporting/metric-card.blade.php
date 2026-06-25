@props(['kpi', 'toneClasses'])

<article class="rounded-box border border-base-300 bg-base-100 p-4">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-sm font-bold">{{ $kpi['label'] }}</h3>
            <p class="mt-2 text-4xl font-bold leading-none tracking-tight">{{ $kpi['value'] }}</p>
            <p class="mt-2 text-sm text-base-content/60">{{ $kpi['unit'] }}</p>
        </div>
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $toneClasses[$kpi['tone']]['soft'] }} {{ $toneClasses[$kpi['tone']]['text'] }}">
            <x-daisy::ui.advanced.icon :name="$kpi['icon']" class="h-5 w-5" />
        </span>
    </div>
    <div class="mt-4 flex min-h-9 items-end justify-between gap-3">
        <p class="text-xs font-semibold {{ $toneClasses[$kpi['tone']]['text'] }}">{{ $kpi['trend'] }}</p>
        @if(! empty($kpi['sparkline']))
            <svg class="h-8 w-32 shrink-0 text-success" data-reporting-chart="sparkline" viewBox="0 0 142 28" aria-label="Tendance {{ $kpi['label'] }}" role="img">
                <polyline points="{{ $kpi['sparkline'] }}" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                <line x1="0" y1="24" x2="142" y2="24" stroke="currentColor" stroke-width="1" class="opacity-20" />
            </svg>
        @endif
    </div>
</article>

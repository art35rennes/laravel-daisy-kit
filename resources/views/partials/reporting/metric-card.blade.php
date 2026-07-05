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
        <p class="min-w-0 text-xs font-semibold leading-snug {{ $toneClasses[$kpi['tone']]['text'] }}">{{ $kpi['trend'] }}</p>
        @if(! empty($kpi['sparklineData']))
            <x-daisy::charts.sparkline
                class="!bg-transparent !p-0 !shadow-none !border-0 shrink-0"
                height="50px"
                width="140px"
                :categories="$kpi['sparklineLabels'] ?? []"
                :series="[['name' => $kpi['label'], 'data' => $kpi['sparklineData']]]"
                :drilldown-url="$kpi['drilldownUrl'] ?? null"
                :drilldown-params="$kpi['drilldownParams'] ?? []"
                value-format="number"
                empty-message="Aucune donnée disponible"
                :aria="true"
            />
        @endif
    </div>
</article>

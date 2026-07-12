@props(['panel', 'section', 'toneClasses', 'detailedUrl' => '#', 'detailModalId' => null])

@php
    $chartKey = $panel['chart'] ?? $panel['type'];
    $chartParams = ['section' => $section['id'], 'chart' => $chartKey];
    $numericValue = function ($value) {
        preg_match('/-?\d+(?:[.,]\d+)?/', (string) $value, $matches);

        return isset($matches[0]) ? (float) str_replace(',', '.', $matches[0]) : 0.0;
    };
    $slug = fn ($value) => \Illuminate\Support\Str::slug((string) $value);
    $segmentColors = [
        'text-primary' => 'primary',
        'text-info' => 'info',
        'text-success' => 'success',
        'text-warning' => 'warning',
        'text-error' => 'error',
        'text-lime-500' => 'accent',
        'text-base-content/30' => 'neutral',
    ];
@endphp

<article class="min-w-0 rounded-box border border-base-300 bg-base-100 p-4">
    <div class="mb-4">
        <h3 class="text-sm font-bold">{{ $panel['title'] }}</h3>
        @if(! empty($panel['caption']))
            <p class="mt-1 text-xs text-base-content/60">{{ $panel['caption'] }}</p>
        @endif
    </div>

    @if($panel['type'] === 'donut')
        @php
            $segments = $panel['segments'] ?? [];
            $categories = array_map(fn ($segment) => $segment['label'], $segments);
            $data = array_map(fn ($segment) => [
                'name' => $segment['label'],
                'value' => $numericValue($segment['value']),
                'color' => $segmentColors[$segment['class'] ?? ''] ?? ($segment['color'] ?? $section['tone']),
                'drilldown' => [$panel['filter'] ?? 'segment' => $slug($segment['label'])],
                'meta' => [
                    'section' => $section['id'],
                    'chart' => $chartKey,
                    $panel['filter'] ?? 'segment' => $slug($segment['label']),
                ],
                'action' => array_filter([
                    'type' => 'event',
                    'intent' => 'detail',
                    'target' => $detailModalId ? '#'.$detailModalId : null,
                ]),
                'tooltip' => [
                    'rows' => array_values(array_filter([
                        ! empty($segment['detail']) ? ['label' => 'Part', 'value' => $segment['detail']] : null,
                    ])),
                ],
            ], $segments);
        @endphp
        <x-daisy::charts.donut
            class="!bg-transparent !p-0 !shadow-none !border-0"
            height="190px"
            :title="null"
            :subtitle="null"
            :categories="$categories"
            :series="[['name' => $panel['title'], 'data' => $data]]"
            :legend="false"
            :drilldown-url="$detailedUrl"
            :drilldown-params="$chartParams"
            :markers="$panel['markers'] ?? []"
            :center-value="$panel['center'] ?? null"
            :center-label="$panel['centerLabel'] ?? null"
            :options="['series' => [['label' => ['show' => false], 'labelLine' => ['show' => false]]]]"
            value-format="number"
            empty-message="Aucune donnée disponible"
        />
        <div class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
            @foreach($segments as $segment)
                <button
                    type="button"
                    class="daisy-chart-legend-item flex w-full min-w-0 items-center justify-between gap-3 rounded-field px-1 py-1 text-left hover:bg-base-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    data-chart-legend-index="{{ $loop->index }}"
                    aria-label="Mettre en évidence {{ $segment['label'] }}"
                >
                    <span class="flex min-w-0 items-center gap-2 truncate">
                        <span class="size-2 shrink-0 rounded-full bg-current {{ $segment['class'] ?? 'text-primary' }}"></span>
                        <span class="truncate">{{ $segment['label'] }}</span>
                    </span>
                    <span class="shrink-0 font-semibold">{{ $segment['value'] }}@if(! empty($segment['detail'])) <span class="text-base-content/50">({{ $segment['detail'] }})</span>@endif</span>
                </button>
            @endforeach
        </div>
    @elseif($panel['type'] === 'bars')
        @php
            $items = $panel['items'] ?? [];
            $categories = array_map(fn ($item) => $item['label'], $items);
            $data = array_map(fn ($item) => [
                'name' => $item['label'],
                'value' => $numericValue($item['value']),
                'drilldown' => [$panel['filter'] ?? 'period' => $slug($item['label'])],
            ], $items);
        @endphp
        <x-daisy::charts.bar
            class="!bg-transparent !p-0 !shadow-none !border-0"
            height="190px"
            :colors="[$panel['tone'] ?? $section['tone']]"
            :categories="$categories"
            :series="[['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]]"
            :legend="false"
            :toolbar="false"
            :drilldown-url="$detailedUrl"
            :drilldown-params="$chartParams"
            :markers="$panel['markers'] ?? []"
            :zoom="$panel['zoom'] ?? false"
            :zoom-mode="$panel['zoomMode'] ?? 'inside'"
            :show-values="true"
            value-format="number"
            empty-message="Aucune donnée disponible"
        />
    @elseif($panel['type'] === 'line')
        @php
            $labels = $panel['labels'] ?? [];
            $values = $panel['values'] ?? [];
            $data = array_map(fn ($value, $index) => [
                'name' => $labels[$index] ?? "Point {$index}",
                'value' => $numericValue($value),
                'drilldown' => [$panel['filter'] ?? 'date' => $slug($labels[$index] ?? $index)],
            ], $values, array_keys($values));
        @endphp
        <x-daisy::charts.line
            class="!bg-transparent !p-0 !shadow-none !border-0"
            height="190px"
            :colors="[$panel['tone'] ?? $section['tone']]"
            :categories="$labels"
            :series="[['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]]"
            :legend="false"
            :drilldown-url="$detailedUrl"
            :drilldown-params="$chartParams"
            :markers="$panel['markers'] ?? []"
            :zoom="$panel['zoom'] ?? false"
            :zoom-mode="$panel['zoomMode'] ?? 'inside'"
            :show-values="true"
            value-format="number"
            empty-message="Aucune donnée disponible"
        />
    @else
        @php
            $items = $panel['items'] ?? [];
            $categories = array_map(fn ($item) => $item['label'], $items);
            $data = array_map(fn ($item) => [
                'name' => $item['label'],
                'value' => $numericValue($item['value']),
                'drilldown' => [$panel['filter'] ?? 'item' => $slug($item['label'])],
            ], $items);
        @endphp
        <x-daisy::charts.bar
            class="!bg-transparent !p-0 !shadow-none !border-0"
            height="190px"
            :colors="[$panel['tone'] ?? $section['tone']]"
            orientation="horizontal"
            :categories="$categories"
            :series="[['name' => $panel['caption'] ?? $panel['title'], 'data' => $data]]"
            :legend="false"
            :toolbar="false"
            :drilldown-url="$detailedUrl"
            :drilldown-params="$chartParams"
            :markers="$panel['markers'] ?? []"
            :zoom="$panel['zoom'] ?? false"
            :zoom-mode="$panel['zoomMode'] ?? 'inside'"
            :show-values="true"
            value-format="number"
            empty-message="Aucune donnée disponible"
        />
    @endif
</article>

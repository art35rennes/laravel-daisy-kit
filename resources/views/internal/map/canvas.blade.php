<div class="daisy-kit-map__viewport rounded-box border border-base-300 bg-base-200">
    <div
        class="daisy-kit-map__canvas"
        id="{{ $mapId }}"
        aria-describedby="{{ $mapId }}-instructions"
        aria-label="{{ $mapView['label'] }}"
        data-daisy-kit-map-canvas
        tabindex="0"
    ></div>

    <p class="sr-only" id="{{ $mapId }}-instructions">{{ $mapView['labels']['mapInstructions'] }}</p>

    <div class="daisy-kit-map__loading bg-base-100/90" data-daisy-kit-map-loading role="status" aria-live="polite">
        <span class="loading loading-spinner loading-md" aria-hidden="true"></span>
        <span>{{ $mapView['labels']['loading'] }}</span>
    </div>

    @if ($mapView['controls']['enabled'])
        @php
            $isRenderableControl = function (array $control) use (&$isRenderableControl, $controlSlots): bool {
                if (! $control['visible']) {
                    return false;
                }

                if ($control['type'] === 'slot') {
                    return isset($controlSlots[\Illuminate\Support\Str::camel('map-'.$control['slot'])]);
                }

                if (in_array($control['type'], ['menu', 'group'], true)) {
                    return collect($control['items'])->contains($isRenderableControl);
                }

                return true;
            };
            $directViewControls = collect($mapView['controls']['items'])
                ->filter(fn (array $control): bool => $control['type'] === 'action'
                    && $control['visible']
                    && in_array($control['action'], ['fitBounds', 'geolocate', 'fullscreen'], true))
                ->values()
                ->all();
        @endphp
        <aside
            class="daisy-kit-map__map-controls daisy-kit-map__map-controls--{{ $mapView['controls']['position'] }}"
            aria-label="{{ $mapView['labels']['mapSettings'] }}"
        >
            @foreach ($mapView['controls']['items'] as $control)
                @include('daisy-kit::internal.map.control', [
                    'control' => $control,
                    'controlSlots' => $controlSlots,
                    'depth' => 0,
                    'mapId' => $mapId,
                    'mapView' => $mapView,
                    'rootLevel' => true,
                ])
            @endforeach

            @if ($directViewControls !== [])
                @include('daisy-kit::internal.map.control', [
                    'control' => [
                        'id' => '__responsive-view',
                        'type' => 'menu',
                        'label' => $mapView['labels']['viewTools'],
                        'action' => null,
                        'customId' => null,
                        'slot' => null,
                        'icon' => 'view',
                        'enabled' => true,
                        'visible' => true,
                        'items' => $directViewControls,
                    ],
                    'controlSlots' => $controlSlots,
                    'depth' => 0,
                    'mapId' => $mapId,
                    'mapView' => $mapView,
                    'rootLevel' => true,
                    'responsiveView' => true,
                ])
            @endif
        </aside>
    @endif

    <output class="daisy-kit-map__measurement badge badge-neutral" aria-live="polite" data-daisy-kit-map-measurement hidden></output>
    <output class="daisy-kit-map__mode badge badge-primary" aria-live="polite" data-daisy-kit-map-active-mode hidden></output>
    <aside class="daisy-kit-map__selection bg-primary/10" data-daisy-kit-map-selection hidden aria-live="polite">
        <p data-daisy-kit-map-selection-summary></p>
    </aside>
</div>

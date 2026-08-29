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
        <aside class="daisy-kit-map__map-controls" aria-label="{{ $mapView['labels']['mapSettings'] }}">
            <details class="daisy-kit-map__menu dropdown dropdown-end" data-daisy-kit-map-menu>
                <summary class="btn btn-sm btn-square bg-base-100" title="{{ $mapView['labels']['mapSettings'] }}" aria-label="{{ $mapView['labels']['mapSettings'] }}">
                    <span aria-hidden="true">&#9776;</span>
                </summary>
                <div class="daisy-kit-map__menu-panel dropdown-content rounded-box border border-base-300 bg-base-100 p-3 text-sm shadow-lg">
                    @foreach ($mapView['controls']['sections'] as $section)
                        @include('daisy-kit::internal.map.menu.'.$section, ['controlsSlot' => $controlsSlot, 'mapId' => $mapId, 'mapView' => $mapView])
                    @endforeach
                </div>
            </details>
        </aside>
    @endif

    <output class="daisy-kit-map__measurement badge badge-neutral" aria-live="polite" data-daisy-kit-map-measurement hidden></output>
    <output class="daisy-kit-map__mode badge badge-primary" aria-live="polite" data-daisy-kit-map-active-mode hidden></output>
</div>

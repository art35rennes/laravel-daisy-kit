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
            @if ($mapView['controls']['layers'])
                <details class="daisy-kit-map__layer-menu dropdown dropdown-end">
                    <summary class="btn btn-sm btn-square bg-base-100" title="{{ $mapView['labels']['layers'] }}" aria-label="{{ $mapView['labels']['layers'] }}">
                        <span aria-hidden="true">&#9776;</span>
                    </summary>
                    <div class="daisy-kit-map__layer-panel dropdown-content rounded-box border border-base-300 bg-base-100 p-3 shadow-lg">
                        <fieldset data-daisy-kit-map-basemaps hidden>
                            <legend class="font-semibold">{{ $mapView['labels']['basemaps'] }}</legend>
                        </fieldset>
                        <fieldset data-daisy-kit-map-layers hidden>
                            <legend class="font-semibold">{{ $mapView['labels']['overlays'] }}</legend>
                        </fieldset>
                    </div>
                </details>
            @endif

            @if ($mapView['controls']['fitBounds'])
                <button class="btn btn-sm btn-square bg-base-100" data-daisy-kit-map-fit-bounds type="button" title="{{ $mapView['labels']['fitBounds'] }}" aria-label="{{ $mapView['labels']['fitBounds'] }}">
                    <span aria-hidden="true">&#8634;</span>
                </button>
            @endif

            @if ($mapView['geolocation'])
                <button class="btn btn-sm btn-square bg-base-100" data-daisy-kit-map-geolocate type="button" title="{{ $mapView['labels']['useMyLocation'] }}" aria-label="{{ $mapView['labels']['useMyLocation'] }}">
                    <span aria-hidden="true">&#9673;</span>
                </button>
            @endif

            @if ($mapView['fullscreen'])
                <button class="btn btn-sm btn-square bg-base-100" data-daisy-kit-map-fullscreen type="button" title="{{ $mapView['labels']['fullscreen'] }}" aria-label="{{ $mapView['labels']['fullscreen'] }}">
                    <span aria-hidden="true">&#x26F6;</span>
                </button>
            @endif
        </aside>
    @endif

    <output class="daisy-kit-map__measurement badge badge-neutral" aria-live="polite" data-daisy-kit-map-measurement hidden></output>
    <output class="daisy-kit-map__mode badge badge-primary" aria-live="polite" data-daisy-kit-map-active-mode hidden></output>
</div>

@props([
    'geojson' => null,
    'center' => [48.1173, -1.6778],
    'zoom' => 12,
    'drawing' => false,
    'layers' => [],
    'tileUrl' => null,
    'tileAttribution' => '',
    'basemaps' => [],
    'wms' => [],
    'markers' => [],
    'geolocation' => false,
    'spatialSelection' => false,
    'label' => 'Map',
])

<section
    {{ $attributes->merge(['data-daisy-kit-module' => 'map']) }}
    aria-label="{{ $label }}"
>
    <p data-daisy-kit-status hidden role="alert"></p>

    <div data-daisy-kit-content>
        <div aria-label="{{ $label }}" data-daisy-kit-map-canvas role="application" tabindex="0"></div>
        <p data-daisy-kit-empty hidden>No geographic data is available.</p>
        <output aria-live="polite" data-daisy-kit-map-measurement></output>
        <input data-daisy-kit-map-value type="hidden">
        @if($geolocation)
            <button data-daisy-kit-map-geolocate type="button">Use my location</button>
        @endif
        <fieldset data-daisy-kit-map-layers hidden>
            <legend>Layers</legend>
        </fieldset>
        <fieldset data-daisy-kit-map-basemaps hidden>
            <legend>Basemaps</legend>
        </fieldset>
        @if($drawing || $spatialSelection)
            <fieldset data-daisy-kit-map-tools>
                <legend>Drawing tools</legend>
                @if($drawing)
                    <button data-daisy-kit-map-mode="point" type="button">Draw point</button>
                    <button data-daisy-kit-map-mode="linestring" type="button">Draw line</button>
                    <button data-daisy-kit-map-mode="polygon" type="button">Draw area</button>
                    <button data-daisy-kit-map-mode="edit" type="button">Edit drawing</button>
                    <button data-daisy-kit-map-mode="select" type="button">Select drawing</button>
                @endif
                @if($spatialSelection)
                    <button data-daisy-kit-map-mode="spatial-select" type="button">Select geographic feature</button>
                @endif
                <button data-daisy-kit-map-history="undo" disabled type="button">Undo</button>
                <button data-daisy-kit-map-history="redo" disabled type="button">Redo</button>
                <button data-daisy-kit-map-export disabled type="button">Export drawing</button>
            </fieldset>
        @endif
    </div>

    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'geojson' => $geojson,
        'center' => $center,
        'zoom' => $zoom,
        'drawing' => $drawing,
        'layers' => $layers,
        'tileUrl' => $tileUrl,
        'tileAttribution' => $tileAttribution,
        'basemaps' => $basemaps,
        'wms' => $wms,
        'markers' => $markers,
        'geolocation' => $geolocation,
        'spatialSelection' => $spatialSelection,
        'label' => $label,
    ]) !!}</script>
</section>

@props([
    'geojson' => null,
    'center' => [48.1173, -1.6778],
    'zoom' => 12,
    'drawing' => false,
    'layers' => [],
    'tileUrl' => null,
    'tileAttribution' => '',
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
        <fieldset data-daisy-kit-map-layers hidden>
            <legend>Layers</legend>
        </fieldset>
        @if($drawing)
            <fieldset data-daisy-kit-map-tools>
                <legend>Drawing tools</legend>
                <button data-daisy-kit-map-mode="linestring" type="button">Draw line</button>
                <button data-daisy-kit-map-mode="polygon" type="button">Draw area</button>
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
        'label' => $label,
    ]) !!}</script>
</section>

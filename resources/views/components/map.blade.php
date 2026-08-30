@props([
    'center' => [48.1173, -1.6778],
    'zoom' => 12,
    'minZoom' => null,
    'maxZoom' => null,
    'fitBounds' => true,
    'preferCanvas' => false,
    'label' => null,
    'geojson' => null,
    'markers' => [],
    'basemaps' => [],
    'layers' => [],
    'provider' => null,
    'tileUrl' => null,
    'tileAttribution' => '',
    'tileOptions' => [],
    'controls' => true,
    'scale' => false,
    'fullscreen' => false,
    'gestureHandling' => false,
    'geolocation' => false,
    'cluster' => false,
    'drawing' => false,
    'measure' => false,
    'objectTypes' => [],
    'drawLayers' => [],
    'drawLayerSelection' => 'single',
    'spatialSelection' => false,
    'name' => null,
    'value' => null,
    'persistState' => false,
    'stateKey' => null,
])

@php
    $controlsSlot = $controls instanceof \Illuminate\View\ComponentSlot ? $controls : null;
    $slotSections = $controlsSlot?->attributes->get('sections');
    $controlsConfiguration = $controlsSlot && is_array($slotSections)
        ? ['sections' => $slotSections]
        : ($controlsSlot ? true : $controls);
    $resolvedProvider = $provider === false
        ? false
        : ($provider ?? ($tileUrl === null && $basemaps === [] ? 'osm.standard' : null));
    $map = \Art35rennes\DaisyKit\Map\MapConfiguration::make([
        'center' => $center,
        'zoom' => $zoom,
        'minZoom' => $minZoom,
        'maxZoom' => $maxZoom,
        'fitBounds' => $fitBounds,
        'preferCanvas' => $preferCanvas,
        'label' => $label,
        'geojson' => $geojson,
        'markers' => $markers,
        'basemaps' => $basemaps,
        'layers' => $layers,
        'provider' => $resolvedProvider,
        'tileUrl' => $tileUrl,
        'tileAttribution' => $tileAttribution,
        'tileOptions' => $tileOptions,
        'controls' => $controlsConfiguration,
        'scale' => $scale,
        'fullscreen' => $fullscreen,
        'gestureHandling' => $gestureHandling,
        'geolocation' => $geolocation,
        'cluster' => $cluster,
        'drawing' => $drawing,
        'measure' => $measure,
        'objectTypes' => $objectTypes,
        'drawLayers' => $drawLayers,
        'drawLayerSelection' => $drawLayerSelection,
        'spatialSelection' => $spatialSelection,
        'name' => $name,
        'value' => $value,
        'persistState' => $persistState,
        'stateKey' => $stateKey,
    ]);
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode($map['configuration']);
    $mapView = $map['view'];
    $mapId = 'daisy-kit-map-'.\Illuminate\Support\Str::uuid();
@endphp

<section
    {{ $attributes
        ->except(['aria-busy', 'data-daisy-kit-config', 'data-daisy-kit-module', 'data-daisy-kit-state', 'wms'])
        ->class(['daisy-kit-map', 'card', 'border', 'border-base-300', 'bg-base-100', 'shadow-sm']) }}
    aria-busy="true"
    aria-label="{{ $mapView['label'] }}"
    data-daisy-kit-module="map"
>
    <p class="daisy-kit-map__status alert alert-info" data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div class="daisy-kit-map__content" data-daisy-kit-content>
        @include('daisy-kit::internal.map.canvas', ['controlsSlot' => $controlsSlot, 'mapId' => $mapId, 'mapView' => $mapView])
        @include('daisy-kit::internal.map.states', ['mapView' => $mapView])

        <input
            data-daisy-kit-map-value
            type="hidden"
            @if ($mapView['name']) name="{{ $mapView['name'] }}" @endif
            value="{{ json_encode($mapView['value'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}"
        >
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>

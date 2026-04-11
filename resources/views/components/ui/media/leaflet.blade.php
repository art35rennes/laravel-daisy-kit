@props([
    'id' => null,
    'class' => '',
    'lat' => 48.117266,
    'lng' => -1.6777926,
    'zoom' => 12,
    'minZoom' => null,
    'maxZoom' => null,
    'fitBounds' => true,
    'scale' => false,
    'preferCanvas' => false,
    'tileUrl' => null,
    'tileOptions' => [],
    'provider' => null,
    'gestureHandling' => false,
    'cluster' => false,
    'clusterOptions' => [],
    'fullscreen' => false,
    'markers' => [],
    'geojson' => null,
    'module' => null,
])

@php
    $mapId = $id ?: 'leaflet-'.\Illuminate\Support\Str::uuid()->toString();

    $config = [
        'containerId' => $mapId,
        'center' => ['lat' => (float) $lat, 'lng' => (float) $lng],
        'zoom' => (int) $zoom,
        'minZoom' => $minZoom !== null ? (int) $minZoom : null,
        'maxZoom' => $maxZoom !== null ? (int) $maxZoom : null,
        'fitBounds' => (bool) $fitBounds,
        'scale' => (bool) $scale,
        'preferCanvas' => (bool) $preferCanvas,
        'tileUrl' => $tileUrl,
        'tileOptions' => $tileOptions,
        'provider' => $provider,
        'gestureHandling' => (bool) $gestureHandling,
        'cluster' => (bool) $cluster,
        'clusterOptions' => $clusterOptions,
        'fullscreen' => (bool) $fullscreen,
        'markers' => $markers,
        'geojson' => $geojson,
    ];

    $hasHeightClass = preg_match('/(?:^|\s)(?:(?:sm|md|lg|xl|2xl):)?(?:h-(?:\d+|full|screen|dvh|svh|lvh|\[.+?\])|min-h-|max-h-|aspect-(?:\[|[\d]+\/[\d]+))/u', (string) $class) === 1;
    $heightClass = $hasHeightClass ? '' : 'h-80';
    $baseClasses = trim("relative z-0 w-full bg-base-200 {$heightClass} {$class}");
@endphp

<div {{ $attributes->merge(['class' => $baseClasses, 'data-module' => ($module ?? 'leaflet')]) }}>
    <div id="{{ $mapId }}" class="w-full h-full"></div>

    <div class="daisy-leaflet-loading absolute inset-0 z-10 flex items-center justify-center">
        <span class="loading loading-spinner loading-lg text-base-content/30"></span>
    </div>

    <div class="daisy-leaflet-error absolute inset-0 z-10 flex items-center justify-center hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
        </svg>
    </div>

    <script type="application/json" data-config>@json($config)</script>
    {{ $slot }}
</div>

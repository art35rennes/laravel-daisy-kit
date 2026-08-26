<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
    'tiles' => null,
    'tileUrl' => null,
    'tileOptions' => [],
    'provider' => null,
    'gestureHandling' => false,
    'cluster' => false,
    'clusterOptions' => [],
    'fullscreen' => false,
    'geolocation' => false,
    'markers' => [],
    'geojson' => null,
    'basemaps' => [],
    'overlays' => [],
    'layerControl' => false,
    'draw' => false,
    'measure' => false,
    'controls' => false,
    'objectTypes' => [],
    'drawLayers' => false,
    'name' => null,
    'value' => null,
    'module' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
    'tiles' => null,
    'tileUrl' => null,
    'tileOptions' => [],
    'provider' => null,
    'gestureHandling' => false,
    'cluster' => false,
    'clusterOptions' => [],
    'fullscreen' => false,
    'geolocation' => false,
    'markers' => [],
    'geojson' => null,
    'basemaps' => [],
    'overlays' => [],
    'layerControl' => false,
    'draw' => false,
    'measure' => false,
    'controls' => false,
    'objectTypes' => [],
    'drawLayers' => false,
    'name' => null,
    'value' => null,
    'module' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $mapId = $id ?: 'leaflet-'.\Illuminate\Support\Str::uuid()->toString();
    $normalizeMapUrl = function($value) {
        if (!is_string($value) && !$value instanceof \Stringable) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '/')) {
            return $value;
        }

        return preg_match('/^https?:\/\//i', $value) === 1 ? $value : null;
    };

    $normalizeLayerCollection = function($layers) use ($normalizeMapUrl) {
        if (!is_array($layers)) {
            return [];
        }

        return collect($layers)
            ->filter(fn ($layer) => is_array($layer))
            ->map(function ($layer) use ($normalizeMapUrl) {
                $type = strtolower((string) ($layer['type'] ?? 'xyz'));
                $allowedTypes = ['geojson', 'wms', 'xyz'];

                if (!in_array($type, $allowedTypes, true)) {
                    $type = 'xyz';
                }

                $normalized = array_merge($layer, [
                    'type' => $type,
                ]);

                if (isset($normalized['url'])) {
                    $normalized['url'] = $normalizeMapUrl($normalized['url']);
                }

                return $normalized;
            })
            ->filter(fn ($layer) => ($layer['type'] === 'geojson') || filled($layer['url'] ?? null))
            ->values()
            ->all();
    };

    $normalizeObjectTypes = function($types) {
        if (!is_array($types)) {
            return [];
        }

        return collect($types)
            ->filter(fn ($type) => is_array($type))
            ->map(function ($type, $index) {
                $id = trim((string) ($type['id'] ?? 'object-'.($index + 1)));
                $geometry = strtolower((string) ($type['geometry'] ?? 'point'));
                $allowedGeometries = ['point', 'line', 'polygon'];

                if (in_array($geometry, ['polyline', 'linestring'], true)) {
                    $geometry = 'line';
                }

                if (!in_array($geometry, $allowedGeometries, true)) {
                    $geometry = 'point';
                }

                return array_merge($type, [
                    'id' => $id !== '' ? $id : 'object-'.($index + 1),
                    'label' => (string) ($type['label'] ?? ($id !== '' ? $id : 'Objet '.($index + 1))),
                    'geometry' => $geometry,
                ]);
            })
            ->values()
            ->all();
    };
    $normalizeDrawLayers = function($drawLayers) {
        if (!$drawLayers || !is_array($drawLayers)) {
            return false;
        }

        $isList = array_is_list($drawLayers);
        $config = $isList ? ['mode' => 'select', 'layers' => $drawLayers] : $drawLayers;
        $mode = strtolower((string) ($config['mode'] ?? 'select'));

        if (!in_array($mode, ['fixed', 'none', 'select'], true)) {
            $mode = 'select';
        }

        $layers = collect($config['layers'] ?? [])
            ->filter(fn ($layer) => is_array($layer))
            ->map(function ($layer, $index) {
                $id = trim((string) ($layer['id'] ?? 'draw-layer-'.($index + 1)));

                return array_merge($layer, [
                    'id' => $id !== '' ? $id : 'draw-layer-'.($index + 1),
                    'label' => (string) ($layer['label'] ?? ($id !== '' ? $id : 'Couche '.($index + 1))),
                    'properties' => is_array($layer['properties'] ?? null) ? $layer['properties'] : [],
                ]);
            })
            ->values()
            ->all();

        return array_merge($config, [
            'mode' => $mode,
            'layers' => $layers,
        ]);
    };

    $tileUrl = $normalizeMapUrl($tileUrl);
    $tilesEnabled = $tiles ?? filled($tileUrl) || filled($provider);
    $fieldValue = $value ?: ['type' => 'FeatureCollection', 'features' => []];
    $defaultDrawConfig = [
        'toolbar' => true,
        'point' => true,
        'line' => true,
        'polygon' => true,
        'rectangle' => true,
        'select' => true,
        'delete' => true,
        'undoRedo' => true,
        'groupedToolbar' => true,
        'actionBadge' => true,
        'selectionDetails' => true,
        'styles' => [],
    ];
    $defaultMeasureConfig = [
        'display' => 'metric',
        'showTooltip' => true,
        'maxLabels' => 16,
        'maxLabelOffsetPx' => 96,
    ];
    $defaultGeolocationConfig = [
        'enabled' => true,
        'button' => true,
        'auto' => false,
        'watch' => false,
        'setView' => true,
        'zoom' => null,
        'maximumAge' => 10000,
        'timeout' => 10000,
        'enableHighAccuracy' => false,
        'showAccuracy' => true,
    ];
    $defaultControlsConfig = [
        'position' => 'topright',
        'basemaps' => true,
        'overlays' => true,
        'draw' => true,
        'measurements' => true,
        'geolocation' => true,
        'fitBounds' => true,
        'persist' => false,
        'storageKey' => null,
    ];
    $drawConfig = $draw ? array_merge($defaultDrawConfig, is_array($draw) ? $draw : []) : false;
    $measureConfig = $measure ? array_merge($defaultMeasureConfig, is_array($measure) ? $measure : []) : false;
    $geolocationConfig = $geolocation ? array_merge($defaultGeolocationConfig, is_array($geolocation) ? $geolocation : []) : false;
    $controlsConfig = $controls ? array_merge($defaultControlsConfig, is_array($controls) ? $controls : []) : false;

    $config = [
        'containerId' => $mapId,
        'center' => ['lat' => (float) $lat, 'lng' => (float) $lng],
        'zoom' => (int) $zoom,
        'minZoom' => $minZoom !== null ? (int) $minZoom : null,
        'maxZoom' => $maxZoom !== null ? (int) $maxZoom : null,
        'fitBounds' => (bool) $fitBounds,
        'scale' => (bool) $scale,
        'preferCanvas' => (bool) $preferCanvas,
        'tiles' => (bool) $tilesEnabled,
        'tileUrl' => $tileUrl,
        'tileOptions' => $tileOptions,
        'provider' => $provider,
        'gestureHandling' => (bool) $gestureHandling,
        'cluster' => (bool) $cluster,
        'clusterOptions' => $clusterOptions,
        'fullscreen' => (bool) $fullscreen,
        'geolocation' => $geolocationConfig,
        'markers' => $markers,
        'geojson' => $geojson,
        'basemaps' => $normalizeLayerCollection($basemaps),
        'overlays' => $normalizeLayerCollection($overlays),
        'layerControl' => is_array($layerControl) ? $layerControl : (bool) $layerControl,
        'draw' => $drawConfig,
        'measure' => $measureConfig,
        'controls' => $controlsConfig,
        'objectTypes' => $normalizeObjectTypes($objectTypes),
        'drawLayers' => $normalizeDrawLayers($drawLayers),
        'value' => $fieldValue,
        'valueInputName' => $name,
    ];

    $hasHeightClass = preg_match('/(?:^|\s)(?:(?:sm|md|lg|xl|2xl):)?(?:h-(?:\d+|full|screen|dvh|svh|lvh|\[.+?\])|min-h-|max-h-|aspect-(?:\[|[\d]+\/[\d]+))/u', (string) $class) === 1;
    $heightClass = $hasHeightClass ? '' : 'h-80';
    $baseClasses = trim("relative z-0 w-full bg-base-200 {$heightClass} {$class}");
?>

<div <?php echo e($attributes->merge(['class' => $baseClasses, 'data-module' => ($module ?? 'leaflet')])); ?>>
    <div id="<?php echo e($mapId); ?>" class="w-full h-full"></div>

    <div class="daisy-leaflet-loading absolute inset-0 z-10 flex items-center justify-center">
        <span class="loading loading-spinner loading-lg text-base-content/30"></span>
    </div>

    <div class="daisy-leaflet-error absolute inset-0 z-10 flex items-center justify-center hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
        </svg>
    </div>

    <script type="application/json" data-config><?php echo json_encode($config, 15, 512) ?></script>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($name)): ?>
        <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e(json_encode($fieldValue)); ?>" data-leaflet-value>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($slot); ?>

</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/media/leaflet.blade.php ENDPATH**/ ?>
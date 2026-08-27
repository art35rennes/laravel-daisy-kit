<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Map;

use Illuminate\Support\Facades\View;
use InvalidArgumentException;

class MapConfiguration
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{configuration: array<string, mixed>, view: array<string, mixed>}
     */
    public static function make(array $input): array
    {
        $labels = self::labels();
        $controls = self::controls($input['controls'] ?? true);
        $drawing = self::featureConfiguration($input['drawing'] ?? false, [
            'point' => true,
            'line' => true,
            'polygon' => true,
            'rectangle' => true,
            'select' => true,
            'edit' => true,
            'delete' => true,
        ]);
        $geolocation = self::featureConfiguration($input['geolocation'] ?? false, [
            'auto' => false,
            'watch' => false,
            'setView' => true,
            'zoom' => null,
            'maximumAge' => 10_000,
            'timeout' => 10_000,
            'enableHighAccuracy' => false,
            'showAccuracy' => true,
        ]);

        $configuration = [
            'center' => self::center($input['center'] ?? [48.1173, -1.6778]),
            'zoom' => self::integer($input['zoom'] ?? 12, 'zoom'),
            'minZoom' => self::nullableInteger($input['minZoom'] ?? null, 'minZoom'),
            'maxZoom' => self::nullableInteger($input['maxZoom'] ?? null, 'maxZoom'),
            'fitBounds' => ($input['fitBounds'] ?? true) === true,
            'preferCanvas' => ($input['preferCanvas'] ?? false) === true,
            'geojson' => self::geojson($input['geojson'] ?? null, 'geojson'),
            'markers' => self::markers($input['markers'] ?? []),
            'provider' => self::provider($input['provider'] ?? null),
            'tileUrl' => self::url($input['tileUrl'] ?? null, 'tileUrl', true),
            'tileAttribution' => is_string($input['tileAttribution'] ?? null) ? $input['tileAttribution'] : '',
            'tileOptions' => self::array($input['tileOptions'] ?? [], 'tileOptions'),
            'basemaps' => self::layers($input['basemaps'] ?? [], true),
            'layers' => self::layers($input['layers'] ?? [], false),
            'controls' => $controls,
            'scale' => ($input['scale'] ?? false) === true,
            'fullscreen' => ($input['fullscreen'] ?? false) === true,
            'gestureHandling' => ($input['gestureHandling'] ?? false) === true,
            'geolocation' => $geolocation,
            'cluster' => self::featureConfiguration($input['cluster'] ?? false, ['maxClusterRadius' => 80]),
            'drawing' => $drawing,
            'measure' => self::featureConfiguration($input['measure'] ?? false, [
                'display' => 'metric',
                'showTooltip' => true,
                'maxLabels' => 16,
            ]),
            'objectTypes' => self::objectTypes($input['objectTypes'] ?? []),
            'drawLayers' => self::drawLayers($input['drawLayers'] ?? []),
            'spatialSelection' => self::featureConfiguration($input['spatialSelection'] ?? false, ['mode' => 'click']),
            'value' => self::geojson($input['value'] ?? null, 'value') ?? self::emptyCollection(),
            'persistState' => self::persistence($input['persistState'] ?? false, $input['stateKey'] ?? null),
            'labels' => $labels,
        ];

        return [
            'configuration' => $configuration,
            'view' => [
                'controls' => $controls,
                'drawing' => $drawing,
                'geolocation' => $geolocation,
                'fullscreen' => $configuration['fullscreen'],
                'label' => is_string($input['label'] ?? null) && trim($input['label']) !== ''
                    ? $input['label']
                    : $labels['map'],
                'labels' => $labels,
                'name' => is_string($input['name'] ?? null) && trim($input['name']) !== '' ? $input['name'] : null,
                'value' => $configuration['value'],
            ],
        ];
    }

    /** @return array<string, string> */
    private static function labels(): array
    {
        $keys = [
            'map', 'map_instructions', 'loading', 'empty', 'error', 'retry', 'layers', 'basemaps', 'overlays',
            'map_settings', 'fit_bounds', 'fullscreen', 'exit_fullscreen', 'use_my_location', 'drawing_tools',
            'draw_point', 'draw_line', 'draw_area', 'draw_rectangle', 'edit_drawing', 'select_drawing',
            'select_feature', 'delete_selected', 'undo', 'redo', 'export_drawing', 'active_mode',
            'selection_details', 'selected_features', 'clear_selection', 'measurements', 'locked_layer',
        ];

        return collect($keys)
            ->mapWithKeys(fn (string $key): array => [str($key)->camel()->toString() => __("daisy-kit::map.{$key}")])
            ->all();
    }

    /** @return array{enabled: bool, position: string, layers: bool, fitBounds: bool, drawing: bool, measurements: bool} */
    private static function controls(mixed $controls): array
    {
        if ($controls === false) {
            return [
                'enabled' => false,
                'position' => 'topright',
                'layers' => false,
                'fitBounds' => false,
                'drawing' => false,
                'measurements' => false,
            ];
        }

        if ($controls !== true && ! is_array($controls)) {
            throw new InvalidArgumentException('Map controls must be a boolean or array.');
        }

        $controls = $controls === true ? [] : $controls;
        $position = $controls['position'] ?? 'topright';

        if (! in_array($position, ['topleft', 'topright', 'bottomleft', 'bottomright'], true)) {
            throw new InvalidArgumentException('Map control position is invalid.');
        }

        return [
            'enabled' => true,
            'position' => $position,
            'layers' => ($controls['layers'] ?? true) === true,
            'fitBounds' => ($controls['fitBounds'] ?? true) === true,
            'drawing' => ($controls['drawing'] ?? true) === true,
            'measurements' => ($controls['measurements'] ?? true) === true,
        ];
    }

    /** @return array<string, mixed>|false */
    private static function featureConfiguration(mixed $value, array $defaults): array|false
    {
        if ($value === false || $value === null) {
            return false;
        }

        if ($value !== true && ! is_array($value)) {
            throw new InvalidArgumentException('Map feature configuration must be a boolean or array.');
        }

        return [...$defaults, ...($value === true ? [] : $value), 'enabled' => true];
    }

    /** @return list<float> */
    private static function center(mixed $center): array
    {
        if (! is_array($center) || count($center) !== 2 || ! is_numeric($center[0]) || ! is_numeric($center[1])) {
            throw new InvalidArgumentException('Map center must contain a latitude and longitude.');
        }

        $latitude = (float) $center[0];
        $longitude = (float) $center[1];

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException('Map center is outside valid latitude or longitude bounds.');
        }

        return [$latitude, $longitude];
    }

    private static function integer(mixed $value, string $name): int
    {
        if (! is_int($value) && ! is_numeric($value)) {
            throw new InvalidArgumentException("Map {$name} must be an integer.");
        }

        return (int) $value;
    }

    private static function nullableInteger(mixed $value, string $name): ?int
    {
        return $value === null ? null : self::integer($value, $name);
    }

    private static function provider(mixed $provider): ?string
    {
        if ($provider === null || $provider === '') {
            return null;
        }

        if (! is_string($provider) || ! in_array($provider, ['osm', 'cartodb.positron', 'cartodb.darkmatter', 'cartodb.voyager'], true)) {
            throw new InvalidArgumentException('Map provider is not supported.');
        }

        return $provider;
    }

    private static function url(mixed $value, string $name, bool $tileTemplate = false): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException("Map {$name} must be a safe same-origin path or HTTPS URL.");
        }

        $isSameOriginPath = str_starts_with($value, '/') && ! str_starts_with($value, '//');
        $isHttps = filter_var($value, FILTER_VALIDATE_URL) !== false && parse_url($value, PHP_URL_SCHEME) === 'https';

        if (! $isSameOriginPath && ! $isHttps) {
            throw new InvalidArgumentException("Map {$name} must be a safe same-origin path or HTTPS URL.");
        }

        if ($tileTemplate && ! collect(['{z}', '{x}', '{y}'])->every(fn (string $token): bool => str_contains($value, $token))) {
            throw new InvalidArgumentException("Map {$name} must contain {z}, {x}, and {y} placeholders.");
        }

        return $value;
    }

    /** @return list<array<string, mixed>> */
    private static function layers(mixed $layers, bool $basemap): array
    {
        $layers = self::array($layers, $basemap ? 'basemaps' : 'layers');
        $normalized = [];
        $seen = [];

        foreach ($layers as $index => $layer) {
            if (! is_array($layer)) {
                throw new InvalidArgumentException('Every map layer must be an array.');
            }

            $id = $layer['id'] ?? null;

            if (! is_string($id) || trim($id) === '' || isset($seen[$id])) {
                throw new InvalidArgumentException('Every map layer requires a unique non-empty id.');
            }

            $type = $layer['type'] ?? ($basemap ? 'xyz' : 'geojson');
            $allowedTypes = $basemap ? ['xyz', 'wms'] : ['geojson', 'xyz', 'wms'];

            if (! is_string($type) || ! in_array($type, $allowedTypes, true)) {
                throw new InvalidArgumentException("Map layer [{$id}] has an invalid type.");
            }

            $url = self::url($layer['url'] ?? null, "layer {$id} url", $type === 'xyz');
            $data = self::geojson($layer['data'] ?? $layer['geojson'] ?? null, "layer {$id} data");

            if ($type === 'geojson' && $data === null && $url === null) {
                throw new InvalidArgumentException("GeoJSON layer [{$id}] requires data or a URL.");
            }

            if ($type !== 'geojson' && $url === null) {
                throw new InvalidArgumentException("Raster layer [{$id}] requires a URL.");
            }

            $seen[$id] = true;
            $normalized[] = [
                ...$layer,
                'id' => $id,
                'label' => is_string($layer['label'] ?? null) && trim($layer['label']) !== '' ? $layer['label'] : $id,
                'type' => $type,
                'url' => $url,
                'data' => $data,
                'options' => self::array($layer['options'] ?? [], "layer {$id} options"),
                'visible' => ($layer['visible'] ?? true) === true,
                'selected' => $basemap && ($layer['selected'] ?? $layer['active'] ?? $index === 0) === true,
                'controllable' => ($layer['controllable'] ?? $layer['control'] ?? true) === true,
                'editable' => ! $basemap && ($layer['editable'] ?? false) === true,
            ];
        }

        return $normalized;
    }

    /** @return list<array<string, mixed>> */
    private static function markers(mixed $markers): array
    {
        $markers = self::array($markers, 'markers');
        $normalized = [];

        foreach ($markers as $index => $marker) {
            if (! is_array($marker)) {
                throw new InvalidArgumentException('Every map marker must be an array.');
            }

            $position = $marker['position'] ?? $marker['coordinates'] ?? null;

            if (! is_array($position) || count($position) !== 2 || ! is_numeric($position[0]) || ! is_numeric($position[1])) {
                throw new InvalidArgumentException('Every map marker requires a valid position.');
            }

            $id = is_string($marker['id'] ?? null) && trim($marker['id']) !== '' ? $marker['id'] : "marker-{$index}";
            $normalizedMarker = [
                ...$marker,
                'id' => $id,
                'position' => [(float) $position[0], (float) $position[1]],
                'label' => is_string($marker['label'] ?? null) ? $marker['label'] : $id,
                'properties' => self::array($marker['properties'] ?? [], "marker {$id} properties"),
            ];
            $normalizedMarker['popup'] = self::popup($marker['popup'] ?? null, $normalizedMarker);
            $normalizedMarker['icon'] = self::icon($marker['icon'] ?? null, $id);
            $normalized[] = $normalizedMarker;
        }

        return $normalized;
    }

    /** @param array<string, mixed> $marker */
    private static function popup(mixed $popup, array $marker): ?array
    {
        if ($popup === null || $popup === false) {
            return null;
        }

        if (is_string($popup)) {
            return ['renderer' => 'text', 'content' => $popup];
        }

        if (! is_array($popup)) {
            throw new InvalidArgumentException('Map marker popup must be text or an array.');
        }

        $renderer = $popup['renderer'] ?? 'text';

        if ($renderer === 'html') {
            throw new InvalidArgumentException('Map HTML popups require popup.renderer to be trusted-html explicitly.');
        }

        if (! in_array($renderer, ['blade', 'text', 'trusted-html'], true)) {
            throw new InvalidArgumentException('Map marker popup renderer is invalid.');
        }

        if ($renderer === 'blade') {
            $view = $popup['view'] ?? null;

            if (! is_string($view) || trim($view) === '' || ! View::exists($view)) {
                throw new InvalidArgumentException('Map Blade popup view does not exist.');
            }

            return [
                'renderer' => 'blade',
                'content' => trim(View::make($view, ['marker' => $marker])->render()),
            ];
        }

        return [
            'renderer' => $renderer,
            'content' => is_string($popup['content'] ?? null) ? $popup['content'] : '',
        ];
    }

    private static function icon(mixed $icon, string $markerId): ?array
    {
        if ($icon === null || $icon === false) {
            return null;
        }

        if (! is_array($icon)) {
            throw new InvalidArgumentException("Map marker [{$markerId}] icon must be an array.");
        }

        $type = $icon['type'] ?? 'image';

        if (! in_array($type, ['image', 'trusted-html'], true)) {
            throw new InvalidArgumentException("Map marker [{$markerId}] icon type is invalid.");
        }

        if ($type === 'image') {
            return [
                ...$icon,
                'type' => 'image',
                'url' => self::url($icon['url'] ?? null, "marker {$markerId} icon"),
            ];
        }

        return [
            'type' => 'trusted-html',
            'content' => is_string($icon['content'] ?? null) ? $icon['content'] : '',
            'className' => is_string($icon['className'] ?? null) ? $icon['className'] : '',
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function objectTypes(mixed $types): array
    {
        $types = self::array($types, 'objectTypes');

        return collect($types)->values()->map(function (mixed $type, int $index): array {
            if (! is_array($type)) {
                throw new InvalidArgumentException('Every map object type must be an array.');
            }

            $id = is_string($type['id'] ?? null) && trim($type['id']) !== '' ? $type['id'] : "object-{$index}";
            $geometry = $type['geometry'] ?? 'point';
            $geometry = in_array($geometry, ['line', 'linestring', 'polyline'], true) ? 'line' : $geometry;

            if (! in_array($geometry, ['point', 'line', 'polygon'], true)) {
                throw new InvalidArgumentException("Map object type [{$id}] has an invalid geometry.");
            }

            return [
                ...$type,
                'id' => $id,
                'label' => is_string($type['label'] ?? null) ? $type['label'] : $id,
                'geometry' => $geometry,
                'properties' => self::array($type['properties'] ?? [], "object type {$id} properties"),
            ];
        })->all();
    }

    /** @return list<array<string, mixed>> */
    private static function drawLayers(mixed $layers): array
    {
        $layers = self::array($layers, 'drawLayers');

        return collect($layers)->values()->map(function (mixed $layer, int $index): array {
            if (! is_array($layer)) {
                throw new InvalidArgumentException('Every map drawing layer must be an array.');
            }

            $id = is_string($layer['id'] ?? null) && trim($layer['id']) !== '' ? $layer['id'] : "draw-layer-{$index}";

            return [
                ...$layer,
                'id' => $id,
                'label' => is_string($layer['label'] ?? null) ? $layer['label'] : $id,
                'properties' => self::array($layer['properties'] ?? [], "drawing layer {$id} properties"),
            ];
        })->all();
    }

    /** @return array{enabled: bool, key: ?string} */
    private static function persistence(mixed $persistState, mixed $stateKey): array
    {
        if ($persistState !== false && $persistState !== true) {
            throw new InvalidArgumentException('Map persistState must be a boolean.');
        }

        if ($stateKey !== null && (! is_string($stateKey) || trim($stateKey) === '')) {
            throw new InvalidArgumentException('Map stateKey must be a non-empty string.');
        }

        return ['enabled' => $persistState, 'key' => $stateKey];
    }

    /** @return array<string, mixed>|null */
    private static function geojson(mixed $value, string $name): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value) || ! is_string($value['type'] ?? null)) {
            throw new InvalidArgumentException("Map {$name} must be valid GeoJSON.");
        }

        return $value;
    }

    /** @return array{type: string, features: array<int, mixed>} */
    private static function emptyCollection(): array
    {
        return ['type' => 'FeatureCollection', 'features' => []];
    }

    /** @return array<mixed> */
    private static function array(mixed $value, string $name): array
    {
        if (! is_array($value)) {
            throw new InvalidArgumentException("Map {$name} must be an array.");
        }

        return $value;
    }
}

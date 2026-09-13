<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Map\MapConfiguration;
use Art35rennes\DaisyKit\Map\MapControl;
use Art35rennes\DaisyKit\Map\MapControls;
use Art35rennes\DaisyKit\Support\JsonConfiguration;

function mapConfiguration(string $html): array
{
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    return JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));
}

it('uses the standard OpenStreetMap mode by default and allows hosts to disable implicit tiles', function (): void {
    $default = mapConfiguration(view('daisy-kit::components.map')->render());
    $disabled = mapConfiguration(view('daisy-kit::components.map', ['provider' => false])->render());
    $configured = mapConfiguration(view('daisy-kit::components.map', [
        'tileUrl' => '/tiles/{z}/{x}/{y}.png',
    ])->render());

    expect($default['provider'])->toBe('osm.standard')
        ->and($disabled['provider'])->toBeNull()
        ->and($configured['provider'])->toBeNull()
        ->and($configured['tileUrl'])->toBe('/tiles/{z}/{x}/{y}.png');
});

it('renders the canonical map contract as CSP-safe configuration', function (): void {
    $html = view('daisy-kit::components.map', [
        'center' => [48.1173, -1.6778],
        'zoom' => 14,
        'minZoom' => 4,
        'maxZoom' => 19,
        'fitBounds' => false,
        'preferCanvas' => true,
        'provider' => 'osm.standard',
        'tileOptions' => ['maxZoom' => 19],
        'basemaps' => [[
            'id' => 'local',
            'label' => 'Local tiles',
            'type' => 'xyz',
            'url' => '/tiles/{z}/{x}/{y}.png',
            'selected' => true,
        ]],
        'layers' => [[
            'id' => 'districts',
            'label' => 'Districts',
            'type' => 'geojson',
            'data' => ['type' => 'FeatureCollection', 'features' => []],
            'editable' => true,
        ], [
            'id' => 'zoning',
            'label' => 'Zoning',
            'type' => 'wms',
            'url' => 'https://maps.example.test/wms',
            'options' => ['layers' => 'city:zoning'],
        ]],
        'controls' => MapControls::make([
            MapControl::menu('layers', 'Layers', [MapControl::basemaps(), MapControl::businessLayers()]),
            MapControl::fitBounds(),
            MapControl::fullscreen(),
        ]),
        'scale' => true,
        'fullscreen' => true,
        'gestureHandling' => true,
        'cluster' => ['enabled' => true, 'maxClusterRadius' => 60],
        'drawing' => ['rectangle' => true, 'delete' => true],
        'measure' => ['display' => 'metric'],
        'objectTypes' => [['id' => 'hydrant', 'label' => 'Hydrant', 'geometry' => 'point']],
        'drawLayers' => [['id' => 'water', 'label' => 'Water network']],
        'spatialSelection' => ['mode' => 'box'],
        'persistState' => true,
        'stateKey' => 'project-map',
        'label' => '</script><img src=x>',
    ])->render();

    $configuration = mapConfiguration($html);

    expect($html)
        ->toContain('data-daisy-kit-module="map"')
        ->toContain('data-daisy-kit-map-canvas')
        ->toContain('aria-busy="true"')
        ->not->toContain('role="application"')
        ->not->toContain('</script><img')
        ->not->toContain('style=')
        ->and($configuration)->toMatchArray([
            'center' => [48.1173, -1.6778],
            'zoom' => 14,
            'minZoom' => 4,
            'maxZoom' => 19,
            'fitBounds' => false,
            'preferCanvas' => true,
            'provider' => 'osm.standard',
            'scale' => true,
            'fullscreen' => true,
            'gestureHandling' => true,
            'persistState' => ['enabled' => true, 'key' => 'project-map'],
        ])->and($configuration['layers'])->toHaveCount(2)
        ->and($configuration['layers'][1])->toMatchArray(['id' => 'zoning', 'type' => 'wms'])
        ->and($configuration['spatialSelection']['mode'])->toBe('area');
});

it('normalizes v5-only OSM modes, drawing-layer selection and composable controls', function (): void {
    $controls = MapControls::make([
        MapControl::menu('layers', 'Layers', [
            MapControl::basemaps(),
            MapControl::drawingLayers(),
        ]),
        MapControl::menu('drawing', 'Drawing', [
            MapControl::objectTypeSelector(),
            MapControl::menu('geometry', 'Geometry', [
                MapControl::drawPoint(),
                MapControl::drawPolygon(),
            ]),
        ]),
        MapControl::fitBounds(),
        MapControl::customAction('inspect', 'Inspect asset'),
    ]);
    $html = view('daisy-kit::components.map', [
        'provider' => 'osm.dark',
        'basemaps' => [[
            'id' => 'light',
            'label' => 'OSM light',
            'provider' => 'osm.light',
        ]],
        'drawLayers' => [
            ['id' => 'water', 'label' => 'Water', 'visible' => true],
            ['id' => 'electricity', 'label' => 'Electricity', 'visible' => false],
        ],
        'drawLayerSelection' => 'multiple',
        'controls' => $controls,
        'drawing' => ['point' => true, 'polygon' => true],
    ])->render();
    $configuration = mapConfiguration($html);

    expect($configuration['provider'])->toBe('osm.dark')
        ->and($configuration['basemaps'][0])->toMatchArray(['id' => 'light', 'provider' => 'osm.light'])
        ->and($configuration['drawLayerSelection'])->toBe('multiple')
        ->and($configuration['drawLayers'][0])->toMatchArray(['id' => 'water', 'visible' => true])
        ->and($configuration['drawLayers'][1])->toMatchArray(['id' => 'electricity', 'visible' => false])
        ->and($configuration['controls']['items'])->toHaveCount(4)
        ->and($configuration['controls']['items'][0])->toMatchArray(['id' => 'layers', 'type' => 'menu'])
        ->and($configuration['controls']['items'][1]['items'][1])->toMatchArray(['id' => 'geometry', 'type' => 'menu'])
        ->and($configuration['controls']['items'][3])->toMatchArray(['action' => 'custom', 'customId' => 'inspect']);

    expect(fn (): array => MapConfiguration::make(['provider' => 'osm']))
        ->toThrow(InvalidArgumentException::class, 'not supported');
    expect(fn (): array => MapConfiguration::make(['provider' => 'cartodb.positron']))
        ->toThrow(InvalidArgumentException::class, 'not supported');
    expect(fn (): array => MapConfiguration::make(['drawLayerSelection' => 'legacy']))
        ->toThrow(InvalidArgumentException::class, 'selection mode is invalid');
    expect(fn (): array => MapConfiguration::make(['controls' => ['sections' => ['legacy']]]))
        ->toThrow(InvalidArgumentException::class, 'boolean or MapControls');
});

it('validates immutable map control trees', function (): void {
    expect(fn (): MapControls => MapControls::make([
        MapControl::fitBounds(),
        MapControl::fitBounds(),
    ]))->toThrow(InvalidArgumentException::class, 'unique id');

    expect(fn (): MapControl => MapControl::menu('empty', 'Empty', []))
        ->toThrow(InvalidArgumentException::class, 'at least one item');

    $tooManyControls = [];

    foreach (range(1, 101) as $index) {
        $tooManyControls[] = MapControl::customAction("action-{$index}", "Action {$index}");
    }

    expect(fn (): MapControls => MapControls::make($tooManyControls))
        ->toThrow(InvalidArgumentException::class, 'more than 100 nodes');

    expect(fn (): MapControls => MapControls::make([
        MapControl::menu('one', 'One', [
            MapControl::menu('two', 'Two', [
                MapControl::menu('three', 'Three', [
                    MapControl::menu('four', 'Four', [MapControl::fitBounds()]),
                ]),
            ]),
        ]),
    ]))->toThrow(InvalidArgumentException::class, 'three levels');
});

it('omits all controls or disables explicit controls whose capability is unavailable', function (): void {
    $withoutControls = MapConfiguration::make(['controls' => false]);
    $withoutGeolocation = MapConfiguration::make([
        'controls' => MapControls::make([MapControl::geolocate()]),
        'geolocation' => false,
    ]);

    expect($withoutControls['view']['controls'])->toMatchArray(['enabled' => false, 'items' => []])
        ->and($withoutGeolocation['view']['controls']['items'][0])->toMatchArray([
            'action' => 'geolocate',
            'enabled' => false,
            'visible' => true,
        ]);
});

it('does not render empty slot menus and makes disabled menus inert', function (): void {
    $controls = MapControls::make([
        MapControl::menu('missing-slot', 'Missing slot', [MapControl::slot('missing')]),
        MapControl::menu('disabled-menu', 'Disabled menu', [MapControl::fitBounds()], enabled: false),
    ], position: 'bottomleft');

    $html = view('daisy-kit::components.map', ['controls' => $controls])->render();

    expect($html)
        ->not->toContain('data-daisy-kit-map-menu="missing-slot"')
        ->toContain('data-daisy-kit-map-menu="disabled-menu"')
        ->toContain('data-daisy-kit-map-control-disabled')
        ->toContain('dropdown-top')
        ->toContain('daisy-kit-map__menu--align-start')
        ->toContain('aria-disabled="true"');
});

it('renders direct actions, nested menus and multiple named control slots', function (): void {
    $controls = MapControls::make([
        MapControl::menu('layers', 'Map layers', [MapControl::basemaps()]),
        MapControl::menu('drawing', 'Drawing', [
            MapControl::group('create', 'Create', [
                MapControl::drawPoint(),
                MapControl::drawLine(enabled: false),
            ]),
        ]),
        MapControl::slot('filters'),
        MapControl::slot('actions'),
        MapControl::fitBounds(),
        MapControl::fullscreen(visible: false),
    ]);

    $html = (string) $this->blade(<<<'BLADE'
        <x-daisy-kit::map :controls="$controls" :drawing="true" :fullscreen="true">
            <x-slot:mapFilters><button type="button" data-host-filter>Filter</button></x-slot:mapFilters>
            <x-slot:mapActions><button type="button" data-host-action>Action</button></x-slot:mapActions>
        </x-daisy-kit::map>
        BLADE, ['controls' => $controls]);

    expect($html)
        ->toContain('data-daisy-kit-map-menu="layers"')
        ->toContain('data-daisy-kit-map-menu="drawing"')
        ->toContain('data-daisy-kit-map-group="create"')
        ->toContain('data-daisy-kit-map-mode="point"')
        ->toMatch('/data-daisy-kit-map-mode="linestring"[^>]*disabled/')
        ->toContain('data-host-filter')
        ->toContain('data-host-action')
        ->toContain('data-daisy-kit-map-fit-bounds')
        ->not->toContain('data-daisy-kit-map-fullscreen');
});

it('rejects unsafe map and marker asset urls', function (): void {
    expect(fn (): array => MapConfiguration::make([
        'tileUrl' => 'javascript:alert(1)',
    ]))->toThrow(InvalidArgumentException::class, 'safe same-origin path or HTTPS URL');

    expect(fn (): array => MapConfiguration::make([
        'markers' => [['id' => 'outside', 'position' => [148.1, -1.6]]],
    ]))->toThrow(InvalidArgumentException::class, 'outside valid latitude or longitude bounds');

    expect(fn (): array => MapConfiguration::make([
        'markers' => [['id' => 'missing-icon', 'position' => [48.1, -1.6], 'icon' => ['type' => 'image']]],
    ]))->toThrow(InvalidArgumentException::class, 'image icon requires a URL');

    expect(fn (): array => MapConfiguration::make([
        'spatialSelection' => ['mode' => 'lasso'],
    ]))->toThrow(InvalidArgumentException::class, 'spatial selection mode is invalid');

    expect(fn (): array => MapConfiguration::make([
        'markers' => [[
            'id' => 'unsafe',
            'position' => [48.1, -1.6],
            'icon' => ['type' => 'image', 'url' => 'data:text/html,unsafe'],
        ]],
    ]))->toThrow(InvalidArgumentException::class, 'safe same-origin path or HTTPS URL');
});

it('renders Blade marker popups and requires explicit trusted html', function (): void {
    view()->addNamespace('map-test', __DIR__.'/../Fixtures/views');

    $html = view('daisy-kit::components.map', [
        'markers' => [[
            'id' => 'office',
            'position' => [48.1173, -1.6778],
            'label' => 'Rennes office',
            'popup' => ['renderer' => 'blade', 'view' => 'map-test::map.office'],
            'properties' => ['address' => '12 rue de la Monnaie'],
        ], [
            'id' => 'trusted',
            'position' => [48.12, -1.68],
            'popup' => ['renderer' => 'trusted-html', 'content' => '<strong>Approved</strong>'],
        ]],
    ])->render();

    $configuration = mapConfiguration($html);

    expect($configuration['markers'][0]['popup']['renderer'])->toBe('blade')
        ->and($configuration['markers'][0]['popup']['content'])->toContain('12 rue de la Monnaie')
        ->and($configuration['markers'][1]['popup'])->toBe([
            'renderer' => 'trusted-html',
            'content' => '<strong>Approved</strong>',
        ]);

    expect(fn (): array => MapConfiguration::make([
        'markers' => [[
            'position' => [48.1, -1.6],
            'popup' => ['renderer' => 'html', 'content' => '<strong>Unsafe</strong>'],
        ]],
    ]))->toThrow(InvalidArgumentException::class, 'trusted-html');
});

it('renders translated controls and private integration slots', function (): void {
    app()->setLocale('fr');

    $controls = MapControls::make([
        MapControl::menu('layers', 'Couches', [MapControl::basemaps()]),
        MapControl::menu('drawing', 'Outils de dessin', [MapControl::drawPoint()]),
        MapControl::slot('filters'),
        MapControl::fitBounds(),
    ]);

    $html = (string) $this->blade(<<<'BLADE'
        <x-daisy-kit::map :drawing="true" :controls="$controls">
            <x-slot:mapFilters><button type="button" data-host-map-control>Filtre métier</button></x-slot:mapFilters>
            <x-slot:empty><p data-host-map-empty>Aucune agence</p></x-slot:empty>
            <x-slot:error><p data-host-map-error>Carte indisponible</p></x-slot:error>
        </x-daisy-kit::map>
        BLADE, ['controls' => $controls]);

    expect($html)
        ->toContain('Outils de dessin')
        ->toContain('Dessiner un point')
        ->toContain('Couches')
        ->toContain('Réessayer')
        ->toContain('data-host-map-control')
        ->toContain('data-host-map-empty')
        ->toContain('data-host-map-error');

    expect(strpos($html, 'data-daisy-kit-map-canvas'))->toBeLessThan(strpos($html, 'data-daisy-kit-map-menu'))
        ->and(strpos($html, 'data-daisy-kit-map-menu'))->toBeLessThan(strpos($html, 'data-host-map-control'))
        ->and(mapConfiguration($html)['controls']['items'])->toHaveCount(4);
});

it('synchronizes the initial GeoJSON value with a named form input', function (): void {
    $value = [
        'type' => 'FeatureCollection',
        'features' => [[
            'type' => 'Feature',
            'id' => 'hydrant-1',
            'properties' => ['objectType' => 'hydrant'],
            'geometry' => ['type' => 'Point', 'coordinates' => [-1.6778, 48.1173]],
        ]],
    ];

    $html = view('daisy-kit::components.map', [
        'drawing' => true,
        'name' => 'geometry',
        'value' => $value,
    ])->render();

    expect($html)
        ->toContain('name="geometry"')
        ->toContain('data-daisy-kit-map-value')
        ->and(mapConfiguration($html)['value'])->toBe($value);

    expect(fn (): array => MapConfiguration::make([
        'value' => ['type' => 'Feature', 'properties' => [], 'geometry' => ['type' => 'Point', 'coordinates' => [-1.6, 48.1]]],
    ]))->toThrow(InvalidArgumentException::class, 'must be a GeoJSON FeatureCollection');
});

it('protects module lifecycle attributes while preserving host attributes', function (): void {
    $html = (string) $this->blade(<<<'BLADE'
        <x-daisy-kit::map
            id="asset-map"
            class="project-map"
            data-analytics="assets"
            data-daisy-kit-module="host-override"
            aria-busy="false"
        />
        BLADE);

    expect($html)
        ->toContain('id="asset-map"')
        ->toContain('project-map')
        ->toContain('data-analytics="assets"')
        ->toContain('data-daisy-kit-module="map"')
        ->toContain('aria-busy="true"')
        ->not->toContain('data-daisy-kit-module="host-override"');
});

it('does not expose the removed alpha wms prop', function (): void {
    $html = (string) $this->blade('<x-daisy-kit::map :wms="[[\'id\' => \'legacy\']]" />');

    expect(mapConfiguration($html))->not->toHaveKey('wms');
});

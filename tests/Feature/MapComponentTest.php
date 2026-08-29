<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Map\MapConfiguration;
use Art35rennes\DaisyKit\Support\JsonConfiguration;
use InvalidArgumentException;

function mapConfiguration(string $html): array
{
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    return JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));
}

it('renders the canonical map contract as CSP-safe configuration', function (): void {
    $html = view('daisy-kit::components.map', [
        'center' => [48.1173, -1.6778],
        'zoom' => 14,
        'minZoom' => 4,
        'maxZoom' => 19,
        'fitBounds' => false,
        'preferCanvas' => true,
        'provider' => 'osm',
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
        'controls' => ['layers' => true, 'fitBounds' => true, 'position' => 'topright'],
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
            'provider' => 'osm',
            'scale' => true,
            'fullscreen' => true,
            'gestureHandling' => true,
            'persistState' => ['enabled' => true, 'key' => 'project-map'],
        ])->and($configuration['layers'])->toHaveCount(2)
        ->and($configuration['layers'][1])->toMatchArray(['id' => 'zoning', 'type' => 'wms'])
        ->and($configuration['spatialSelection']['mode'])->toBe('area');
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

    $html = (string) $this->blade(<<<'BLADE'
        <x-daisy-kit::map :drawing="true" :controls="true">
            <x-slot:controls><button type="button" data-host-map-control>Filtre métier</button></x-slot:controls>
            <x-slot:empty><p data-host-map-empty>Aucune agence</p></x-slot:empty>
            <x-slot:error><p data-host-map-error>Carte indisponible</p></x-slot:error>
        </x-daisy-kit::map>
        BLADE);

    expect($html)
        ->toContain('Outils de dessin')
        ->toContain('Dessiner un point')
        ->toContain('Couches')
        ->toContain('Réessayer')
        ->toContain('data-host-map-control')
        ->toContain('data-host-map-empty')
        ->toContain('data-host-map-error');
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

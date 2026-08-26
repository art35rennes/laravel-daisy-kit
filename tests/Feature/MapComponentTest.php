<?php

declare(strict_types=1);

test('the map component serializes geojson safely', function (): void {
    $html = view('daisy-kit::components.map', [
        'geojson' => [
            'type' => 'FeatureCollection',
            'features' => [],
        ],
        'label' => '</script><img src=x>',
    ])->render();

    expect($html)
        ->toContain('data-daisy-kit-module="map"')
        ->toContain('data-daisy-kit-map-canvas')
        ->not->toContain('</script><img')
        ->not->toContain('onclick=')
        ->not->toContain('style=');
});

test('the map component exposes a semantic empty and measurement state', function (): void {
    $html = view('daisy-kit::components.map')->render();

    expect($html)
        ->toContain('data-daisy-kit-empty')
        ->toContain('data-daisy-kit-map-measurement')
        ->toContain('role="application"');
});

test('the map component is available through the public blade namespace', function (): void {
    $this->blade('<x-daisy-kit::map />')
        ->assertSee('data-daisy-kit-module="map"', false);
});

<?php

declare(strict_types=1);

it('enables OSM in the interactive Workbench independently of the testing environment', function (): void {
    $this->withoutVite();
    config(['workbench-map.external_tiles' => true]);

    $html = view('workbench::index')->render();
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $maps = $xpath->query('//*[@data-daisy-kit-module="map"]/script[@data-daisy-kit-config]');

    expect($maps)->toHaveCount(4);

    foreach ($maps as $index => $node) {
        $map = json_decode($node->textContent, true, flags: JSON_THROW_ON_ERROR);

        if ($index === 1) {
            expect($map['basemaps'][0])->toMatchArray(['provider' => 'osm.standard', 'selected' => true]);

            continue;
        }

        expect($map['provider'])->toBe('osm.standard');
    }
});

it('keeps every automated Workbench map offline', function (): void {
    $this->withoutVite();

    expect(config('workbench-map.external_tiles'))->toBeFalse();

    $html = view('workbench::index')->render();
    $document = new DOMDocument;
    @$document->loadHTML($html);
    $xpath = new DOMXPath($document);
    $maps = $xpath->query('//*[@data-daisy-kit-module="map"]/script[@data-daisy-kit-config]');

    expect($maps)->toHaveCount(4);

    foreach ($maps as $node) {
        $map = json_decode($node->textContent, true, flags: JSON_THROW_ON_ERROR);
        expect($map['provider'])->toBeNull();
        expect($map['basemaps'])->toBeEmpty();
    }
});

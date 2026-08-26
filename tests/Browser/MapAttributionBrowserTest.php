<?php

declare(strict_types=1);

it('keeps the legally required Leaflet attribution readable in the host theme', function (): void {
    $map = '[data-daisy-kit-module="map"]';
    $attribution = "{$map} .leaflet-control-attribution";

    $page = $this->visit('/')->on()->desktop()
        ->waitForEvent('networkidle')
        ->wait(1)
        ->assertNoSmoke()
        ->assertScript("(() => { const attribution = document.querySelector('{$attribution}'); const link = attribution?.querySelector('a[href$=\"leafletjs.com\"]'); return Boolean(attribution && link && getComputedStyle(attribution).visibility !== 'hidden' && getComputedStyle(link).visibility !== 'hidden'); })()");

    foreach (['light', 'dark'] as $theme) {
        $page->script("document.documentElement.dataset.theme = '{$theme}';");

        $appearance = $page->script(<<<'JS'
            () => {
                const attribution = document.querySelector('[data-daisy-kit-module="map"] .leaflet-control-attribution');
                const link = attribution?.querySelector('a[href$="leafletjs.com"]');
                const attributionStyle = getComputedStyle(attribution);
                const linkStyle = getComputedStyle(link);

                return {
                    opaqueSurface: !attributionStyle.backgroundColor.startsWith('rgba('),
                    linkInheritsTextColor: linkStyle.color === attributionStyle.color,
                };
            }
            JS);

        expect($appearance)->toBe([
            'opaqueSurface' => true,
            'linkInheritsTextColor' => true,
        ]);

        $violations = $page->script(<<<'JS'
            async () => {
                const attribution = document.querySelector('[data-daisy-kit-module="map"] .leaflet-control-attribution');
                const result = await window.axe.run(attribution, { runOnly: { type: 'rule', values: ['color-contrast'] } });

                return result.violations.map((violation) => violation.id);
            }
            JS);

        expect($violations)->toBe([]);
    }
})->group('browser');

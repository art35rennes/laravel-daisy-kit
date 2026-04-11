<?php

use Illuminate\Support\Facades\View;

/**
 * Renders the leaflet component with the given data.
 *
 * @param  array<string, mixed>  $data
 */
function renderLeaflet(array $data = []): string
{
    return View::make('daisy::components.ui.media.leaflet', array_merge([
        'slot' => '',
    ], $data))->render();
}

/**
 * Extracts the JSON config from the rendered HTML.
 *
 * @return array<string, mixed>|null
 */
function extractConfig(string $html): ?array
{
    preg_match('/<script[^>]*data-config[^>]*>(.*?)<\/script>/s', $html, $matches);

    return isset($matches[1]) ? json_decode($matches[1], true) : null;
}

// ============================================================================
// Core rendering
// ============================================================================

describe('Leaflet component rendering', function () {
    it('renders with data-module="leaflet"', function () {
        $html = renderLeaflet(['lat' => 48.8566, 'lng' => 2.3522, 'zoom' => 13]);

        expect($html)->toContain('data-module="leaflet"');
    });

    it('renders the loading overlay by default', function () {
        $html = renderLeaflet();

        expect($html)
            ->toContain('daisy-leaflet-loading')
            ->toContain('loading loading-spinner');
    });

    it('renders the error overlay hidden by default', function () {
        $html = renderLeaflet();

        expect($html)
            ->toContain('daisy-leaflet-error')
            ->toContain('hidden');
    });

    it('renders a JSON config script with correct center and zoom', function () {
        $html = renderLeaflet(['lat' => 48.8566, 'lng' => 2.3522, 'zoom' => 13]);
        $config = extractConfig($html);

        expect($config)
            ->toHaveKey('center')
            ->and($config['center']['lat'])->toBe(48.8566)
            ->and($config['center']['lng'])->toBe(2.3522)
            ->and($config['zoom'])->toBe(13);
    });

    it('renders markers in the config', function () {
        $markers = [[48.8566, 2.3522, '<b>Paris</b>']];
        $html = renderLeaflet(['lat' => 48.8566, 'lng' => 2.3522, 'markers' => $markers]);
        $config = extractConfig($html);

        expect($config['markers'])->toHaveCount(1)
            ->and($config['markers'][0][0])->toBe(48.8566)
            ->and($config['markers'][0][2])->toBe('<b>Paris</b>');
    });

    it('accepts geojson data', function () {
        $geojson = ['type' => 'FeatureCollection', 'features' => []];
        $html = renderLeaflet(['geojson' => $geojson]);
        $config = extractConfig($html);

        expect($config['geojson'])->toBe($geojson);
    });
});

// ============================================================================
// Height class detection
// ============================================================================

describe('Leaflet height class detection', function () {
    it('applies default h-80 when no height class is provided', function () {
        $html = renderLeaflet(['class' => 'rounded-box shadow']);

        expect($html)->toContain('h-80');
    });

    it('does not add h-80 when h-64 is provided', function () {
        $html = renderLeaflet(['class' => 'h-64']);

        expect($html)->toContain('h-64')->not->toContain('h-80');
    });

    it('does not add h-80 when h-full is provided', function () {
        $html = renderLeaflet(['class' => 'h-full']);

        expect($html)->toContain('h-full')->not->toContain('h-80');
    });

    it('does not add h-80 when h-screen is provided', function () {
        $html = renderLeaflet(['class' => 'h-screen']);

        expect($html)->toContain('h-screen')->not->toContain('h-80');
    });

    it('detects arbitrary height values like h-[500px]', function () {
        $html = renderLeaflet(['class' => 'h-[500px]']);

        expect($html)->toContain('h-[500px]')->not->toContain('h-80');
    });

    it('detects viewport height units like h-dvh', function () {
        $html = renderLeaflet(['class' => 'h-dvh']);

        expect($html)->toContain('h-dvh')->not->toContain('h-80');
    });

    it('detects responsive height prefixes like sm:h-64', function () {
        $html = renderLeaflet(['class' => 'sm:h-64']);

        expect($html)->toContain('sm:h-64')->not->toContain('h-80');
    });

    it('detects min-h classes', function () {
        $html = renderLeaflet(['class' => 'min-h-96']);

        expect($html)->toContain('min-h-96')->not->toContain('h-80');
    });

    it('detects aspect ratio classes', function () {
        $html = renderLeaflet(['class' => 'aspect-16/9']);

        expect($html)->toContain('aspect-16/9')->not->toContain('h-80');
    });
});

// ============================================================================
// Module and ID
// ============================================================================

describe('Leaflet module and ID', function () {
    it('supports custom module name via module prop', function () {
        $html = renderLeaflet(['module' => 'custom-leaflet']);

        expect($html)->toContain('data-module="custom-leaflet"');
    });

    it('uses a generated container ID when none provided', function () {
        $html = renderLeaflet();

        expect($html)->toMatch('/id="leaflet-[0-9a-f\-]+"/');
    });

    it('uses the provided ID and reflects it in config', function () {
        $html = renderLeaflet(['id' => 'my-map']);

        expect($html)->toContain('id="my-map"');

        $config = extractConfig($html);

        expect($config['containerId'])->toBe('my-map');
    });
});

// ============================================================================
// New props (V2)
// ============================================================================

describe('Leaflet V2 props', function () {
    it('includes minZoom and maxZoom in config when set', function () {
        $html = renderLeaflet(['minZoom' => 3, 'maxZoom' => 18]);
        $config = extractConfig($html);

        expect($config['minZoom'])->toBe(3)
            ->and($config['maxZoom'])->toBe(18);
    });

    it('sets minZoom and maxZoom to null by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['minZoom'])->toBeNull()
            ->and($config['maxZoom'])->toBeNull();
    });

    it('sets fitBounds to true by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['fitBounds'])->toBeTrue();
    });

    it('allows disabling fitBounds', function () {
        $html = renderLeaflet(['fitBounds' => false]);
        $config = extractConfig($html);

        expect($config['fitBounds'])->toBeFalse();
    });

    it('sets scale to false by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['scale'])->toBeFalse();
    });

    it('enables scale when prop is true', function () {
        $html = renderLeaflet(['scale' => true]);
        $config = extractConfig($html);

        expect($config['scale'])->toBeTrue();
    });

    it('sets preferCanvas to false by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['preferCanvas'])->toBeFalse();
    });

    it('includes tileUrl when provided', function () {
        $url = 'https://example.com/{z}/{x}/{y}.png';
        $html = renderLeaflet(['tileUrl' => $url]);
        $config = extractConfig($html);

        expect($config['tileUrl'])->toBe($url);
    });

    it('sets tileUrl to null by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['tileUrl'])->toBeNull();
    });

    it('includes tileOptions when provided', function () {
        $options = ['maxZoom' => 20, 'attribution' => 'Test'];
        $html = renderLeaflet(['tileOptions' => $options]);
        $config = extractConfig($html);

        expect($config['tileOptions'])->toBe($options);
    });

    it('sets tileOptions to empty array by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['tileOptions'])->toBe([]);
    });

    it('includes provider name when provided', function () {
        $html = renderLeaflet(['provider' => 'cartodb.positron']);
        $config = extractConfig($html);

        expect($config['provider'])->toBe('cartodb.positron');
    });

    it('sets provider to null by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['provider'])->toBeNull();
    });
});

// ============================================================================
// Plugin flags (V2)
// ============================================================================

describe('Leaflet V2 plugin flags', function () {
    it('sets gestureHandling to false by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['gestureHandling'])->toBeFalse();
    });

    it('enables gestureHandling when prop is true', function () {
        $html = renderLeaflet(['gestureHandling' => true]);
        $config = extractConfig($html);

        expect($config['gestureHandling'])->toBeTrue();
    });

    it('sets cluster to false by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['cluster'])->toBeFalse();
    });

    it('enables cluster with options', function () {
        $options = ['maxClusterRadius' => 80];
        $html = renderLeaflet(['cluster' => true, 'clusterOptions' => $options]);
        $config = extractConfig($html);

        expect($config['cluster'])->toBeTrue()
            ->and($config['clusterOptions'])->toBe($options);
    });

    it('sets clusterOptions to empty array by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['clusterOptions'])->toBe([]);
    });

    it('sets fullscreen to false by default', function () {
        $html = renderLeaflet();
        $config = extractConfig($html);

        expect($config['fullscreen'])->toBeFalse();
    });

    it('enables fullscreen when prop is true', function () {
        $html = renderLeaflet(['fullscreen' => true]);
        $config = extractConfig($html);

        expect($config['fullscreen'])->toBeTrue();
    });
});

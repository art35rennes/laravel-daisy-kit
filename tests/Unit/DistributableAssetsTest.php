<?php

use Art35rennes\DaisyKit\Support\PackagePaths;

it('ships prebuilt assets for host publishing', function (): void {
    $distPath = PackagePaths::distributableAssets();
    $manifestPath = $distPath.DIRECTORY_SEPARATOR.'manifest.json';

    expect(is_dir($distPath))->toBeTrue("Missing distributable assets at {$distPath}. Run `npm run build` before releasing.")
        ->and(is_file($manifestPath))->toBeTrue("Missing Vite manifest at {$manifestPath}. Run `npm run build` before releasing.");

    $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

    expect($manifest)
        ->toHaveKey('resources/css/app.css')
        ->toHaveKey('resources/js/app.js');
});

it('ships grid layout column utilities in the published css bundle', function (): void {
    $distPath = PackagePaths::distributableAssets();
    $manifestPath = $distPath.DIRECTORY_SEPARATOR.'manifest.json';

    $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;

    expect($cssFile)->toBeString();

    $cssPath = $distPath.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $cssFile);
    $css = (string) file_get_contents($cssPath);

    expect($css)
        ->toContain('.daisy-grid>*')
        ->toContain('.col-12{grid-column:span 12/span 12}')
        ->toContain('.col-lg-4{grid-column:span 4/span 4}')
        ->toContain('.col-xl-4{grid-column:span 4/span 4}');
});

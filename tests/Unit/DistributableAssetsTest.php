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

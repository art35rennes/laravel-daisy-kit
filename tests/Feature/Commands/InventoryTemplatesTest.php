<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('generates templates inventory cache successfully', function () {
    $cachePath = base_path('bootstrap/cache/daisy-templates.php');

    if (File::exists($cachePath)) {
        File::delete($cachePath);
    }

    $exitCode = Artisan::call('inventory:templates');

    expect($exitCode)->toBe(0)
        ->and(File::exists($cachePath))->toBeTrue();

    $manifest = require $cachePath;

    expect($manifest)
        ->toHaveKey('generated_at')
        ->toHaveKey('files_hash')
        ->toHaveKey('templates')
        ->toHaveKey('categories')
        ->and($manifest['templates'])->toBeArray()
        ->and($manifest['categories'])->toBeArray();
});

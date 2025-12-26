<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $componentsCachePath = base_path('bootstrap/cache/daisy-components.php');
    $templatesCachePath = base_path('bootstrap/cache/daisy-templates.php');

    if (File::exists($componentsCachePath)) {
        File::delete($componentsCachePath);
    }

    if (File::exists($templatesCachePath)) {
        File::delete($templatesCachePath);
    }
});

it('rebuilds only components cache when --components is provided', function () {
    $componentsCachePath = base_path('bootstrap/cache/daisy-components.php');
    $templatesCachePath = base_path('bootstrap/cache/daisy-templates.php');

    $exitCode = Artisan::call('inventory:cache:rebuild', [
        '--components' => true,
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0)
        ->and(File::exists($componentsCachePath))->toBeTrue()
        ->and(File::exists($templatesCachePath))->toBeFalse();
});

it('rebuilds only templates cache when --templates is provided', function () {
    $componentsCachePath = base_path('bootstrap/cache/daisy-components.php');
    $templatesCachePath = base_path('bootstrap/cache/daisy-templates.php');

    $exitCode = Artisan::call('inventory:cache:rebuild', [
        '--templates' => true,
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0)
        ->and(File::exists($componentsCachePath))->toBeFalse()
        ->and(File::exists($templatesCachePath))->toBeTrue();
});

it('clears caches according to provided options', function () {
    $componentsCachePath = base_path('bootstrap/cache/daisy-components.php');
    $templatesCachePath = base_path('bootstrap/cache/daisy-templates.php');

    Artisan::call('inventory:cache:rebuild', [
        '--no-interaction' => true,
    ]);

    expect(File::exists($componentsCachePath))->toBeTrue()
        ->and(File::exists($templatesCachePath))->toBeTrue();

    $exitCode = Artisan::call('inventory:cache:clear', [
        '--components' => true,
        '--no-interaction' => true,
    ]);

    expect($exitCode)->toBe(0)
        ->and(File::exists($componentsCachePath))->toBeFalse()
        ->and(File::exists($templatesCachePath))->toBeTrue();
});

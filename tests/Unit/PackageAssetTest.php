<?php

use Art35rennes\DaisyKit\Support\PackageAsset;
use Illuminate\Support\Facades\File;

covers(PackageAsset::class);

beforeEach(function () {
    File::deleteDirectory(public_path('vendor/art35rennes/laravel-daisy-kit'));
    File::deleteDirectory(resource_path('vendor/daisy-kit'));
});

it('builds manifest paths from the configured build directory', function () {
    config(['daisy-kit.vite_build_directory' => '/vendor/custom-build/']);

    expect(PackageAsset::buildDirectory())->toBe('vendor/custom-build')
        ->and(PackageAsset::manifestPath())->toBe(public_path('vendor/custom-build/manifest.json'));
});

it('prefers published source entries when package sources are published into the host app', function (string $type, string $expected) {
    File::ensureDirectoryExists(resource_path("vendor/daisy-kit/{$type}"));
    File::put(resource_path("vendor/daisy-kit/{$type}/app.{$type}"), '/* test */');

    expect(PackageAsset::hasPublishedSource($type))->toBeTrue()
        ->and(PackageAsset::sourceEntry($type))->toBe($expected);
})->with('asset source entries');

it('falls back to the default host resource entry when no published source exists', function () {
    expect(PackageAsset::sourceEntry('js'))->toBe('resources/js/app.js');
});

it('renders stylesheet tags from the manifest contract', function () {
    $buildPath = public_path('vendor/art35rennes/laravel-daisy-kit');
    File::ensureDirectoryExists($buildPath);
    File::put($buildPath.'/manifest.json', json_encode([
        'resources/css/app.css' => [
            'file' => 'assets/app.css',
            'css' => ['assets/vendor.css', 'assets/app.css'],
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $html = PackageAsset::stylesheetTags('resources/css/app.css')->toHtml();

    expect($html)
        ->toContain('vendor/art35rennes/laravel-daisy-kit/assets/app.css')
        ->toContain('vendor/art35rennes/laravel-daisy-kit/assets/vendor.css');

    expect(substr_count($html, 'assets/app.css'))->toBe(1);
});

it('renders script tags from the manifest contract', function () {
    $buildPath = public_path('vendor/art35rennes/laravel-daisy-kit');
    File::ensureDirectoryExists($buildPath);
    File::put($buildPath.'/manifest.json', json_encode([
        'resources/js/app.js' => [
            'file' => 'assets/app.js',
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $html = PackageAsset::scriptTags('resources/js/app.js')->toHtml();

    expect($html)
        ->toContain('<script type="module"')
        ->toContain('vendor/art35rennes/laravel-daisy-kit/assets/app.js');
});

it('falls back to configured bundle assets when the manifest cannot provide entries', function () {
    $buildPath = public_path('vendor/art35rennes/laravel-daisy-kit');
    File::ensureDirectoryExists($buildPath);
    File::put($buildPath.'/manifest.json', '{invalid-json');

    config([
        'daisy-kit.bundle.css' => 'vendor/daisy-kit/fallback.css',
        'daisy-kit.bundle.js' => 'vendor/daisy-kit/fallback.js',
    ]);

    expect(PackageAsset::stylesheetTags('resources/css/app.css')->toHtml())
        ->toContain('vendor/daisy-kit/fallback.css');

    expect(PackageAsset::scriptTags('resources/js/app.js')->toHtml())
        ->toContain('vendor/daisy-kit/fallback.js')
        ->toContain('defer');
});

<?php

use Art35rennes\DaisyKit\Support\PackagePaths;

covers(PackagePaths::class);

it('resolves distributable assets with native directory separators', function (): void {
    $path = PackagePaths::distributableAssets();

    expect($path)
        ->toEndWith('dist'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'art35rennes'.DIRECTORY_SEPARATOR.'laravel-daisy-kit')
        ->and(is_dir($path))->toBeTrue();
});

it('builds nested package paths from segments', function (): void {
    expect(PackagePaths::path('resources', 'views', 'components'))
        ->toBe(PackagePaths::root().DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'components');
});

<?php

use App\Console\Commands\MigrateComponentsToCategories;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

it('runs successfully when manifest exists', function () {
    // S'assurer que le manifeste existe
    Artisan::call('inventory:components');

    $exitCode = Artisan::call('migrate:components-to-categories', ['--dry-run' => true]);

    expect($exitCode)->toBe(0);
});

it('handles dry-run option correctly', function () {
    // S'assurer que le manifeste existe
    Artisan::call('inventory:components');

    $exitCode = Artisan::call('migrate:components-to-categories', ['--dry-run' => true]);

    expect($exitCode)->toBe(0);
    

});


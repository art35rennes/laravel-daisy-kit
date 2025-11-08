<?php

use App\Console\Commands\UpdateComponentReferences;
use Illuminate\Support\Facades\Artisan;

it('runs in dry-run mode without errors', function () {
    $exitCode = Artisan::call('update:component-references', ['--dry-run' => true]);

    expect($exitCode)->toBe(0);
});


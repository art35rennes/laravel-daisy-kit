<?php

it('declares the v4 runtime dependency baseline', function () {
    $composer = json_decode(
        file_get_contents(packagePath('composer.json')),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($composer['require'])
        ->toMatchArray([
            'php' => '^8.3',
            'illuminate/http' => '^13.0',
            'illuminate/routing' => '^13.0',
            'illuminate/support' => '^13.0',
            'illuminate/view' => '^13.0',
            'livewire/livewire' => '^4.3',
        ])
        ->and($composer['require-dev']['laravel/framework'])->toBe('^13.0')
        ->and($composer['config']['platform']['php'])->toBe('8.3.0');
});

it('uses the current docx preview generation', function () {
    $package = json_decode(
        file_get_contents(packagePath('package.json')),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($package['dependencies']['docx-preview'])->toBe('^0.4.0');
});

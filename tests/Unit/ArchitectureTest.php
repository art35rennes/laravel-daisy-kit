<?php

use Art35rennes\DaisyKit\DaisyKitServiceProvider;
use Illuminate\Support\ServiceProvider;

arch('package source contains only classes')
    ->expect('Art35rennes\\DaisyKit')
    ->toBeClasses();

arch('service provider extends Laravel service provider')
    ->expect(DaisyKitServiceProvider::class)
    ->toExtend(ServiceProvider::class);

arch('theme helper keeps the helper suffix')
    ->expect('Art35rennes\\DaisyKit\\Helpers\\ThemeHelper')
    ->toHaveSuffix('Helper');

arch('support and helper utility classes stay framework-light')
    ->expect([
        'Art35rennes\\DaisyKit\\Helpers',
        'Art35rennes\\DaisyKit\\Support',
    ])
    ->toExtendNothing();

arch('controller contract stays invokable')
    ->expect('Art35rennes\\DaisyKit\\Http\\Controllers')
    ->toBeInvokable();

arch('package source does not contain debugging helpers')
    ->expect('Art35rennes\\DaisyKit')
    ->not->toUse(['dd', 'dump', 'die', 'var_dump']);

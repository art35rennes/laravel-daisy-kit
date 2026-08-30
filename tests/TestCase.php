<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Tests;

use Art35rennes\DaisyKit\DaisyKitServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithWorkbench;

    /** @return array<int, class-string> */
    protected function getPackageProviders($app): array
    {
        return [DaisyKitServiceProvider::class];
    }
}

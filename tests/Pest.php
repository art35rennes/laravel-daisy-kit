<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

pest()->browser()->timeout(10_000);

pest()->tia()->locally()->baselined()->directory(__DIR__.'/.pest/tia');

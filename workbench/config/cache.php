<?php

declare(strict_types=1);

/**
 * The Workbench is an interactive demonstrator, not an application that owns a
 * cache migration. Livewire 4 rate-limits snapshot checksum failures through
 * the configured store, so the Testbench skeleton's database default causes
 * every mutation request to fail before the component is reached.
 */
$configuration = require base_path('config/cache.php');

$configuration['default'] = 'array';

return $configuration;

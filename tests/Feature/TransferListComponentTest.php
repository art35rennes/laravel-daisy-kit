<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders a semantic CSP-safe transfer list shell', function (): void {
    $html = view('daisy-kit::components.transfer-list', [
        'name' => 'permissions', 'value' => ['read'],
        'items' => [['value' => 'read', 'label' => '</script><img src=x>']],
    ])->render();
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)->toContain('data-daisy-kit-module="transfer-list"')
        ->toContain('data-daisy-kit-transfer-source')
        ->toContain('data-daisy-kit-transfer-target')
        ->toContain('aria-multiselectable="true"')
        ->not->toContain('<img src=x>')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''))['value'])->toBe(['read']);
});

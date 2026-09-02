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
        ->toContain('card card-border')
        ->toContain('checkbox checkbox-sm')
        ->toContain('data-daisy-kit-transfer-select-all="source"')
        ->toContain('data-daisy-kit-transfer-count="target"')
        ->toContain('data-daisy-kit-transfer-page="source:previous"')
        ->toContain('aria-multiselectable="true"')
        ->not->toContain('<img src=x>')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''))['value'])->toBe(['read']);
});

it('serializes the additive assignment experience options', function (): void {
    $html = view('daisy-kit::components.transfer-list', [
        'oneWay' => true,
        'pagination' => true,
        'pageSize' => 12,
        'selectAllScope' => 'filtered',
        'showSelectAll' => false,
    ])->render();
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);
    $configuration = JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));

    expect($configuration)->toMatchArray([
        'oneWay' => true,
        'pagination' => true,
        'pageSize' => 12,
        'selectAllScope' => 'filtered',
        'showSelectAll' => false,
    ]);
});

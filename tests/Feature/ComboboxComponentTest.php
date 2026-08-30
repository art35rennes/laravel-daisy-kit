<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders a CSP-safe combobox configuration and Laravel field shell', function (): void {
    $html = view('daisy-kit::components.combobox', [
        'name' => 'assignees', 'multiple' => true, 'allowCustom' => true,
        'options' => [['value' => 'ada', 'label' => '</script><img src=x>']],
    ])->render();
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)->toContain('data-daisy-kit-module="combobox"')
        ->toContain('role="combobox"')
        ->toContain('role="listbox"')
        ->not->toContain('<img src=x>')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''))['name'])->toBe('assignees');
});

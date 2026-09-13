<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders a CSP-safe combobox configuration and Laravel field shell', function (): void {
    $html = view('daisy-kit::components.combobox', [
        'name' => 'assignees', 'multiple' => true, 'allowCustom' => true,
        'size' => 'lg', 'maxSuggestions' => 12,
        'searchFields' => ['label', 'description'],
        'options' => [['value' => 'ada', 'label' => '</script><img src=x>', 'avatar' => '/ada.jpg']],
    ])->render();
    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);

    expect($html)->toContain('data-daisy-kit-module="combobox"')
        ->toContain('role="combobox"')
        ->toContain('role="listbox"')
        ->toContain('data-daisy-kit-combobox-control')
        ->toContain('data-daisy-kit-combobox-toggle')
        ->toContain('input-lg')
        ->not->toContain('<img src=x>')
        ->not->toContain('style=')
        ->and(JsonConfiguration::decode(html_entity_decode($matches[1] ?? '')))->toMatchArray([
            'name' => 'assignees',
            'maxSuggestions' => 12,
            'searchFields' => ['label', 'description'],
        ]);

    expect($html)->toMatch('/data-daisy-kit-combobox-control[^>]*>.*data-daisy-kit-combobox-tokens.*data-daisy-kit-combobox-input/s');
});

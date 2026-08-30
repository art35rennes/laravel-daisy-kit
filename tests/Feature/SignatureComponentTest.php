<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders a CSP-safe signature control with native submission', function (): void {
    $html = view('daisy-kit::components.signature', [
        'name' => 'approval',
        'label' => 'Approval signature',
        'required' => true,
        'value' => 'data:image/png;base64,AAAA',
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);
    $configuration = JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));

    expect($html)->toContain('data-daisy-kit-module="signature"')
        ->toContain('name="approval"')
        ->toContain('required')
        ->toContain('<canvas')
        ->not->toContain('style=')
        ->and($configuration)->toMatchArray([
            'value' => 'data:image/png;base64,AAAA',
            'required' => true,
            'disabled' => false,
        ]);
});

<?php

declare(strict_types=1);

it('renders a JSON-safe forms viewer mount point with a semantic loading state', function (): void {
    $html = view('daisy-kit::components.forms.viewer', [
        'schema' => [
            'fields' => [[
                'name' => 'display_name',
                'label' => '</script><img src=x onerror=alert(1)>',
                'type' => 'text',
            ]],
        ],
        'value' => ['display_name' => 'Ada'],
    ])->render();

    expect($html)->toContain('data-daisy-kit-module="forms-viewer"')
        ->toContain('type="application/json"')
        ->toContain('aria-busy="true"')
        ->toContain('Loading form…')
        ->not->toContain('</script><img');
});

it('renders a forms builder mount point without executable configuration', function (): void {
    $html = view('daisy-kit::components.forms.builder', [
        'schema' => ['fields' => []],
    ])->render();

    expect($html)->toContain('data-daisy-kit-module="forms-builder"')
        ->toContain('type="application/json"')
        ->toContain('role="status"')
        ->not->toContain('<script>')
        ->not->toContain(' style=')
        ->not->toContain(' on');
});

it('serializes viewer errors, readonly state, and submit mode as non-executable configuration', function (): void {
    $html = view('daisy-kit::components.forms.viewer', [
        'schema' => [
            'fields' => [[
                'name' => 'email',
                'type' => 'email',
            ]],
        ],
        'value' => ['email' => 'ada@example.test'],
        'errors' => ['email' => ['This address is already used.']],
        'readonly' => true,
        'submitMode' => 'none',
    ])->render();

    expect($html)
        ->toContain('"errors":{"email":["This address is already used."]}')
        ->toContain('"readonly":true')
        ->toContain('"submitMode":"none"')
        ->toContain('type="application/json"')
        ->not->toContain('onerror=');
});

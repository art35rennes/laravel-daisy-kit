<?php

declare(strict_types=1);

use Illuminate\Support\MessageBag;

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

    expect($html)->not->toContain('<livewire:');

    expect($html)->toContain('data-daisy-kit-module="forms-builder"')
        ->toContain('type="application/json"')
        ->toContain('role="status"')
        ->not->toContain('<script>')
        ->not->toContain(' style=')
        ->not->toContain(' on');
});

it('keeps the forms builder JSON configuration as a direct mount child when Livewire is available', function (): void {
    $html = view('daisy-kit::components.forms.builder', [
        'schema' => ['fields' => []],
    ])->render();

    $document = new DOMDocument;
    @$document->loadHTML($html);

    $root = $document->getElementById('daisy-kit-forms-builder-contract')
        ?? (new DOMXPath($document))->query('//*[@data-daisy-kit-module="forms-builder"]')->item(0);

    expect($root)->toBeInstanceOf(DOMElement::class);

    $configuration = (new DOMXPath($document))->query('./script[@data-daisy-kit-config]', $root);

    expect($configuration)->toHaveCount(1)
        ->and($configuration->item(0)?->textContent)->toBe('{"livewireAvailable":true,"schema":{"fields":[]}}');
});

it('forwards the complete public Builder contract to the optional Livewire authoring surface', function (): void {
    $html = view('daisy-kit::components.forms.builder', [
        'schema' => ['fields' => [['name' => 'email', 'label' => 'Email', 'type' => 'email']]],
        'name' => 'profile_schema',
        'value' => ['email' => 'ada@example.test'],
        'errors' => ['email' => ['Already taken.']],
        'preview' => false,
        'jsonEditor' => false,
    ])->render();

    expect($html)
        ->toContain('name="profile_schema"')
        ->toContain('data-daisy-kit-livewire-builder')
        ->not->toContain('data-daisy-kit-builder-preview')
        ->not->toContain('data-daisy-kit-builder-json');
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

it('serializes native form transport and progressive validation configuration without inline handlers', function (): void {
    $html = view('daisy-kit::components.forms.viewer', [
        'schema' => [
            'fields' => [[
                'name' => 'attachment',
                'type' => 'file',
            ]],
        ],
        'action' => '/profiles/1',
        'method' => 'PATCH',
        'validateOn' => 'input',
    ])->render();

    expect($html)
        ->toContain('"action":"\\/profiles\\/1"')
        ->toContain('"method":"PATCH"')
        ->toContain('"validateOn":"input"')
        ->not->toContain(' onsubmit=')
        ->not->toContain(' style=');
});

it('maps Laravel message bags into the viewer error configuration', function (): void {
    $html = view('daisy-kit::components.forms.viewer', [
        'schema' => ['fields' => [['name' => 'email', 'type' => 'email']]],
        'errors' => new MessageBag([
            'email' => ['This address is already used.'],
        ]),
    ])->render();

    expect($html)->toContain('"errors":{"email":["This address is already used."]}');
});

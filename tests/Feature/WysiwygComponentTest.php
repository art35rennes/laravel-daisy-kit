<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\JsonConfiguration;

it('renders a CSP-safe DaisyUI WYSIWYG field with native form configuration', function (): void {
    $html = view('daisy-kit::components.wysiwyg', [
        'name' => 'body',
        'label' => 'Article body',
        'value' => '<p>Hello <strong>world</strong></p>',
        'placeholder' => 'Write the article',
        'required' => true,
        'attachments' => true,
        'size' => 'lg',
    ])->render();

    preg_match('/<script data-daisy-kit-config type="application\/json">(.*?)<\/script>/s', $html, $matches);
    $configuration = JsonConfiguration::decode(html_entity_decode($matches[1] ?? ''));

    expect($html)
        ->toContain('data-daisy-kit-module="wysiwyg"')
        ->toContain('class="fieldset-legend"')
        ->toContain('data-daisy-kit-wysiwyg-editor')
        ->toContain('name="body"')
        ->toContain('type="hidden"')
        ->toContain('role="alert"')
        ->toContain('<svg aria-hidden="true"')
        ->toContain('data-trix-attribute="bold"')
        ->toContain('data-trix-attribute="italic"')
        ->toContain('data-trix-attribute="strike"')
        ->toContain('data-trix-attribute="heading1"')
        ->toContain('data-trix-attribute="quote"')
        ->toContain('data-trix-attribute="code"')
        ->toContain('data-trix-attribute="bullet"')
        ->toContain('data-trix-attribute="number"')
        ->toContain('data-trix-action="attachFiles"')
        ->toContain('data-trix-action="undo"')
        ->toContain('data-trix-action="redo"')
        ->not->toContain('<script>alert')
        ->not->toContain('style=')
        ->not->toContain('onclick=')
        ->and($configuration)->toMatchArray([
            'attachments' => true,
            'autofocus' => false,
            'disabled' => false,
            'placeholder' => 'Write the article',
            'readonly' => false,
            'required' => true,
            'showToolbar' => true,
            'size' => 'lg',
            'value' => '<p>Hello <strong>world</strong></p>',
        ]);
});

it('does not render toolbar markup or its slot when the toolbar is hidden', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-daisy-kit::wysiwyg name="summary" :show-toolbar="false">
            <x-slot:toolbar><button type="button">Hidden custom action</button></x-slot:toolbar>
        </x-daisy-kit::wysiwyg>
    BLADE);

    expect($html)
        ->not->toContain('data-daisy-kit-wysiwyg-toolbar-template')
        ->not->toContain('Hidden custom action');
});

it('escapes hostile initial HTML in attributes and JSON configuration', function (): void {
    $payload = '</script><script>alert(1)</script><img src=x onerror=alert(2)>';
    $html = view('daisy-kit::components.wysiwyg', ['name' => 'body', 'value' => $payload])->render();

    expect($html)
        ->not->toContain('</script><script>alert(1)</script>')
        ->not->toContain('value="'.$payload.'"')
        ->toContain('\\u003C\\/script\\u003E');
});

it('renders a custom toolbar as inert markup and supports disabled fields', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-daisy-kit::wysiwyg name="summary" label="Summary" disabled :show-toolbar="true">
            <x-slot:toolbar>
                <button type="button" data-trix-attribute="bold">Custom bold</button>
            </x-slot:toolbar>
        </x-daisy-kit::wysiwyg>
    BLADE);

    expect($html)
        ->toContain('data-daisy-kit-wysiwyg-toolbar-template')
        ->toContain('data-trix-attribute="bold"')
        ->toContain('Custom bold')
        ->toContain('disabled');
});

it('documents the Trix runtime CSP in the Workbench response', function (): void {
    $response = $this->get('/wysiwyg')->assertOk();
    $policy = (string) $response->headers->get('Content-Security-Policy');

    expect($response->getContent())
        ->toContain('name="trix-csp-nonce"')
        ->and($policy)
        ->toContain("style-src 'self' 'nonce-")
        ->toContain("style-src-attr 'unsafe-inline'")
        ->toContain('img-src \'self\' data: blob:');
});

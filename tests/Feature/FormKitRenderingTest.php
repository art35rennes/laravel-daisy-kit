<?php

use Illuminate\Support\Facades\Blade;

it('renders the form viewer public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            :schema="[
                'version' => '1.0',
                'id' => 'contact',
                'meta' => ['title' => 'Contact'],
                'fields' => [
                    ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email', 'rules' => ['required', 'email']],
                    ['id' => 'total', 'type' => 'number', 'name' => 'total', 'label' => 'Total', 'computed' => ['type' => 'jsonata', 'expression' => '1 + 1', 'dependsOn' => [], 'mode' => 'readonly']],
                ],
                'submit' => ['mode' => 'event', 'label' => 'Send'],
            ]"
            :value="['email' => 'jane@example.com']"
        />
    BLADE);

    expect($html)
        ->toContain('data-module="form-viewer"')
        ->toContain('data-form-input="email"')
        ->toContain('jane@example.com')
        ->toContain('data-form-schema')
        ->toContain('Send');
});

it('renders the form builder public alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.builder
            name="schema"
            :functionCatalog="[
                ['name' => '$uuid', 'signature' => '<s:s>', 'description' => 'UUID'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('data-module="form-builder"')
        ->toContain('data-builder-palette')
        ->toContain('data-builder-inspector')
        ->toContain('data-builder-json')
        ->toContain('name="schema"')
        ->toContain('$uuid');
});

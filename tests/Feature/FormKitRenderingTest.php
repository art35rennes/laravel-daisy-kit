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

it('renders a multi step form viewer with Blade-owned step markup', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::forms.viewer
            :schema="[
                'version' => '1.0',
                'id' => 'onboarding',
                'layout' => ['type' => 'multi-step'],
                'fields' => [
                    [
                        'id' => 'contact',
                        'type' => 'wizardStep',
                        'label' => 'Contact',
                        'fields' => [
                            ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                        ],
                    ],
                    [
                        'id' => 'profile',
                        'type' => 'wizardStep',
                        'label' => 'Profile',
                        'fields' => [
                            ['id' => 'bio', 'type' => 'textarea', 'name' => 'bio', 'label' => 'Bio'],
                        ],
                    ],
                ],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('data-form-step="contact"')
        ->toContain('data-form-step="profile"')
        ->toContain('data-form-previous')
        ->toContain('data-form-next')
        ->toContain('data-form-submit');
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
        ->toContain('data-builder-template="palette-item"')
        ->toContain('data-builder-template="outline-item"')
        ->toContain('data-builder-template="inspector-input"')
        ->toContain('name="schema"')
        ->toContain('$uuid');
});

it('renders the form builder template with builder and viewer surfaces', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::templates.form.builder
            :schema="[
                'version' => '1.0',
                'id' => 'contact',
                'meta' => ['title' => 'Contact'],
                'fields' => [
                    ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
                ],
            ]"
            :value="['email' => 'ada@example.com']"
            schema-name="form_schema"
        />
    BLADE);

    expect($html)
        ->toContain('data-module="form-builder"')
        ->toContain('data-module="form-viewer"')
        ->toContain('name="form_schema"')
        ->toContain('ada@example.com');
});

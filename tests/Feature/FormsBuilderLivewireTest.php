<?php

declare(strict_types=1);

use Livewire\Livewire;

it('authors a safe field and renders a viewer preview through Livewire 4', function (): void {
    Livewire::test('daisy-kit.forms.builder', [
        'schema' => [
            'fields' => [[
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
            ]],
        ],
    ])
        ->call('addField')
        ->call('updateField', 1, 'name', 'profile_url')
        ->set('schema.fields.1.type', 'unsafe-legacy-type')
        ->assertSet('schema.fields.1', [
            'name' => 'profile_url',
            'label' => 'Field 2',
            'type' => 'text',
        ])
        ->assertSee('Form preview')
        ->assertSee('profile_url');
});

it('exports a synchronized hidden schema value', function (): void {
    Livewire::test('daisy-kit.forms.builder', ['schema' => ['fields' => [
        ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
    ]]])
        ->assertSee('name="schema"', false)
        ->assertSee('"name":"email"', false);
});

it('imports JSON schema metadata for authored fields', function (): void {
    Livewire::test('daisy-kit.forms.builder')
        ->call('importSchema', '{"fields":[{"name":"role","label":"Role","type":"select","options":["member"],"rules":["required"],"visibleWhen":"enabled"}]}')
        ->assertSet('schema.fields.0.options.0', 'member')
        ->assertSet('schema.fields.0.rules.0', 'required')
        ->assertSet('schema.fields.0.visibleWhen', 'enabled');
});

it('removes, reorders, and restores authored fields', function (): void {
    Livewire::test('daisy-kit.forms.builder', ['schema' => ['fields' => [
        ['name' => 'first', 'label' => 'First', 'type' => 'text'],
        ['name' => 'second', 'label' => 'Second', 'type' => 'text'],
    ]]])
        ->call('moveField', 1, -1)
        ->assertSet('schema.fields.0.name', 'second')
        ->call('removeField', 0)
        ->assertSet('schema.fields.0.name', 'first')
        ->call('undo')
        ->assertSet('schema.fields.0.name', 'second')
        ->call('redo')
        ->assertSet('schema.fields.0.name', 'first');
});

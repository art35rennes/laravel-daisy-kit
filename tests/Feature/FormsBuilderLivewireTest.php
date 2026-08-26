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

it('diagnoses non-canonical JSONata input instead of translating it to a legacy dialect', function (): void {
    Livewire::test('daisy-kit.forms.builder')
        ->call('importSchema', '{"fields":[{"name":"role","label":"Role","type":"select","options":["member"],"rules":["required"],"visibleWhen":"enabled"}]}')
        ->assertSet('schema.fields.0.options.0', 'member')
        ->assertSet('schema.fields.0.rules.0', 'required')
        ->assertSet('schema.fields.0.visibleWhen', 'enabled')
        ->assertSet('diagnostics.0.code', 'invalid_jsonata');
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

it('authors a nested form through the catalogue and keeps editor state synchronized', function (): void {
    Livewire::test('daisy-kit.forms.builder')
        ->assertSet('fieldCatalogue.0.type', 'text')
        ->call('addSection')
        ->call('selectField', 'section_1')
        ->call('addField', 'select')
        ->call('updateSelectedPath', 'name', 'role')
        ->call('updateSelectedPath', 'label', 'Role')
        ->call('updateSelectedJson', 'attrs', '{"required":true,"data-source":"profile"}')
        ->call('updateSelectedJson', 'options', '[{"label":"Member","value":"member"}]')
        ->call('updateSelectedJson', 'rules', '["required",{"type":"jsonata","expression":"role = true","dependsOn":["role"],"message":"Choose a role"}]')
        ->call('updateSelectedJson', 'visibleWhen', '{"type":"jsonata","expression":"accountType = true","dependsOn":["accountType"]}')
        ->assertSet('schema.fields.0.type', 'section')
        ->assertSet('schema.fields.0.fields.0.type', 'select')
        ->assertSet('schema.fields.0.fields.0.attrs.required', true)
        ->assertSet('schema.fields.0.fields.0.options.0.value', 'member')
        ->assertSet('schema.fields.0.fields.0.rules.1.message', 'Choose a role')
        ->assertSet('schema.fields.0.fields.0.visibleWhen.dependsOn.0', 'accountType')
        ->assertSee('Role')
        ->assertSee('data-daisy-kit-builder-depth="2"', false)
        ->assertSee('data-daisy-kit-builder-preview', false)
        ->assertSee('name="schema"', false)
        ->assertSee('"type":"section"', false);
});

it('keeps nested tree mutations and their reversible history stable', function (): void {
    Livewire::test('daisy-kit.forms.builder')
        ->call('addSection')
        ->call('selectField', 'section_1')
        ->call('addField', 'select')
        ->call('addField', 'text')
        ->call('moveField', 'text_3', -1)
        ->assertSet('schema.fields.0.fields.0.name', 'text_3')
        ->assertSet('schema.fields.0.fields.1.name', 'select_2')
        ->call('removeField', 'select_2')
        ->assertSet('schema.fields.0.fields.0.name', 'text_3')
        ->call('undo')
        ->assertSet('schema.fields.0.fields.1.name', 'select_2');
});

it('reports invalid authoring JSON without discarding the last valid schema and can undo it', function (): void {
    Livewire::test('daisy-kit.forms.builder', ['schema' => ['fields' => [
        ['id' => 'email', 'name' => 'email', 'label' => 'Email', 'type' => 'email'],
    ]]])
        ->call('selectField', 'email')
        ->call('updateSelectedJson', 'visibleWhen', '{invalid')
        ->assertSet('schema.fields.0.name', 'email')
        ->assertSet('jsonError', 'visibleWhen: Invalid JSON.')
        ->assertSee('visibleWhen: Invalid JSON.')
        ->call('updateSelectedPath', 'label', 'Work email')
        ->assertSet('schema.fields.0.label', 'Work email')
        ->call('undo')
        ->assertSet('schema.fields.0.label', 'Email')
        ->assertSet('schema.fields.0.name', 'email');
});

it('imports and exports valid JSON while preserving its hidden name and value contract', function (): void {
    $schema = '{"layout":{"type":"multi-step"},"fields":[{"id":"details","type":"wizardStep","label":"Details","fields":[{"id":"phone","type":"tel","name":"phone","label":"Phone"}]}]}';

    Livewire::test('daisy-kit.forms.builder', ['name' => 'profile_schema'])
        ->call('importSchema', $schema)
        ->assertSet('schema.layout.type', 'multi-step')
        ->assertSet('schema.fields.0.fields.0.type', 'tel')
        ->assertSee('name="profile_schema"', false)
        ->assertSee('"name":"phone"', false)
        ->call('undo')
        ->assertSet('schema.fields', []);
});

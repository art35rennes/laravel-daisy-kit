<?php

use Art35rennes\DaisyKit\FormKit\Livewire\FormBuilder;
use Livewire\Livewire;

it('updates canonical JSON from Livewire builder state', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [],
        ],
        'fieldTypes' => [
            ['type' => 'text', 'label' => 'Text'],
        ],
        'name' => 'schema',
    ])
        ->call('addField', 'text')
        ->call('updateSelectedField', 'label', 'Full name')
        ->call('updateSelectedJson', 'rules', '["required"]')
        ->assertSet('schema.fields.0.label', 'Full name')
        ->assertSet('schemaJson', fn (string $json): bool => str_contains($json, '"label": "Full name"')
            && str_contains($json, '"rules": [')
            && str_contains($json, '"required"'));
});

it('selects rows without opening the field editor', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ],
    ])
        ->assertSet('selectedId', null)
        ->assertSet('fieldEditorOpen', false)
        ->assertSeeHtml('data-builder-select')
        ->call('selectField', 'email')
        ->assertSet('selectedId', 'email')
        ->assertSet('fieldEditorOpen', false)
        ->assertSeeHtml('data-builder-selected="true"')
        ->call('clearSelection')
        ->assertSet('selectedId', null);
});

it('can read canonical JSON back into the Livewire builder for editing', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [],
        ],
    ])
        ->call('updateFromJsonPayload', json_encode([
            'version' => '1.0',
            'id' => 'imported',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ]))
        ->assertSet('schema.id', 'imported')
        ->assertSet('selectedId', null)
        ->call('selectField', 'email')
        ->call('updateSelectedPath', 'label', 'Work email')
        ->assertSet('schema.fields.0.label', 'Work email');
});

it('renders the real forms viewer as the reactive preview', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ],
        'value' => ['email' => 'ada@example.com'],
    ])
        ->assertSeeHtml('data-form-builder-livewire')
        ->assertSee('Inputs')
        ->assertSee('Schema settings')
        ->assertSee('Visual editing')
        ->assertSeeHtml('data-module="code-editor"')
        ->assertSeeHtml('data-builder-json')
        ->assertSeeHtml('data-module="form-viewer"')
        ->assertSeeHtml('data-form-input="email"')
        ->assertSeeHtml('data-builder-stop-propagation data-builder-preview')
        ->assertSeeHtml('data-builder-type-badge="email"')
        ->assertSeeHtml('daisy-form-builder-type-badge')
        ->assertSeeHtml('badge-info text-info')
        ->assertSeeHtml('data-builder-outline')
        ->assertSeeHtml('btn-outline btn-info')
        ->assertSeeHtml('btn-outline btn-error')
        ->assertSeeHtml('btn-success')
        ->assertSee('ada@example.com')
        ->call('selectField', 'email')
        ->assertSeeHtml('data-builder-field-editor')
        ->assertSeeHtml('data-builder-editor-tabs')
        ->assertSeeHtml('data-builder-editor-tab-panel="display"')
        ->assertSeeHtml('data-builder-custom-id')
        ->assertSeeHtml('data-builder-json-property="rules"')
        ->call('updateSelectedField', 'label', 'Email address')
        ->assertSee('Email address');
});

it('uses DaisyUI type badge colors aligned with field catalog groups', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'section', 'type' => 'section', 'label' => 'Section'],
                ['id' => 'plan', 'type' => 'select', 'name' => 'plan', 'label' => 'Plan'],
                ['id' => 'notify', 'type' => 'toggle', 'name' => 'notify', 'label' => 'Notify'],
                ['id' => 'start_date', 'type' => 'date', 'name' => 'start_date', 'label' => 'Start date'],
            ],
        ],
    ])
        ->assertSeeHtml('data-builder-type-badge="section"')
        ->assertSeeHtml('badge-primary text-primary')
        ->assertSeeHtml('data-builder-type-badge="select"')
        ->assertSeeHtml('badge-secondary text-secondary')
        ->assertSeeHtml('data-builder-type-badge="toggle"')
        ->assertSeeHtml('badge-secondary text-secondary')
        ->assertSeeHtml('data-builder-type-badge="date"')
        ->assertSeeHtml('badge-warning text-warning');
});

it('opens field editing in a responsive modal without a backdrop', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ],
    ])
        ->assertSet('fieldEditorOpen', false)
        ->assertDontSeeHtml('data-builder-editor-modal')
        ->assertDontSeeHtml('modal-backdrop')
        ->call('editField', 'email')
        ->assertSet('fieldEditorOpen', true)
        ->assertSet('selectedId', 'email')
        ->assertSeeHtml('data-builder-editor-modal')
        ->assertSeeHtml('data-builder-editor-cancel')
        ->assertSeeHtml('data-builder-editor-confirm')
        ->assertSeeHtml('btn-outline btn-error')
        ->assertSeeHtml('btn-success')
        ->assertSee('Payload name')
        ->assertSee('Custom identifier')
        ->assertSee('Enable only when the internal identifier must differ')
        ->call('closeFieldEditor')
        ->assertSet('fieldEditorOpen', false)
        ->assertSet('selectedId', 'email');
});

it('can cancel or validate field editor changes', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ],
    ])
        ->call('editField', 'email')
        ->call('updateSelectedPath', 'label', 'Work email')
        ->assertSet('schema.fields.0.label', 'Work email')
        ->call('cancelFieldEditor')
        ->assertSet('schema.fields.0.label', 'Email')
        ->call('editField', 'email')
        ->call('updateSelectedPath', 'label', 'Work email')
        ->call('closeFieldEditor')
        ->assertSet('schema.fields.0.label', 'Work email')
        ->assertSet('fieldEditorOpen', false);
});

it('restores the previous selection when cancelling field editing', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'first_name', 'type' => 'text', 'name' => 'first_name', 'label' => 'First name'],
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
        ],
    ])
        ->call('editField', 'email')
        ->call('updateSelectedPath', 'label', 'Work email')
        ->call('cancelFieldEditor')
        ->assertSet('selectedId', 'email')
        ->assertSet('schema.fields.1.label', 'Email');
});

it('adds new fields relative to the selected object without opening the editor', function () {
    $component = Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                [
                    'id' => 'identity',
                    'type' => 'section',
                    'label' => 'Identity',
                    'fields' => [
                        ['id' => 'first_name', 'type' => 'text', 'name' => 'first_name'],
                    ],
                ],
                ['id' => 'email', 'type' => 'email', 'name' => 'email'],
            ],
        ],
        'fieldTypes' => [
            ['type' => 'text', 'label' => 'Text'],
            ['type' => 'email', 'label' => 'Email'],
        ],
    ])
        ->assertSet('selectedId', null)
        ->call('addField', 'text')
        ->assertSet('selectedId', 'text-4')
        ->assertSet('fieldEditorOpen', false);

    $schema = $component->instance()->canonicalSchema();
    expect(array_column($schema['fields'], 'id'))->toBe(['identity', 'email', 'text-4']);

    $component
        ->call('selectField', 'first_name')
        ->call('addField', 'text')
        ->assertSet('selectedId', 'text-5')
        ->assertSet('fieldEditorOpen', false);

    $schema = $component->instance()->canonicalSchema();
    expect(array_column($schema['fields'][0]['fields'], 'id'))->toBe(['first_name', 'text-5'])
        ->and(array_column($schema['fields'], 'id'))->toBe(['identity', 'email', 'text-4']);

    $component
        ->call('selectField', 'identity')
        ->call('addField', 'email')
        ->assertSet('selectedId', 'email-6')
        ->assertSet('fieldEditorOpen', false);

    $schema = $component->instance()->canonicalSchema();
    expect(array_column($schema['fields'][0]['fields'], 'id'))->toBe(['first_name', 'text-5', 'email-6']);
});

it('updates canonical JSON from schema settings fields', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email', 'label' => 'Email'],
            ],
            'submit' => ['mode' => 'event', 'label' => 'Send'],
        ],
    ])
        ->assertSeeHtml('data-builder-schema-settings')
        ->assertSee('Schema title')
        ->assertSee('Schema description')
        ->call('updateSchemaKey', 'meta.title', 'Lead capture')
        ->call('updateSchemaKey', 'meta.description', 'Collect qualified contacts')
        ->call('updateSchemaKey', 'submit.mode', 'fetch')
        ->assertSet('schema.meta.title', 'Lead capture')
        ->assertSet('schema.meta.description', 'Collect qualified contacts')
        ->assertSet('schemaJson', fn (string $json): bool => str_contains($json, '"title": "Lead capture"')
            && str_contains($json, '"description": "Collect qualified contacts"'))
        ->assertSet('schema.submit.mode', 'fetch');
});

it('shows schema diagnostics in the Livewire Blade output', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'email', 'type' => 'email', 'name' => 'email'],
                ['id' => 'email', 'type' => 'text', 'name' => 'email'],
            ],
        ],
    ])
        ->assertSeeHtml('data-builder-diagnostics')
        ->assertSee('duplicated');
});

it('reorders nested fields inside their current parent only', function () {
    $component = Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                [
                    'id' => 'identity',
                    'type' => 'section',
                    'label' => 'Identity',
                    'fields' => [
                        ['id' => 'first_name', 'type' => 'text', 'name' => 'first_name'],
                        ['id' => 'last_name', 'type' => 'text', 'name' => 'last_name'],
                    ],
                ],
                ['id' => 'email', 'type' => 'email', 'name' => 'email'],
            ],
        ],
    ])->call('moveField', 'last_name', -1);

    $schema = $component->instance()->canonicalSchema();

    expect(array_column($schema['fields'][0]['fields'], 'id'))->toBe(['last_name', 'first_name'])
        ->and(array_column($schema['fields'], 'id'))->toBe(['identity', 'email']);

    $component->call('moveField', 'email', -1);
    $schema = $component->instance()->canonicalSchema();

    expect(array_column($schema['fields'], 'id'))->toBe(['email', 'identity']);
});

it('reorders fields from drag and drop zones across parents', function () {
    $component = Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                [
                    'id' => 'identity',
                    'type' => 'section',
                    'label' => 'Identity',
                    'fields' => [
                        ['id' => 'first_name', 'type' => 'text', 'name' => 'first_name'],
                        ['id' => 'last_name', 'type' => 'text', 'name' => 'last_name'],
                    ],
                ],
                [
                    'id' => 'project',
                    'type' => 'section',
                    'label' => 'Project',
                    'fields' => [
                        ['id' => 'plan', 'type' => 'select', 'name' => 'plan'],
                    ],
                ],
                ['id' => 'email', 'type' => 'email', 'name' => 'email'],
            ],
        ],
    ])
        ->assertSeeHtml('data-builder-drag-handle')
        ->assertSeeHtml('data-builder-drop-zone="before"')
        ->assertSeeHtml('data-builder-drop-target="identity"')
        ->assertSeeHtml('data-builder-drop-tone="0"')
        ->assertSeeHtml('data-builder-drop-tone="1"')
        ->assertSeeHtml('data-builder-drag-descendants')
        ->assertSeeHtml('data-builder-drag-field="identity"')
        ->assertSeeHtml('data-builder-drop-action="before"')
        ->assertSeeHtml('data-builder-drop-action="after"')
        ->assertSeeHtml('data-builder-drop-kind="position"')
        ->assertSee('Insert at this position')
        ->assertDontSeeHtml('wire:dragstart')
        ->call('startDragging', 'last_name')
        ->assertSet('draggingFieldId', 'last_name')
        ->call('dropField', 'first_name', 'before')
        ->assertSet('draggingFieldId', null);

    $schema = $component->instance()->canonicalSchema();

    expect(array_column($schema['fields'][0]['fields'], 'id'))->toBe(['last_name', 'first_name'])
        ->and(array_column($schema['fields'], 'id'))->toBe(['identity', 'project', 'email']);

    $component
        ->call('dropField', 'plan', 'before', 'last_name');

    $schema = $component->instance()->canonicalSchema();

    expect(array_column($schema['fields'][0]['fields'], 'id'))->toBe(['first_name'])
        ->and(array_column($schema['fields'][1]['fields'], 'id'))->toBe(['last_name', 'plan']);

    $component
        ->call('dropField', 'project', 'inside', 'email');

    $schema = $component->instance()->canonicalSchema();

    expect(array_column($schema['fields'], 'id'))->toBe(['identity', 'project'])
        ->and(array_column($schema['fields'][1]['fields'], 'id'))->toBe(['last_name', 'plan', 'email']);

    $component
        ->call('dropField', 'last_name', 'inside', 'project');

    $schema = $component->instance()->canonicalSchema();

    expect(array_column($schema['fields'], 'id'))->toBe(['identity', 'project'])
        ->and(array_column($schema['fields'][1]['fields'], 'id'))->toBe(['last_name', 'plan', 'email']);
});

it('collapses and expands nested fields in the builder outline', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                [
                    'id' => 'identity',
                    'type' => 'section',
                    'label' => 'Identity',
                    'fields' => [
                        ['id' => 'first_name', 'type' => 'text', 'name' => 'first_name'],
                        ['id' => 'last_name', 'type' => 'text', 'name' => 'last_name'],
                    ],
                ],
            ],
        ],
    ])
        ->call('selectField', 'first_name')
        ->assertSet('selectedId', 'first_name')
        ->assertSeeHtml('data-builder-field="first_name"')
        ->call('toggleFieldCollapsed', 'identity')
        ->assertSet('selectedId', 'identity')
        ->assertSeeHtml('data-builder-collapse="closed"')
        ->assertDontSeeHtml('data-builder-field="first_name"')
        ->call('toggleFieldCollapsed', 'identity')
        ->assertSeeHtml('data-builder-collapse="open"')
        ->assertSeeHtml('data-builder-field="first_name"');
});

it('supports global builder actions for search collapse undo redo and export', function () {
    $component = Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                [
                    'id' => 'identity',
                    'type' => 'section',
                    'label' => 'Identity',
                    'fields' => [
                        ['id' => 'first_name', 'type' => 'text', 'name' => 'first_name'],
                        ['id' => 'last_name', 'type' => 'text', 'name' => 'last_name'],
                    ],
                ],
                ['id' => 'email', 'type' => 'email', 'name' => 'email'],
            ],
        ],
    ])
        ->assertSeeHtml('data-builder-search')
        ->assertSeeHtml('data-builder-export')
        ->assertSeeHtml('data-builder-export-json')
        ->assertSeeHtml('data-builder-collapse-all')
        ->assertSeeHtml('data-builder-expand-all')
        ->assertSeeHtml('data-builder-undo')
        ->assertSeeHtml('data-builder-redo')
        ->set('fieldSearch', 'last')
        ->assertSeeHtml('data-builder-field="identity"')
        ->assertSeeHtml('data-builder-field="last_name"')
        ->assertDontSeeHtml('data-builder-field="first_name"')
        ->set('fieldSearch', '')
        ->call('collapseAllFields')
        ->assertSeeHtml('data-builder-collapse="closed"')
        ->assertDontSeeHtml('data-builder-field="first_name"')
        ->call('expandAllFields')
        ->assertSeeHtml('data-builder-field="first_name"')
        ->call('selectField', 'identity')
        ->call('updateSelectedPath', 'label', 'Contact details')
        ->assertSet('schema.fields.0.label', 'Contact details')
        ->call('undo')
        ->assertSet('schema.fields.0.label', 'Identity')
        ->call('redo')
        ->assertSet('schema.fields.0.label', 'Contact details');

    expect($component->instance()->schemaJson)->toContain('"Contact details"');
});

it('covers advanced field authoring attributes', function () {
    $component = Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'title', 'type' => 'text', 'name' => 'title', 'label' => 'Title'],
            ],
        ],
    ])
        ->call('selectField', 'title')
        ->call('updateSelectedJson', 'default', '"draft"')
        ->call('updateSelectedJson', 'options', '[{"label":"A","value":"a"}]')
        ->call('updateSelectedJson', 'visibleWhen', '{"type":"jsonata","expression":"values.enabled = true","dependsOn":["enabled"]}')
        ->call('updateSelectedJson', 'computed', '{"type":"jsonata","expression":"values.first & values.last","dependsOn":["first","last"],"mode":"readonly"}')
        ->call('updateSelectedJson', 'attrs', '{"autocomplete":"name"}')
        ->call('updateSelectedJson', 'ui', '{"width":"1/2"}');

    $field = $component->instance()->canonicalSchema()['fields'][0];

    expect($field)
        ->toMatchArray([
            'default' => 'draft',
            'options' => [['label' => 'A', 'value' => 'a']],
            'visibleWhen' => ['type' => 'jsonata', 'expression' => 'values.enabled = true', 'dependsOn' => ['enabled']],
            'computed' => ['type' => 'jsonata', 'expression' => 'values.first & values.last', 'dependsOn' => ['first', 'last'], 'mode' => 'readonly'],
            'attrs' => ['autocomplete' => 'name'],
            'ui' => ['width' => '1/2'],
        ]);
});

it('keeps static text authoring focused on content and visibility', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'builder',
            'fields' => [
                ['id' => 'copy', 'type' => 'staticText', 'text' => 'Original'],
            ],
        ],
    ])
        ->call('selectField', 'copy')
        ->call('editField', 'copy')
        ->assertSee('Static text')
        ->assertSee('Visibility condition')
        ->assertDontSee('Default value')
        ->assertDontSee('Validation rules')
        ->assertDontSee('Computed value')
        ->call('updateSelectedField', 'text', 'Updated static copy')
        ->call('updateSelectedJson', 'visibleWhen', '{"type":"jsonata","expression":"values.enabled = true","dependsOn":["enabled"]}')
        ->assertSet('schema.fields.0.text', 'Updated static copy')
        ->assertSet('schema.fields.0.visibleWhen.expression', 'values.enabled = true');
});

it('exposes signature component properties in the builder', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'agreement',
            'fields' => [
                ['id' => 'signature', 'type' => 'signature', 'name' => 'signature', 'label' => 'Signature'],
            ],
        ],
    ])
        ->call('selectField', 'signature')
        ->call('editField', 'signature')
        ->assertSeeHtml('data-builder-json-property')
        ->assertSeeHtml('data-builder-field-editor')
        ->assertSee('Canvas width')
        ->assertSee('Canvas height')
        ->assertSee('Pen color')
        ->assertSee('Show signature actions')
        ->call('updateSelectedPath', 'attrs.width', '620')
        ->call('updateSelectedPath', 'attrs.height', '240')
        ->call('updateSelectedPath', 'attrs.penColor', '#123456')
        ->assertSet('schema.fields.0.attrs.width', '620')
        ->assertSet('schema.fields.0.attrs.height', '240')
        ->assertSet('schema.fields.0.attrs.penColor', '#123456');
});

it('exposes package input mask and obfuscation properties in the builder', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'masked',
            'fields' => [
                ['id' => 'phone', 'type' => 'tel', 'name' => 'phone', 'label' => 'Phone'],
            ],
        ],
    ])
        ->call('selectField', 'phone')
        ->call('editField', 'phone')
        ->assertSee('Input mask')
        ->assertSee('Mask placeholder character')
        ->assertSee('Obfuscate value')
        ->assertSee('Visible trailing characters')
        ->call('updateSelectedPath', 'attrs.mask', '99 99 99 99 99')
        ->call('updateSelectedPath', 'attrs.maskCharPlaceholder', '_')
        ->call('updateSelectedPath', 'attrs.maskPlaceholder', true)
        ->call('updateSelectedPath', 'attrs.obfuscate', true)
        ->call('updateSelectedPath', 'attrs.obfuscateKeepEnd', '2')
        ->assertSet('schema.fields.0.attrs.mask', '99 99 99 99 99')
        ->assertSet('schema.fields.0.attrs.maskCharPlaceholder', '_')
        ->assertSet('schema.fields.0.attrs.maskPlaceholder', true)
        ->assertSet('schema.fields.0.attrs.obfuscate', true)
        ->assertSet('schema.fields.0.attrs.obfuscateKeepEnd', '2');
});

it('exposes color picker component properties in the builder', function () {
    Livewire::test(FormBuilder::class, [
        'schema' => [
            'version' => '1.0',
            'id' => 'brand',
            'fields' => [
                ['id' => 'brand_color', 'type' => 'color', 'name' => 'brand_color', 'label' => 'Brand color'],
            ],
        ],
    ])
        ->call('selectField', 'brand_color')
        ->call('editField', 'brand_color')
        ->assertSee('Color picker mode')
        ->assertSee('Show palette')
        ->assertSee('Color swatches')
        ->assertSee('Show alpha slider')
        ->call('updateSelectedPath', 'attrs.mode', 'advanced')
        ->call('updateSelectedPath', 'attrs.dropdown', true)
        ->call('updateSelectedJson', 'attrs.swatches', '[["#123456","#abcdef"]]')
        ->call('updateSelectedPath', 'attrs.showAlpha', false)
        ->assertSet('schema.fields.0.attrs.mode', 'advanced')
        ->assertSet('schema.fields.0.attrs.dropdown', true)
        ->assertSet('schema.fields.0.attrs.swatches', [['#123456', '#abcdef']])
        ->assertSet('schema.fields.0.attrs.showAlpha', false);
});

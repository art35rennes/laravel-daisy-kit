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

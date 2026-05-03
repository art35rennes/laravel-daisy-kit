<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

it('renders a radio choice card group with labels descriptions and selection', function () {
    $html = View::make('daisy::components.ui.inputs.choice-card-group', [
        'name' => 'profile',
        'legend' => 'Vous êtes ?',
        'value' => 'enterprise',
        'items' => [
            [
                'value' => 'individual',
                'label' => 'Particulier',
                'description' => 'Je rencontre un problème avec mon abonnement.',
            ],
            [
                'value' => 'enterprise',
                'label' => 'Entreprise',
                'description' => 'Je signale un dysfonctionnement professionnel.',
            ],
        ],
    ])->render();

    expect($html)
        ->toContain('<fieldset')
        ->toContain('<legend')
        ->toContain('Vous êtes ?')
        ->toContain('type="radio"')
        ->toContain('name="profile"')
        ->toContain('value="individual"')
        ->toContain('value="enterprise"')
        ->toContain('Particulier')
        ->toContain('Je rencontre un problème avec mon abonnement.')
        ->toContain('Entreprise')
        ->toContain('Je signale un dysfonctionnement professionnel.')
        ->toContain('peer-checked:border-primary')
        ->and(preg_match_all('/\schecked(?:\s|>)/', $html))->toBe(1);
});

it('renders a checkbox choice card group with multiple selected values', function () {
    $html = View::make('daisy::components.ui.inputs.choice-card-group', [
        'name' => 'topics',
        'type' => 'checkbox',
        'values' => ['mobile', 'postal'],
        'items' => [
            ['value' => 'mobile', 'label' => 'Mobile'],
            ['value' => 'internet', 'label' => 'Internet fixe'],
            ['value' => 'postal', 'label' => 'Postal'],
        ],
    ])->render();

    expect($html)
        ->toContain('type="checkbox"')
        ->toContain('name="topics[]"')
        ->toContain('value="mobile"')
        ->toContain('value="internet"')
        ->toContain('value="postal"')
        ->and(preg_match_all('/\schecked(?:\s|>)/', $html))->toBe(2);
});

it('propagates required and disabled states', function () {
    $html = View::make('daisy::components.ui.inputs.choice-card-group', [
        'name' => 'subject',
        'required' => true,
        'items' => [
            ['value' => 'mobile', 'label' => 'Mobile'],
            ['value' => 'postal', 'label' => 'Postal', 'disabled' => true],
        ],
    ])->render();

    expect($html)
        ->toContain('required')
        ->toContain('disabled')
        ->toContain('cursor-not-allowed opacity-60');
});

it('renders icons through the public Daisy Kit alias', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.inputs.choice-card-group
            name="profile"
            :items="[
                ['value' => 'individual', 'label' => 'Particulier', 'icon' => 'person'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('type="radio"')
        ->toContain('Particulier')
        ->toContain('<svg');
});

it('keeps accessible group and clickable label structure', function () {
    $html = View::make('daisy::components.ui.inputs.choice-card-group', [
        'name' => 'profile',
        'legend' => 'Profil',
        'hint' => 'Choisissez le profil qui correspond à votre situation.',
        'items' => [
            ['value' => 'individual', 'label' => 'Particulier'],
        ],
    ])->render();

    expect($html)
        ->toContain('<fieldset')
        ->toContain('<legend')
        ->toContain('Profil')
        ->toContain('Choisissez le profil qui correspond à votre situation.')
        ->toContain('<label')
        ->toContain('for="')
        ->toContain('id="')
        ->toContain('class="peer sr-only"');
});

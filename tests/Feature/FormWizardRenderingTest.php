<?php

use Art35rennes\DaisyKit\Helpers\WizardPersistence;

it('renders form wizard with default props and backend fields', function () {
    $view = view('daisy::templates.form.form-wizard', [
        'steps' => [
            ['key' => 'profile', 'label' => 'Profil'],
            ['key' => 'settings', 'label' => 'Paramètres'],
        ],
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('Profil')
        ->toContain('Paramètres')
        ->toContain('data-module="wizard"')
        // Les champs cachés nécessaires au backend pour suivre l\'étape courante.
        ->toContain('name="_wizard_step"')
        ->toContain('name="_wizard_key" value="wizard"');
});

it('persists wizard data in session', function () {
    WizardPersistence::put(['name' => 'John', 'email' => 'john@example.com']);

    $data = WizardPersistence::get();

    expect($data)
        ->toHaveKey('name')
        ->toHaveKey('email')
        ->and($data['name'])->toBe('John')
        ->and($data['email'])->toBe('john@example.com');
});

it('retrieves wizard current step from session', function () {
    WizardPersistence::setCurrentStep(2);

    $step = WizardPersistence::getCurrentStep();

    expect($step)->toBe(2);
});

it('shows summary on last step when enabled', function () {
    $view = view('daisy::templates.form.form-wizard', [
        'steps' => [
            ['key' => 'step1', 'label' => 'Step 1'],
            ['key' => 'step2', 'label' => 'Step 2'],
        ],
        'currentStep' => 2,
        'showSummary' => true,
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('summary')
        ->toContain('card');
});

it('includes csrf keeper when autoRefreshCsrf is enabled', function () {
    $view = view('daisy::templates.form.form-wizard', [
        'steps' => [
            ['key' => 'step1', 'label' => 'Step 1'],
        ],
        'autoRefreshCsrf' => true,
        'method' => 'POST',
    ]);

    $html = $view->render();

    expect($html)->toContain('csrf-keeper');
});

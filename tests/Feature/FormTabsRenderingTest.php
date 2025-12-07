<?php

use Art35rennes\DaisyKit\Helpers\TabErrorBag;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

it('renders form with tabs by default and backend field for active tab', function () {
    View::share('errors', new MessageBag);

    $view = view('daisy::templates.form.form-with-tabs', [
        'tabs' => [
            ['id' => 'general', 'label' => 'Général'],
            ['id' => 'advanced', 'label' => 'Avancé'],
        ],
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('form-tabs')
        // Navigation
        ->toContain('Général')
        ->toContain('Avancé')
        ->toContain('data-module="tabs"')
        // Champ caché envoyé au backend pour savoir quel onglet est actif.
        ->toContain('name="_active_tab"');
});

it('restores active tab from old input', function () {
    View::share('errors', new MessageBag);
    request()->merge(['_active_tab' => 'advanced']);

    $view = view('daisy::templates.form.form-with-tabs', [
        'tabs' => [
            ['id' => 'general', 'label' => 'Général'],
            ['id' => 'advanced', 'label' => 'Avancé'],
        ],
    ]);

    $html = $view->render();

    expect($html)->toContain('data-tab-id="advanced"');
});

it('shows error badges on tabs with errors', function () {
    $errors = new MessageBag([
        'general_name' => ['The name field is required.'],
        'general_email' => ['The email field is required.'],
        'advanced_notes' => ['The notes field is required.'],
    ]);

    View::share('errors', $errors);

    $view = view('daisy::templates.form.form-with-tabs', [
        'tabs' => [
            ['id' => 'general', 'label' => 'Général'],
            ['id' => 'advanced', 'label' => 'Avancé'],
        ],
        'fieldToTabMap' => [
            'general_name' => 'general',
            'general_email' => 'general',
            'advanced_notes' => 'advanced',
        ],
        'showErrorBadges' => true,
        'highlightErrors' => true,
    ]);

    $html = $view->render();

    // Vérifier que les badges d'erreur sont présents
    expect($html)
        ->toContain('badge')
        ->toContain('error');
});

it('counts errors by tab using TabErrorBag helper', function () {
    $errors = new MessageBag([
        'general_name' => ['Required'],
        'general_email' => ['Required'],
        'advanced_notes' => ['Required'],
    ]);

    $fieldToTabMap = [
        'general_name' => 'general',
        'general_email' => 'general',
        'advanced_notes' => 'advanced',
    ];

    $counts = TabErrorBag::countErrorsByTab($fieldToTabMap, $errors);

    expect($counts)
        ->toHaveKey('general')
        ->toHaveKey('advanced')
        ->and($counts['general'])->toBe(2)
        ->and($counts['advanced'])->toBe(1);
});

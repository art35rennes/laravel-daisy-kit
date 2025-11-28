<?php

it('renders form inline with GET method without csrf', function () {
    $view = view('daisy::templates.form-inline', [
        'action' => '/search',
        'method' => 'GET',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('method="GET"')
        ->not->toContain('@csrf')
        ->toContain('data-module="inline"');
});

it('renders form inline with POST method with csrf', function () {
    $view = view('daisy::templates.form-inline', [
        'action' => '/search',
        'method' => 'POST',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('method="POST"')
        ->toContain('name="_token"')
        ->toContain('data-module="inline"');
});

it('displays active filter tokens', function () {
    $view = view('daisy::templates.form-inline', [
        'activeFilters' => [
            ['label' => 'Statut', 'value' => 'Actif', 'param' => 'status'],
            ['label' => 'Type', 'value' => 'Premium', 'param' => 'type'],
        ],
        'filters' => '<input type="text" name="search" />',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('Statut')
        ->toContain('Actif')
        ->toContain('Type')
        ->toContain('Premium')
        ->toContain('data-filter-clear');
});

it('shows advanced filters drawer when enabled', function () {
    $view = view('daisy::templates.form-inline', [
        'showAdvanced' => true,
        'advancedTitle' => 'Filtres avancés',
        'filters' => '<input type="text" name="search" />',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('drawer')
        ->toContain('Filtres avancés')
        ->toContain('form-inline-advanced-drawer');
});

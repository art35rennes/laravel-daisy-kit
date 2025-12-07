<?php

it('renders form inline with GET method without csrf', function () {
    $view = view('daisy::templates.form.form-inline', [
        'action' => '/search',
        'method' => 'GET',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('method="GET"')
        ->not->toContain('name="_token"')
        ->toContain('data-module="inline"');
});

it('renders form inline with POST method with csrf and csrf keeper', function () {
    $view = view('daisy::templates.form.form-inline', [
        'action' => '/search',
        'method' => 'POST',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('method="POST"')
        // Token CSRF pour que le backend reçoive bien les données.
        ->toContain('name="_token"')
        // Composant CSRF Keeper pour garder le formulaire valide après une longue inactivité.
        ->toContain('csrf-keeper')
        ->toContain('data-module="inline"');
});

it('displays active filter tokens with clear buttons for backend params', function () {
    $view = view('daisy::templates.form.form-inline', [
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
        // Chaque token expose le paramètre de filtre utilisé côté backend.
        ->toContain('data-filter-param="status"')
        ->toContain('data-filter-param="type"')
        ->toContain('data-filter-clear');
});

it('shows advanced filters drawer when enabled', function () {
    $view = view('daisy::templates.form.form-inline', [
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

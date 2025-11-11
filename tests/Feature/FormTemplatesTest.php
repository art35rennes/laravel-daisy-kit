<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

beforeEach(function () {
    View::share('errors', new MessageBag);
});

it('renders form-inline template with default props', function () {
    $html = View::make('daisy::form.inline', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
    ])->render();

    expect($html)
        ->toContain('form')
        ->toContain('method="GET"')
        ->toContain(__('form.search'));
});

it('renders form-inline template with POST method', function () {
    $html = View::make('daisy::form.inline', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'method' => 'POST',
    ])->render();

    expect($html)
        ->toContain('method="POST"')
        ->toContain('_token');
});

it('renders form-inline template with custom submit text', function () {
    $html = View::make('daisy::form.inline', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'submitText' => 'Filtrer',
    ])->render();

    expect($html)
        ->toContain('Filtrer');
});

it('renders form-with-tabs template with default props', function () {
    $html = View::make('daisy::form.with-tabs', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'tabs' => [
            ['id' => 'general', 'label' => 'Général'],
            ['id' => 'address', 'label' => 'Adresse'],
        ],
    ])->render();

    expect($html)
        ->toContain('form')
        ->toContain('method="POST"')
        ->toContain('Général')
        ->toContain('Adresse')
        ->toContain('_active_tab');
});

it('renders form-with-tabs template with active tab', function () {
    $html = View::make('daisy::form.with-tabs', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'tabs' => [
            ['id' => 'general', 'label' => 'Général'],
            ['id' => 'address', 'label' => 'Adresse'],
        ],
        'activeTab' => 'address',
    ])->render();

    expect($html)
        ->toContain('value="address"');
});

it('renders form-with-tabs template with tabs style', function () {
    $html = View::make('daisy::form.with-tabs', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'tabs' => [
            ['id' => 'general', 'label' => 'Général'],
        ],
        'tabsStyle' => 'border',
    ])->render();

    expect($html)
        ->toContain('tabs-border');
});

it('renders form-wizard template with default props', function () {
    $html = View::make('daisy::form.wizard', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'steps' => [
            ['label' => 'Étape 1', 'icon' => 'person'],
            ['label' => 'Étape 2', 'icon' => 'envelope'],
        ],
    ])->render();

    expect($html)
        ->toContain('form')
        ->toContain('method="POST"')
        ->toContain('Étape 1')
        ->toContain('Étape 2')
        ->toContain('wizard-step-input')
        ->toContain('data-stepper');
});

it('renders form-wizard template with current step', function () {
    $html = View::make('daisy::form.wizard', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'steps' => [
            ['label' => 'Étape 1'],
            ['label' => 'Étape 2'],
        ],
        'currentStep' => 2,
    ])->render();

    expect($html)
        ->toContain('value="2"');
});

it('renders form-wizard template with linear mode', function () {
    $html = View::make('daisy::form.wizard', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'steps' => [
            ['label' => 'Étape 1'],
        ],
        'linear' => true,
    ])->render();

    expect($html)
        ->toContain('data-linear="true"');
});

it('renders form-wizard template with custom texts', function () {
    $html = View::make('daisy::form.wizard', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'steps' => [
            ['label' => 'Étape 1'],
        ],
        'prevText' => 'Retour',
        'nextText' => 'Continuer',
        'finishText' => 'Valider',
    ])->render();

    expect($html)
        ->toContain('Retour')
        ->toContain('Continuer')
        ->toContain('Valider');
});

it('renders form-wizard template vertically', function () {
    $html = View::make('daisy::form.wizard', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        'steps' => [
            ['label' => 'Étape 1'],
            ['label' => 'Étape 2'],
        ],
        'vertical' => true,
    ])->render();

    expect($html)
        ->toContain('steps-vertical');
});

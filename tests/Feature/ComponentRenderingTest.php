<?php

use Illuminate\Support\Facades\View;

it('renders a button component', function () {
    $html = View::make('daisy::components.ui.inputs.button', [
        'slot' => 'Click me',
    ])->render();

    expect($html)
        ->toContain('btn')
        ->toContain('Click me');
});

it('renders a badge component', function () {
    $html = View::make('daisy::components.ui.data-display.badge', [
        'slot' => 'New',
    ])->render();

    expect($html)
        ->toContain('badge')
        ->toContain('New');
});

it('renders an alert component', function () {
    $html = View::make('daisy::components.ui.feedback.alert', [
        'slot' => 'Alert message',
    ])->render();

    expect($html)
        ->toContain('alert')
        ->toContain('Alert message');
});

it('renders an input component', function () {
    $html = View::make('daisy::components.ui.inputs.input', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['placeholder' => 'Type here']),
    ])->render();

    expect($html)
        ->toContain('input')
        ->toContain('Type here');
});

it('renders a divider component', function () {
    $html = View::make('daisy::components.ui.layout.divider', [
        'slot' => '',
    ])->render();

    expect($html)
        ->toContain('divider');
});

it('renders a link component', function () {
    $html = View::make('daisy::components.ui.advanced.link', [
        'slot' => 'Link text',
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['href' => '/test']),
    ])->render();

    expect($html)
        ->toContain('link')
        ->toContain('Link text')
        ->toContain('/test');
});


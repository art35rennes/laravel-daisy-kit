<?php

use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentAttributeBag;

it('documents copyable technical value usage through renderable props', function () {
    $html = View::make('daisy::components.ui.utilities.copyable', [
        'value' => '550e8400-e29b-41d4-a716-446655440000',
        'iconPosition' => 'inline',
        'successMessage' => 'UUID copié',
        'underline' => false,
        'attributes' => new ComponentAttributeBag(['class' => 'font-mono break-all']),
        'slot' => '550e8400-e29b-41d4-a716-446655440000',
    ])->render();

    expect($html)
        ->toContain('font-mono break-all')
        ->toContain('data-icon-position="inline"')
        ->toContain('data-success-message="UUID copié"')
        ->not->toContain('copyable-underline');
});

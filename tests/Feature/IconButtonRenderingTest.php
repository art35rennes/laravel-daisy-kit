<?php

use Illuminate\Support\Facades\View;

it('renders accessible icon-only buttons with tooltips', function () {
    $html = View::make('daisy::components.ui.inputs.icon-button', [
        'icon' => 'bi-trash',
        'label' => 'Supprimer',
        'tooltip' => 'Supprimer le document',
        'variant' => 'outline',
        'color' => 'error',
        'href' => '/documents/1',
    ])->render();

    expect($html)
        ->toContain('tooltip tooltip-top')
        ->toContain('data-tip="Supprimer le document"')
        ->toContain('aria-label="Supprimer"')
        ->toContain('title="Supprimer le document"')
        ->toContain('btn btn-circle btn-xs h-8 min-h-8 w-8 btn-outline btn-error');
});

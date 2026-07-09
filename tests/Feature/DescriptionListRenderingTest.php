<?php

use Illuminate\Support\Facades\View;

it('renders rich description list sections with links, copyable values, help and wide items', function () {
    $html = View::make('daisy::components.ui.data-display.description-list', [
        'sections' => [[
            'title' => 'Document',
            'icon' => 'bi-file-earmark',
            'items' => [
                ['label' => 'Nom', 'value' => 'Contrat.pdf', 'icon' => 'bi-file-text'],
                ['label' => 'Empreinte', 'value' => 'abc123', 'copyable' => true, 'help' => 'SHA-256'],
                ['label' => 'Source', 'value' => 'Voir', 'href' => '/documents/1', 'link' => true],
                ['label' => 'Note', 'value' => null, 'wide' => true],
            ],
        ]],
        'emptyLabel' => 'Non renseigné',
    ])->render();

    expect($html)
        ->toContain('Document')
        ->toContain('bi-file-earmark')
        ->toContain('data-copy-value="abc123"')
        ->toContain('tooltip-content')
        ->toContain('SHA-256')
        ->not->toContain('data-tip="SHA-256"')
        ->toContain('href="/documents/1"')
        ->toContain('md:col-span-full')
        ->toContain('Non renseigné');
});

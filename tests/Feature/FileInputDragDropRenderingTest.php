<?php

use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentAttributeBag;

it('renders configurable single file input drag and drop with preview', function () {
    $html = View::make('daisy::components.ui.inputs.file-input', [
        'dragdrop' => true,
        'preview' => true,
        'multiple' => false,
        'accept' => '.pdf,image/*',
        'dropzoneText' => 'Déposer le justificatif',
        'helpText' => 'PDF ou image, un seul fichier.',
        'browseText' => 'Choisir un fichier',
        'attributes' => new ComponentAttributeBag(['name' => 'document']),
    ])->render();

    expect($html)
        ->toContain('data-fileinput="1"')
        ->toContain('data-multiple="false"')
        ->toContain('accept=".pdf,image/*"')
        ->toContain('Déposer le justificatif')
        ->toContain('PDF ou image, un seul fichier.')
        ->toContain('Choisir un fichier')
        ->not->toContain(' multiple');
});

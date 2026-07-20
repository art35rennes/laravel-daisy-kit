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
        'previewContainerClass' => 'grid-cols-1 w-full',
        'previewItemClass' => 'min-h-96 shadow-md',
        'attributes' => new ComponentAttributeBag(['name' => 'document']),
    ])->render();

    expect($html)
        ->toContain('data-fileinput="1"')
        ->toContain('data-multiple="false"')
        ->toContain('accept=".pdf,image/*"')
        ->toContain('Déposer le justificatif')
        ->toContain('PDF ou image, un seul fichier.')
        ->toContain('Choisir un fichier')
        ->toContain('grid-cols-1 w-full grid gap-2')
        ->toContain('data-preview-item-class="min-h-96 shadow-md"')
        ->not->toContain(' multiple');
});

<?php

use Illuminate\Support\Facades\View;

it('renders file preview as a standalone stable preview action', function () {
    $html = View::make('daisy::components.ui.data-display.file-preview', [
        'url' => 'https://example.com/document.pdf',
        'name' => 'document.pdf',
        'type' => 'pdf',
        'previewMode' => 'modal',
        'layout' => 'action-only',
        'actionOrder' => ['preview'],
    ])->render();

    expect($html)
        ->toContain('data-file-preview-open-modal')
        ->toContain('btn btn-ghost')
        ->toContain('h-8 min-h-8 w-8')
        ->not->toContain('document.pdf</p>');
});

it('applies a custom file preview action button class', function () {
    $html = View::make('daisy::components.ui.data-display.file-preview', [
        'url' => 'https://example.com/image.jpg',
        'type' => 'image',
        'previewMode' => 'modal',
        'actionOnly' => true,
        'actionButtonClass' => 'btn-ghost btn-xs btn-circle h-8 min-h-8 w-8 text-info',
    ])->render();

    expect($html)
        ->toContain('text-info')
        ->toContain('aria-label="Preview"');
});

<?php

declare(strict_types=1);

test('the file preview component emits a CSP-safe configuration', function (): void {
    $html = view('daisy-kit::components.file-preview', [
        'src' => 'https://files.example.test/report.docx',
        'name' => '</script><img src=x>',
    ])->render();

    expect($html)
        ->toContain('data-daisy-kit-module="file-preview"')
        ->toContain('data-daisy-kit-file-preview-frame')
        ->toContain('data-daisy-kit-file-preview-metadata')
        ->toContain('data-daisy-kit-file-preview-layout')
        ->toContain('data-daisy-kit-file-preview-actions')
        ->toContain('rel="noopener"')
        ->toContain('sandbox="allow-scripts"')
        ->not->toContain('allow-same-origin')
        ->not->toContain('</script><img')
        ->not->toContain('onerror=')
        ->not->toContain('style=');
});

test('the file preview component includes loading empty and error markup', function (): void {
    $html = view('daisy-kit::components.file-preview')->render();

    expect($html)
        ->toContain('data-daisy-kit-loading')
        ->toContain('data-daisy-kit-empty')
        ->toContain('role="alert"');
});

test('the file preview component is available through the public blade namespace', function (): void {
    $this->blade('<x-daisy-kit::file-preview />')
        ->assertSee('data-daisy-kit-module="file-preview"', false);
});

<?php

declare(strict_types=1);

test('the file preview emits the restored CSP-safe product contract', function (): void {
    $html = view('daisy-kit::components.file-preview', [
        'url' => 'https://files.example.test/report.docx',
        'name' => '</script><img src=x>',
        'mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'previewMode' => 'modal',
        'layout' => 'card',
    ])->render();

    expect($html)
        ->toContain('data-daisy-kit-module="file-preview"')
        ->toContain('data-daisy-kit-file-preview-frame')
        ->toContain('data-daisy-kit-file-preview-modal-box')
        ->toContain('data-daisy-kit-file-preview-modal-content')
        ->toContain('data-daisy-kit-file-preview-modal-download')
        ->toContain('data-daisy-kit-file-preview-zoom="fit"')
        ->toContain('tabindex="0"')
        ->toContain('data-daisy-kit-file-preview-retry')
        ->toContain('class="skeleton')
        ->toContain('sandbox="allow-scripts"')
        ->toContain('"url":"https:\/\/files.example.test\/report.docx"')
        ->toContain('"previewMode":"modal"')
        ->not->toContain('"src":')
        ->not->toContain('allow-same-origin')
        ->not->toContain('</script><img')
        ->not->toContain('onerror=')
        ->not->toContain('style=');
});

test('the file preview renders useful unsupported and empty states', function (): void {
    $unsupported = view('daisy-kit::components.file-preview', [
        'url' => '/files/forecast.xlsx',
        'name' => 'forecast.xlsx',
        'extension' => 'xlsx',
    ])->render();
    $empty = view('daisy-kit::components.file-preview')->render();

    expect($unsupported)
        ->toContain('data-daisy-kit-file-preview-capability="unsupported"')
        ->toContain('forecast.xlsx')
        ->toContain('data-daisy-kit-file-preview-download')
        ->not->toContain('data-daisy-kit-file-preview-open-preview')
        ->and($empty)
        ->toContain('data-daisy-kit-empty')
        ->toContain('role="status"');
});

test('the file preview accepts private region slots without adding another public component', function (): void {
    $this->blade(<<<'BLADE'
        <x-daisy-kit::file-preview url="/files/report.pdf" type="pdf" preview-mode="modal">
            <x-slot:trigger><button type="button" data-custom-trigger>Inspect report</button></x-slot:trigger>
            <x-slot:metadata><p data-custom-metadata>Custom metadata</p></x-slot:metadata>
            <x-slot:actions><p data-custom-actions>Custom actions</p></x-slot:actions>
            <x-slot:notice><p data-custom-notice>Custom notice</p></x-slot:notice>
            <x-slot:modalFooter><p data-custom-footer>Custom footer</p></x-slot:modalFooter>
        </x-daisy-kit::file-preview>
        BLADE)
        ->assertSee('data-custom-trigger', false)
        ->assertSee('data-custom-metadata', false)
        ->assertSee('data-custom-actions', false)
        ->assertSee('data-custom-notice', false)
        ->assertSee('data-custom-footer', false);
});

test('the file preview translates its interface in French', function (): void {
    app()->setLocale('fr');

    $html = view('daisy-kit::components.file-preview', [
        'url' => '/files/report.docx',
        'name' => 'report.docx',
        'type' => 'docx',
    ])->render();

    expect($html)
        ->toContain('Prévisualiser')
        ->toContain('Ajuster')
        ->toContain('Télécharger')
        ->toContain('Réessayer');
});

test('the file preview de-duplicates action order and keeps audio compact', function (): void {
    $html = view('daisy-kit::components.file-preview', [
        'actionOrder' => ['preview', 'preview', 'download', 'download'],
        'mimeType' => 'audio/mpeg',
        'name' => 'brief.mp3',
        'type' => 'audio',
        'url' => '/files/brief.mp3',
    ])->render();

    expect(substr_count($html, 'data-daisy-kit-file-preview-open-preview'))
        ->toBe(1)
        ->and(substr_count($html, 'data-daisy-kit-file-preview-download'))
        ->toBe(2)
        ->and($html)
        ->toContain('data-daisy-kit-file-preview-type="audio"');
});

test('the file preview remains the only public preview Blade entry', function (): void {
    $this->blade('<x-daisy-kit::file-preview url="/files/report.txt" />')
        ->assertSee('data-daisy-kit-module="file-preview"', false);
});

test('the workbench uses genuine fixtures for every preview renderer', function (): void {
    $fixtures = dirname(__DIR__, 2).'/workbench/resources/fixtures/file-preview';
    $document = new ZipArchive;

    expect(file_get_contents("{$fixtures}/preview.txt"))
        ->toContain('genuine UTF-8 text fixture')
        ->and(file_get_contents("{$fixtures}/preview.svg"))
        ->toStartWith('<svg')
        ->and(file_get_contents("{$fixtures}/preview.wav", false, null, 0, 12))
        ->toStartWith('RIFF')
        ->toContain('WAVE')
        ->and(file_get_contents("{$fixtures}/preview.pdf", false, null, 0, 5))
        ->toBe('%PDF-')
        ->and(file_get_contents("{$fixtures}/preview.mp4", false, null, 4, 4))
        ->toBe('ftyp')
        ->and($document->open("{$fixtures}/preview.docx"))
        ->toBeTrue();

    $documentXml = $document->getFromName('word/document.xml');
    $document->close();

    expect($documentXml)
        ->toBeString()
        ->toContain('Product preview brief')
        ->toContain('Multipage verification')
        ->and(substr_count($documentXml, 'w:type="page"'))
        ->toBeGreaterThanOrEqual(2);
});

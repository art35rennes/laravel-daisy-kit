<?php

use Art35rennes\DaisyKit\Support\FilePreview;
use Illuminate\Http\UploadedFile;

it('detects previewable file types from extensions and mime types', function () {
    expect(FilePreview::type('https://example.com/photo.jpg'))->toBe('image')
        ->and(FilePreview::type(['url' => '/files/report.pdf']))->toBe('pdf')
        ->and(FilePreview::type(['name' => 'notes.txt']))->toBe('text')
        ->and(FilePreview::type(['mimeType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']))->toBe('docx')
        ->and(FilePreview::type(['extension' => 'xlsx']))->toBe('spreadsheet')
        ->and(FilePreview::type(['extension' => 'zip']))->toBe('archive');
});

it('exposes preview eligibility and renderer capabilities', function () {
    expect(FilePreview::isPreviewable(['name' => 'contract.docx']))->toBeTrue()
        ->and(FilePreview::isPreviewable(['name' => 'legacy.doc']))->toBeFalse();

    expect(FilePreview::capabilities(['url' => '/files/contract.docx']))->toMatchArray([
        'type' => 'docx',
        'isPreviewable' => true,
        'canDownload' => true,
        'renderer' => 'docx-preview',
        'reason' => null,
    ]);

    expect(FilePreview::capabilities(['url' => '/files/table.xlsx']))->toMatchArray([
        'type' => 'spreadsheet',
        'isPreviewable' => false,
        'canDownload' => true,
        'renderer' => null,
        'reason' => 'spreadsheet_not_previewable',
    ]);
});

it('accepts uploaded files and objects with common getters', function () {
    $upload = UploadedFile::fake()->create('document.pdf', 120, 'application/pdf');

    $object = new class
    {
        public function getUrl(): string
        {
            return '/files/pitch.pptx';
        }

        public function getMimeType(): string
        {
            return 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        }
    };

    expect(FilePreview::type($upload))->toBe('pdf')
        ->and(FilePreview::type($object))->toBe('presentation');
});

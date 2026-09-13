<?php

declare(strict_types=1);

use Art35rennes\DaisyKit\Support\FilePreview;
use Illuminate\Http\UploadedFile;

it('normalizes v4-shaped file metadata and detects preview capabilities', function (): void {
    $upload = UploadedFile::fake()->create(
        'editorial-brief.docx',
        120,
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );

    expect(FilePreview::metadata($upload))
        ->toMatchArray([
            'name' => 'editorial-brief.docx',
            'extension' => 'docx',
            'size' => 120 * 1024,
        ])
        ->and(FilePreview::type($upload))->toBe('docx')
        ->and(FilePreview::capabilities(['name' => 'recording.mp3']))->toMatchArray([
            'type' => 'audio',
            'isPreviewable' => true,
            'renderer' => 'native',
        ])
        ->and(FilePreview::capabilities(['name' => 'forecast.xlsx']))->toMatchArray([
            'type' => 'spreadsheet',
            'isPreviewable' => false,
            'reason' => 'spreadsheet_not_previewable',
        ]);
});

it('accepts relative and http sources while rejecting executable URL schemes', function (): void {
    expect(FilePreview::safeUrl('/files/report.pdf'))->toBe('/files/report.pdf')
        ->and(FilePreview::safeUrl('https://files.example.test/report.pdf'))->toBe('https://files.example.test/report.pdf')
        ->and(FilePreview::safeUrl('javascript:alert(1)'))->toBeNull()
        ->and(FilePreview::safeUrl('data:text/html,unsafe'))->toBeNull()
        ->and(FilePreview::safeUrl('blob:https://files.example.test/unsafe'))->toBeNull();
});

it('formats file sizes with stable localized-independent units', function (): void {
    expect(FilePreview::formatSize(0))->toBe('0 B')
        ->and(FilePreview::formatSize(1024))->toBe('1 KB')
        ->and(FilePreview::formatSize(1_572_864))->toBe('1.5 MB');
});

<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Support;

use Illuminate\Http\UploadedFile;
use SplFileInfo;
use Stringable;

final class FilePreview
{
    /** @var array<string, string> */
    private const EXTENSION_TYPES = [
        'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image', 'webp' => 'image',
        'svg' => 'image', 'bmp' => 'image', 'avif' => 'image',
        'mp4' => 'video', 'webm' => 'video', 'ogv' => 'video', 'mov' => 'video', 'm4v' => 'video',
        'mp3' => 'audio', 'wav' => 'audio', 'm4a' => 'audio', 'aac' => 'audio', 'flac' => 'audio', 'ogg' => 'audio',
        'pdf' => 'pdf',
        'txt' => 'text', 'md' => 'text', 'markdown' => 'text', 'csv' => 'text', 'json' => 'text',
        'xml' => 'text', 'log' => 'text',
        'docx' => 'docx',
        'doc' => 'document', 'rtf' => 'document', 'odt' => 'document',
        'xls' => 'spreadsheet', 'xlsx' => 'spreadsheet', 'ods' => 'spreadsheet',
        'ppt' => 'presentation', 'pptx' => 'presentation', 'odp' => 'presentation',
        'zip' => 'archive', 'rar' => 'archive', '7z' => 'archive', 'tar' => 'archive', 'gz' => 'archive',
    ];

    /** @var array<string, string> */
    private const MIME_TYPES = [
        'application/pdf' => 'pdf',
        'application/json' => 'text',
        'application/xml' => 'text',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/msword' => 'document',
        'application/rtf' => 'document',
        'application/vnd.oasis.opendocument.text' => 'document',
        'application/vnd.ms-excel' => 'spreadsheet',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'spreadsheet',
        'application/vnd.oasis.opendocument.spreadsheet' => 'spreadsheet',
        'application/vnd.ms-powerpoint' => 'presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'presentation',
        'application/vnd.oasis.opendocument.presentation' => 'presentation',
        'application/zip' => 'archive',
        'application/x-zip-compressed' => 'archive',
        'application/x-rar-compressed' => 'archive',
        'application/x-7z-compressed' => 'archive',
        'application/gzip' => 'archive',
    ];

    /** @var list<string> */
    private const PREVIEWABLE_TYPES = ['image', 'video', 'audio', 'pdf', 'text', 'docx'];

    public static function type(mixed $file): string
    {
        $metadata = self::metadata($file);
        $declaredType = $metadata['type'] ?? null;

        if (is_string($declaredType) && $declaredType !== '') {
            return self::normalizeType($declaredType);
        }

        $mimeType = strtolower((string) ($metadata['mimeType'] ?? ''));

        foreach (['image', 'video', 'audio', 'text'] as $family) {
            if (str_starts_with($mimeType, $family.'/')) {
                return $family;
            }
        }

        if (isset(self::MIME_TYPES[$mimeType])) {
            return self::MIME_TYPES[$mimeType];
        }

        $extension = strtolower(ltrim((string) ($metadata['extension'] ?? ''), '.'));

        return self::EXTENSION_TYPES[$extension] ?? 'other';
    }

    public static function isPreviewable(mixed $file): bool
    {
        return in_array(self::type($file), self::PREVIEWABLE_TYPES, true);
    }

    /**
     * @return array{type: string, isPreviewable: bool, canDownload: bool, renderer: string|null, reason: string|null}
     */
    public static function capabilities(mixed $file): array
    {
        $metadata = self::metadata($file);
        $type = self::type($metadata);
        $isPreviewable = in_array($type, self::PREVIEWABLE_TYPES, true);

        return [
            'type' => $type,
            'isPreviewable' => $isPreviewable,
            'canDownload' => self::safeUrl($metadata['downloadUrl'] ?? $metadata['url'] ?? null) !== null,
            'renderer' => match ($type) {
                'image', 'video', 'audio' => 'native',
                'pdf' => 'browser-pdf',
                'text' => 'text',
                'docx' => 'docx-preview',
                default => null,
            },
            'reason' => $isPreviewable ? null : match ($type) {
                'document' => 'document_not_previewable',
                'spreadsheet' => 'spreadsheet_not_previewable',
                'presentation' => 'presentation_not_previewable',
                'archive' => 'archive_not_previewable',
                default => 'unsupported_type',
            },
        ];
    }

    /** @return array<string, mixed> */
    public static function metadata(mixed $file): array
    {
        if (is_array($file)) {
            return self::normalizeMetadata($file);
        }

        if ($file instanceof UploadedFile) {
            return self::normalizeMetadata([
                'name' => $file->getClientOriginalName(),
                'mimeType' => $file->getClientMimeType() ?: $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension() ?: $file->getExtension(),
                'size' => $file->getSize(),
                'path' => $file->getPathname(),
            ]);
        }

        if ($file instanceof SplFileInfo) {
            return self::normalizeMetadata([
                'name' => $file->getFilename(),
                'extension' => $file->getExtension(),
                'size' => $file->isFile() ? $file->getSize() : null,
                'path' => $file->getPathname(),
            ]);
        }

        if (is_string($file) || $file instanceof Stringable) {
            $url = trim((string) $file);

            return self::normalizeMetadata([
                'url' => $url,
                'name' => basename(parse_url($url, PHP_URL_PATH) ?: $url),
            ]);
        }

        return is_object($file) ? self::metadataFromObject($file) : [];
    }

    public static function safeUrl(mixed $url): ?string
    {
        if (! is_string($url) && ! $url instanceof Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url) === 1) {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return filter_var($url, FILTER_VALIDATE_URL) !== false ? $url : null;
    }

    public static function formatSize(mixed $bytes): ?string
    {
        if (! is_numeric($bytes) || (float) $bytes < 0) {
            return null;
        }

        $size = (float) $bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        $precision = $unit === 0 || floor($size) === $size ? 0 : 1;

        return number_format($size, $precision, '.', '').' '.$units[$unit];
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private static function normalizeMetadata(array $metadata): array
    {
        $normalized = [];

        foreach ($metadata as $key => $value) {
            $normalized[self::normalizeKey((string) $key)] = $value;
        }

        if (empty($normalized['extension'])) {
            $source = $normalized['name'] ?? $normalized['url'] ?? $normalized['path'] ?? null;

            if (is_string($source) || $source instanceof Stringable) {
                $path = parse_url((string) $source, PHP_URL_PATH) ?: (string) $source;
                $normalized['extension'] = pathinfo($path, PATHINFO_EXTENSION) ?: null;
            }
        }

        return $normalized;
    }

    /** @return array<string, mixed> */
    private static function metadataFromObject(object $file): array
    {
        $metadata = [];
        $mapping = [
            'url' => ['getUrl', 'url'],
            'previewUrl' => ['getPreviewUrl', 'previewUrl'],
            'downloadUrl' => ['getDownloadUrl', 'downloadUrl'],
            'name' => ['getClientOriginalName', 'getName', 'name', 'getFilename', 'filename'],
            'type' => ['type'],
            'mimeType' => ['getClientMimeType', 'getMimeType', 'mimeType', 'mime_type'],
            'extension' => ['getClientOriginalExtension', 'getExtension', 'extension'],
            'size' => ['getSize', 'size'],
            'path' => ['getPath', 'path', 'getPathname', 'pathname'],
        ];

        foreach ($mapping as $targetKey => $candidates) {
            foreach ($candidates as $candidate) {
                if (method_exists($file, $candidate)) {
                    $metadata[$targetKey] = $file->{$candidate}();
                    break;
                }

                if (property_exists($file, $candidate) && isset($file->{$candidate})) {
                    $metadata[$targetKey] = $file->{$candidate};
                    break;
                }
            }
        }

        return self::normalizeMetadata($metadata);
    }

    private static function normalizeKey(string $key): string
    {
        return match ($key) {
            'mime_type', 'mime', 'content_type', 'contentType' => 'mimeType',
            'file_size', 'fileSize' => 'size',
            'preview_url', 'preview' => 'previewUrl',
            'download_url', 'download' => 'downloadUrl',
            default => $key,
        };
    }

    private static function normalizeType(string $type): string
    {
        $type = strtolower(trim($type));

        return match ($type) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif' => 'image',
            'mp4', 'webm', 'ogv', 'mov', 'm4v' => 'video',
            'mp3', 'wav', 'm4a', 'aac', 'flac', 'ogg' => 'audio',
            'txt', 'md', 'markdown', 'csv', 'json', 'xml', 'log' => 'text',
            'word' => 'docx',
            'office', 'doc', 'rtf', 'odt' => 'document',
            'excel', 'xls', 'xlsx', 'ods' => 'spreadsheet',
            'powerpoint', 'ppt', 'pptx', 'odp' => 'presentation',
            'zip', 'rar', '7z', 'tar', 'gz' => 'archive',
            default => in_array($type, [
                'image', 'video', 'audio', 'pdf', 'text', 'docx', 'document', 'spreadsheet',
                'presentation', 'archive', 'other',
            ], true) ? $type : 'other',
        };
    }
}

<?php

namespace Art35rennes\DaisyKit\Support;

class DaisyTableUrlPolicy
{
    public const DefaultAllowedSchemes = ['http', 'https', 'mailto', 'tel'];

    public const BlockedSchemes = ['javascript', 'data', 'vbscript'];

    public static function isSafeHref(mixed $href, array $tablePolicy = [], array $cellPolicy = []): bool
    {
        $href = trim((string) $href);

        if ($href === '') {
            return false;
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $href) === 1) {
            return false;
        }

        if (str_starts_with($href, '/') || str_starts_with($href, '#') || str_starts_with($href, '?')) {
            return true;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        if (! is_string($scheme) || $scheme === '') {
            return true;
        }

        $scheme = strtolower($scheme);

        if (in_array($scheme, self::BlockedSchemes, true)) {
            return false;
        }

        return in_array($scheme, self::allowedSchemes($tablePolicy, $cellPolicy), true);
    }

    public static function allowedSchemes(array $tablePolicy = [], array $cellPolicy = []): array
    {
        return array_values(array_unique(array_merge(
            self::DefaultAllowedSchemes,
            self::normalizeSchemes($tablePolicy['allowedSchemes'] ?? []),
            self::normalizeSchemes($cellPolicy['allowedSchemes'] ?? []),
        )));
    }

    protected static function normalizeSchemes(mixed $schemes): array
    {
        if (! is_array($schemes)) {
            return [];
        }

        return collect($schemes)
            ->map(fn ($scheme) => strtolower(rtrim(trim((string) $scheme), ':')))
            ->filter(fn (string $scheme) => preg_match('/^[a-z][a-z0-9+.-]*$/', $scheme) === 1)
            ->reject(fn (string $scheme) => in_array($scheme, self::BlockedSchemes, true))
            ->values()
            ->all();
    }
}

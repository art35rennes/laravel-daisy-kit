<?php

namespace Art35rennes\DaisyKit\Support;

use Illuminate\Support\HtmlString;

class PackageAsset
{
    public static function buildDirectory(): string
    {
        return trim((string) config('daisy-kit.vite_build_directory', 'vendor/art35rennes/laravel-daisy-kit'), '/');
    }

    public static function manifestPath(): string
    {
        return public_path(self::buildDirectory().'/manifest.json');
    }

    public static function hasManifest(): bool
    {
        return is_file(self::manifestPath());
    }

    public static function hasPublishedSource(string $type): bool
    {
        return is_file(resource_path("vendor/daisy-kit/{$type}/app.{$type}"));
    }

    public static function sourceEntry(string $type): string
    {
        if (self::hasPublishedSource($type)) {
            return "resources/vendor/daisy-kit/{$type}/app.{$type}";
        }

        return "resources/{$type}/app.{$type}";
    }

    public static function stylesheetTags(string $entry): HtmlString
    {
        if (self::hasManifest()) {
            $manifest = self::manifest();
            $chunk = $manifest[$entry] ?? null;

            if (is_array($chunk)) {
                $paths = [];

                if (($chunk['file'] ?? null) && str_ends_with((string) $chunk['file'], '.css')) {
                    $paths[] = $chunk['file'];
                }

                foreach (($chunk['css'] ?? []) as $cssFile) {
                    $paths[] = $cssFile;
                }

                $paths = array_values(array_unique(array_filter($paths)));

                if ($paths !== []) {
                    return new HtmlString(collect($paths)
                        ->map(fn (string $path) => '<link rel="stylesheet" href="'.e(asset(self::buildDirectory().'/'.$path)).'">')
                        ->implode("\n"));
                }
            }
        }

        $bundle = (string) config('daisy-kit.bundle.css', '');

        if ($bundle === '') {
            return new HtmlString('');
        }

        return new HtmlString('<link rel="stylesheet" href="'.e(asset($bundle)).'">');
    }

    public static function scriptTags(string $entry): HtmlString
    {
        if (self::hasManifest()) {
            $manifest = self::manifest();
            $chunk = $manifest[$entry] ?? null;

            if (is_array($chunk) && ($chunk['file'] ?? null)) {
                return new HtmlString(
                    '<script type="module" src="'.e(asset(self::buildDirectory().'/'.$chunk['file'])).'"></script>'
                );
            }
        }

        $bundle = (string) config('daisy-kit.bundle.js', '');

        if ($bundle === '') {
            return new HtmlString('');
        }

        return new HtmlString('<script src="'.e(asset($bundle)).'" defer></script>');
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected static function manifest(): array
    {
        $manifest = json_decode((string) file_get_contents(self::manifestPath()), true);

        return is_array($manifest) ? $manifest : [];
    }
}

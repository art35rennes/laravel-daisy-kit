<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class ComponentScanner extends AbstractScanner
{
    private const string CACHE_FILENAME = 'daisy-components.php';

    /**
     * @return array{components: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function readCached(): array
    {
        $cachePath = self::cachePath();
        $cached = self::readPhpCacheFile($cachePath);

        if (! isset($cached['files_hash']) || ! is_string($cached['files_hash'])) {
            throw new RuntimeException("Invalid components cache (missing files_hash): {$cachePath}");
        }

        if (! self::isCacheValid()) {
            throw new RuntimeException('Components cache is missing or stale. Run: php artisan inventory:cache:rebuild --components');
        }

        return $cached;
    }

    /**
     * @return array{components: array<int, array<string, mixed>>, generated_at: string, files_hash: string, dataAttributesMap?: array<string, array<int, string>>, jsDeps?: array<string, string>}
     */
    public static function rebuildCache(): array
    {
        $files = self::componentFiles();
        $filesHash = self::computeFilesHash($files);
        $cachePath = self::cachePath();

        if (File::exists($cachePath)) {
            try {
                $cached = self::readPhpCacheFile($cachePath);
                if (($cached['files_hash'] ?? null) === $filesHash) {
                    return $cached;
                }
            } catch (\Throwable) {
                // If cache is corrupted, rebuild it.
            }
        }

        $inventory = self::scanFiles($files, $filesHash);
        self::writePhpCacheFile($cachePath, $inventory);

        return $inventory;
    }

    public static function clearCache(): void
    {
        $cachePath = self::cachePath();

        if (File::exists($cachePath)) {
            File::delete($cachePath);
        }
    }

    public static function isCacheValid(): bool
    {
        $cachePath = self::cachePath();
        if (! File::exists($cachePath)) {
            return false;
        }

        try {
            $cached = self::readPhpCacheFile($cachePath);
            $cachedHash = $cached['files_hash'] ?? null;
            if (! is_string($cachedHash) || $cachedHash === '') {
                return false;
            }

            $files = self::componentFiles();
            $currentHash = self::computeFilesHash($files);

            return hash_equals($cachedHash, $currentHash);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function cachePath(): string
    {
        return base_path('bootstrap/cache/'.self::CACHE_FILENAME);
    }

    /**
     * @return array<int, string>
     */
    private static function componentFiles(): array
    {
        $componentsPath = resource_path('views/components/ui');
        if (! File::isDirectory($componentsPath)) {
            return [];
        }

        $files = File::allFiles($componentsPath);
        $paths = [];

        foreach ($files as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $normalized = str_replace('\\', '/', $file->getPathname());
            if (str_contains($normalized, '/partials/')) {
                continue;
            }

            $paths[] = $file->getPathname();
        }

        sort($paths);

        return $paths;
    }

    /**
     * @param  array<int, string>  $paths
     * @return array{components: array<int, array<string, mixed>>, generated_at: string, files_hash: string, dataAttributesMap: array<string, array<int, string>>, jsDeps: array<string, string>}
     */
    private static function scanFiles(array $paths, string $filesHash): array
    {
        $components = [];
        $dataAttributesMap = [];
        $jsDeps = [];

        $componentsPath = rtrim(str_replace('\\', '/', resource_path('views/components/ui')), '/').'/';

        foreach ($paths as $path) {
            $fullPath = str_replace('\\', '/', $path);
            $relativePath = Str::startsWith($fullPath, $componentsPath) ? substr($fullPath, strlen($componentsPath)) : $fullPath;
            $pathParts = explode('/', $relativePath);

            $category = $pathParts[0] ?? 'misc';
            $name = basename($path, '.blade.php');

            $content = File::get($path);
            $jsModule = self::extractJsModule($content);

            preg_match_all('/data-([a-z-]+)=["\']([^"\']*)["\']/', $content, $matches);
            $dataAttrs = [];
            if (! empty($matches[1])) {
                foreach ($matches[1] as $index => $attr) {
                    $dataAttrs[$attr] = $matches[2][$index] ?? '';
                }
            }

            preg_match_all('/@props\(\[(.*?)\]\)/s', $content, $propsMatches);
            $props = [];
            if (! empty($propsMatches[1])) {
                $propsContent = $propsMatches[1][0];
                preg_match_all("/(['\"]?)([a-zA-Z_][a-zA-Z0-9_]*)\\1\\s*=>/", $propsContent, $propMatches);
                if (! empty($propMatches[2])) {
                    $props = array_unique($propMatches[2]);
                }
            }

            $tags = self::generateTags($category, $jsModule);

            $viewPath = "daisy::components.ui.{$category}.{$name}";

            $components[] = [
                'name' => $name,
                'view' => $viewPath,
                'category' => $category,
                'tags' => $tags,
                'jsModule' => $jsModule,
                'status' => 'active',
                'props' => array_values($props),
                'dataAttributes' => array_keys($dataAttrs),
            ];

            foreach (array_keys($dataAttrs) as $attr) {
                $dataAttributesMap[$attr] ??= [];
                $dataAttributesMap[$attr][] = $name;
            }

            if ($jsModule !== null) {
                $jsDeps[$name] = $jsModule;
            }
        }

        return [
            'generated_at' => now()->toIso8601String(),
            'files_hash' => $filesHash,
            'components' => $components,
            'dataAttributesMap' => $dataAttributesMap,
            'jsDeps' => $jsDeps,
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function generateTags(string $category, ?string $jsModule): array
    {
        $tags = [$category];

        if ($jsModule !== null) {
            $tags[] = 'js';
            $tags[] = 'async';
        }

        if ($category === 'inputs') {
            $tags[] = 'form';
        }

        if ($category === 'overlay') {
            $tags[] = 'overlay';
            $tags[] = 'a11y';
        }

        if (in_array($category, ['advanced', 'data-display', 'communication'], true)) {
            $tags[] = 'lazy';
        }

        return array_values(array_unique($tags));
    }

    private static function extractJsModule(string $content): ?string
    {
        // 1) Literal HTML attribute: data-module="stepper"
        if (preg_match('/\bdata-module\s*=\s*["\']([^"\']+)["\']/', $content, $matches) === 1) {
            $value = trim($matches[1]);

            return $value !== '' && ! str_contains($value, '{{') ? $value : null;
        }

        // 2) Blade default: data-module="{{ $module ?? 'treeview' }}"
        if (preg_match('/\bdata-module\b[\s\S]{0,200}\?\?\s*[\'"]([^\'"]+)[\'"]/', $content, $matches) === 1) {
            $value = trim($matches[1]);

            return $value !== '' ? $value : null;
        }

        // 3) Merged attributes array: 'data-module' => ($module ?? 'popover')
        if (preg_match('/[\'"]data-module[\'"]\s*=>\s*\(?\s*\$module\s*\?\?\s*[\'"]([^\'"]+)[\'"]/', $content, $matches) === 1) {
            $value = trim($matches[1]);

            return $value !== '' ? $value : null;
        }

        return null;
    }
}

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use RuntimeException;

class TemplateScanner extends AbstractScanner
{
    private const string CACHE_FILENAME = 'daisy-templates.php';

    /**
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function readCached(): array
    {
        $cachePath = self::cachePath();
        $cached = self::readPhpCacheFile($cachePath);

        if (! isset($cached['files_hash']) || ! is_string($cached['files_hash'])) {
            throw new RuntimeException("Invalid templates cache (missing files_hash): {$cachePath}");
        }

        if (! self::isCacheValid()) {
            throw new RuntimeException('Templates cache is missing or stale. Run: php artisan inventory:cache:rebuild --templates');
        }

        return $cached;
    }

    /**
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    public static function rebuildCache(): array
    {
        $files = self::templateFiles();
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

            $files = self::templateFiles();
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
    private static function templateFiles(): array
    {
        $templatesPath = resource_path('views/templates');
        if (! File::isDirectory($templatesPath)) {
            return [];
        }

        $files = File::allFiles($templatesPath);
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
     * @return array{templates: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>, generated_at: string, files_hash: string}
     */
    private static function scanFiles(array $paths, string $filesHash): array
    {
        if (file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');
        }

        $templatesPath = rtrim(str_replace('\\', '/', resource_path('views/templates')), '/');

        $templates = [];
        $categories = [];

        foreach ($paths as $path) {
            $relativePath = str_replace([$templatesPath, '\\'], ['', '/'], $path);
            $relativePath = ltrim($relativePath, '/');

            $relativeWithoutExtension = str_replace('.blade.php', '', $relativePath);
            $segments = explode('/', $relativeWithoutExtension);
            $category = $segments[0] ?? 'misc';
            $name = $segments[count($segments) - 1] ?? $relativeWithoutExtension;
            $viewPath = 'daisy::templates.'.str_replace('/', '.', $relativeWithoutExtension);

            $content = File::get($path);
            $annotations = self::extractAnnotations($content);

            $type = self::resolveType($category, $annotations['type'] ?? null);
            $routeName = self::resolveRoute($category, $name, $annotations['route'] ?? null);

            $templateData = [
                'name' => $name,
                'category' => $category,
                'label' => $annotations['label'] ?? self::labelize($name),
                'description' => $annotations['description'] ?? 'Template '.self::labelize($name).'.',
                'view' => $viewPath,
                'route' => $routeName,
                'type' => $type,
                'tags' => self::resolveTags($annotations['tags'] ?? null, $category),
            ];

            if ($type === 'reusable') {
                $templateData['component'] = $viewPath;
            }

            $templates[] = $templateData;

            if (! isset($categories[$category])) {
                $categories[$category] = [
                    'id' => $category,
                    'label' => self::labelize($category),
                    'icon' => null,
                ];
            }
        }

        return [
            'generated_at' => now()->toIso8601String(),
            'files_hash' => $filesHash,
            'templates' => $templates,
            'categories' => array_values($categories),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function extractAnnotations(string $content): array
    {
        $annotations = [];

        if (preg_match('/@template-label\s+(.*)/i', $content, $matches)) {
            $annotations['label'] = self::sanitizeAnnotationValue($matches[1]);
        }

        if (preg_match('/@template-description\s+(.*)/i', $content, $matches)) {
            $annotations['description'] = self::sanitizeAnnotationValue($matches[1]);
        }

        if (preg_match('/@template-tags\s+(.*)/i', $content, $matches)) {
            $value = self::sanitizeAnnotationValue($matches[1] ?? '');
            $tags = array_filter(array_map('trim', preg_split('/[,;]+/', $value)));
            if (! empty($tags)) {
                $annotations['tags'] = $tags;
            }
        }

        if (preg_match('/@template-type\s+(reusable|example)/i', $content, $matches)) {
            $annotations['type'] = strtolower(self::sanitizeAnnotationValue($matches[1]));
        }

        if (preg_match('/@template-route\s+(.*)/i', $content, $matches)) {
            $annotations['route'] = self::sanitizeAnnotationValue($matches[1]);
        }

        return $annotations;
    }

    private static function sanitizeAnnotationValue(string $value): string
    {
        $value = trim($value);

        // Support annotations written inside Blade / HTML comments.
        $value = preg_replace('/\s*(--}}|-->)+\s*$/', '', $value) ?? $value;

        return trim($value);
    }

    private static function resolveType(string $category, ?string $annotationType): string
    {
        if (in_array($annotationType, ['reusable', 'example'], true)) {
            return $annotationType;
        }

        return in_array($category, ['auth', 'errors'], true) ? 'reusable' : 'example';
    }

    private static function resolveRoute(string $category, string $name, ?string $annotationRoute): ?string
    {
        if ($annotationRoute !== null && $annotationRoute !== '') {
            return self::routeExists($annotationRoute) ? $annotationRoute : null;
        }

        $guessedRoute = "templates.{$category}.{$name}";

        return self::routeExists($guessedRoute) ? $guessedRoute : null;
    }

    private static function routeExists(string $routeName): bool
    {
        try {
            return Route::has($routeName);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<int, string>
     */
    private static function resolveTags(mixed $annotationTags, string $category): array
    {
        if (is_array($annotationTags)) {
            $tags = $annotationTags;
        } elseif (is_string($annotationTags)) {
            $tags = preg_split('/[,;]+/', $annotationTags) ?: [];
        } else {
            $tags = [];
        }

        $tags[] = $category;

        return array_values(array_unique(array_filter(array_map('trim', $tags))));
    }

    private static function labelize(string $slug): string
    {
        $slug = str_replace(['-', '_'], ' ', $slug);
        $slug = preg_replace('/\s+/', ' ', $slug ?? '') ?? '';

        return mb_convert_case(trim($slug), MB_CASE_TITLE, 'UTF-8');
    }
}

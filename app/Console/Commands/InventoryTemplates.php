<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class InventoryTemplates extends Command
{
    protected $signature = 'inventory:templates';

    protected $description = 'Génère l\'inventaire complet des templates avec catégories et routes';

    public function handle(): int
    {
        $this->info('Génération de l\'inventaire des templates...');

        if (file_exists(base_path('routes/web.php'))) {
            require base_path('routes/web.php');
        }

        $templatesPath = resource_path('views/templates');

        if (! File::isDirectory($templatesPath)) {
            $this->error('Aucun dossier templates trouvé.');

            return Command::FAILURE;
        }

        $files = File::allFiles($templatesPath);

        if (empty($files)) {
            $this->error('Aucun template trouvé dans templates/');

            return Command::FAILURE;
        }

        $templates = [];
        $categories = [];

        foreach ($files as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $relativePath = str_replace([$templatesPath, '\\'], ['', '/'], $file->getPathname());
            $relativePath = ltrim($relativePath, '/');

            if (str_contains($relativePath, '/partials/')) {
                continue;
            }

            $relativeWithoutExtension = str_replace('.blade.php', '', $relativePath);
            $segments = explode('/', $relativeWithoutExtension);
            $category = $segments[0] ?? 'misc';
            $name = $segments[count($segments) - 1] ?? $relativeWithoutExtension;
            $viewPath = 'daisy::templates.'.str_replace('/', '.', $relativeWithoutExtension);

            $content = File::get($file->getPathname());
            $annotations = $this->extractAnnotations($content);

            $type = $this->resolveType($category, $annotations['type'] ?? null);
            $routeName = $this->resolveRoute($category, $name, $annotations['route'] ?? null);

            $templateData = [
                'name' => $name,
                'category' => $category,
                'label' => $annotations['label'] ?? $this->labelize($name),
                'description' => $annotations['description'] ?? 'Template '.$this->labelize($name).'.',
                'view' => $viewPath,
                'route' => $routeName,
                'type' => $type,
                'tags' => $this->resolveTags($annotations['tags'] ?? null, $category),
            ];

            if ($type === 'reusable') {
                $templateData['component'] = $viewPath;
            }

            $templates[] = $templateData;

            if (! isset($categories[$category])) {
                $categories[$category] = [
                    'id' => $category,
                    'label' => $this->labelize($category),
                    'icon' => null,
                ];
            }
        }

        $devDataPath = resource_path('dev/data');
        File::ensureDirectoryExists($devDataPath);

        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'templates' => $templates,
            'categories' => array_values($categories),
        ];

        File::put(
            $devDataPath.'/templates.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $this->info('✓ '.count($templates).' templates inventoriés');
        $this->info('✓ Manifeste généré: resources/dev/data/templates.json');

        return Command::SUCCESS;
    }

    private function extractAnnotations(string $content): array
    {
        $annotations = [];

        if (preg_match('/@template-label\s+(.*)/i', $content, $matches)) {
            $annotations['label'] = trim($matches[1]);
        }

        if (preg_match('/@template-description\s+(.*)/i', $content, $matches)) {
            $annotations['description'] = trim($matches[1]);
        }

        if (preg_match('/@template-tags\s+(.*)/i', $content, $matches)) {
            $tags = array_filter(array_map('trim', preg_split('/[,;]+/', $matches[1] ?? '')));
            if (! empty($tags)) {
                $annotations['tags'] = $tags;
            }
        }

        if (preg_match('/@template-type\s+(reusable|example)/i', $content, $matches)) {
            $annotations['type'] = strtolower(trim($matches[1]));
        }

        if (preg_match('/@template-route\s+(.*)/i', $content, $matches)) {
            $annotations['route'] = trim($matches[1]);
        }

        return $annotations;
    }

    private function resolveType(string $category, ?string $annotationType): string
    {
        if (in_array($annotationType, ['reusable', 'example'], true)) {
            return $annotationType;
        }

        return in_array($category, ['auth', 'errors'], true) ? 'reusable' : 'example';
    }

    private function resolveRoute(string $category, string $name, ?string $annotationRoute): ?string
    {
        if ($annotationRoute !== null && $annotationRoute !== '') {
            return $this->routeExists($annotationRoute) ? $annotationRoute : null;
        }

        $guessedRoute = "templates.{$category}.{$name}";

        return $this->routeExists($guessedRoute) ? $guessedRoute : null;
    }

    private function routeExists(string $routeName): bool
    {
        try {
            return Route::has($routeName);
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function resolveTags($annotationTags, string $category): array
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

    private function labelize(string $slug): string
    {
        $slug = str_replace(['-', '_'], ' ', $slug);
        $slug = preg_replace('/\s+/', ' ', $slug ?? '') ?? '';

        return mb_convert_case(trim($slug), MB_CASE_TITLE, 'UTF-8');
    }
}

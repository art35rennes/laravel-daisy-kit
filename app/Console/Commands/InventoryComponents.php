<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InventoryComponents extends Command
{
    protected $signature = 'inventory:components';

    protected $description = 'Génère l\'inventaire complet des composants avec classification, dépendances JS et data-attributes';

    private array $categories = [
        'inputs' => ['button', 'input', 'textarea', 'select', 'checkbox', 'radio', 'range', 'toggle', 'file-input', 'color-picker'],
        'navigation' => ['breadcrumbs', 'menu', 'pagination', 'navbar', 'sidebar', 'tabs', 'steps', 'stepper'],
        'layout' => ['card', 'hero', 'footer', 'divider', 'list', 'list-row', 'stack'],
        'data-display' => ['badge', 'avatar', 'kbd', 'table', 'stat', 'progress', 'radial-progress', 'status', 'timeline'],
        'overlay' => ['modal', 'drawer', 'dropdown', 'popover', 'popconfirm', 'tooltip'],
        'media' => ['carousel', 'lightbox', 'media-gallery', 'embed', 'leaflet'],
        'feedback' => ['alert', 'toast', 'loading', 'skeleton', 'callout'],
        'utilities' => ['mockup-browser', 'mockup-code', 'mockup-phone', 'mockup-window', 'indicator', 'dock'],
        'advanced' => ['calendar', 'calendar-full', 'calendar-cally', 'calendar-native', 'chart', 'code-editor', 'filter', 'onboarding', 'scroll-status', 'scrollspy', 'transfer', 'tree-view', 'validator', 'login-button', 'wysiwyg'],
    ];

    private array $jsModules = [
        'calendar-full' => 'calendar-full',
        'chart' => 'chart',
        'code-editor' => 'code-editor',
        'color-picker' => 'color-picker',
        'file-input' => 'file-input',
        'input-mask' => 'input-mask',
        'lazy-editors' => 'lazy-editors',
        'leaflet' => 'leaflet',
        'lightbox' => 'lightbox',
        'media-gallery' => 'media-gallery',
        'onboarding' => 'onboarding',
        'popconfirm' => 'popconfirm',
        'popover' => 'popover',
        'scroll-status' => 'scroll-status',
        'scrollspy' => 'scrollspy',
        'stepper' => 'stepper',
        'table' => 'table',
        'transfer' => 'transfer',
        'tree-view' => 'treeview',
    ];

    public function handle(): int
    {
        $this->info('Génération de l\'inventaire des composants...');

        $componentsPath = realpath(resource_path('views/components/ui')) ?: resource_path('views/components/ui');
        $components = [];
        $dataAttributes = [];
        $jsDeps = [];

        // Scanner tous les composants (récursif)
        if (! File::isDirectory($componentsPath)) {
            $this->error("Le dossier n'existe pas: {$componentsPath}");

            return Command::FAILURE;
        }

        // Utiliser glob récursif pour Windows
        $pattern = str_replace('\\', '/', $componentsPath).'/**/*.blade.php';
        $files = glob($pattern, GLOB_BRACE);

        if (empty($files)) {
            // Essayer avec backslashes pour Windows
            $pattern = $componentsPath.'\**\*.blade.php';
            $files = glob($pattern, GLOB_BRACE);
        }

        $files = array_map(fn ($path) => new \SplFileInfo($path), $files ?? []);

        foreach ($files as $file) {
            // Ignorer les partials
            if (str_contains($file->getPathname(), '/partials/')) {
                continue;
            }

            $name = basename($file->getPathname(), '.blade.php');

            // Extraire le chemin relatif pour déterminer la catégorie depuis le chemin
            // Calcul fiable du chemin relatif par rapport à ui/
            $fullPath = str_replace('\\', '/', $file->getPathname());
            $basePath = rtrim(str_replace('\\', '/', $componentsPath), '/').'/';
            $relativePath = Str::startsWith($fullPath, $basePath) ? substr($fullPath, strlen($basePath)) : $fullPath;
            $pathParts = explode('/', $relativePath);

            // Si le fichier est sous ui/{category}/{name}.blade.php, le premier segment est la catégorie
            if (count($pathParts) >= 2) {
                // Format: {category}/{name}.blade.php
                $category = $pathParts[0];
            } else {
                // Fallback: utiliser la méthode getCategory
                $category = $this->getCategory($name);
            }

            $content = File::get($file->getPathname());

            // Identifier le module JS
            $jsModule = $this->jsModules[$name] ?? null;

            // Extraire les data-attributes
            preg_match_all('/data-([a-z-]+)=["\']([^"\']*)["\']/', $content, $matches);
            $dataAttrs = [];
            if (! empty($matches[1])) {
                foreach ($matches[1] as $index => $attr) {
                    $dataAttrs[$attr] = $matches[2][$index] ?? '';
                }
            }

            // Extraire les props
            preg_match_all('/@props\(\[(.*?)\]\)/s', $content, $propsMatches);
            $props = [];
            if (! empty($propsMatches[1])) {
                $propsContent = $propsMatches[1][0];
                preg_match_all("/(['\"]?)([a-zA-Z_][a-zA-Z0-9_]*)\\1\\s*=>/", $propsContent, $propMatches);
                if (! empty($propMatches[2])) {
                    $props = array_unique($propMatches[2]);
                }
            }

            // Tags
            $tags = $this->generateTags($name, $category, $jsModule, $dataAttrs);

            // Construire le chemin de vue
            // Tous les composants sont dans ui/{category}/ ou ui/, donc toujours inclure la catégorie
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

            // Collecter les data-attributes
            foreach (array_keys($dataAttrs) as $attr) {
                if (! isset($dataAttributes[$attr])) {
                    $dataAttributes[$attr] = [];
                }
                $dataAttributes[$attr][] = $name;
            }

            // Collecter les dépendances JS
            if ($jsModule) {
                $jsDeps[$name] = $jsModule;
            }
        }

        // Créer le dossier de destination
        $devDataPath = resource_path('dev/data');
        File::ensureDirectoryExists($devDataPath);

        // Générer le manifeste JSON
        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'components' => $components,
        ];
        File::put($devDataPath.'/components.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Générer le CSV des composants
        $csvPath = base_path('docs/inventory');
        File::ensureDirectoryExists($csvPath);
        $csv = fopen($csvPath.'/components.csv', 'w');
        fputcsv($csv, ['name', 'view', 'category', 'tags', 'jsModule', 'status']);
        foreach ($components as $comp) {
            fputcsv($csv, [
                $comp['name'],
                $comp['view'],
                $comp['category'],
                implode(', ', $comp['tags']),
                $comp['jsModule'] ?? '',
                $comp['status'],
            ]);
        }
        fclose($csv);

        // Générer le CSV des data-attributes
        $dataAttrCsv = fopen($csvPath.'/data-attributes.csv', 'w');
        fputcsv($dataAttrCsv, ['attribute', 'components']);
        foreach ($dataAttributes as $attr => $comps) {
            fputcsv($dataAttrCsv, [$attr, implode(', ', $comps)]);
        }
        fclose($dataAttrCsv);

        // Générer le JSON des dépendances JS
        File::put($csvPath.'/js-deps.json', json_encode($jsDeps, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->info('✓ '.count($components).' composants inventoriés');
        $this->info('✓ Manifeste généré: resources/dev/data/components.json');
        $this->info('✓ CSV généré: docs/inventory/components.csv');
        $this->info('✓ Data-attributes: docs/inventory/data-attributes.csv');
        $this->info('✓ Dépendances JS: docs/inventory/js-deps.json');

        return Command::SUCCESS;
    }

    private function getCategory(string $name): string
    {
        foreach ($this->categories as $category => $names) {
            if (in_array($name, $names, true)) {
                return $category;
            }
        }

        // Fallback pour les composants non listés
        if (Str::startsWith($name, 'mockup-')) {
            return 'utilities';
        }
        if (Str::startsWith($name, 'calendar')) {
            return 'advanced';
        }

        return 'advanced';
    }

    private function generateTags(string $name, string $category, ?string $jsModule, array $dataAttrs): array
    {
        $tags = [$category];

        if ($jsModule) {
            $tags[] = 'js';
            $tags[] = 'async';
        }

        if (in_array($name, ['input', 'textarea', 'select', 'checkbox', 'radio', 'file-input'], true)) {
            $tags[] = 'form';
        }

        if (in_array($name, ['modal', 'drawer', 'dropdown', 'popover', 'popconfirm'], true)) {
            $tags[] = 'overlay';
        }

        if (in_array($name, ['tree-view', 'transfer', 'calendar-full'], true)) {
            $tags[] = 'lazy';
        }

        if (in_array($name, ['modal', 'drawer', 'popover', 'popconfirm'], true)) {
            $tags[] = 'aria';
            $tags[] = 'a11y';
        }

        return array_unique($tags);
    }
}

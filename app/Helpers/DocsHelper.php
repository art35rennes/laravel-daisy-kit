<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class DocsHelper
{
    /**
     * Lit le manifeste des composants et retourne la navigation hiérarchique par catégories.
     *
     * @return array<int, array<string,mixed>>
     */
    public static function getNavigationItems(string $prefix = 'docs'): array
    {
        $manifest = self::readManifest();
        $grouped = [];
        foreach ($manifest['components'] ?? [] as $c) {
            $category = (string) ($c['category'] ?? 'misc');
            $name = (string) ($c['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $grouped[$category]['label'] = self::labelize($category);
            $grouped[$category]['children'] ??= [];
            $grouped[$category]['children'][] = [
                'label' => self::labelize($name),
                'href' => '/'.trim($prefix, '/').'/'.$category.'/'.$name,
            ];
        }
        // Ordonner les catégories par nom
        ksort($grouped);

        return array_values($grouped);
    }

    /**
     * Génère la navigation pour les templates à partir du manifest templates.json.
     *
     * @return array<int, array<string,mixed>>
     */
    public static function getTemplateNavigationItems(string $prefix = 'docs'): array
    {
        $manifest = self::readTemplatesManifest();
        $templates = $manifest['templates'] ?? [];
        $grouped = [];

        foreach ($templates as $template) {
            $category = (string) ($template['category'] ?? 'misc');
            $name = (string) ($template['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $categoryInfo = self::getCategoryInfo($category, $manifest);
            $grouped[$category]['label'] = $categoryInfo['label'] ?? self::labelize($category);
            $grouped[$category]['children'] ??= [];
            $grouped[$category]['children'][] = [
                'label' => $template['label'] ?? self::labelize($name),
                'href' => '/'.trim($prefix, '/').'/templates/'.$category.'/'.$name,
            ];
        }

        // Ordonner selon l'ordre des catégories du manifest si présent
        $orderedCategories = array_column($manifest['categories'] ?? [], 'id');
        $ordered = [];
        foreach ($orderedCategories as $catId) {
            if (isset($grouped[$catId])) {
                $ordered[$catId] = $grouped[$catId];
            }
        }
        foreach ($grouped as $catId => $data) {
            if (! isset($ordered[$catId])) {
                $ordered[$catId] = $data;
            }
        }

        return array_values($ordered);
    }

    /**
     * Retourne la liste de props pour un composant donné (noms simples, pour compatibilité).
     *
     * @return array<int,string>
     */
    public static function getComponentProps(string $category, string $name): array
    {
        $props = self::getComponentPropsDetailed($category, $name);

        return array_column($props, 'name');
    }

    /**
     * Retourne les props détaillées avec type, default, values, description.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getComponentPropsDetailed(string $category, string $name): array
    {
        return ComponentPropsParser::parseComponent($category, $name);
    }

    /**
     * Retourne les composants groupés par catégorie avec leurs métadonnées.
     *
     * @return array<string, array<string,mixed>>
     */
    public static function getComponentsByCategory(string $prefix = 'docs'): array
    {
        $manifest = self::readManifest();
        $grouped = [];

        foreach ($manifest['components'] ?? [] as $component) {
            $category = (string) ($component['category'] ?? 'misc');
            $name = (string) ($component['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $grouped[$category]['label'] = self::labelize($category);
            $grouped[$category]['components'] ??= [];
            $grouped[$category]['components'][] = array_merge($component, [
                'href' => '/'.trim($prefix, '/').'/'.$category.'/'.$name,
            ]);
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * Formate un extrait de code pour affichage.
     */
    public static function formatCodeExample(string $code): string
    {
        // Pour l'instant, retourne tel quel (placeholder pour éventuelles transformations)
        return trim($code);
    }

    /**
     * Lecture du fichier manifeste.
     *
     * @return array<string,mixed>
     */
    private static function readManifest(): array
    {
        $path = resource_path('dev/data/components.json');
        if (! File::exists($path)) {
            return ['components' => []];
        }
        $json = File::get($path);
        $data = json_decode($json, true);

        return is_array($data) ? $data : ['components' => []];
    }

    /**
     * Retourne tous les templates organisés par catégorie.
     *
     * @return array<string, array<string,mixed>>
     */
    public static function getTemplatesByCategory(): array
    {
        $manifest = self::readTemplatesManifest();
        $grouped = [];
        foreach ($manifest['templates'] ?? [] as $template) {
            $category = (string) ($template['category'] ?? 'misc');
            if (! isset($grouped[$category])) {
                $grouped[$category] = [
                    'category' => self::getCategoryInfo($category, $manifest),
                    'templates' => [],
                ];
            }
            $grouped[$category]['templates'][] = $template;
        }
        // Ordonner les catégories par ordre défini dans le manifest
        $orderedCategories = array_column($manifest['categories'] ?? [], 'id');
        $ordered = [];
        foreach ($orderedCategories as $catId) {
            if (isset($grouped[$catId])) {
                $ordered[$catId] = $grouped[$catId];
            }
        }
        // Ajouter les catégories non définies dans l'ordre
        foreach ($grouped as $catId => $data) {
            if (! isset($ordered[$catId])) {
                $ordered[$catId] = $data;
            }
        }

        return $ordered;
    }

    /**
     * Retourne les informations d'une catégorie depuis le manifest.
     *
     * @return array<string,mixed>
     */
    public static function getCategoryInfo(string $categoryId, ?array $manifest = null): array
    {
        $manifest = $manifest ?? self::readTemplatesManifest();
        foreach ($manifest['categories'] ?? [] as $cat) {
            if (($cat['id'] ?? '') === $categoryId) {
                return [
                    'id' => $cat['id'],
                    'label' => $cat['label'] ?? self::labelize($categoryId),
                    'description' => $cat['description'] ?? '',
                    'icon' => $cat['icon'] ?? null,
                ];
            }
        }

        return [
            'id' => $categoryId,
            'label' => self::labelize($categoryId),
            'description' => '',
            'icon' => null,
        ];
    }

    /**
     * Retourne toutes les catégories de templates.
     *
     * @return array<int, array<string,mixed>>
     */
    public static function getTemplateCategories(): array
    {
        $manifest = self::readTemplatesManifest();
        $categories = [];
        foreach ($manifest['categories'] ?? [] as $cat) {
            $categories[] = [
                'id' => $cat['id'] ?? '',
                'label' => $cat['label'] ?? self::labelize($cat['id'] ?? ''),
                'description' => $cat['description'] ?? '',
                'icon' => $cat['icon'] ?? null,
            ];
        }

        return $categories;
    }

    /**
     * Lecture du fichier manifeste des templates.
     *
     * @return array<string,mixed>
     */
    private static function readTemplatesManifest(): array
    {
        $path = resource_path('dev/data/templates.json');
        if (! File::exists($path)) {
            return ['templates' => [], 'categories' => []];
        }
        $json = File::get($path);
        $data = json_decode($json, true);

        return is_array($data) ? $data : ['templates' => [], 'categories' => []];
    }

    private static function labelize(string $slug): string
    {
        // Essayer d'abord la traduction
        $translationKey = "daisy::components.{$slug}";
        if (__($translationKey) !== $translationKey) {
            return __($translationKey);
        }

        // Sinon, formater le slug
        $slug = str_replace(['-', '_'], ' ', $slug);
        $slug = preg_replace('/\s+/', ' ', $slug ?? '') ?? '';

        return mb_convert_case(trim($slug), MB_CASE_TITLE, 'UTF-8');
    }
}

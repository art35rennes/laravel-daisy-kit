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
     * Retourne la liste de props pour un composant donné.
     *
     * @return array<int,string>
     */
    public static function getComponentProps(string $category, string $name): array
    {
        $manifest = self::readManifest();
        foreach ($manifest['components'] ?? [] as $c) {
            if (($c['category'] ?? '') === $category && ($c['name'] ?? '') === $name) {
                return array_values($c['props'] ?? []);
            }
        }

        return [];
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

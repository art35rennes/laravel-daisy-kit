<?php

namespace Art35rennes\DaisyKit\Helpers;

class ThemeHelper
{
    /**
     * Génère le CSS pour les thèmes personnalisés daisyUI.
     */
    public static function generateCustomThemesCss(): string
    {
        $customThemes = config('daisy-kit.themes.custom', []);

        if (empty($customThemes)) {
            return '';
        }

        $css = '';

        foreach ($customThemes as $themeName => $themeConfig) {
            $css .= self::generateThemeCss($themeName, $themeConfig);
        }

        return $css;
    }

    /**
     * Génère le CSS pour un thème personnalisé.
     */
    protected static function generateThemeCss(string $themeName, array $config): string
    {
        $name = $config['name'] ?? $themeName;
        $default = $config['default'] ?? false;
        $prefersdark = $config['prefersdark'] ?? false;
        $colorScheme = $config['color-scheme'] ?? 'light';

        $css = "@plugin \"daisyui/theme\" {\n";
        $css .= "  name: \"{$name}\";\n";
        $css .= '  default: '.($default ? 'true' : 'false').";\n";
        $css .= '  prefersdark: '.($prefersdark ? 'true' : 'false').";\n";
        $css .= "  color-scheme: \"{$colorScheme}\";\n\n";

        // Couleurs
        if (isset($config['colors']) && is_array($config['colors'])) {
            foreach ($config['colors'] as $colorName => $colorValue) {
                $css .= "  --color-{$colorName}: {$colorValue};\n";
            }
        }

        // Rayons (radius)
        if (isset($config['radius']) && is_array($config['radius'])) {
            foreach ($config['radius'] as $radiusName => $radiusValue) {
                $css .= "  --radius-{$radiusName}: {$radiusValue};\n";
            }
        }

        // Tailles (size)
        if (isset($config['size']) && is_array($config['size'])) {
            foreach ($config['size'] as $sizeName => $sizeValue) {
                $css .= "  --size-{$sizeName}: {$sizeValue};\n";
            }
        }

        // Bordure
        if (isset($config['border'])) {
            $css .= "  --border: {$config['border']};\n";
        }

        // Profondeur (depth)
        if (isset($config['depth'])) {
            $css .= "  --depth: {$config['depth']};\n";
        }

        // Bruit (noise)
        if (isset($config['noise'])) {
            $css .= "  --noise: {$config['noise']};\n";
        }

        $css .= "}\n\n";

        return $css;
    }

    /**
     * Récupère la liste de tous les thèmes disponibles (intégrés + personnalisés).
     */
    public static function getAllThemes(): array
    {
        $builtin = config('daisy-kit.themes.builtin', []);
        $custom = config('daisy-kit.themes.custom', []);

        $themes = [];

        // Ajouter les thèmes intégrés
        foreach ($builtin as $key => $value) {
            if (is_string($value)) {
                // Format: 'cupcake' => 'cupcake' ou simplement 'cupcake' dans un array
                $themes[] = is_numeric($key) ? $value : $key;
            } elseif (is_array($value)) {
                // Format: 'light' => ['default' => true]
                $themes[] = $key;
            }
        }

        // Ajouter les thèmes personnalisés
        foreach (array_keys($custom) as $themeName) {
            $themes[] = $themeName;
        }

        return array_values(array_unique($themes));
    }
}

<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentSlot;

if (! function_exists('renderComponent')) {
    /**
     * Helper pour rendre un composant Blade et normaliser le HTML.
     */
    function renderComponent(string $view, array $data = []): string
    {
        // Normaliser le slot: beaucoup de composants s'attendent à un ComponentSlot
        if (array_key_exists('slot', $data) && ! $data['slot'] instanceof ComponentSlot) {
            $data['slot'] = new ComponentSlot((string) $data['slot']);
        }

        $html = View::make($view, $data)->render();

        // Normaliser les fins de ligne : convertir \r\n en \n
        $html = str_replace("\r\n", "\n", $html);
        $html = str_replace("\r", "\n", $html);

        // Normalisation basique : supprimer les espaces multiples, normaliser les retours à la ligne
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/>\s+</', '><', $html);
        $html = trim($html);

        return $html;
    }
}

if (! function_exists('langPath')) {
    /**
     * Retourne le chemin du répertoire de langues pour les tests.
     */
    function langPath(string $suffix = 'default'): string
    {
        return storage_path("framework/testing/lang-{$suffix}");
    }
}

if (! function_exists('writeTranslations')) {
    /**
     * Écrit un fichier de traduction pour les tests.
     */
    function writeTranslations(string $locale, string $name, array $data, string $pathSuffix = 'default'): void
    {
        $directory = langPath($pathSuffix)."/{$locale}";
        File::ensureDirectoryExists($directory);

        $content = "<?php\n\nreturn ".var_export($data, true).";\n";
        File::put("{$directory}/{$name}.php", $content);
    }
}

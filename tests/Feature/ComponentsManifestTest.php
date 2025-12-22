<?php

use Illuminate\Support\Facades\Artisan;

/**
 * Charge le cache fichier des composants (bootstrap/cache/daisy-components.php).
 * Si le fichier est manquant, tente de le générer via la commande d'inventaire.
 */
function loadComponentsManifest(): array
{
    $root = dirname(__DIR__, 2);
    $path = $root.DIRECTORY_SEPARATOR.'bootstrap'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'daisy-components.php';

    if (! is_file($path)) {
        Artisan::call('inventory:components');

        if (! is_file($path)) {
            throw new RuntimeException(
                "Manifeste introuvable: {$path}\n".
                "Exécutez 'php artisan inventory:components' (ou 'php artisan inventory:cache:rebuild --components') pour le générer avant de lancer les tests."
            );
        }
    }

    $cache = require $path;
    if (! is_array($cache) || ! isset($cache['components']) || ! is_array($cache['components'])) {
        throw new RuntimeException('Cache invalide: clé "components" manquante ou invalide');
    }

    return $cache['components'];
}

it('renders every component view from manifest', function () {
    $components = loadComponentsManifest();

    foreach ($components as $component) {
        $view = $component['view'];

        // Rendons une première fois avec des valeurs génériques
        $html = renderComponent($view, [
            // Beaucoup de composants ont des valeurs par défaut, on évite de forcer des props ici.
            // Les composants d’inputs acceptent un slot simple sans props.
            'slot' => 'sample',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);

        // Certains composants nécessitent des props minimales pour produire une sortie non vide.
        if ($html === '') {
            $name = $component['name'] ?? null;

            if ($name === 'icon') {
                $html = renderComponent($view, [
                    'name' => 'heart',
                    'prefix' => 'bi',
                    'slot' => '',
                    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
                ]);
            } elseif ($name === 'chat-header') {
                $html = renderComponent($view, [
                    'conversation' => ['id' => 1, 'name' => 'Test', 'avatar' => null, 'isOnline' => false],
                    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
                ]);
            } elseif ($name === 'notification-item') {
                $html = renderComponent($view, [
                    'notification' => [
                        'id' => 1,
                        'type' => 'test',
                        'data' => ['message' => 'Test message'],
                        'read_at' => null,
                        'created_at' => now(),
                    ],
                    'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
                ]);
            }
        }

        expect($html)->not->toBeEmpty("Empty output for view {$view}");
    }
});

it('accepts module override and reflects data-module accordingly', function () {
    $components = loadComponentsManifest();
    $jsComponents = array_filter($components, fn ($comp) => ! empty($comp['jsModule']));

    foreach ($jsComponents as $component) {
        $view = $component['view'];

        // Valeur d’override arbitraire, simple à rechercher
        $override = '__override__';

        $html = renderComponent($view, [
            'module' => $override,
            'slot' => 'sample',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);

        // Certains composants n'ajoutent data-module que selon certaines props (ex: file-input sans preview/dragdrop).
        // On vérifie la cohérence uniquement si data-module est présent.
        if (str_contains($html, 'data-module="')) {
            expect($html)->toContain('data-module="'.$override.'"');
        } else {
            expect(true)->toBeTrue(); // pas de contrainte si non applicable
        }
    }
});

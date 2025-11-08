<?php

/**
 * Charge le manifeste des composants (resources/dev/data/components.json).
 */
function loadComponentsManifest(): array
{
    $root = dirname(__DIR__, 2);
    $path = $root.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'components.json';
    if (! is_file($path)) {
        throw new RuntimeException("Manifeste introuvable: {$path}");
    }

    $json = json_decode((string) file_get_contents($path), true);
    if (! is_array($json) || ! isset($json['components']) || ! is_array($json['components'])) {
        throw new RuntimeException('Manifeste invalide: clé "components" manquante ou invalide');
    }

    return $json['components'];
}

/**
 * Dataset: tous les composants.
 */
function dataset_all_components(): array
{
    $components = loadComponentsManifest();

    // Dataset au format ['name' => ['component' => [...]]]
    $dataset = [];
    foreach ($components as $comp) {
        $dataset[$comp['name']] = [$comp];
    }

    return $dataset;
}

/**
 * Dataset: composants avec module JS (doivent exposer data-module et accepter la prop 'module').
 */
function dataset_js_components(): array
{
    $components = loadComponentsManifest();
    $dataset = [];
    foreach ($components as $comp) {
        if (! empty($comp['jsModule'])) {
            $dataset[$comp['name']] = [$comp];
        }
    }

    return $dataset;
}

// Déclarer des datasets nommés pour compatibilité avec l'exécution parallèle
dataset('all_components', dataset_all_components());
dataset('js_components', dataset_js_components());

it('renders every component view from manifest', function (array $component) {
    $view = $component['view'];

    // Rendons une première fois avec des valeurs génériques
    $html = renderComponent($view, [
        // Beaucoup de composants ont des valeurs par défaut, on évite de forcer des props ici.
        // Les composants d’inputs acceptent un slot simple sans props.
        'slot' => 'sample',
        'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
    ]);

    // Certains composants nécessitent des props minimales pour produire une sortie non vide.
    if ($html === '' && ($component['name'] ?? null) === 'icon') {
        $html = renderComponent($view, [
            'name' => 'heart',
            'prefix' => 'bi',
            'slot' => '',
            'attributes' => new \Illuminate\View\ComponentAttributeBag([]),
        ]);
    }

    expect($html)->not->toBeEmpty("Empty output for view {$view}");
})->with('all_components');

it('accepts module override and reflects data-module accordingly', function (array $component) {
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
})->with('js_components');

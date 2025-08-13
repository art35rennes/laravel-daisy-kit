<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Page de démo réservée au dev (non publiée) via le namespace daisy-dev
Route::get('/demo', function () {
    return view('daisy-dev::demo.index');
})->name('demo');

// Endpoint REST pour lazy-loading du TreeView en démo
Route::get('/demo/api/tree-children', function (\Illuminate\Http\Request $request) {
    $node = (string) $request->query('node', '');
    // Exemple simple: renvoie des enfants factices selon l'id du nœud
    $data = match ($node) {
        'b' => [
            ['id' => 'b1', 'label' => 'Fichier B1'],
            // Exemple de nœud lazy imbriqué (avec disabled supporté)
            ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'lazy' => true, 'disabled' => true],
            ['id' => 'b3', 'label' => 'Fichier B3'],
        ],
        // Quand on ouvre B2, on renvoie ses enfants (B2-2 devient lazy)
        'b2' => [
            ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
            ['id' => 'b2-2', 'label' => 'Dossier B2-2 (lazy)', 'lazy' => true],
        ],
        // Les enfants de B2-2 ne sont renvoyés que lorsqu'on ouvre B2-2
        'b2-2' => [
            ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
            ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
        ],
        'root' => [
            ['id' => 'r1', 'label' => 'Fichier Racine 1'],
            ['id' => 'r2', 'label' => 'Fichier Racine 2'],
        ],
        default => [
            ['id' => $node.'-1', 'label' => 'Fichier '.$node.'-1'],
            ['id' => $node.'-2', 'label' => 'Fichier '.$node.'-2'],
        ],
    };
    return response()->json($data);
})->name('demo.tree.children');

// Endpoint REST pour recherche dans le TreeView (démo)
Route::get('/demo/api/tree-search', function (\Illuminate\Http\Request $request) {
    $q = strtolower((string) $request->query('q', ''));
    // Données de démo correspondant à demoTreeSingle
    $data = [
        ['id' => 'root', 'label' => 'Racine', 'children' => [
            ['id' => 'a', 'label' => 'Dossier A', 'children' => [
                ['id' => 'a1', 'label' => 'Fichier A1'],
                ['id' => 'a2', 'label' => 'Fichier A2'],
            ]],
            ['id' => 'b', 'label' => 'Dossier B (lazy)', 'children' => [
                ['id' => 'b1', 'label' => 'Fichier B1'],
                ['id' => 'b2', 'label' => 'Dossier B2 (lazy, disabled)', 'children' => [
                    ['id' => 'b2-1', 'label' => 'Fichier B2-1'],
                    ['id' => 'b2-2', 'label' => 'Dossier B2-2 (lazy)', 'children' => [
                        ['id' => 'b2-2-1', 'label' => 'Fichier B2-2-1'],
                        ['id' => 'b2-2-2', 'label' => 'Fichier B2-2-2'],
                    ]],
                ]],
            ]],
            ['id' => 'c', 'label' => 'Fichier C'],
        ]],
    ];
    $paths = [];
    $cur = [];
    $walk = function ($nodes) use (&$walk, &$paths, &$cur, $q) {
        foreach ($nodes as $n) {
            $cur[] = $n['id'];
            $label = strtolower((string)($n['label'] ?? (string)$n['id']));
            if ($q !== '' && (str_contains($label, $q) || str_contains(strtolower((string)$n['id']), $q))) {
                // On retourne uniquement le chemin jusqu'au nœud trouvé (sans forcer l'extension des descendants)
                $paths[] = $cur; // copie implicite
            }
            $children = $n['children'] ?? [];
            if (!empty($children)) $walk($children);
            array_pop($cur);
        }
    };
    $walk($data);
    // Limite pour éviter les réponses trop volumineuses côté démo
    $paths = array_slice($paths, 0, 50);
    return response()->json(['paths' => $paths]);
})->name('demo.tree.search');

// Pages dédiées aux layouts/templates avancés
Route::view('/templates', 'templates.index')->name('templates.index');
Route::view('/templates/navbar', 'templates.navbar')->name('layouts.navbar');
Route::view('/templates/sidebar', 'templates.sidebar')->name('layouts.sidebar');
Route::view('/templates/navbar-sidebar', 'templates.navbar-sidebar')->name('layouts.navbar-sidebar');

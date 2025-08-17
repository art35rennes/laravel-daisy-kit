<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Page de démo réservée au dev (non publiée) via le namespace daisy-dev
Route::get('/demo', function () {
    return view('daisy-dev::demo.ui.index');
})->name('demo');

// Endpoint REST simple pour Calendar Full (démo)
Route::get('/demo/api/calendar-events', function (\Illuminate\Http\Request $request) {
    $start = new DateTime((string) $request->query('start', date('Y-m-01')));
    $end = new DateTime((string) $request->query('end', date('Y-m-t')));
    // Génère quelques évènements factices dans la plage demandée
    $events = [];
    $cur = clone $start;
    while ($cur < $end) {
        $day = (int)$cur->format('j');
        if (in_array($day, [1,7,12,14,28], true)) {
            $iso = $cur->format('Y-m-d');
            if ($day === 1) $events[] = ['id'=>"a-$iso", 'title'=>'All Day Event', 'start'=>$iso, 'allDay'=>true];
            if ($day === 12) $events[] = ['id'=>"m1-$iso", 'title'=>'Meeting', 'start'=>"$iso 10:30", 'end'=>"$iso 12:30"];
            if ($day === 14) $events[] = ['id'=>"b-$iso", 'title'=>'Birthday Party', 'start'=>"$iso 07:00"];
            if ($day === 28) $events[] = ['id'=>"g-$iso", 'title'=>'Click for Google', 'start'=>$iso, 'url'=>'https://google.com'];
            if ($day === 7) $events[] = ['id'=>"long-$iso", 'title'=>'Long Event', 'start'=>$iso, 'end'=>$cur->modify('+7 day')->format('Y-m-d')];
        }
        $cur->modify('+1 day');
    }
    return response()->json($events);
})->name('demo.calendar.events');

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
Route::view('/templates', 'daisy-dev::demo.templates.index')->name('templates.index');
Route::view('/templates/navbar', 'daisy-dev::demo.templates.test-navbar')->name('layouts.navbar');
Route::view('/templates/sidebar', 'daisy-dev::demo.templates.test-sidebar')->name('layouts.sidebar');
Route::view('/templates/navbar-sidebar', 'daisy-dev::demo.templates.test-navbar-sidebar')->name('layouts.navbar-sidebar');

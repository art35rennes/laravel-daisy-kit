<?php

use function Pest\Laravel\visit;

it('expands lazy node and selects items in treeview without errors', function () {
    $page = visit('/demo');

    $page->assertSee('TreeView')
        ->assertNoJavascriptErrors();

    // Développe le dossier B (lazy) via le bouton dédié de la démo
    $page->click('#btnExpandB');

    // Clique sur un élément pour sélectionner (dans l’arbre single)
    $page->click('#demoTreeSingle [role="tree"] li[role="treeitem"] [data-label]');

    // Dans l’arbre multi, clique sur une checkbox
    $page->click('#demoTreeMulti input[type="checkbox"]');

    $page->assertNoConsoleLogs();
});

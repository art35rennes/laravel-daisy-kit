<?php

use function Pest\Laravel\visit;

it('handles inline popconfirm confirm and cancel without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Popconfirm')
        ->assertNoJavascriptErrors();

    // Ouvre le premier popconfirm inline
    $page->click('[data-popconfirm] .popconfirm-trigger');

    // Clique sur confirmer puis ré-ouvre et clique sur annuler
    $page->click('[data-popconfirm] .popconfirm-panel [data-popconfirm-action="confirm"]');
    $page->click('[data-popconfirm] .popconfirm-trigger');
    $page->click('[data-popconfirm] .popconfirm-panel [data-popconfirm-action="cancel"]');

    $page->assertNoConsoleLogs();
});

it('opens popconfirm in modal mode without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Popconfirm')
        ->assertNoJavascriptErrors();

    // Bouton modal
    $page->click('[data-popconfirm-modal]');

    // Le texte de confirmation de l’exemple modal doit être présent
    $page->assertSee('Voulez-vous enregistrer ces modifications ?')
        ->assertNoConsoleLogs();
});

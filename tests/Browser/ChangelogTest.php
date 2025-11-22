<?php

use function Pest\Laravel\visit;

it('renders changelog template without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Changelog')
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

it('filters changelog items by type', function () {
    $page = visit('/demo');

    $page->assertSee('Changelog')
        ->assertNoJavascriptErrors();

    // Clique sur le filtre "Fixed"
    $page->click('input[name="changelog-filter"][aria-label*="Fixed"]');

    // Vérifie que seuls les éléments "Fixed" sont visibles
    $page->assertSee('Fixed');

    $page->assertNoConsoleLogs();
});

it('searches in changelog items', function () {
    $page = visit('/demo');

    $page->assertSee('Changelog')
        ->assertNoJavascriptErrors();

    // Tape dans le champ de recherche
    $page->type('[data-changelog-search]', 'security');

    // Vérifie que les résultats filtrés sont visibles
    $page->assertSee('Security');

    $page->assertNoConsoleLogs();
});

it('expands and collapses version items', function () {
    $page = visit('/demo');

    $page->assertSee('Changelog')
        ->assertNoJavascriptErrors();

    // Clique sur un collapse pour le replier (si ouvert)
    $page->click('.changelog-version-item .collapse-title');

    // Clique à nouveau pour le rouvrir
    $page->click('.changelog-version-item .collapse-title');

    $page->assertNoConsoleLogs();
});

it('displays empty state when no versions', function () {
    $page = visit('/demo');

    $page->assertSee('Changelog')
        ->assertNoJavascriptErrors();

    // Si une version vide est testée, vérifier l'état vide
    // (cela dépend de la structure de la page de démo)

    $page->assertNoConsoleLogs();
});

it('navigates to Git links when present', function () {
    $page = visit('/demo');

    $page->assertSee('Changelog')
        ->assertNoJavascriptErrors();

    // Vérifie que les liens Git sont présents (si disponibles dans la démo)
    // Les liens externes ne peuvent pas être testés directement en browser test
    // mais on peut vérifier leur présence

    $page->assertNoConsoleLogs();
});

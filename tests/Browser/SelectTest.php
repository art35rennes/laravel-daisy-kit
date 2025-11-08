<?php

use function Pest\Laravel\visit;

it('enhances select with search and autocomplete without JS errors', function () {
    $page = visit('/demo');

    $page->assertSee('Selects')
        ->assertNoJavascriptErrors();

    // Mode Search: taper un terme et vérifier l’absence d’erreurs
    $page->type('section:has(h2:contains("Selects")) [data-select-search-input]', 'can');

    // Mode Autocomplete: taper un terme, ouvrir les suggestions et choisir un item
    $page->type('section:has(h2:contains("Selects")) [data-select-autocomplete-input]', 'brav')
        ->click('section:has(h2:contains("Selects")) .dropdown-open [role="option"]:not(.disabled)');

    $page->assertNoConsoleLogs();
});

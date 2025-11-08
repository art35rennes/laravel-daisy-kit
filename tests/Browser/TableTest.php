<?php

use function Pest\Laravel\visit;

it('selects table rows without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Table')
        ->assertNoJavascriptErrors();

    // Sélection multiple: coche une case
    $page->click('section:has(h2:contains("Table")) [data-row-select]');

    // Sélection simple (dans le second tableau)
    $page->click('section:has(h2:contains("Table")) input[type="radio"][data-row-select]');

    $page->assertNoConsoleLogs();
});

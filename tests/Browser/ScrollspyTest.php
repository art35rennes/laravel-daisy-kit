<?php

use function Pest\Laravel\visit;

it('navigates with scrollspy links without errors', function () {
    $page = visit('/demo');

    $page->assertSee('ScrollSpy')
        ->assertNoJavascriptErrors();

    // Cliquer sur Section 3 dans le premier scrollspy (items explicites)
    $page->click('nav[data-scrollspy] a[href="#sec-3"]');

    // Cliquer ensuite Section 4
    $page->click('nav[data-scrollspy] a[href="#sec-4"]');

    $page->assertNoConsoleLogs();
});

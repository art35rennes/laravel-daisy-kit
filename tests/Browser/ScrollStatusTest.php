<?php

use function Pest\Laravel\visit;

it('initializes scroll status bars without console errors', function () {
    $page = visit('/demo');

    $page->assertSee('Scroll Status')
        ->assertNoJavascriptErrors();

    // Fait défiler un conteneur local (simple interaction en cliquant à l’intérieur)
    $page->click('section:has(h2:contains("Scroll Status")) .border.rounded-box');

    $page->assertNoConsoleLogs();
});

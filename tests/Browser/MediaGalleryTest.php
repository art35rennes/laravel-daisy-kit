<?php

use function Pest\Laravel\visit;

it('switches media gallery main image without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Media Gallery')
        ->assertNoJavascriptErrors();

    // Clique sur une vignette de la première galerie
    $page->click('[data-media-gallery="1"] [data-thumbs] [data-thumb][data-index="1"]');

    // Clique ensuite une autre vignette
    $page->click('[data-media-gallery="1"] [data-thumbs] [data-thumb][data-index="2"]');

    $page->assertNoConsoleLogs();
});

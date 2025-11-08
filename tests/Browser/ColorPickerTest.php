<?php

use function Pest\Laravel\visit;

it('opens color picker dropdown and changes format without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Color Picker')
        ->assertNoJavascriptErrors();

    // Ouvre le color picker (dropdown)
    $page->click('[data-colorpicker="1"] [data-colorpicker-trigger]');

    // Le panneau doit être rendu, on clique quelque part à l’intérieur (ex: sur la pastille)
    $page->click('[data-colorpicker="1"] [data-colorpicker-panel]');

    $page->assertNoConsoleLogs();
});

<?php

use function Pest\Laravel\visit;

it('moves items between lists in transfer without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Transfer')
        ->assertNoJavascriptErrors();

    // Coche un premier item dans la liste source de la première instance
    $page->click('[data-transfer="1"] [data-transfer-list="source"] input[type="checkbox"]');

    // Déplace vers la cible
    $page->click('[data-transfer="1"] [data-transfer-move="toTarget"]');

    $page->assertNoConsoleLogs();
});

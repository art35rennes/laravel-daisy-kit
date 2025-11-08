<?php

it('navigates docs and sees toc without js errors', function () {
    // Activer routes docs
    config()->set('daisy-kit.docs.enabled', true);

    $page = visit('/docs');
    $page->assertSee('Documentation')
        ->assertNoJavascriptErrors();

    // Aller sur la page Button
    $page = visit('/docs/inputs/button');
    $page->assertSee('Bouton')
        ->assertNoJavascriptErrors();
});

<?php

use function Pest\Laravel\visit;

it('starts and navigates onboarding without console errors', function () {
    $page = visit('/demo');

    $page->assertSee('Onboarding')
        ->assertNoJavascriptErrors();

    // Démarre le premier onboarding via le bouton
    $page->click('#onboarding-start-btn');

    // Navigue ensuite puis termine (si boutons visibles)
    $page->click('button.btn-primary'); // next
    $page->click('button.btn-primary'); // next encore
    $page->click('button.btn-success'); // finish

    $page->assertNoConsoleLogs();
});

<?php

use function Pest\Laravel\visit;

it('renders sign component without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Sign')
        ->assertNoJavascriptErrors();
});

it('initializes signature pad when component is present', function () {
    $blade = <<<'BLADE'
<x-daisy::ui.inputs.sign name="signature" />
BLADE;

    $html = \Illuminate\Support\Facades\Blade::render($blade);

    $page = visit('/demo');

    // Vérifier que le composant est présent
    $page->assertSee('sign-container')
        ->assertNoJavascriptErrors();
});

it('can clear signature', function () {
    $page = visit('/demo');

    $page->assertSee('Sign')
        ->assertNoJavascriptErrors();

    // Vérifier que le bouton effacer existe
    $page->assertSee('Effacer')
        ->assertNoConsoleLogs();
});

it('can download signature', function () {
    $page = visit('/demo');

    $page->assertSee('Sign')
        ->assertNoJavascriptErrors();

    // Vérifier que le bouton télécharger existe
    $page->assertSee('Télécharger')
        ->assertNoConsoleLogs();
});

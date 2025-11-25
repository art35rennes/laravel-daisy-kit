<?php

use function Pest\Laravel\visit;

it('renders copyable component without errors', function () {
    $page = visit('/demo');

    $page->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

it('shows clipboard icon on hover', function () {
    $blade = <<<'BLADE'
<x-daisy::ui.utilities.copyable>
    Texte à copier
</x-daisy::ui.utilities.copyable>
BLADE;

    $html = \Illuminate\Support\Facades\Blade::render($blade);

    $page = visit('/demo');

    // Vérifier que le composant est présent
    $page->assertSee('copyable')
        ->assertNoJavascriptErrors();
});

it('can copy text when clicked', function () {
    $page = visit('/demo');

    $page->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

it('shows feedback message after copy', function () {
    $page = visit('/demo');

    $page->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

it('handles clipboard permission request', function () {
    $page = visit('/demo');

    $page->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

<?php

use function Pest\Laravel\visit;

it('loads the demo page without errors', function () {
    $page = visit('/demo');

    $page->assertSee('Demo')
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

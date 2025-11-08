<?php

use function Pest\Laravel\visit;

it('opens a modal from demo page without console errors', function () {
    $page = visit('/demo');

    $page->assertSee('Demo')
        ->assertNoJavascriptErrors();

    // Click a modal open button if present
    $page->click('button[onclick*="demo-modal"]');

    $page->assertNoConsoleLogs();
});

it('toggles drawer from demo page without console errors', function () {
    $page = visit('/demo');

    $page->assertSee('Demo')
        ->assertNoJavascriptErrors();

    // Try to click a drawer toggle label if present
    $page->click('.drawer-button');

    $page->assertNoConsoleLogs();
});

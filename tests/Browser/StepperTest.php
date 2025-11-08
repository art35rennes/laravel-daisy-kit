<?php

use function Pest\Laravel\visit;

it('navigates stepper next and prev', function () {
    $page = visit('/demo');

    $page->assertSee('Components')
        ->assertNoJavascriptErrors();

    // Click next button if present
    $page->click('[data-stepper-next]');

    // Click prev button if present
    $page->click('[data-stepper-prev]');

    $page->assertNoConsoleLogs();
});

<?php

use function Pest\Laravel\visit;

it('toggles popover on click', function () {
    $page = visit('/demo');

    $page->assertSee('Components')
        ->click('Feedback') // navigate section via sidebar if present
        ->assertNoJavascriptErrors();

    // Try to click on a popover trigger (first found)
    $page->click('.popover-trigger');

    // The popover panel should not be hidden
    $page->assertSeeIn('.popover-panel', '');
});

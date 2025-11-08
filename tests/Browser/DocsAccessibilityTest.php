<?php

it('docs pages basic accessibility smoke', function () {
    config()->set('daisy-kit.docs.enabled', true);

    $page = visit(['/docs', '/docs/inputs/button', '/docs/templates']);
    $page->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

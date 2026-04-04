<?php

it('does not expose a default endpoint when csrf refresh is disabled', function () {
    config([
        'daisy-kit.csrf_refresh.enabled' => false,
    ]);

    $html = view('daisy::components.ui.utilities.csrf-keeper')->render();

    expect($html)
        ->not->toContain('data-endpoint=')
        ->toContain('data-refresh-interval=');
});

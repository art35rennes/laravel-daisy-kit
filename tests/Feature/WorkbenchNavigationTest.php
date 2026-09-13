<?php

declare(strict_types=1);

it('uses the Workbench root as a module directory', function (): void {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Component modules')
        ->assertSee('data-workbench-module=""', false)
        ->assertDontSee('data-daisy-kit-module=', false);

    foreach (['table', 'tree', 'blueprint', 'file-preview', 'map', 'copyable', 'combobox', 'signature', 'truncate', 'scrollspy', 'transfer-list'] as $module) {
        $response->assertSee("href=\"/{$module}\"", false);
    }
});

it('renders every component module on its own route', function (string $module): void {
    $response = $this->get("/{$module}");

    $response->assertOk()
        ->assertSee("data-workbench-module=\"{$module}\"", false)
        ->assertSee("data-daisy-kit-module=\"{$module}\"", false);

    foreach (['table', 'tree', 'blueprint', 'file-preview', 'map', 'copyable', 'combobox', 'signature', 'truncate', 'scrollspy', 'transfer-list'] as $otherModule) {
        if ($otherModule !== $module) {
            $response->assertDontSee("data-daisy-kit-module=\"{$otherModule}\"", false);
        }
    }
})->with([
    'table', 'tree', 'blueprint', 'file-preview', 'map', 'copyable',
    'combobox', 'signature', 'truncate', 'scrollspy', 'transfer-list',
]);

it('demonstrates Copyable with its optional icon and visual feedback', function (): void {
    $this->get('/copyable')
        ->assertOk()
        ->assertSee('data-copyable-scenario="explicit-value"', false)
        ->assertSee('data-copyable-scenario="visible-text"', false)
        ->assertSee('data-copyable-scenario="structured-text"', false)
        ->assertSee('data-copyable-scenario="disabled"', false)
        ->assertSee('php artisan boost:update --discover')
        ->assertSee('Copy JSON payload')
        ->assertSee('Copy protected value')
        ->assertSee('disabled', false)
        ->assertSee('data-daisy-kit-copyable-icon', false)
        ->assertSee('data-daisy-kit-copyable-feedback', false)
        ->assertSee('Release identifier copied.');
});

it('demonstrates local and server-backed Combobox workflows without an API console', function (): void {
    $this->get('/combobox')
        ->assertOk()
        ->assertSee('Remote people directory')
        ->assertSee('Local release vocabulary')
        ->assertSee('remote-reviewers-combobox', false)
        ->assertSee('local-release-tags-combobox', false)
        ->assertSee('analytical-engine.org')
        ->assertSee('"searchFields":["label","description","meta"]', false)
        ->assertDontSee('event log', false)
        ->assertDontSee('facade inspector', false);
});

it('demonstrates Transfer List as a realistic paginated Laravel assignment form', function (): void {
    $response = $this->get('/transfer-list');

    $response->assertOk()
        ->assertSee('Assign the release review team')
        ->assertSee('Company directory')
        ->assertSee('Assigned reviewers')
        ->assertSee('data-daisy-kit-transfer-select-all="source"', false)
        ->assertSee('"pagination":true', false)
        ->assertSee('"pageSize":5', false)
        ->assertSee('External auditor');
});

it('demonstrates Truncate with dense table values instead of an API fixture', function (): void {
    $this->get('/truncate')
        ->assertOk()
        ->assertSee('Delivery addresses')
        ->assertSee('Grace Hopper')
        ->assertSee('1701 North Beauregard Street')
        ->assertSee('data-daisy-kit-module="truncate"', false)
        ->assertDontSee('event log', false)
        ->assertDontSee('facade inspector', false);
});

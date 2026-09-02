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

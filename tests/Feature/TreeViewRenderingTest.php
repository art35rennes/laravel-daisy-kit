<?php

declare(strict_types=1);
use Illuminate\View\ViewException;

function renderTreeView(array $data = []): string
{
    return app('view')->make('daisy::components.ui.advanced.tree-view', $data)->render();
}

it('renders a labelled multiple tree with leaf form values and tri state parents', function () {
    $html = renderTreeView([
        'id' => 'permissions-tree',
        'label' => 'Permissions',
        'name' => 'permissions',
        'selection' => 'multiple',
        'value' => ['reports.view'],
        'data' => [
            [
                'id' => 'reports',
                'label' => 'Reports',
                'children' => [
                    ['id' => 'reports.view', 'label' => 'View reports'],
                    ['id' => 'reports.edit', 'label' => 'Edit reports'],
                ],
            ],
        ],
    ]);

    expect($html)
        ->toContain('id="permissions-tree"')
        ->toContain('role="tree"')
        ->toContain('aria-label="Permissions"')
        ->toContain('aria-multiselectable="true"')
        ->toContain('aria-checked="mixed"')
        ->toContain('name="permissions[]"')
        ->toContain('value="reports.view"')
        ->not->toContain('name="permissions[]" value="reports"')
        ->not->toContain('id="permissions-tree" class="menu');
});

it('renders a single tree with one scalar form value', function () {
    $html = renderTreeView([
        'id' => 'destination-tree',
        'label' => 'Destination',
        'name' => 'destination',
        'selection' => 'single',
        'value' => 'archive',
        'data' => [
            ['id' => 'inbox', 'label' => 'Inbox'],
            ['id' => 'archive', 'label' => 'Archive'],
        ],
    ]);

    expect($html)
        ->toContain('aria-selected="true"')
        ->toContain('name="destination"')
        ->toContain('value="archive"')
        ->not->toContain('aria-checked=');
});

it('omits aria expanded from leaves and exposes it on parents only', function () {
    $html = renderTreeView([
        'label' => 'Files',
        'data' => [[
            'id' => 'docs',
            'label' => 'Documents',
            'children' => [['id' => 'readme', 'label' => 'README']],
        ]],
    ]);

    expect(substr_count($html, 'aria-expanded='))->toBe(1);
});

it('rejects a lazy branch with preloaded children', function () {
    expect(fn () => renderTreeView([
        'label' => 'Contracts',
        'data' => [[
            'id' => 'country',
            'label' => 'Country',
            'lazy' => true,
            'expanded' => true,
            'children' => [['id' => 'contract', 'label' => 'Contract']],
        ]],
    ]))->toThrow(ViewException::class, 'cannot include children');
});

it('renders initial values and expansion paths as component state', function () {
    $html = renderTreeView([
        'id' => 'contract-tree',
        'label' => 'Contracts',
        'valueMode' => 'selected-roots',
        'value' => ['France.53'],
        'initialExpandPaths' => [['France']],
        'data' => [['id' => 'France', 'label' => 'France', 'lazy' => true]],
    ]);

    expect($html)
        ->toContain('data-value-mode="selected-roots"')
        ->toContain('data-initial-value=')
        ->toContain('data-initial-expand-paths=');
});

it('escapes node labels and ids', function () {
    $html = renderTreeView([
        'label' => 'Unsafe tree',
        'value' => ['"><img src=x onerror=alert(1)>'],
        'data' => [[
            'id' => '"><img src=x onerror=alert(1)>',
            'label' => '<img src=x onerror=alert(1)>',
        ]],
    ]);

    expect($html)
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->not->toContain('<img src=x onerror=alert(1)>');
});

it('requires a stable id when expansion persistence is enabled', function () {
    expect(fn () => renderTreeView([
        'label' => 'Persistent tree',
        'persist' => true,
    ]))->toThrow(ViewException::class, 'requires an explicit id');
});

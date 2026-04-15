<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;

it('renders a client table with DaisyUI classes and serialized config', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="client"
            size="sm"
            zebra
            pin-rows
            pin-cols
            caption="Users"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'sortable' => true],
                ['key' => 'role', 'label' => 'Role', 'cellClass' => 'text-right'],
            ]"
            :rows="[
                ['name' => 'Jane', 'role' => 'Admin'],
            ]"
            :page-size-options="[10, 25]"
            column-visibility
        />
    BLADE);

    expect($html)
        ->toContain('data-daisy-table="1"')
        ->toContain('table table-zebra table-sm table-pin-rows table-pin-cols w-full')
        ->toContain('daisy-table-shell')
        ->toContain('Users')
        ->toContain('"mode":"client"')
        ->toContain('"pageSizeOptions":[10,25]')
        ->toContain('"columnVisibility":true')
        ->toContain('Jane')
        ->toContain('Admin');
});

it('renders a server table with endpoint config', function () {
    $html = View::make('daisy::components.ui.data-display.table', [
        'mode' => 'server',
        'endpoint' => '/api/users',
        'method' => 'POST',
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'sortable' => true],
            ['key' => 'email', 'label' => 'Email'],
        ],
        'initialState' => [
            'sorting' => [['id' => 'name', 'desc' => false]],
            'pagination' => ['pageSize' => 25],
        ],
    ])->render();

    expect($html)
        ->toContain('data-daisy-table="1"')
        ->toContain('"mode":"server"')
        ->toContain('"method":"POST"')
        ->toContain('"url":"\/api\/users"')
        ->toContain('"pageSize":25')
        ->toContain('Loading');
});

it('renders spatie query builder adapter config and filter controls', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="server"
            server-adapter="spatie-query-builder"
            persist-state="url"
            state-key="users-table"
            global-filter-key="global"
            endpoint="/users"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true, 'sortKey' => 'users.name', 'filterKey' => 'name', 'filter' => ['type' => 'text']],
                ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'filterable' => true, 'sortKey' => 'status', 'filterKey' => 'status', 'filter' => ['type' => 'select', 'options' => [['value' => 'active', 'label' => 'Active']]]],
                ['key' => 'is_published', 'label' => 'Published', 'filterable' => true, 'filter' => ['type' => 'boolean']],
            ]"
        />
    BLADE, [
        'users' => collect(),
    ]);

    expect($html)
        ->toContain('"serverAdapter":"spatie-query-builder"')
        ->toContain('"persistState":"url"')
        ->toContain('"stateKey":"users-table"')
        ->toContain('"globalFilterKey":"global"')
        ->toContain('"sortKey":"users.name"')
        ->toContain('"filterKey":"status"')
        ->toContain('data-table-filter="name"')
        ->toContain('data-table-filter="status"')
        ->toContain('data-table-filter="is_published"');
});

it('renders a client table with trusted html cells', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[
                ['key' => 'status', 'label' => 'Status', 'html' => true],
            ]"
            :rows="[
                ['status' => '<span class=&quot;badge badge-success&quot;>Active</span>'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('badge badge-success')
        ->toContain('Active');
});

it('requires an endpoint when mode is server', function () {
    $render = fn () => View::make('daisy::components.ui.data-display.table', [
        'mode' => 'server',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
    ])->render();

    expect($render)->toThrow(ViewException::class);
});

it('requires server mode when a server adapter is provided', function () {
    $render = fn () => View::make('daisy::components.ui.data-display.table', [
        'serverAdapter' => 'spatie-query-builder',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [
            ['name' => 'Jane'],
        ],
    ])->render();

    expect($render)->toThrow(ViewException::class);
});

it('keeps the legacy datatable alias only as an explicit migration error', function () {
    $render = fn () => Blade::render('<x-daisy::ui.data-display.datatable />');

    expect(View::exists('daisy::components.ui.data-display.table'))->toBeTrue()
        ->and($render)->toThrow(ViewException::class, 'x-daisy::ui.data-display.datatable')
        ->and(View::exists('daisy::components.ui.advanced.table'))->toBeFalse();
});

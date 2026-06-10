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
                ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'width' => '180px'],
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
        ->toContain('daisy-table-width-px-180')
        ->toContain('Users')
        ->toContain('"mode":"client"')
        ->toContain('"pageSizeOptions":[10,25]')
        ->toContain('"columnVisibility":true')
        ->toContain('Jane')
        ->toContain('Admin')
        ->not->toContain('data-daisy-css-width');
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
        ->toContain('"searchDebounceMs":500')
        ->toContain('"filterDebounceMs":500')
        ->toContain('"minSearchChars":3')
        ->toContain('Loading');
});

it('allows table search pacing to be configured explicitly', function () {
    $html = View::make('daisy::components.ui.data-display.table', [
        'mode' => 'server',
        'endpoint' => '/api/users',
        'searchDebounce' => 750,
        'filterDebounce' => 650,
        'minSearchChars' => 4,
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'filterable' => true, 'filter' => ['type' => 'text']],
        ],
    ])->render();

    expect($html)
        ->toContain('"searchDebounceMs":750')
        ->toContain('"filterDebounceMs":650')
        ->toContain('"minSearchChars":4');
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

it('renders table filters in a stable responsive grid before technical controls', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="server"
            endpoint="/interventions"
            column-visibility
            :columns="[
                ['key' => 'external_note', 'label' => 'External note', 'filterable' => true, 'filter' => ['type' => 'text']],
                ['key' => 'compile_status', 'label' => 'Compile status', 'filterable' => true, 'filter' => ['type' => 'select']],
                ['key' => 'name', 'label' => 'Name', 'filterable' => true, 'filter' => ['type' => 'text']],
                ['key' => 'company', 'label' => 'Company', 'filterable' => true, 'filter' => ['type' => 'text']],
                ['key' => 'city', 'label' => 'City', 'filterable' => true, 'filter' => ['type' => 'text']],
                ['key' => 'reference_internal', 'label' => 'Reference', 'filterable' => true, 'filter' => ['type' => 'text']],
            ]"
            :filters="[
                ['key' => 'intervention_type_code', 'label' => 'Intervention type', 'type' => 'text'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('daisy-table-toolbar grid gap-3')
        ->toContain('daisy-table-filters grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4')
        ->toContain('data-table-search')
        ->toContain('data-table-filter="reference_internal"')
        ->toContain('data-table-filter="name"')
        ->toContain('data-table-filter="city"')
        ->toContain('data-table-filter="company"')
        ->toContain('data-table-filter="compile_status"')
        ->toContain('data-table-filter="intervention_type_code"')
        ->toContain('data-table-filter="external_note"')
        ->toContain('data-table-page-size')
        ->toContain('data-table-column-menu');

    expect(strpos($html, 'data-table-search'))->toBeLessThan(strpos($html, 'data-table-filter="reference_internal"'))
        ->and(strpos($html, 'data-table-filter="reference_internal"'))->toBeLessThan(strpos($html, 'data-table-filter="name"'))
        ->and(strpos($html, 'data-table-filter="name"'))->toBeLessThan(strpos($html, 'data-table-filter="city"'))
        ->and(strpos($html, 'data-table-filter="city"'))->toBeLessThan(strpos($html, 'data-table-filter="company"'))
        ->and(strpos($html, 'data-table-filter="company"'))->toBeLessThan(strpos($html, 'data-table-filter="compile_status"'))
        ->and(strpos($html, 'data-table-filter="compile_status"'))->toBeLessThan(strpos($html, 'data-table-filter="intervention_type_code"'))
        ->and(strpos($html, 'data-table-filter="intervention_type_code"'))->toBeLessThan(strpos($html, 'data-table-filter="external_note"'))
        ->and(strpos($html, 'data-table-filter="external_note"'))->toBeLessThan(strpos($html, 'data-table-page-size'))
        ->and(strpos($html, 'data-table-page-size'))->toBeLessThan(strpos($html, 'data-table-column-menu'));
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

it('requires at least one valid column key', function () {
    $render = fn () => View::make('daisy::components.ui.data-display.table', [
        'columns' => [
            ['label' => 'Missing key'],
        ],
        'rows' => [
            ['name' => 'Jane'],
        ],
    ])->render();

    expect($render)->toThrow(ViewException::class, 'at least one column with a non-empty key');
});

it('keeps the legacy datatable alias only as an explicit migration error', function () {
    $render = fn () => Blade::render('<x-daisy::ui.data-display.datatable />');

    expect(View::exists('daisy::components.ui.data-display.table'))->toBeTrue()
        ->and($render)->toThrow(ViewException::class, 'x-daisy::ui.data-display.datatable')
        ->and(View::exists('daisy::components.ui.advanced.table'))->toBeFalse();
});

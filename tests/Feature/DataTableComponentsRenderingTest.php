<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;

beforeEach(function (): void {
    View::addNamespace('table-test', dirname(__DIR__).'/Fixtures/views');
});

function decodeTableConfig(string $html): array
{
    preg_match("/data-table-config='([^']+)'/", $html, $matches);

    return json_decode(html_entity_decode($matches[1] ?? '{}', ENT_QUOTES), true, flags: JSON_THROW_ON_ERROR);
}

it('renders a client table with DaisyUI classes and serialized config', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="client"
            size="sm"
            zebra
            hover
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
        ->toContain('data-module="table"')
        ->toContain('data-daisy-table="1"')
        ->toContain('data-table-layout="auto"')
        ->toContain('table table-zebra daisy-table-row-hover table-sm table-pin-rows table-pin-cols table-auto w-full')
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

it('places a consumer table identifier on the Daisy table root', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            id="scope-users"
            class="consumer-table"
            aria-describedby="scope-users-caption"
            data-consumer-table="users"
            :columns="[['key' => 'name', 'label' => 'Name']]"
            :rows="[['name' => 'Ada']]"
        />
    BLADE);

    expect($html)
        ->toMatch('/<div\b[^>]*\bid="scope-users"/s')
        ->toContain('data-daisy-table="1"')
        ->toMatch('/<table\b[^>]*class="[^"]*consumer-table[^"]*"[^>]*aria-describedby="scope-users-caption"[^>]*data-consumer-table="users"/s')
        ->not->toMatch('/<div\b[^>]*aria-describedby="scope-users-caption"/s')
        ->not->toContain('<table id="scope-users"');
});

it('rejects missing and duplicate client row keys before rendering structured actions', function (): void {
    $missingKey = fn (): string => Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            :columns="[['key' => 'actions', 'label' => 'Actions', 'type' => 'actions']]"
            :rows="[['actions' => ['action' => 'open']]]"
        />
    BLADE);
    $duplicateNestedKey = fn (): string => Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            sub-rows-key="children"
            :columns="[['key' => 'name', 'label' => 'Name']]"
            :rows="[
                ['id' => 'parent', 'name' => 'Parent', 'children' => [['id' => 'child', 'name' => 'First']]],
                ['id' => 'other', 'name' => 'Other', 'children' => [['id' => 'child', 'name' => 'Duplicate']]],
            ]"
        />
    BLADE);

    expect($missingKey)->toThrow(ViewException::class, 'non-empty id')
        ->and($duplicateNestedKey)->toThrow(ViewException::class, 'Duplicate value: child');
});

it('renders configurable table layout, scroll and native action column attributes', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            table-layout="auto"
            min-width="128rem"
            scroll-x="always"
            external-filters
            livewire-mode="ignore"
            :columns="[
                ['key' => '_action', 'label' => 'Actions', 'type' => 'actions'],
                ['key' => 'status_badge', 'label' => 'Status', 'align' => 'center', 'width' => '140px', 'cell' => ['renderer' => 'trusted-html']],
                ['key' => 'postal_address', 'label' => 'Address', 'truncate' => 2, 'width' => '260px', 'minWidth' => 'max-content', 'nowrap' => true],
            ]"
            :rows="[
                ['id' => 'intervention-1', '_action' => ['action' => 'open', 'label' => 'Open'], 'status_badge' => '<span class=&quot;badge&quot;>Open</span>', 'postal_address' => '12 rue longue'],
            ]"
            :filters="[
                ['key' => 'status', 'label' => 'Status', 'type' => 'text'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('data-table-layout="auto"')
        ->toContain('data-table-scroll-x="always"')
        ->toContain('data-table-livewire-mode="ignore"')
        ->toContain('data-table-min-width="128rem"')
        ->toContain('wire:ignore')
        ->toContain('daisy-table-scroll-always overflow-x-scroll')
        ->toContain('daisy-table-root-min-width-rem-512')
        ->toContain('daisy-table-width-fit')
        ->toContain('daisy-table-actions-cell')
        ->toContain('daisy-table-actions-content')
        ->toContain('text-center')
        ->toContain('daisy-table-width-px-140')
        ->toContain('daisy-table-width-px-260')
        ->toContain('daisy-table-min-width-max')
        ->toContain('whitespace-nowrap')
        ->toContain('line-clamp-2')
        ->toContain('"externalFilters":true')
        ->toContain('"livewireMode":"ignore"')
        ->not->toContain('table-auto w-full daisy-table-min-width-rem-512')
        ->not->toContain('width:1%')
        ->not->toContain('daisy-table-filters')
        ->not->toContain('data-table-filter="status"');

    preg_match('/<table[^>]+class="([^"]+)"/', $html, $tableClassMatches);
    preg_match('/<colgroup[^>]*>(.*?)<\/colgroup>/s', $html, $colgroupMatches);

    expect($tableClassMatches[1] ?? '')
        ->toContain('daisy-table-root-min-width-rem-512')
        ->not->toContain('daisy-table-min-width-rem-512');

    expect($colgroupMatches[1] ?? '')
        ->toContain('daisy-table-width-px-140')
        ->toContain('daisy-table-width-px-260')
        ->not->toContain('daisy-table-root-min-width-rem-512')
        ->not->toContain('daisy-table-min-width-rem-512');
});

it('provides a content-width table helper for wide containers', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            table-class="daisy-table-width-content"
            row-key="id"
            :columns="[
                ['key' => '_action', 'label' => 'Actions', 'type' => 'actions'],
                ['key' => 'name', 'label' => 'Name'],
            ]"
            :rows="[
                ['id' => 'user-1', '_action' => ['action' => 'open', 'label' => 'Open'], 'name' => 'Jane'],
            ]"
        />
    BLADE);

    $css = file_get_contents(dirname(__DIR__, 2).'/resources/css/table.css');

    expect($html)
        ->toContain('table table-auto daisy-table-width-content')
        ->toContain('daisy-table-width-fit')
        ->and($css)
        ->toContain('table.daisy-table-width-content')
        ->toContain('width: max-content')
        ->toContain('margin-inline: auto');
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
        ->toContain('daisy-table-controls flex flex-wrap items-center justify-start gap-3 lg:justify-end')
        ->toContain('daisy-table-filters rounded-box grid grid-cols-1 gap-3 border border-base-content/10 bg-base-200/40 p-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 lg:col-span-2')
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

    expect(strpos($html, 'data-table-search'))->toBeLessThan(strpos($html, 'data-table-page-size'))
        ->and(strpos($html, 'data-table-page-size'))->toBeLessThan(strpos($html, 'data-table-column-menu'))
        ->and(strpos($html, 'data-table-column-menu'))->toBeLessThan(strpos($html, 'data-table-filter="reference_internal"'))
        ->and(strpos($html, 'data-table-filter="reference_internal"'))->toBeLessThan(strpos($html, 'data-table-filter="name"'))
        ->and(strpos($html, 'data-table-filter="name"'))->toBeLessThan(strpos($html, 'data-table-filter="city"'))
        ->and(strpos($html, 'data-table-filter="city"'))->toBeLessThan(strpos($html, 'data-table-filter="company"'))
        ->and(strpos($html, 'data-table-filter="company"'))->toBeLessThan(strpos($html, 'data-table-filter="compile_status"'))
        ->and(strpos($html, 'data-table-filter="compile_status"'))->toBeLessThan(strpos($html, 'data-table-filter="intervention_type_code"'))
        ->and(strpos($html, 'data-table-filter="intervention_type_code"'))->toBeLessThan(strpos($html, 'data-table-filter="external_note"'))
        ->and(strpos($html, 'data-table-filter="external_note"'))->toBeGreaterThan(strpos($html, 'data-table-column-menu'));
});

it('keeps table filter configuration separate from the external filters slot', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="server"
            endpoint="/interventions"
            :columns="[
                ['key' => 'name', 'label' => 'Name'],
            ]"
            :filters="[
                ['key' => 'reference_internal', 'label' => 'Reference', 'type' => 'text'],
            ]"
        >
            <x-slot:filtersSlot>
                <div data-external-filters>External filters</div>
            </x-slot:filtersSlot>
        </x-daisy::ui.data-display.table>
    BLADE);

    expect($html)
        ->toContain('data-external-filters')
        ->toContain('data-table-filter="reference_internal"')
        ->not->toContain('Array');
});

it('renders a client table with trusted html cells', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[
                ['key' => 'status', 'label' => 'Status', 'cell' => ['renderer' => 'trusted-html']],
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

it('serializes custom cell renderers and renders blade cells in client rows', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'actions', 'label' => 'Actions', 'type' => 'actions', 'view' => 'table-test::table.actions'],
                ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
            ]"
            :rows="[
                ['id' => 1, 'name' => 'Jane', 'actions' => 'open', 'profile' => ['label' => 'Profile', 'href' => 'https://example.test/users/1', 'target' => '_blank']],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('"renderer":"blade"')
        ->toContain('"view":"table-test::table.actions"')
        ->toContain('"renderer":"link"')
        ->toContain('data-value="open"')
        ->toContain('Jane actions');
});

it('escapes unsafe resource link hrefs during initial blade render', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :columns="[
                ['key' => 'profile', 'label' => 'Profile', 'type' => 'resource-link'],
            ]"
            :rows="[
                ['profile' => ['label' => '<Open>', 'href' => 'javascript:alert(1)', 'target' => '_blank']],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('&lt;Open&gt;')
        ->not->toContain('<a href=');
});

it('supports deeplink resource links through table and column policies', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :link-policy="['allowedSchemes' => ['myapp']]"
            :columns="[
                ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
                ['key' => 'scan', 'label' => 'Scan', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['intent']]],
            ]"
            :rows="[
                ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123', 'target' => '_blank'], 'scan' => ['label' => 'Scan', 'href' => 'intent://scan/#Intent;scheme=zxing;end']],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('href="myapp://ticket/123"')
        ->toContain('rel="noopener noreferrer"')
        ->toContain('href="intent://scan/#Intent;scheme=zxing;end"')
        ->toContain('"linkPolicy":{"allowedSchemes":["myapp"]}')
        ->toContain('"allowedSchemes":["intent"]');
});

it('does not allow deeplinks without policy or dangerous schemes with policy', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            :link-policy="['allowedSchemes' => ['javascript']]"
            :columns="[
                ['key' => 'mobile', 'label' => 'Mobile', 'type' => 'resource-link'],
                ['key' => 'danger', 'label' => 'Danger', 'type' => 'resource-link', 'cell' => ['allowedSchemes' => ['javascript']]],
            ]"
            :rows="[
                ['mobile' => ['label' => 'Open app', 'href' => 'myapp://ticket/123'], 'danger' => ['label' => '<Bad>', 'href' => 'javascript:alert(1)']],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('Open app')
        ->toContain('&lt;Bad&gt;')
        ->not->toContain('href="myapp://ticket/123"')
        ->not->toContain('href="javascript:alert(1)"')
        ->not->toContain('"allowedSchemes":["javascript"]');
});

it('renders date filters and row detail configuration', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="server"
            endpoint="/audits"
            row-key="id"
            row-detail="modal"
            row-detail-view="table-test::table.actions"
            column-resizing
            :columns="[
                ['key' => 'created_at', 'label' => 'Created', 'filterable' => true, 'filter' => ['type' => 'date']],
            ]"
            :filters="[
                ['key' => 'period', 'label' => 'Period', 'type' => 'date-range', 'filterKeyFrom' => 'started_after', 'filterKeyTo' => 'started_before'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('data-table-filter-type="date"')
        ->toContain('data-table-filter-type="date-range"')
        ->toContain('data-table-filter-bound="from"')
        ->toContain('"rowDetail":{"mode":"modal","view":"table-test::table.actions"}')
        ->toContain('"columnResizing":true')
        ->toContain('"filterKeyFrom":"started_after"')
        ->toContain('"filterKeyTo":"started_before"');
});

it('renders row detail blade views into client row data', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            row-detail="inline"
            row-detail-view="table-test::table.detail"
            :columns="[
                ['key' => 'name', 'label' => 'Name'],
            ]"
            :rows="[
                ['id' => 'user-1', 'name' => 'Jane'],
            ]"
        />
    BLADE);
    $config = decodeTableConfig($html);

    expect($config['rows'][0]['_detailHtml'])->toBe('<aside data-row-detail="user-1">Jane Table</aside>')
        ->and($html)->toContain('data-table-row-detail="user-1"');
});

it('serializes TanStack-first search, sub rows, resizing and editable options', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="client"
            row-key="id"
            search-mode="includes"
            sub-rows-key="children"
            column-resizing
            editable
            edit-endpoint="/users/edit"
            edit-method="PUT"
            edit-mode="row"
            :editable-columns="['name']"
            :edit-policy="['required' => ['name']]"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'size' => 160, 'minSize' => 80, 'maxSize' => 320],
                ['key' => 'status', 'label' => 'Status'],
            ]"
            :rows="[
                ['id' => 'user-1', 'name' => 'Jane', 'status' => 'draft', 'children' => []],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('"rowKey":"id"')
        ->toContain('"searchMode":"includes"')
        ->toContain('"subRowsKey":"children"')
        ->toContain('"columnResizing":true')
        ->toContain('"size":160')
        ->toContain('"minSize":80')
        ->toContain('"maxSize":320')
        ->toContain('"editable":{"enabled":true,"endpoint":{"url":"\/users\/edit"},"method":"PUT","mode":"row","columns":["name"],"policy":{"required":["name"]},"update":{"strategy":"remote"')
        ->toContain('data-table-resize="name"')
        ->toContain('data-table-edit-cell')
        ->toContain('data-table-row-id="user-1"')
        ->toContain('data-table-column-id="name"')
        ->not->toContain('data-table-column-id="status"');
});

it('keeps Blade serialized config compatible with the shared TanStack fixture', function () {
    $fixture = json_decode(file_get_contents(dirname(__DIR__).'/Fixtures/table-config/tanstack-first.json'), true, flags: JSON_THROW_ON_ERROR);
    $html = View::make('daisy::components.ui.data-display.table', [
        'mode' => $fixture['mode'],
        'rowKey' => $fixture['rowKey'],
        'searchMode' => $fixture['searchMode'],
        'subRowsKey' => $fixture['subRowsKey'],
        'columnResizing' => $fixture['columnResizing'],
        'editable' => true,
        'editEndpoint' => $fixture['editable']['endpoint']['url'],
        'editMethod' => $fixture['editable']['method'],
        'editMode' => $fixture['editable']['mode'],
        'editableColumns' => $fixture['editable']['columns'],
        'editPolicy' => $fixture['editable']['policy'],
        'columns' => $fixture['columns'],
        'rows' => $fixture['rows'],
        'initialState' => $fixture['initialState'],
        'pageSizeOptions' => $fixture['pageSizeOptions'],
    ])->render();
    $config = decodeTableConfig($html);

    expect($config)
        ->toMatchArray([
            'mode' => $fixture['mode'],
            'rowKey' => $fixture['rowKey'],
            'searchMode' => $fixture['searchMode'],
            'subRowsKey' => $fixture['subRowsKey'],
            'columnResizing' => true,
            'editable' => [
                'enabled' => true,
                'endpoint' => ['url' => $fixture['editable']['endpoint']['url']],
                'method' => $fixture['editable']['method'],
                'mode' => $fixture['editable']['mode'],
                'columns' => $fixture['editable']['columns'],
                'policy' => $fixture['editable']['policy'],
                'update' => [
                    'strategy' => 'remote',
                    'endpoint' => ['url' => $fixture['editable']['endpoint']['url']],
                    'method' => $fixture['editable']['method'],
                ],
                'create' => [
                    'enabled' => false,
                    'strategy' => 'remote',
                    'endpoint' => null,
                    'method' => 'POST',
                    'defaults' => [],
                    'position' => 'top',
                ],
                'rowKey' => $fixture['rowKey'],
            ],
        ])
        ->and($config['columns'][0])
        ->toMatchArray([
            'key' => 'name',
            'size' => 180,
            'minSize' => 120,
            'maxSize' => 320,
        ])
        ->and($config['initialState']['columnSizing'])->toEqual(['name' => 200]);
});

it('fails clearly when a custom blade cell view is missing', function () {
    $render = fn () => Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            mode="server"
            endpoint="/audits"
            :columns="[
                ['key' => 'actions', 'label' => 'Actions', 'view' => 'table-test::missing'],
            ]"
        />
    BLADE);

    expect($render)->toThrow(ViewException::class, 'Daisy table cell view [table-test::missing] does not exist.');
});

it('fails clearly when a row detail blade view is missing', function () {
    $render = fn () => Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            row-detail="inline"
            row-detail-view="table-test::missing"
            :columns="[
                ['key' => 'name', 'label' => 'Name'],
            ]"
            :rows="[
                ['id' => 'user-1', 'name' => 'Jane'],
            ]"
        />
    BLADE);

    expect($render)->toThrow(ViewException::class, 'Daisy table row detail view [table-test::missing] does not exist.');
});

it('renders row selection controls and deferred bulk actions', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            selection="multiple"
            row-key="uuid"
            :columns="[
                ['key' => 'name', 'label' => 'Name'],
            ]"
            :rows="[
                ['uuid' => 'user-1', 'name' => 'Jane'],
                ['uuid' => 'user-2', 'name' => 'John'],
            ]"
        >
            <x-slot:bulkActions>
                <button type="button" data-table-bulk-action="archive">Archive</button>
            </x-slot:bulkActions>
        </x-daisy::ui.data-display.table>
    BLADE);

    expect($html)
        ->toContain('"selection":{"enabled":true,"mode":"multiple","rowKey":"uuid"')
        ->toContain('data-table-select-page')
        ->toContain('data-table-row-select="user-1"')
        ->toContain('data-table-row-select="user-2"')
        ->toContain('data-table-selection-feedback')
        ->toContain('daisy-table-selection-bar flex flex-col items-stretch gap-3')
        ->toContain('sm:flex-row sm:items-center sm:justify-between')
        ->toContain('btn btn-xs btn-ghost justify-center')
        ->toContain('data-table-bulk-actions')
        ->toContain('sm:flex-row sm:flex-wrap sm:items-center sm:justify-end')
        ->toContain('data-table-bulk-action="archive"');
});

it('can hide filtered selection when a host submits explicit selected ids', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            selection="multiple"
            row-key="uuid"
            :select-filtered="false"
            :columns="[
                ['key' => 'name', 'label' => 'Name'],
            ]"
            :rows="[
                ['uuid' => 'user-1', 'name' => 'Jane'],
            ]"
        />
    BLADE);

    expect($html)
        ->toContain('"selectFiltered":false')
        ->not->toContain('data-table-select-filtered');
});

it('requires a row key when table selection is enabled', function () {
    $render = fn () => View::make('daisy::components.ui.data-display.table', [
        'selection' => 'multiple',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
    ])->render();

    expect($render)->toThrow(ViewException::class, 'rowKey prop when selection is enabled');
});

it('requires a row key for row detail, sub rows and editable rows', function () {
    $rowDetailRender = fn () => View::make('daisy::components.ui.data-display.table', [
        'rowDetail' => 'inline',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [
            ['name' => 'Jane'],
        ],
    ])->render();
    $subRowsRender = fn () => View::make('daisy::components.ui.data-display.table', [
        'subRowsKey' => 'children',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [
            ['name' => 'Jane', 'children' => []],
        ],
    ])->render();
    $editableRender = fn () => View::make('daisy::components.ui.data-display.table', [
        'editable' => true,
        'editEndpoint' => '/users/edit',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [
            ['name' => 'Jane'],
        ],
    ])->render();

    expect($rowDetailRender)->toThrow(ViewException::class, 'rowKey prop for row details, sub rows, or editable rows')
        ->and($subRowsRender)->toThrow(ViewException::class, 'rowKey prop for row details, sub rows, or editable rows')
        ->and($editableRender)->toThrow(ViewException::class, 'rowKey prop for row details, sub rows, or editable rows');
});

it('requires an edit endpoint when editable rows are enabled', function () {
    $render = fn () => View::make('daisy::components.ui.data-display.table', [
        'editable' => true,
        'rowKey' => 'id',
        'columns' => [
            ['key' => 'name', 'label' => 'Name'],
        ],
        'rows' => [
            ['id' => 'user-1', 'name' => 'Jane'],
        ],
    ])->render();

    expect($render)->toThrow(ViewException::class, 'editEndpoint prop when editable is enabled');
});

it('serializes typed editors and remote row creation', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            :editable="[
                'enabled' => true,
                'mode' => 'row',
                'update' => ['strategy' => 'local'],
                'create' => [
                    'enabled' => true,
                    'strategy' => 'remote',
                    'endpoint' => ['url' => '/projects', 'method' => 'POST'],
                    'defaults' => ['status' => 'draft'],
                ],
            ]"
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'editor' => ['type' => 'text', 'required' => true]],
                ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'select', 'options' => [['value' => 'draft', 'label' => 'Draft']]]],
            ]"
        />
    BLADE);

    $config = decodeTableConfig($html);

    expect($config['editable'])->toMatchArray([
        'update' => ['strategy' => 'local', 'endpoint' => null, 'method' => 'PATCH'],
        'create' => [
            'enabled' => true,
            'strategy' => 'remote',
            'endpoint' => ['url' => '/projects', 'method' => 'POST'],
            'method' => 'POST',
            'defaults' => ['status' => 'draft'],
            'position' => 'top',
        ],
    ])
        ->and($config['columns'][0]['editor'])->toMatchArray(['type' => 'text', 'required' => true])
        ->and($html)->toContain('data-table-create');
});

it('renders trusted Blade editor templates into the table configuration', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table
            row-key="id"
            :editable="[
                'enabled' => true,
                'update' => ['strategy' => 'local'],
            ]"
            :columns="[
                ['key' => 'status', 'label' => 'Status', 'editor' => ['type' => 'blade', 'view' => 'table-test::table.editor']],
            ]"
        />
    BLADE);

    $config = decodeTableConfig($html);

    expect($config['columns'][0]['editor'])
        ->toMatchArray(['type' => 'blade', 'view' => 'table-test::table.editor'])
        ->and($config['columns'][0]['editor']['template'])->toContain('data-table-editor-input');
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

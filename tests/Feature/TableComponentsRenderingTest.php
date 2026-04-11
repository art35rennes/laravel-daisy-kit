<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\LengthAwarePaginator;

it('renders structured slots in the base table component', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.data-display.table size="sm" zebra pin-rows pin-cols caption="Users">
            <x-slot:head>
                <tr><th>Name</th><th>Role</th></tr>
            </x-slot:head>
            <x-slot:body>
                <tr><td>Jane</td><td>Admin</td></tr>
            </x-slot:body>
            <x-slot:foot>
                <tr><th colspan="2">Summary</th></tr>
            </x-slot:foot>
        </x-daisy::ui.data-display.table>
    BLADE);

    expect($html)
        ->toContain('table table-zebra table-sm table-pin-rows table-pin-cols')
        ->toContain('<thead>')
        ->toContain('<tbody>')
        ->toContain('<tfoot>')
        ->toContain('Users');
});

it('renders the base table without the responsive wrapper when disabled', function () {
    $html = View::make('daisy::components.ui.data-display.table', [
        'responsive' => false,
        'containerClass' => 'custom-shell',
        'slot' => new \Illuminate\Support\HtmlString('<tbody><tr><td>Cell</td></tr></tbody>'),
    ])->render();

    expect($html)
        ->toContain('custom-shell')
        ->toContain('<tbody><tr><td>Cell</td></tr></tbody>')
        ->not->toContain('overflow-x-auto');
});

it('renders simple table server pagination controls', function () {
    request()->merge([
        'filter' => ['status' => 'active'],
    ]);

    $paginator = new LengthAwarePaginator(
        items: collect([['name' => 'Jane']]),
        total: 30,
        perPage: 10,
        currentPage: 2,
        options: [
            'path' => 'http://localhost/users',
            'pageName' => 'page',
        ],
    );

    $html = View::make('daisy::components.ui.data-display.table', [
        'paginationMode' => 'server',
        'paginator' => $paginator,
        'perPageOptions' => [10, 25, 50],
        'slot' => new \Illuminate\Support\HtmlString('<tbody><tr><td>Jane</td></tr></tbody>'),
    ])->render();

    expect($html)
        ->toContain('Rows per page')
        ->toContain('filter%5Bstatus%5D=active&amp;page=1')
        ->toContain('data-simple-table-root');
});

it('renders simple table client pagination controls', function () {
    $html = View::make('daisy::components.ui.data-display.table', [
        'paginationMode' => 'client',
        'perPage' => 5,
        'perPageOptions' => [5, 10],
        'slot' => new \Illuminate\Support\HtmlString('<tbody><tr><td>Jane</td></tr><tr><td>John</td></tr></tbody>'),
    ])->render();

    expect($html)
        ->toContain('data-simple-table="1"')
        ->toContain('data-table-pagination-mode="client"')
        ->toContain('data-table-page-size="5"')
        ->toContain('data-table-page-info')
        ->toContain('data-table-page-size-select');
});

it('renders the advanced table with toolbar and after-table slots', function () {
    $html = Blade::render(<<<'BLADE'
        <x-daisy::ui.advanced.table
            :columns="[
                ['key' => 'name', 'label' => 'Name', 'rowHeader' => true],
                ['key' => 'email', 'label' => 'Email', 'align' => 'end'],
            ]"
            :rows="[
                ['id' => 1, 'name' => 'Jane', 'email' => 'jane@example.com'],
            ]"
            selectable="single"
            :selected="[1]"
        >
            <x-slot:toolbar>
                <div>Toolbar</div>
            </x-slot:toolbar>
            <x-slot:afterTable>
                <div>After table</div>
            </x-slot:afterTable>
        </x-daisy::ui.advanced.table>
    BLADE);

    expect($html)
        ->toContain('Toolbar')
        ->toContain('After table')
        ->toContain('type="radio"')
        ->toContain('aria-selected="true"')
        ->toContain('text-right');
});

it('renders html cells in the advanced table when enabled per column', function () {
    $html = View::make('daisy::components.ui.advanced.table', [
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'html' => true],
        ],
        'rows' => [
            ['id' => 1, 'name' => '<strong>Jane</strong>'],
        ],
    ])->render();

    expect($html)->toContain('<strong>Jane</strong>');
});

it('renders pagination and per-page controls for advanced table', function () {
    request()->merge([
        'filter' => ['status' => 'active'],
        'sort' => 'name',
    ]);

    $paginator = new LengthAwarePaginator(
        items: collect([
            ['id' => 1, 'name' => 'Jane'],
            ['id' => 2, 'name' => 'John'],
        ]),
        total: 42,
        perPage: 10,
        currentPage: 2,
        options: [
            'path' => 'http://localhost/users',
            'pageName' => 'page',
        ],
    );

    $html = View::make('daisy::components.ui.advanced.table', [
        'mode' => 'server',
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'sortable' => true],
        ],
        'rows' => $paginator->items(),
        'paginator' => $paginator,
        'perPageOptions' => [10, 25, 50],
        'selectable' => 'multiple',
        'selected' => [1, 2],
    ])->render();

    expect($html)
        ->toContain('Rows per page')
        ->toContain('Showing 11 to 12 of 42 results')
        ->toContain('2 selected')
        ->toContain('name="per_page"')
        ->toContain('value="active"')
        ->toContain('filter%5Bstatus%5D=active&amp;sort=name&amp;page=1')
        ->toContain('filter%5Bstatus%5D=active&amp;sort=name&amp;page=3');
});

it('renders query builder controls natively for advanced table', function () {
    request()->merge([
        'filter' => [
            'search' => 'Jane',
            'status' => 'ready',
        ],
        'sort' => '-name',
        'include' => 'company',
    ]);

    $paginator = new LengthAwarePaginator(
        items: collect([
            ['id' => 1, 'name' => 'Jane', 'status' => 'ready'],
        ]),
        total: 1,
        perPage: 10,
        currentPage: 1,
        options: [
            'path' => 'http://localhost/users',
            'pageName' => 'page',
        ],
    );

    $html = View::make('daisy::components.ui.advanced.table', [
        'queryBuilder' => true,
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'filterable' => true, 'filterOptions' => ['ready' => 'Ready', 'draft' => 'Draft']],
        ],
        'searchable' => true,
        'rows' => $paginator->items(),
        'paginator' => $paginator,
        'sortBy' => 'name',
        'sortDirection' => 'desc',
    ])->render();

    expect($html)
        ->toContain('name="filter[search]"')
        ->toContain('value="Jane"')
        ->toContain('name="filter[status]"')
        ->toContain('value="company"')
        ->toContain('?filter%5Bsearch%5D=Jane&amp;filter%5Bstatus%5D=ready&amp;sort=name&amp;include=company');
});

it('renders advanced table in client mode with search filters and pagination controls', function () {
    $html = View::make('daisy::components.ui.advanced.table', [
        'mode' => 'client',
        'searchable' => true,
        'perPage' => 5,
        'perPageOptions' => [5, 10],
        'columns' => [
            ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true],
            ['key' => 'status', 'label' => 'Status', 'filterable' => true, 'filterOptions' => ['ready' => 'Ready']],
        ],
        'rows' => [
            ['id' => 1, 'name' => 'Jane', 'status' => 'ready'],
            ['id' => 2, 'name' => 'John', 'status' => 'ready'],
        ],
    ])->render();

    expect($html)
        ->toContain('data-table-mode="client"')
        ->toContain('data-client-sort-key="name"')
        ->toContain('data-table-search')
        ->toContain('data-column-filter="status"')
        ->toContain('data-table-page-indicator')
        ->toContain('data-value="Jane"');
});

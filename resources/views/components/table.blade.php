@props([
    'columns' => [],
    'rows' => [],
    'pageSize' => 10,
    'selectable' => false,
    'source' => null,
    'bulkActions' => [],
    'rowActions' => [],
    'rowDetails' => null,
    'editable' => false,
    'persistence' => null,
    'initialState' => [],
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'columns' => $columns,
        'rows' => $rows,
        'pageSize' => $pageSize,
        'selectable' => $selectable,
        'source' => $source,
        'bulkActions' => $bulkActions,
        'rowActions' => $rowActions,
        'rowDetails' => $rowDetails,
        'editable' => $editable,
        'persistence' => $persistence,
        'initialState' => $initialState,
    ]);
@endphp

<section {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['daisy-kit-table', 'card', 'border', 'border-base-300', 'bg-base-100', 'p-4', 'shadow-sm']) }} aria-busy="true" data-daisy-kit-module="table">
    <p class="alert alert-info" data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div data-daisy-kit-content>
        <label class="daisy-kit-table__filter">
            <span>Filter table</span>
            <input class="input input-bordered w-full" data-daisy-kit-table-filter type="search" autocomplete="off">
        </label>

        <div class="daisy-kit-table__scroll">
            <table class="table table-zebra" data-daisy-kit-table aria-busy="true">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>

        <nav class="daisy-kit-table__pagination" aria-label="Table pagination">
            <button class="btn btn-sm" data-daisy-kit-table-previous type="button">Previous page</button>
            <span class="badge badge-outline" data-daisy-kit-table-page aria-live="polite"></span>
            <button class="btn btn-sm" data-daisy-kit-table-next type="button">Next page</button>
        </nav>
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>

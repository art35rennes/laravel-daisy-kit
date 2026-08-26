@props([
    'columns' => [],
    'rows' => [],
    'pageSize' => 10,
])

@php
    $configuration = \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'columns' => $columns,
        'rows' => $rows,
        'pageSize' => $pageSize,
    ]);
@endphp

<section {{ $attributes->only(['id', 'class', 'aria-describedby'])->class(['daisy-kit-table']) }} aria-busy="true" data-daisy-kit-module="table">
    <p data-daisy-kit-status hidden role="status" aria-live="polite"></p>

    <div data-daisy-kit-content>
        <label class="daisy-kit-table__filter">
            <span>Filter table</span>
            <input data-daisy-kit-table-filter type="search" autocomplete="off">
        </label>

        <div class="daisy-kit-table__scroll">
            <table data-daisy-kit-table aria-busy="true">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>

        <nav class="daisy-kit-table__pagination" aria-label="Table pagination">
            <button data-daisy-kit-table-previous type="button">Previous page</button>
            <span data-daisy-kit-table-page aria-live="polite"></span>
            <button data-daisy-kit-table-next type="button">Next page</button>
        </nav>
    </div>

    <script data-daisy-kit-config type="application/json">{!! $configuration !!}</script>
</section>

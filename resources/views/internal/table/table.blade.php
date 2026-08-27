<div class="daisy-kit-table__scroll" tabindex="0" role="region" aria-label="{{ $tableView['labels']['scrollableTable'] }}">
    <p class="daisy-kit-table__scroll-hint bg-base-200 text-base-content" aria-hidden="true">{{ $tableView['labels']['scrollHint'] }}</p>
    <table @class([
        'table',
        "table-{$tableView['size']}" => $tableView['size'] !== 'md',
        'table-zebra' => $tableView['zebra'],
        'daisy-kit-table--hover' => $tableView['hover'],
    ]) data-daisy-kit-table aria-busy="true">
        @if ($tableView['caption'])
            <caption>{{ $tableView['caption'] }}</caption>
        @endif
        <thead></thead>
        <tbody></tbody>
    </table>
</div>

<div class="daisy-kit-table__scroll" tabindex="0" role="region" aria-label="{{ __('Scrollable table') }}">
    <p class="daisy-kit-table__scroll-hint" aria-hidden="true">{{ __('Scroll horizontally to see every column') }}</p>
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

@if($tableView['selection']['mode'] !== 'none')
    <aside
        class="daisy-kit-table__selection border border-primary/30 bg-primary/10"
        data-daisy-kit-table-selection
        @if($tableView['selection']['mode'] === 'single') hidden @endif
        aria-live="polite"
    >
        <div class="daisy-kit-table__selection-summary" data-daisy-kit-table-selection-summary @if($tableView['selection']['summaryVisibility'] === 'after-first-selection') hidden @endif>
            <p><strong data-daisy-kit-table-selection-count>0</strong> {{ $tableView['labels']['rowsSelected'] }}</p>
            <p class="daisy-kit-table__selection-breakdown" data-daisy-kit-table-selection-breakdown hidden>
                <strong data-daisy-kit-table-selection-page-count>0</strong> {{ $tableView['labels']['onThisPage'] }}
                <span aria-hidden="true">·</span>
                <strong data-daisy-kit-table-selection-off-page-count>0</strong> {{ $tableView['labels']['outsideThisPage'] }}
            </p>
        </div>

        @if($tableView['selection']['mode'] === 'multiple')
            <div class="daisy-kit-table__selection-controls">
                <button class="btn btn-outline btn-sm" data-daisy-kit-table-select-page type="button">{{ $tableView['labels']['selectPage'] }}</button>
                @if($tableView['selection']['selectFiltered'])
                    <button class="btn btn-outline btn-sm" data-daisy-kit-table-select-filtered type="button">{{ str_replace(':count', '', $tableView['labels']['selectAllResults']) }}</button>
                @endif
                <button class="btn btn-ghost btn-sm" data-daisy-kit-table-clear-selection type="button" disabled>{{ $tableView['labels']['clearSelection'] }}</button>
            </div>
        @endif

        <div class="daisy-kit-table__bulk-actions" data-daisy-kit-table-bulk-actions></div>
    </aside>
@endif

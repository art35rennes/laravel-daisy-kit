@if($tableView['selection']['mode'] !== 'none')
    <aside
        class="daisy-kit-table__selection"
        data-daisy-kit-table-selection
        @if($tableView['selection']['mode'] === 'single') hidden @endif
        aria-live="polite"
    >
        <div class="daisy-kit-table__selection-summary">
            <p><strong data-daisy-kit-table-selection-count>0</strong> {{ __('rows selected') }}</p>
            <p class="daisy-kit-table__selection-breakdown" data-daisy-kit-table-selection-breakdown hidden>
                <strong data-daisy-kit-table-selection-page-count>0</strong> {{ __('on this page') }}
                <span aria-hidden="true">·</span>
                <strong data-daisy-kit-table-selection-off-page-count>0</strong> {{ __('outside this page') }}
            </p>
        </div>

        @if($tableView['selection']['mode'] === 'multiple')
            <div class="daisy-kit-table__selection-controls">
                <button class="btn btn-outline btn-sm" data-daisy-kit-table-select-page type="button">{{ __('Select this page') }}</button>
                @if($tableView['selection']['selectFiltered'])
                    <button class="btn btn-outline btn-sm" data-daisy-kit-table-select-filtered type="button">{{ __('Select all results') }}</button>
                @endif
                <button class="btn btn-ghost btn-sm" data-daisy-kit-table-clear-selection type="button" disabled>{{ __('Clear selection') }}</button>
            </div>
        @endif

        <div class="daisy-kit-table__bulk-actions" data-daisy-kit-table-bulk-actions></div>
    </aside>
@endif

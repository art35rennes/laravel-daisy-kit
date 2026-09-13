<div class="daisy-kit-table__toolbar" role="group" aria-label="{{ $tableView['labels']['tableControls'] }}">
    @if ($tableView['search']['enabled'])
        <label class="daisy-kit-table__search form-control">
            <span class="label-text">{{ $tableView['labels']['search'] }}</span>
            <input
                class="input input-bordered input-sm w-full"
                data-daisy-kit-table-filter
                type="search"
                autocomplete="off"
                placeholder="{{ $tableView['labels']['searchPlaceholder'] }}"
            >
        </label>
    @else
        <input data-daisy-kit-table-filter type="search" hidden aria-hidden="true" tabindex="-1">
    @endif

    <label class="daisy-kit-table__page-size form-control">
        <span class="label-text">{{ $tableView['labels']['rowsPerPage'] }}</span>
        <select class="select select-bordered select-sm" data-daisy-kit-table-page-size>
            @foreach ($tableView['pageSizeOptions'] as $option)
                <option value="{{ $option }}" @selected($option === $tableView['pageSize'])>{{ $option }}</option>
            @endforeach
        </select>
    </label>

    @if ($tableView['columnVisibility'])
        <details class="daisy-kit-table__columns dropdown dropdown-end">
            <summary class="btn btn-outline btn-sm">{{ $tableView['labels']['columns'] }}</summary>
            <fieldset class="daisy-kit-table__column-controls dropdown-content fieldset rounded-box border border-base-300 bg-base-100 p-3 shadow-lg" data-daisy-kit-table-column-controls>
                <legend class="sr-only">{{ $tableView['labels']['visibleColumns'] }}</legend>
            </fieldset>
        </details>
    @endif
</div>

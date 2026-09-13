<footer class="daisy-kit-table__footer">
    <p class="daisy-kit-table__results text-base-content/70" data-daisy-kit-table-results role="status" aria-live="polite"></p>

    <nav class="daisy-kit-table__pagination" aria-label="{{ $tableView['labels']['pagination'] }}">
        <button class="btn btn-ghost btn-sm" data-daisy-kit-table-previous type="button">
            <span aria-hidden="true">&larr;</span>
            <span>{{ $tableView['labels']['previous'] }}</span>
        </button>
        <span class="daisy-kit-table__page-status text-base-content/70" data-daisy-kit-table-page data-daisy-kit-table-page-status aria-live="polite"></span>
        <button class="btn btn-ghost btn-sm" data-daisy-kit-table-next type="button">
            <span>{{ $tableView['labels']['next'] }}</span>
            <span aria-hidden="true">&rarr;</span>
        </button>
    </nav>
</footer>

<footer class="daisy-kit-table__footer">
    <p class="daisy-kit-table__results" data-daisy-kit-table-results role="status" aria-live="polite"></p>

    <nav class="daisy-kit-table__pagination join" aria-label="{{ __('Table pagination') }}">
        <button class="btn btn-ghost btn-sm join-item" data-daisy-kit-table-previous type="button">
            <span aria-hidden="true">&larr;</span>
            <span>{{ __('Previous') }}</span>
        </button>
        <span class="daisy-kit-table__page-status join-item" data-daisy-kit-table-page data-daisy-kit-table-page-status aria-live="polite"></span>
        <button class="btn btn-ghost btn-sm join-item" data-daisy-kit-table-next type="button">
            <span>{{ __('Next') }}</span>
            <span aria-hidden="true">&rarr;</span>
        </button>
    </nav>
</footer>

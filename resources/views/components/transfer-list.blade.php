@props([
    'name' => null,
    'label' => 'Transfer list',
    'items' => [],
    'value' => [],
    'sourceLabel' => 'Available',
    'targetLabel' => 'Selected',
    'searchable' => true,
    'maxItems' => null,
    'disabled' => false,
    'required' => false,
    'sortable' => true,
    'oneWay' => false,
    'showSelectAll' => true,
    'pagination' => false,
    'pageSize' => 10,
    'selectAllScope' => 'page',
])

<section {{ $attributes->class(['daisy-kit-transfer-list']) }} data-daisy-kit-module="transfer-list">
    <p class="alert alert-error" data-daisy-kit-status hidden role="alert"></p>
    <p class="mb-3 font-medium">{{ $label }}</p>

    <div class="daisy-kit-transfer-list__columns" data-daisy-kit-transfer-content>
        <article class="card card-border bg-base-100" data-daisy-kit-transfer-panel="source">
            <div class="card-body gap-0 p-0">
                <header class="daisy-kit-transfer-list__header">
                    <div class="min-w-0">
                        <h3 class="truncate font-semibold">{{ $sourceLabel }}</h3>
                        <p class="text-xs text-base-content/60" data-daisy-kit-transfer-count="source" aria-live="polite"></p>
                    </div>
                    @if ($showSelectAll)
                        <label class="daisy-kit-transfer-list__select-all label cursor-pointer gap-2">
                            <span class="text-xs">Select all</span>
                            <input class="checkbox checkbox-sm" data-daisy-kit-transfer-select-all="source" type="checkbox" aria-label="Select all {{ $sourceLabel }}" @disabled($disabled)>
                        </label>
                    @endif
                </header>

                @if ($searchable)
                    <label class="input daisy-kit-transfer-list__search">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"/></svg>
                        <input data-daisy-kit-transfer-search="source" type="search" placeholder="Search {{ $sourceLabel }}" aria-label="Search {{ $sourceLabel }}" @disabled($disabled)>
                    </label>
                @endif

                <div class="daisy-kit-transfer-list__viewport">
                    <ul aria-label="{{ $sourceLabel }}" data-daisy-kit-transfer-source role="listbox" aria-multiselectable="true"></ul>
                    <p class="daisy-kit-transfer-list__empty" data-daisy-kit-transfer-empty="source" hidden role="status"></p>
                </div>

                <footer class="daisy-kit-transfer-list__footer" data-daisy-kit-transfer-pagination="source" hidden>
                    <div class="join">
                        <button class="btn btn-ghost btn-sm btn-square join-item" data-daisy-kit-transfer-page="source:previous" type="button" aria-label="Previous available page" @disabled($disabled)>‹</button>
                        <span class="btn btn-ghost btn-sm join-item pointer-events-none" data-daisy-kit-transfer-page-status="source" aria-live="polite"></span>
                        <button class="btn btn-ghost btn-sm btn-square join-item" data-daisy-kit-transfer-page="source:next" type="button" aria-label="Next available page" @disabled($disabled)>›</button>
                    </div>
                </footer>
            </div>
        </article>

        <div class="daisy-kit-transfer-list__actions" aria-label="Transfer controls" role="group">
            <button class="btn btn-primary btn-sm btn-square" data-daisy-kit-transfer-move="to-target" type="button" aria-label="Add selected items" title="Add selected items" disabled><span aria-hidden="true">→</span></button>
            @unless ($oneWay)
                <button class="btn btn-outline btn-sm btn-square" data-daisy-kit-transfer-move="to-source" type="button" aria-label="Remove selected items" title="Remove selected items" disabled><span aria-hidden="true">←</span></button>
            @endunless
        </div>

        <article class="card card-border bg-base-100" data-daisy-kit-transfer-panel="target">
            <div class="card-body gap-0 p-0">
                <header class="daisy-kit-transfer-list__header">
                    <div class="min-w-0">
                        <h3 class="truncate font-semibold">{{ $targetLabel }}</h3>
                        <p class="text-xs text-base-content/60" data-daisy-kit-transfer-count="target" aria-live="polite"></p>
                    </div>
                    @if ($showSelectAll)
                        <label class="daisy-kit-transfer-list__select-all label cursor-pointer gap-2">
                            <span class="text-xs">Select all</span>
                            <input class="checkbox checkbox-sm" data-daisy-kit-transfer-select-all="target" type="checkbox" aria-label="Select all {{ $targetLabel }}" @disabled($disabled)>
                        </label>
                    @endif
                </header>

                @if ($searchable)
                    <label class="input daisy-kit-transfer-list__search">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"/></svg>
                        <input data-daisy-kit-transfer-search="target" type="search" placeholder="Search {{ $targetLabel }}" aria-label="Search {{ $targetLabel }}" @disabled($disabled)>
                    </label>
                @endif

                <div class="daisy-kit-transfer-list__viewport">
                    <ul aria-label="{{ $targetLabel }}" data-daisy-kit-transfer-target role="listbox" aria-multiselectable="true"></ul>
                    <p class="daisy-kit-transfer-list__empty" data-daisy-kit-transfer-empty="target" hidden role="status"></p>
                </div>

                <footer class="daisy-kit-transfer-list__footer">
                    <div class="join" data-daisy-kit-transfer-pagination="target" hidden>
                        <button class="btn btn-ghost btn-sm btn-square join-item" data-daisy-kit-transfer-page="target:previous" type="button" aria-label="Previous selected page" @disabled($disabled)>‹</button>
                        <span class="btn btn-ghost btn-sm join-item pointer-events-none" data-daisy-kit-transfer-page-status="target" aria-live="polite"></span>
                        <button class="btn btn-ghost btn-sm btn-square join-item" data-daisy-kit-transfer-page="target:next" type="button" aria-label="Next selected page" @disabled($disabled)>›</button>
                    </div>
                    @if ($sortable)
                        <div class="join ms-auto" role="group" aria-label="Reorder {{ $targetLabel }}">
                            <button class="btn btn-ghost btn-sm btn-square join-item" data-daisy-kit-transfer-reorder="up" type="button" aria-label="Move selected items up" title="Move selected items up" @disabled($disabled)>↑</button>
                            <button class="btn btn-ghost btn-sm btn-square join-item" data-daisy-kit-transfer-reorder="down" type="button" aria-label="Move selected items down" title="Move selected items down" @disabled($disabled)>↓</button>
                        </div>
                    @endif
                </footer>
            </div>
        </article>
    </div>

    <div data-daisy-kit-transfer-values></div>
    @if ($required)
        <input class="sr-only" data-daisy-kit-transfer-required type="text" tabindex="-1" aria-label="{{ $label }} selection" required @disabled($disabled)>
    @endif
    <script data-daisy-kit-config type="application/json">{!! \Art35rennes\DaisyKit\Support\JsonConfiguration::encode([
        'name' => $name,
        'items' => $items,
        'value' => $value,
        'searchable' => $searchable === true,
        'maxItems' => $maxItems,
        'disabled' => $disabled === true,
        'required' => $required === true,
        'sortable' => $sortable === true,
        'oneWay' => $oneWay === true,
        'showSelectAll' => $showSelectAll === true,
        'pagination' => $pagination === true,
        'pageSize' => $pageSize,
        'selectAllScope' => $selectAllScope,
    ]) !!}</script>
</section>

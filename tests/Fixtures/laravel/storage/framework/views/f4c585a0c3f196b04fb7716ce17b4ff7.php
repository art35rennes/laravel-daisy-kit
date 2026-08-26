<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'columns' => [],
    'rows' => [],
    'mode' => 'client',
    'endpoint' => null,
    'method' => 'GET',
    'serverAdapter' => null,
    'persistState' => false,
    'stateKey' => null,
    'persistStateFields' => null,
    'globalFilterKey' => 'global',
    'filters' => [],
    'initialState' => [],
    'pageSizeOptions' => [10, 25, 50],
    'search' => true,
    'searchDebounce' => 500,
    'filterDebounce' => 500,
    'minSearchChars' => 3,
    'searchMode' => 'fuzzy',
    'columnVisibility' => false,
    'selection' => 'none',
    'rowKey' => null,
    'selectFiltered' => true,
    'selectionReadOnly' => false,
    'subRowsKey' => null,
    'subRowSelection' => 'independent',
    'tableLayout' => 'auto',
    'minWidth' => null,
    'scrollX' => 'auto',
    'externalFilters' => false,
    'toolbarLayout' => 'default',
    'livewireMode' => 'none',
    'caption' => null,
    'size' => null,
    'zebra' => false,
    'hover' => false,
    'pinRows' => false,
    'pinCols' => false,
    'rowDetail' => 'none',
    'rowDetailView' => null,
    'columnResizing' => false,
    'editable' => false,
    'editEndpoint' => null,
    'editMethod' => null,
    'editMode' => 'cell',
    'editableColumns' => [],
    'editPolicy' => [],
    'linkPolicy' => [],
    'emptyLabel' => null,
    'loadingLabel' => null,
    'errorLabel' => null,
    'containerClass' => 'rounded-box border border-base-content/5 bg-base-100 p-4',
    'tableClass' => 'w-full',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'columns' => [],
    'rows' => [],
    'mode' => 'client',
    'endpoint' => null,
    'method' => 'GET',
    'serverAdapter' => null,
    'persistState' => false,
    'stateKey' => null,
    'persistStateFields' => null,
    'globalFilterKey' => 'global',
    'filters' => [],
    'initialState' => [],
    'pageSizeOptions' => [10, 25, 50],
    'search' => true,
    'searchDebounce' => 500,
    'filterDebounce' => 500,
    'minSearchChars' => 3,
    'searchMode' => 'fuzzy',
    'columnVisibility' => false,
    'selection' => 'none',
    'rowKey' => null,
    'selectFiltered' => true,
    'selectionReadOnly' => false,
    'subRowsKey' => null,
    'subRowSelection' => 'independent',
    'tableLayout' => 'auto',
    'minWidth' => null,
    'scrollX' => 'auto',
    'externalFilters' => false,
    'toolbarLayout' => 'default',
    'livewireMode' => 'none',
    'caption' => null,
    'size' => null,
    'zebra' => false,
    'hover' => false,
    'pinRows' => false,
    'pinCols' => false,
    'rowDetail' => 'none',
    'rowDetailView' => null,
    'columnResizing' => false,
    'editable' => false,
    'editEndpoint' => null,
    'editMethod' => null,
    'editMode' => 'cell',
    'editableColumns' => [],
    'editPolicy' => [],
    'linkPolicy' => [],
    'emptyLabel' => null,
    'loadingLabel' => null,
    'errorLabel' => null,
    'containerClass' => 'rounded-box border border-base-content/5 bg-base-100 p-4',
    'tableClass' => 'w-full',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $resolvedMode = $mode === 'server' ? 'server' : 'client';
    $resolvedServerAdapter = $serverAdapter === 'spatie-query-builder' ? 'spatie-query-builder' : null;
    $resolvedPersistState = in_array($persistState, ['url', 'local'], true) ? $persistState : false;
    $resolvedStateKey = is_string($stateKey) && filled($stateKey)
        ? $stateKey
        : (is_string($attributes->get('id')) && filled($attributes->get('id')) ? $attributes->get('id') : null);
    $resolvedPersistStateFields = \Art35rennes\DaisyKit\Support\DaisyTableConfig::normalizePersistedStateFields($persistStateFields);
    $resolvedSearchDebounce = max(0, (int) $searchDebounce);
    $resolvedFilterDebounce = max(0, (int) $filterDebounce);
    $resolvedMinSearchChars = max(0, (int) $minSearchChars);
    $resolvedSearchMode = $searchMode === 'includes' ? 'includes' : 'fuzzy';
    $resolvedTableLayout = $tableLayout === 'fixed' ? 'fixed' : 'auto';
    $resolvedScrollX = in_array($scrollX, ['auto', 'always', 'none'], true) ? $scrollX : 'auto';
    $resolvedToolbarLayout = in_array($toolbarLayout, ['default', 'split', 'hidden'], true) ? $toolbarLayout : 'default';
    $resolvedLivewireMode = in_array($livewireMode, ['ignore', 'morph', 'none'], true) ? $livewireMode : 'none';
    $resolvedRowDetail = in_array($rowDetail, ['none', 'inline', 'modal'], true) ? $rowDetail : 'none';
    $resolvedExternalFilters = (bool) $externalFilters;
    $resolvedSelection = in_array($selection, ['multiple', 'single'], true) ? $selection : 'none';
    $resolvedRowKey = is_string($rowKey) && filled($rowKey) ? $rowKey : null;
    $resolvedSubRowsKey = is_string($subRowsKey) && filled($subRowsKey) ? $subRowsKey : null;
    $allowedSubRowSelectionModes = ['independent', 'cascade', 'master-only'];
    $resolvedSubRowSelection = in_array($subRowSelection, $allowedSubRowSelectionModes, true)
        ? $subRowSelection
        : null;
    $resolvedLinkPolicy = \Art35rennes\DaisyKit\Support\DaisyTableColumns::normalizeLinkPolicy($linkPolicy);
    $selectionEnabled = $resolvedSelection !== 'none';
    $showSelectionControls = $resolvedSelection === 'multiple';
    $showSelectionFeedback = $selectionEnabled;
    $resolvedSelectFiltered = $showSelectionControls && $resolvedSubRowSelection !== 'cascade' && (bool) $selectFiltered;
    $resolvedSelectionReadOnly = (bool) $selectionReadOnly;
    $hasToolbarStartSlot = isset($toolbarStart) && $toolbarStart instanceof \Illuminate\View\ComponentSlot;
    $hasToolbarSlot = isset($toolbar) && $toolbar instanceof \Illuminate\View\ComponentSlot;
    $hasToolbarEndSlot = isset($toolbarEnd) && $toolbarEnd instanceof \Illuminate\View\ComponentSlot;
    $hasFiltersSlot = isset($filtersSlot) && $filtersSlot instanceof \Illuminate\View\ComponentSlot;
    $hasActionsSlot = isset($actions) && $actions instanceof \Illuminate\View\ComponentSlot;
    $hasControlsSlot = isset($controls) && $controls instanceof \Illuminate\View\ComponentSlot;
    $hasBulkActionsSlot = isset($bulkActions) && $bulkActions instanceof \Illuminate\View\ComponentSlot;

    if ($resolvedMode === 'server' && blank($endpoint)) {
        throw new InvalidArgumentException('The table component requires an endpoint prop when mode is set to server.');
    }

    if ($resolvedMode !== 'server' && $resolvedServerAdapter !== null) {
        throw new InvalidArgumentException('The table component only allows a serverAdapter when mode is set to server.');
    }

    if ($selectionEnabled && blank($resolvedRowKey)) {
        throw new InvalidArgumentException('The table component requires a non-empty rowKey prop when selection is enabled.');
    }

    if ($resolvedSubRowSelection === null) {
        throw new InvalidArgumentException('The table component subRowSelection prop must be independent, cascade, or master-only.');
    }

    if ($resolvedSubRowSelection !== 'independent' && $resolvedSubRowsKey === null) {
        throw new InvalidArgumentException('The table component hierarchical subRowSelection mode requires a non-empty subRowsKey prop.');
    }

    if ($resolvedSubRowSelection === 'cascade' && $resolvedSelection !== 'multiple') {
        throw new InvalidArgumentException('The table component cascade subRowSelection mode requires selection to be multiple.');
    }

    if ($resolvedSubRowSelection === 'master-only' && ! $selectionEnabled) {
        throw new InvalidArgumentException('The table component master-only subRowSelection mode requires selection to be enabled.');
    }

    if ($resolvedPersistState !== false && blank($resolvedStateKey)) {
        throw new InvalidArgumentException('The table component requires a stateKey or root id when state persistence is enabled.');
    }

    if (($resolvedRowDetail !== 'none' || $resolvedSubRowsKey !== null || (bool) $editable) && blank($resolvedRowKey)) {
        throw new InvalidArgumentException('The table component requires a non-empty rowKey prop for row details, sub rows, or editable rows.');
    }


    if ($resolvedRowDetail !== 'none' && is_string($rowDetailView) && filled($rowDetailView) && ! View::exists($rowDetailView)) {
        throw new InvalidArgumentException("Daisy table row detail view [{$rowDetailView}] does not exist.");
    }

    $sizeMap = ['xs', 'sm', 'md', 'lg', 'xl'];
    $tableClasses = 'table';

    if ($zebra) {
        $tableClasses .= ' table-zebra';
    }

    if ($hover) {
        $tableClasses .= ' daisy-table-row-hover';
    }

    if (in_array($size, $sizeMap, true)) {
        $tableClasses .= ' table-'.$size;
    }

    if ($pinRows) {
        $tableClasses .= ' table-pin-rows';
    }

    if ($pinCols) {
        $tableClasses .= ' table-pin-cols';
    }

    $tableClasses .= $resolvedTableLayout === 'fixed' ? ' table-fixed' : ' table-auto';

    $tableClasses = trim($tableClasses.' '.$tableClass);
    $wrapperClasses = trim('daisy-table-shell space-y-4 '.$containerClass);
    $scrollClasses = match ($resolvedScrollX) {
        'always' => 'daisy-table-scroll daisy-table-scroll-always',
        'none' => 'daisy-table-scroll daisy-table-scroll-none',
        default => 'daisy-table-scroll',
    };

    // `key` stays the stable client-side identifier, while `sortKey` / `filterKey`
    // allow the host app to point server requests at different backend field names.
    $columnClasses = static function (array $column, string $target): string {
        $classes = [];

        if ($target === 'header' && filled($column['headerClass'])) {
            $classes[] = $column['headerClass'];
        }

        if ($target === 'cell' && filled($column['cellClass'])) {
            $classes[] = $column['cellClass'];
        }

        array_push($classes, ...array_filter([
            $column['widthClass'],
            $column['minWidthClass'],
            $column['maxWidthClass'],
            $column['alignClass'],
            $column['verticalAlignClass'],
            $column['paddingClass'],
            $column['densityClass'],
            $column['nowrap'] ? 'whitespace-nowrap' : null,
            $column['type'] === 'actions' ? 'daisy-table-actions-cell' : null,
        ]));

        return trim(implode(' ', array_unique(array_filter($classes))));
    };

    $wrapperClassesForColumn = static function (array $column, string $target): string {
        $classes = [$target === 'header' ? 'daisy-table-header-content' : 'daisy-table-cell-content'];

        if ($target === 'header' && filled($column['headerWrapperClass'])) {
            $classes[] = $column['headerWrapperClass'];
        }

        if ($target === 'cell' && filled($column['cellWrapperClass'])) {
            $classes[] = $column['cellWrapperClass'];
        }

        if ($target === 'cell' && $column['type'] === 'actions') {
            $classes[] = 'daisy-table-actions-content';
        }

        if ($target === 'cell') {
            if ($column['truncate'] === 'line') {
                $classes[] = 'truncate';
            } elseif (in_array($column['truncate'], [2, 3], true)) {
                $classes[] = 'line-clamp-'.$column['truncate'];
            }
        }

        return trim(implode(' ', array_unique(array_filter($classes))));
    };

    $tableMinWidthClass = $minWidth === 'full'
        ? 'min-w-full'
        : (\Art35rennes\DaisyKit\Support\DaisyTableColumns::numericClass($minWidth, 'daisy-table-root-min-width') ?? null);

    $tableClasses = trim($tableClasses.' '.$tableMinWidthClass);

    if (! is_array($columns)) {
        throw new InvalidArgumentException('The table component requires columns to be an array.');
    }

    \Art35rennes\DaisyKit\Support\DaisyTableConfig::validateColumns($columns);

    $resolvedColumns = array_values(array_filter(
        array_map(\Art35rennes\DaisyKit\Support\DaisyTableColumns::normalize(...), is_array($columns) ? $columns : []),
        static fn (array $column) => $column['key'] !== ''
    ));

    $resolvedEditable = \Art35rennes\DaisyKit\Support\DaisyTableColumns::normalizeEditable(
        $editable,
        $editEndpoint,
        $editMethod,
        $editMode,
        $editableColumns,
        $editPolicy,
        $resolvedColumns,
    );

    if ($resolvedEditable['enabled'] && $resolvedEditable['update']['strategy'] === 'remote' && $resolvedEditable['update']['endpoint'] === null) {
        throw new InvalidArgumentException('The table component requires an editEndpoint prop when editable is enabled.');
    }

    if ($resolvedEditable['create']['enabled'] && $resolvedEditable['create']['strategy'] === 'remote' && $resolvedEditable['create']['endpoint'] === null) {
        throw new InvalidArgumentException('The table component requires a create endpoint when remote row creation is enabled.');
    }

    if (is_array($columns) && $columns !== [] && $resolvedColumns === []) {
        throw new InvalidArgumentException('The table component requires at least one column with a non-empty key.');
    }

    if (collect($resolvedColumns)->contains(fn (array $column) => ($column['cell']['renderer'] ?? null) === 'actions') && blank($resolvedRowKey)) {
        throw new InvalidArgumentException('The table component requires a non-empty rowKey prop for structured row actions.');
    }

    foreach ($resolvedColumns as $column) {
        if (($column['cell']['renderer'] ?? null) !== 'blade') {
            continue;
        }

        if (! is_string($column['cell']['view'] ?? null) || ! View::exists($column['cell']['view'])) {
            throw new InvalidArgumentException("Daisy table cell view [{$column['cell']['view']}] does not exist.");
        }
    }

    foreach ($resolvedColumns as &$column) {
        if ($column['editor']['type'] !== 'blade') {
            continue;
        }

        if ($column['editor']['view'] === null || ! View::exists($column['editor']['view'])) {
            throw new InvalidArgumentException("Daisy table editor view [{$column['editor']['view']}] does not exist.");
        }

        $column['editor']['template'] = trim(View::make($column['editor']['view'], [
            'column' => $column,
            'table' => ['rowKey' => $resolvedRowKey],
        ])->render());
    }
    unset($column);

    $columnFilters = collect($resolvedColumns)
        ->filter(fn (array $column) => $column['filterable'] && is_array($column['filter']))
        ->map(fn (array $column) => [
            'id' => $column['key'],
            'label' => $column['label'],
            'type' => $column['filter']['type'],
            'filterKey' => $column['filterKey'],
            'filterKeyFrom' => $column['filter']['filterKeyFrom'] ?? null,
            'filterKeyTo' => $column['filter']['filterKeyTo'] ?? null,
            'options' => $column['filter']['options'] ?? [],
        ]);

    $toolbarFilters = collect(is_array($filters) ? $filters : [])
        ->map(fn (array $filter) => \Art35rennes\DaisyKit\Support\DaisyTableColumns::normalizeToolbarFilter($filter))
        ->filter(fn (array $filter) => $filter['id'] !== '' && $filter['type'] !== null);

    $filterPriority = static function (array $filter): int {
        $id = strtolower(str_replace(['-', '.'], '_', (string) ($filter['id'] ?? '')));

        $priorities = [
            'reference_internal' => 10,
            'reference' => 11,
            'code' => 12,
            'name' => 13,
            'email' => 14,
            'city' => 20,
            'agent_affected' => 21,
            'compliance' => 22,
            'company' => 30,
            'country' => 31,
            'contract_tree_path' => 32,
            'contract_scope_path' => 33,
            'document_kind' => 40,
            'compile_status' => 41,
            'version' => 42,
            'guard_name' => 43,
            'description' => 44,
        ];

        if (array_key_exists($id, $priorities)) {
            return $priorities[$id];
        }

        return str_contains($id, 'intervention') ? 45 : 99;
    };

    $resolvedFilters = $columnFilters
        ->merge($toolbarFilters)
        ->values()
        ->map(fn (array $filter, int $index) => $filter + ['_originalIndex' => $index])
        ->unique('id')
        ->sort(fn (array $first, array $second) => [
            $filterPriority($first),
            $first['_originalIndex'],
        ] <=> [
            $filterPriority($second),
            $second['_originalIndex'],
        ])
        ->map(function (array $filter): array {
            unset($filter['_originalIndex']);

            return $filter;
        })
        ->values()
        ->all();

    $resolvedPageSizeOptions = array_values(array_unique(array_filter(
        array_map(static fn ($value) => is_numeric($value) ? (int) $value : null, is_array($pageSizeOptions) ? $pageSizeOptions : []),
        static fn ($value) => is_int($value) && $value > 0
    )));

    if ($resolvedPageSizeOptions === []) {
        $resolvedPageSizeOptions = [10, 25, 50];
    }

    $filterIds = collect($resolvedFilters)->pluck('id')->all();

    $resolvedInitialState = [
        'sorting' => array_values(array_filter(
            is_array($initialState['sorting'] ?? null) ? $initialState['sorting'] : [],
            static fn ($entry) => filled($entry['id'] ?? null)
        )),
        'pagination' => [
            'pageIndex' => max(0, (int) data_get($initialState, 'pagination.pageIndex', 0)),
            'pageSize' => in_array((int) data_get($initialState, 'pagination.pageSize', $resolvedPageSizeOptions[0]), $resolvedPageSizeOptions, true)
                ? (int) data_get($initialState, 'pagination.pageSize', $resolvedPageSizeOptions[0])
                : $resolvedPageSizeOptions[0],
        ],
        'globalFilter' => (string) ($initialState['globalFilter'] ?? ''),
        'columnFilters' => array_values(array_filter(
            is_array($initialState['columnFilters'] ?? null) ? $initialState['columnFilters'] : [],
            static fn ($entry) => in_array($entry['id'] ?? null, $filterIds, true)
        )),
        'columnVisibility' => array_reduce($resolvedColumns, function (array $carry, array $column) use ($initialState): array {
            $carry[$column['key']] = data_get($initialState, 'columnVisibility.'.$column['key'], $column['visible']) !== false;

            return $carry;
        }, []),
        'selection' => [
            'selectedIds' => collect(data_get($initialState, 'selection.selectedIds', []))
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value) => (string) $value)
                ->values()
                ->all(),
            'excludedIds' => collect(data_get($initialState, 'selection.excludedIds', []))
                ->filter(fn ($value) => filled($value))
                ->map(fn ($value) => (string) $value)
                ->values()
                ->all(),
            'allFilteredSelected' => (bool) data_get($initialState, 'selection.allFilteredSelected', false),
            'selectionScope' => data_get($initialState, 'selection.selectionScope') === 'filtered' ? 'filtered' : 'page',
            'filterSignature' => (string) data_get($initialState, 'selection.filterSignature', ''),
        ],
        'expanded' => is_array($initialState['expanded'] ?? null) ? $initialState['expanded'] : [],
        'columnOrder' => array_values(array_filter(
            is_array($initialState['columnOrder'] ?? null) ? $initialState['columnOrder'] : [],
            static fn ($key) => is_string($key) && in_array($key, collect($resolvedColumns)->pluck('key')->all(), true)
        )),
        'columnPinning' => [
            'left' => array_values(array_filter(
                is_array(data_get($initialState, 'columnPinning.left')) ? data_get($initialState, 'columnPinning.left') : [],
                static fn ($key) => is_string($key) && in_array($key, collect($resolvedColumns)->pluck('key')->all(), true)
            )),
            'right' => array_values(array_filter(
                is_array(data_get($initialState, 'columnPinning.right')) ? data_get($initialState, 'columnPinning.right') : [],
                static fn ($key) => is_string($key) && in_array($key, collect($resolvedColumns)->pluck('key')->all(), true)
            )),
        ],
        'columnSizing' => is_array($initialState['columnSizing'] ?? null) ? $initialState['columnSizing'] : [],
        'columnSizingInfo' => [
            'startOffset' => null,
            'startSize' => null,
            'deltaOffset' => null,
            'deltaPercentage' => null,
            'isResizingColumn' => false,
            'columnSizingStart' => [],
        ],
        'rowSelection' => is_array($initialState['rowSelection'] ?? null) ? $initialState['rowSelection'] : [],
    ];

    $resolvedEndpoint = \Art35rennes\DaisyKit\Support\DaisyTableConfig::normalizeEndpoint($endpoint, (string) $method);
    $resolvedMethod = \Art35rennes\DaisyKit\Support\DaisyTableConfig::normalizeMethod(
        is_array($endpoint) ? ($endpoint['method'] ?? $method) : $method,
        'GET',
    );

    $resolvedRows = $resolvedMode === 'client' && is_iterable($rows)
        ? \Art35rennes\DaisyKit\Support\DaisyTableRows::for($rows, $resolvedColumns)
            ->table(['rowKey' => $resolvedRowKey])
            ->rowDetailView($resolvedRowDetail !== 'none' && is_string($rowDetailView) && filled($rowDetailView) ? $rowDetailView : null)
            ->renderCells()
        : [];

    if ($resolvedRowKey !== null) {
        \Art35rennes\DaisyKit\Support\DaisyTableConfig::validateRows($resolvedRows, $resolvedRowKey, $resolvedSubRowsKey);
    }

    $renderCell = static function (array $row, array $column) use ($resolvedLinkPolicy, $resolvedRowKey) {
        $value = data_get($row, $column['key']);

        if (($column['cell']['renderer'] ?? null) === 'link') {
            $link = is_array($value) ? $value : ['href' => $value, 'label' => $value];
            $href = trim((string) ($link['href'] ?? ''));
            $label = trim((string) ($link['label'] ?? $href));
            $target = ($link['target'] ?? null) === '_blank' ? '_blank' : null;
            $targetAttribute = $target ? ' target="'.e($target).'" rel="noopener noreferrer"' : '';

            if ($href === '' || ! \Art35rennes\DaisyKit\Support\DaisyTableUrlPolicy::isSafeHref($href, $resolvedLinkPolicy, $column['cell'] ?? [])) {
                return $label;
            }

            return new Illuminate\Support\HtmlString('<a href="'.e($href).'"'.$targetAttribute.' class="link link-hover">'.e($label).($target === '_blank' ? ' <span aria-hidden="true">&nearr;</span>' : '').'</a>');
        }

        if (($column['cell']['renderer'] ?? null) === 'actions') {
            return new Illuminate\Support\HtmlString(View::make('daisy::partials.table-actions', [
                'actions' => \Art35rennes\DaisyKit\Support\DaisyTableActions::normalize($value),
                'rowId' => data_get($row, $resolvedRowKey),
                'columnId' => $column['key'],
            ])->render());
        }

        if ($column['trusted']) {
            return new Illuminate\Support\HtmlString((string) $value);
        }

        return $value;
    };

    // Keep the serialized config explicit so the JS runtime can switch transport
    // adapters without exposing a generic frontend options surface.
    $config = [
        'contractVersion' => \Art35rennes\DaisyKit\Support\DaisyTableConfig::ContractVersion,
        'mode' => $resolvedMode,
        'method' => $resolvedMethod,
        'serverAdapter' => $resolvedServerAdapter,
        'persistState' => $resolvedPersistState,
        'stateKey' => $resolvedStateKey,
        'persistStateFields' => $resolvedPersistStateFields,
        'globalFilterKey' => filled($globalFilterKey) ? (string) $globalFilterKey : 'global',
        'rowKey' => $resolvedRowKey,
        'searchMode' => $resolvedSearchMode,
        'subRowsKey' => $resolvedSubRowsKey,
        'endpoint' => $resolvedEndpoint,
        'columns' => $resolvedColumns,
        'filters' => $resolvedFilters,
        'externalFilters' => $resolvedExternalFilters,
        'linkPolicy' => $resolvedLinkPolicy,
        'livewireMode' => $resolvedLivewireMode,
        'rows' => $resolvedRows,
        'initialState' => $resolvedInitialState,
        'pageSizeOptions' => $resolvedPageSizeOptions,
        'search' => (bool) $search,
        'searchDebounceMs' => $resolvedSearchDebounce,
        'filterDebounceMs' => $resolvedFilterDebounce,
        'minSearchChars' => $resolvedMinSearchChars,
        'columnVisibility' => (bool) $columnVisibility,
        'selection' => [
            'enabled' => $selectionEnabled,
            'mode' => $resolvedSelection,
            'rowKey' => $selectionEnabled ? $resolvedRowKey : null,
            'selectFiltered' => $resolvedSelectFiltered,
            'readOnly' => $resolvedSelectionReadOnly,
            'subRowSelection' => $resolvedSubRowSelection,
        ],
        'rowDetail' => [
            'mode' => $resolvedRowDetail,
            'view' => is_string($rowDetailView) && filled($rowDetailView) ? $rowDetailView : null,
        ],
        'columnResizing' => (bool) $columnResizing,
        'editable' => $resolvedEditable + ['rowKey' => $resolvedRowKey],
        'emptyLabel' => $emptyLabel ?: __('daisy::common.no_results'),
        'loadingLabel' => $loadingLabel ?: __('daisy::common.loading'),
        'errorLabel' => $errorLabel ?: __('daisy::components.table_error'),
        'labels' => [
            'search' => __('daisy::common.search'),
            'rowsPerPage' => __('daisy::components.rows_per_page'),
            'showingResults' => __('daisy::components.showing_results'),
            'previous' => __('daisy::components.table_previous'),
            'next' => __('daisy::components.table_next'),
            'columns' => __('daisy::components.table_columns'),
            'page' => __('daisy::components.table_page'),
            'filters' => __('daisy::components.table_filters'),
            'all' => __('daisy::common.all'),
            'selectedRows' => __('daisy::components.selected_rows'),
            'selectAllRows' => __('daisy::components.select_all_rows'),
            'selectRow' => __('daisy::components.select_row'),
            'clearSelection' => __('daisy::components.clear_selection'),
            'selectFilteredRows' => __('daisy::components.select_filtered_rows'),
            'selectedCount' => __('daisy::components.selected_count'),
            'selectedOnPageCount' => __('daisy::components.selected_on_page_count'),
            'selectedOffPageCount' => __('daisy::components.selected_off_page_count'),
            'allFilteredRowsSelected' => __('daisy::components.all_filtered_rows_selected'),
            'selectionResetAfterFilter' => __('daisy::components.selection_reset_after_filter'),
            'addRow' => __('daisy::common.add'),
        ],
    ];
?>

<div
    <?php echo e($attributes->only(['id', 'data-daisy-table-id'])->class([$wrapperClasses])); ?>

    data-module="table"
    data-daisy-table="1"
    data-table-layout="<?php echo e($resolvedTableLayout); ?>"
    data-table-scroll-x="<?php echo e($resolvedScrollX); ?>"
    data-table-livewire-mode="<?php echo e($resolvedLivewireMode); ?>"
    data-table-selection-readonly="<?php echo e($resolvedSelectionReadOnly ? 'true' : 'false'); ?>"
    <?php if($selectionEnabled): ?> aria-disabled="<?php echo e($resolvedSelectionReadOnly ? 'true' : 'false'); ?>" <?php endif; ?>
    <?php if(filled($minWidth)): ?> data-table-min-width="<?php echo e($minWidth); ?>" <?php endif; ?>
    data-table-config='<?php echo json_encode($config, 15, 512) ?>'
    <?php if($resolvedLivewireMode === 'ignore'): ?> wire:ignore <?php endif; ?>
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedToolbarLayout !== 'hidden'): ?>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'daisy-table-toolbar grid gap-3',
            'lg:grid-cols-[minmax(0,1fr)_auto]' => $resolvedToolbarLayout === 'split',
        ]); ?>">
            <div class="daisy-table-toolbar-start flex flex-wrap items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasToolbarStartSlot): ?>
                    <?php echo e($toolbarStart); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasToolbarSlot): ?>
                    <?php echo e($toolbar); ?>

                <?php elseif($search): ?>
                    <label class="input input-bordered input-sm flex w-full max-w-md items-center gap-2 bg-base-100">
                        <span class="sr-only"><?php echo e(__('daisy::common.search')); ?></span>
                        <input
                            type="search"
                            class="daisy-table-search grow"
                            data-table-search
                            placeholder="<?php echo e(__('daisy::common.search')); ?>"
                        >
                    </label>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasToolbarEndSlot): ?>
                <div class="daisy-table-toolbar-end flex flex-wrap items-center justify-end gap-3">
                    <?php echo e($toolbarEnd); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="daisy-table-controls flex flex-wrap items-center justify-start gap-3 lg:justify-end">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasActionsSlot): ?>
                    <?php echo e($actions); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedEditable['create']['enabled']): ?>
                    <button type="button" class="btn btn-sm btn-primary" data-table-create>
                        <?php echo e(__('daisy::common.add')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasControlsSlot): ?>
                    <?php echo e($controls); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <label class="label flex items-center gap-2 p-0">
                    <span class="label-text text-sm text-base-content/70"><?php echo e(__('daisy::components.rows_per_page')); ?></span>
                    <select class="select select-bordered select-sm bg-base-100" data-table-page-size>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resolvedPageSizeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($option); ?>" <?php if($option === $resolvedInitialState['pagination']['pageSize']): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </label>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($columnVisibility): ?>
                    <details class="dropdown dropdown-end">
                        <summary class="btn btn-sm btn-ghost"><?php echo e(__('daisy::components.table_columns')); ?></summary>
                        <div class="daisy-table-column-menu dropdown-content rounded-box border border-base-content/10 bg-base-100 p-1 shadow" data-table-column-menu></div>
                    </details>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFiltersSlot): ?>
                <div class="daisy-table-external-filters lg:col-span-2">
                    <?php echo e($filtersSlot); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $resolvedExternalFilters && count($resolvedFilters) > 0): ?>
                <div class="daisy-table-filters rounded-box grid grid-cols-1 gap-3 border border-base-content/10 bg-base-200/40 p-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 lg:col-span-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resolvedFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filter['type'] === 'text'): ?>
                        <label class="form-control w-full min-w-0">
                            <span class="label px-0 py-0 pb-1">
                                <span class="label-text text-xs font-medium text-base-content/70"><?php echo e($filter['label']); ?></span>
                            </span>
                            <input
                                type="text"
                                class="input input-bordered input-sm w-full bg-base-100"
                                data-table-filter="<?php echo e($filter['id']); ?>"
                                data-table-filter-type="text"
                                placeholder="<?php echo e($filter['label']); ?>"
                            >
                        </label>
                    <?php elseif($filter['type'] === 'select'): ?>
                        <label class="form-control w-full min-w-0">
                            <span class="label px-0 py-0 pb-1">
                                <span class="label-text text-xs font-medium text-base-content/70"><?php echo e($filter['label']); ?></span>
                            </span>
                            <select
                                class="select select-bordered select-sm w-full bg-base-100"
                                data-table-filter="<?php echo e($filter['id']); ?>"
                                data-table-filter-type="select"
                            >
                                <option value=""><?php echo e(__('daisy::common.all')); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $filter['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($option['value']); ?>"><?php echo e($option['label'] ?? $option['value']); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </label>
                    <?php elseif($filter['type'] === 'boolean'): ?>
                        <label class="label min-h-10 w-full cursor-pointer gap-2 rounded-field border border-base-content/10 bg-base-100 px-3">
                            <span class="label-text text-sm text-base-content/70"><?php echo e($filter['label']); ?></span>
                            <input
                                type="checkbox"
                                class="toggle toggle-sm"
                                data-table-filter="<?php echo e($filter['id']); ?>"
                                data-table-filter-type="boolean"
                            >
                        </label>
                    <?php elseif($filter['type'] === 'date'): ?>
                        <label class="form-control w-full min-w-0">
                            <span class="label px-0 py-0 pb-1">
                                <span class="label-text text-xs font-medium text-base-content/70"><?php echo e($filter['label']); ?></span>
                            </span>
                            <input
                                type="date"
                                class="input input-bordered input-sm w-full bg-base-100"
                                data-table-filter="<?php echo e($filter['id']); ?>"
                                data-table-filter-type="date"
                            >
                        </label>
                    <?php elseif($filter['type'] === 'date-range'): ?>
                        <fieldset class="grid w-full min-w-0 grid-cols-2 gap-2">
                            <legend class="col-span-2 text-xs font-medium text-base-content/70"><?php echo e($filter['label']); ?></legend>
                            <input
                                type="date"
                                class="input input-bordered input-sm w-full bg-base-100"
                                data-table-filter="<?php echo e($filter['id']); ?>"
                                data-table-filter-type="date-range"
                                data-table-filter-bound="from"
                                aria-label="<?php echo e($filter['label']); ?> from"
                            >
                            <input
                                type="date"
                                class="input input-bordered input-sm w-full bg-base-100"
                                data-table-filter="<?php echo e($filter['id']); ?>"
                                data-table-filter-type="date-range"
                                data-table-filter-bound="to"
                                aria-label="<?php echo e($filter['label']); ?> to"
                            >
                        </fieldset>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSelectionFeedback): ?>
        <div class="daisy-table-selection-bar flex flex-col items-stretch gap-3 rounded-box border border-base-content/10 bg-base-200/60 px-3 py-2 text-sm sm:flex-row sm:items-center sm:justify-between" data-table-selection-feedback>
            <div class="flex min-w-0 flex-wrap items-center gap-2">
                <span class="font-medium" data-table-selection-summary></span>
                <span class="break-words text-base-content/60" data-table-selection-note></span>
            </div>

            <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedSelectFiltered): ?>
                    <button type="button" class="btn btn-xs btn-ghost justify-center" data-table-select-filtered>
                        <?php echo e(__('daisy::components.select_filtered_rows')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" class="btn btn-xs btn-ghost justify-center" data-table-clear-selection>
                    <?php echo e(__('daisy::components.clear_selection')); ?>

                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBulkActionsSlot): ?>
                    <div class="daisy-table-bulk-actions flex flex-col items-stretch gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end" data-table-bulk-actions>
                        <?php echo e($bulkActions); ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="<?php echo e($scrollClasses); ?>" <?php if($resolvedScrollX !== 'none'): ?> tabindex="0" <?php endif; ?>>
        <table
            <?php echo e($attributes->except(['id', 'data-daisy-table-id', 'data-module', 'data-daisy-table', 'data-table-config'])->class([$tableClasses])); ?>

            data-table-layout="<?php echo e($resolvedTableLayout); ?>"
        >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($caption): ?>
                <caption class="text-start text-xs opacity-70"><?php echo e($caption); ?></caption>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <colgroup data-table-colgroup>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectionEnabled): ?>
                    <col class="daisy-table-selection-col">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedRowDetail !== 'none'): ?>
                    <col class="daisy-table-detail-col">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resolvedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false) continue; ?>

                    <col class="<?php echo \Illuminate\Support\Arr::toCssClasses([$column['widthClass'], $column['minWidthClass'], $column['maxWidthClass']]); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </colgroup>

            <thead>
                <tr data-table-head-row>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectionEnabled): ?>
                        <th class="daisy-table-selection-cell">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSelectionControls): ?>
                            <input
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                data-table-select-page
                                aria-label="<?php echo e(__('daisy::components.select_all_rows')); ?>"
                                <?php if($resolvedSelectionReadOnly): echo 'disabled'; endif; ?>
                            >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedRowDetail !== 'none'): ?>
                        <th class="daisy-table-detail-cell">
                            <span class="sr-only">Details</span>
                        </th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resolvedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false) continue; ?>

                        <th
                            class="<?php echo e($columnClasses($column, 'header')); ?>"
                        >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($column['sortable']): ?>
                                <button
                                    type="button"
                                    class="daisy-table-head-button"
                                    data-table-sort="<?php echo e($column['key']); ?>"
                                    aria-sort="none"
                                >
                                    <span class="<?php echo e($wrapperClassesForColumn($column, 'header')); ?>">
                                        <?php echo e($column['label']); ?>

                                        <span class="daisy-table-sort-indicator" aria-hidden="true">&harr;</span>
                                    </span>
                                </button>
                            <?php else: ?>
                                <span class="<?php echo e($wrapperClassesForColumn($column, 'header')); ?>"><?php echo e($column['label']); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($columnResizing && $column['enableResizing']): ?>
                                <button
                                    type="button"
                                    class="daisy-table-resize-handle"
                                    data-table-resize="<?php echo e($column['key']); ?>"
                                    aria-label="Resize <?php echo e($column['label']); ?>"
                                    title="Resize <?php echo e($column['label']); ?>"
                                ></button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tr>
            </thead>

            <tbody data-table-body>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedMode === 'client' && count($resolvedRows) > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resolvedRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectionEnabled): ?>
                                <?php
                                    $rowSelectionId = data_get($row, $resolvedRowKey);
                                ?>
                                <td class="daisy-table-selection-cell">
                                    <input
                                        type="<?php echo e($resolvedSelection === 'single' ? 'radio' : 'checkbox'); ?>"
                                        class="<?php echo e($resolvedSelection === 'single' ? 'radio radio-sm' : 'checkbox checkbox-sm'); ?>"
                                        data-table-row-select="<?php echo e($rowSelectionId); ?>"
                                        aria-label="<?php echo e(__('daisy::components.select_row')); ?>"
                                        <?php if($resolvedSelectionReadOnly): echo 'disabled'; endif; ?>
                                    >
                                </td>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedRowDetail !== 'none'): ?>
                                <?php
                                    $rowDetailId = data_get($row, $resolvedRowKey);
                                ?>
                                <td class="daisy-table-detail-cell">
                                    <button
                                        type="button"
                                        class="btn btn-xs btn-ghost"
                                        data-table-row-detail="<?php echo e($rowDetailId); ?>"
                                        aria-expanded="false"
                                    >...</button>
                                </td>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $resolvedColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php if(data_get($resolvedInitialState, 'columnVisibility.'.$column['key']) === false) continue; ?>

                                <?php
                                    $value = $renderCell($row, $column);
                                ?>

                                <?php
                                    $isEditableCell = $resolvedEditable['enabled']
                                        && ! $column['trusted']
                                        && ! in_array($column['type'], ['actions', 'link', 'resource-link'], true)
                                        && ($resolvedEditable['columns'] === [] || in_array($column['key'], $resolvedEditable['columns'], true));
                                ?>

                                <td
                                    class="<?php echo e($columnClasses($column, 'cell')); ?>"
                                    <?php if($isEditableCell): ?>
                                        data-table-edit-cell
                                        data-table-row-id="<?php echo e(data_get($row, $resolvedRowKey)); ?>"
                                        data-table-column-id="<?php echo e($column['key']); ?>"
                                    <?php endif; ?>
                                >
                                    <span class="<?php echo e($wrapperClassesForColumn($column, 'cell')); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value instanceof Illuminate\Support\HtmlString): ?>
                                            <?php echo $value; ?>

                                        <?php else: ?>
                                            <?php echo e($value); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php elseif($resolvedMode === 'server'): ?>
                    <tr class="daisy-table-loading-row">
                        <td colspan="<?php echo e(max(1, count($resolvedColumns) + ($selectionEnabled ? 1 : 0) + ($resolvedRowDetail !== 'none' ? 1 : 0))); ?>"><?php echo e($loadingLabel ?: __('daisy::common.loading')); ?></td>
                    </tr>
                <?php else: ?>
                    <tr class="daisy-table-empty-row">
                        <td colspan="<?php echo e(max(1, count($resolvedColumns) + ($selectionEnabled ? 1 : 0) + ($resolvedRowDetail !== 'none' ? 1 : 0))); ?>"><?php echo e($emptyLabel ?: __('daisy::common.no_results')); ?></td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="daisy-table-footer flex flex-wrap items-center justify-between gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer)): ?>
            <?php echo e($footer); ?>

        <?php else: ?>
        <p class="daisy-table-status text-sm text-base-content/70" data-table-info>
            <?php echo e(__('daisy::components.showing_results', ['from' => count($resolvedRows) > 0 ? 1 : 0, 'to' => count($resolvedRows), 'total' => count($resolvedRows)])); ?>

        </p>

        <div class="flex items-center gap-3">
            <span class="text-sm text-base-content/70" data-table-page-indicator>
                <?php echo e(__('daisy::components.table_page', ['page' => 1, 'pages' => 1])); ?>

            </span>

            <div class="join">
                <button type="button" class="btn btn-sm join-item" data-table-prev><?php echo e(__('daisy::components.table_previous')); ?></button>
                <button type="button" class="btn btn-sm join-item" data-table-next><?php echo e(__('daisy::components.table_next')); ?></button>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/table.blade.php ENDPATH**/ ?>
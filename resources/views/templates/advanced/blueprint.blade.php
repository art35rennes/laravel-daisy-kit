{{--
    Blueprint Template

    @template-label Blueprint examples
    @template-description Reusable Blueprint showcase with workflow, readonly, data schema, and integration pipeline examples.
    @template-tags blueprint,workflow,graph,schema,rete
    @template-type example
    @template-route templates.advanced.blueprint

    Host applications keep ownership of persistence. This template ships reusable sample graphs
    that exercise the public Blueprint JSON contract and DaisyUI theming.
--}}

@props([
    'title' => __('daisy::components.blueprint_template.title'),
    'description' => __('daisy::components.blueprint_template.description'),
    'showHeader' => true,
    'showThemeStress' => true,
    'showFeatureCoverage' => true,
    'showContract' => true,
    'showThemeTokens' => true,
    'showReadonly' => true,
    'showTyped' => true,
    'showSchema' => true,
    'showIntegration' => true,
    'showMinimal' => true,
    'namePrefix' => 'blueprint',
    'themeStressThemes' => ['light', 'dark', 'corporate', 'business', 'night', 'wireframe', 'cyberpunk', 'synthwave'],
    'workflowHeight' => '560px',
    'exampleHeight' => '420px',
    'workflowNodeTypes' => null,
    'workflowValue' => null,
    'typedNodeTypes' => null,
    'typedValue' => null,
    'themeNodeTypes' => null,
    'themeValue' => null,
    'schemaNodeTypes' => null,
    'schemaValue' => null,
    'minimalNodeTypes' => null,
    'integrationNodeTypes' => null,
    'integrationValue' => null,
])

@php
    $workflowNodeTypes ??= [
        [
            'type' => 'trigger',
            'label' => __('daisy::components.blueprint_template.workflow.trigger'),
            'category' => __('daisy::components.blueprint_template.categories.workflow'),
            'description' => __('daisy::components.blueprint_template.workflow.trigger_description'),
            'theme' => 'primary',
            'icon' => 'EV',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Trigger'],
            'outputs' => [['key' => 'next', 'label' => 'Next', 'kind' => 'flow', 'type' => 'flow', 'multiple' => true]],
            'controls' => [
                ['key' => 'event', 'label' => 'Event', 'type' => 'select', 'options' => ['Order paid', 'Order shipped', 'Refund created']],
            ],
            'defaults' => ['event' => 'Order paid'],
        ],
        [
            'type' => 'transform',
            'label' => __('daisy::components.blueprint_template.workflow.transform'),
            'category' => __('daisy::components.blueprint_template.categories.workflow'),
            'description' => __('daisy::components.blueprint_template.workflow.transform_description'),
            'theme' => 'info',
            'icon' => 'FN',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Transform'],
            'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'flow', 'type' => 'flow']],
            'outputs' => [['key' => 'out', 'label' => 'Out', 'kind' => 'flow', 'type' => 'flow', 'multiple' => true]],
            'controls' => [
                ['key' => 'mapping', 'label' => 'Mapping', 'type' => 'textarea', 'help' => 'Payload normalization expression'],
            ],
            'defaults' => ['mapping' => 'normalize-order'],
        ],
        [
            'type' => 'branch',
            'label' => __('daisy::components.blueprint_template.workflow.branch'),
            'category' => __('daisy::components.blueprint_template.categories.logic'),
            'description' => __('daisy::components.blueprint_template.workflow.branch_description'),
            'theme' => 'warning',
            'icon' => 'IF',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Condition'],
            'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'flow', 'type' => 'flow']],
            'outputs' => [
                ['key' => 'success', 'label' => 'Success', 'kind' => 'flow', 'type' => 'flow', 'multiple' => true],
                ['key' => 'manual', 'label' => 'Manual', 'kind' => 'flow', 'type' => 'flow', 'multiple' => true],
            ],
            'controls' => [
                ['key' => 'rule', 'label' => 'Rule', 'type' => 'text'],
                ['key' => 'priority', 'label' => 'Priority', 'type' => 'range', 'min' => 1, 'max' => 5, 'step' => 1, 'default' => 3],
            ],
            'defaults' => ['rule' => 'total > 500'],
        ],
        [
            'type' => 'notify',
            'label' => __('daisy::components.blueprint_template.workflow.notify'),
            'category' => __('daisy::components.blueprint_template.categories.communication'),
            'description' => __('daisy::components.blueprint_template.workflow.notify_description'),
            'theme' => 'success',
            'icon' => 'OP',
            'nameStrategy' => ['mode' => 'preset', 'value' => __('daisy::components.blueprint_template.workflow.notify')],
            'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'flow', 'type' => 'flow']],
            'controls' => [
                ['key' => 'channel', 'label' => 'Channel', 'type' => 'select', 'options' => ['ops', 'support', 'finance']],
                ['key' => 'urgent', 'label' => 'Urgent', 'type' => 'checkbox', 'default' => false],
            ],
            'defaults' => ['channel' => 'ops'],
        ],
    ];

    $workflowValue ??= [
        'version' => 1,
        'nodes' => [
            ['id' => 'trigger-1', 'type' => 'trigger', 'label' => __('daisy::components.blueprint_template.workflow.order_paid'), 'position' => ['x' => 40, 'y' => 90], 'data' => ['event' => 'Order paid']],
            ['id' => 'transform-1', 'type' => 'transform', 'label' => __('daisy::components.blueprint_template.workflow.normalize_order'), 'position' => ['x' => 340, 'y' => 90], 'data' => ['mapping' => 'normalize-order']],
            ['id' => 'branch-1', 'type' => 'branch', 'label' => __('daisy::components.blueprint_template.workflow.priority_check'), 'position' => ['x' => 640, 'y' => 90], 'data' => ['rule' => 'total > 500']],
            ['id' => 'notify-1', 'type' => 'notify', 'label' => __('daisy::components.blueprint_template.workflow.notify_ops'), 'position' => ['x' => 940, 'y' => 90], 'data' => ['channel' => 'ops']],
        ],
        'edges' => [
            ['id' => 'edge-1', 'source' => 'trigger-1', 'sourcePort' => 'next', 'target' => 'transform-1', 'targetPort' => 'in', 'data' => []],
            ['id' => 'edge-2', 'source' => 'transform-1', 'sourcePort' => 'out', 'target' => 'branch-1', 'targetPort' => 'in', 'data' => []],
            ['id' => 'edge-3', 'source' => 'branch-1', 'sourcePort' => 'manual', 'target' => 'notify-1', 'targetPort' => 'in', 'data' => []],
        ],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $typedNodeTypes ??= [
        [
            'type' => 'metric-source',
            'label' => __('daisy::components.blueprint_template.typed.metric_source'),
            'category' => __('daisy::components.blueprint_template.categories.typed'),
            'description' => __('daisy::components.blueprint_template.typed.metric_source_description'),
            'theme' => 'accent',
            'icon' => 'DB',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Metric'],
            'outputs' => [['key' => 'count', 'label' => 'Count', 'kind' => 'metric', 'type' => 'int', 'multiple' => true]],
            'controls' => [
                ['key' => 'source', 'label' => 'Source', 'type' => 'text'],
            ],
            'defaults' => ['source' => 'orders.count'],
        ],
        [
            'type' => 'threshold',
            'label' => __('daisy::components.blueprint_template.typed.threshold'),
            'category' => __('daisy::components.blueprint_template.categories.logic'),
            'description' => __('daisy::components.blueprint_template.typed.threshold_description'),
            'theme' => 'warning',
            'icon' => '>=',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Threshold'],
            'inputs' => [['key' => 'value', 'label' => 'Value', 'kind' => 'metric', 'type' => 'float']],
            'outputs' => [['key' => 'pass', 'label' => 'Pass', 'kind' => 'flow', 'type' => 'flow', 'multiple' => true]],
            'controls' => [
                ['key' => 'operator', 'label' => 'Operator', 'type' => 'radio', 'options' => ['>=', '>', '=']],
                ['key' => 'threshold', 'label' => 'Threshold', 'type' => 'number', 'min' => 0, 'step' => 1],
            ],
            'defaults' => ['operator' => '>=', 'threshold' => 500],
        ],
        [
            'type' => 'message-template',
            'label' => __('daisy::components.blueprint_template.typed.message_template'),
            'category' => __('daisy::components.blueprint_template.categories.communication'),
            'description' => __('daisy::components.blueprint_template.typed.message_template_description'),
            'theme' => 'info',
            'icon' => 'TXT',
            'nameStrategy' => ['mode' => 'preset', 'value' => __('daisy::components.blueprint_template.typed.message_template')],
            'outputs' => [['key' => 'message', 'label' => 'Message', 'kind' => 'payload', 'type' => 'str', 'multiple' => true]],
            'controls' => [
                ['key' => 'template', 'label' => 'Template', 'type' => 'textarea'],
            ],
            'defaults' => ['template' => 'High value order'],
        ],
        [
            'type' => 'dispatch',
            'label' => __('daisy::components.blueprint_template.typed.dispatch'),
            'category' => __('daisy::components.blueprint_template.categories.workflow'),
            'description' => __('daisy::components.blueprint_template.typed.dispatch_description'),
            'theme' => 'success',
            'icon' => 'Q',
            'nameStrategy' => ['mode' => 'free'],
            'inputs' => [
                ['key' => 'in', 'label' => 'In', 'kind' => 'flow', 'type' => 'flow'],
                ['key' => 'message', 'label' => 'Message', 'kind' => 'payload', 'type' => 'str'],
            ],
            'controls' => [
                ['key' => 'queue', 'label' => 'Queue', 'type' => 'select', 'options' => ['ops', 'critical', 'default']],
            ],
            'defaults' => ['queue' => 'ops'],
        ],
    ];

    $typedValue ??= [
        'version' => 1,
        'nodes' => [
            ['id' => 'metric-1', 'type' => 'metric-source', 'label' => __('daisy::components.blueprint_template.typed.order_count'), 'position' => ['x' => 40, 'y' => 80], 'data' => ['source' => 'orders.count']],
            ['id' => 'threshold-1', 'type' => 'threshold', 'label' => __('daisy::components.blueprint_template.typed.high_value'), 'position' => ['x' => 350, 'y' => 80], 'data' => ['operator' => '>=', 'threshold' => 500]],
            ['id' => 'template-1', 'type' => 'message-template', 'label' => __('daisy::components.blueprint_template.typed.message_template'), 'position' => ['x' => 350, 'y' => 270], 'data' => ['template' => 'High value order']],
            ['id' => 'dispatch-1', 'type' => 'dispatch', 'label' => __('daisy::components.blueprint_template.typed.dispatch_ops'), 'position' => ['x' => 700, 'y' => 140], 'data' => ['queue' => 'ops']],
        ],
        'edges' => [
            ['id' => 'typed-edge-1', 'source' => 'metric-1', 'sourcePort' => 'count', 'target' => 'threshold-1', 'targetPort' => 'value', 'data' => []],
            ['id' => 'typed-edge-2', 'source' => 'threshold-1', 'sourcePort' => 'pass', 'target' => 'dispatch-1', 'targetPort' => 'in', 'data' => []],
            ['id' => 'typed-edge-3', 'source' => 'template-1', 'sourcePort' => 'message', 'target' => 'dispatch-1', 'targetPort' => 'message', 'data' => []],
        ],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $themeNodeTypes ??= collect(['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'])
        ->map(fn ($theme) => [
            'type' => "theme-{$theme}",
            'label' => ucfirst($theme),
            'category' => __('daisy::components.blueprint_template.categories.theme_tokens'),
            'description' => __('daisy::components.blueprint_template.theme_tokens.description', ['theme' => $theme]),
            'theme' => $theme,
            'icon' => strtoupper(substr($theme, 0, 2)),
            'nameStrategy' => ['mode' => 'preset', 'value' => ucfirst($theme)],
            'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'theme', 'type' => 'obj']],
            'outputs' => [['key' => 'out', 'label' => 'Out', 'kind' => 'theme', 'type' => 'obj', 'multiple' => true]],
            'defaults' => ['token' => $theme],
        ])
        ->all();

    $themeValue ??= [
        'version' => 1,
        'nodes' => collect(['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'])
            ->map(fn ($theme, $index) => [
                'id' => "theme-{$theme}",
                'type' => "theme-{$theme}",
                'label' => ucfirst($theme),
                'position' => ['x' => 80 + ($index % 4) * 300, 'y' => 70 + intdiv($index, 4) * 150],
                'data' => ['token' => $theme],
            ])
            ->all(),
        'edges' => [],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $schemaNodeTypes ??= [
        [
            'type' => 'entity',
            'label' => __('daisy::components.blueprint_template.schema.entity'),
            'category' => __('daisy::components.blueprint_template.categories.schema'),
            'description' => __('daisy::components.blueprint_template.schema.entity_description'),
            'theme' => 'secondary',
            'icon' => 'DB',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Entity'],
            'outputs' => [['key' => 'fields', 'label' => 'Fields', 'kind' => 'schema', 'type' => 'class', 'multiple' => true]],
            'controls' => [
                ['key' => 'table', 'label' => 'Table', 'type' => 'text'],
            ],
            'defaults' => ['table' => 'orders'],
        ],
        [
            'type' => 'field',
            'label' => __('daisy::components.blueprint_template.schema.field'),
            'category' => __('daisy::components.blueprint_template.categories.schema'),
            'description' => __('daisy::components.blueprint_template.schema.field_description'),
            'theme' => 'accent',
            'icon' => 'COL',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Field'],
            'inputs' => [['key' => 'entity', 'label' => 'Entity', 'kind' => 'schema', 'type' => 'class']],
            'outputs' => [['key' => 'relation', 'label' => 'Relation', 'kind' => 'relation', 'type' => 'obj', 'multiple' => true]],
            'controls' => [
                ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
                ['key' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => ['uuid', 'string', 'integer', 'datetime']],
            ],
            'defaults' => ['name' => 'customer_id', 'type' => 'uuid'],
        ],
        [
            'type' => 'relation',
            'label' => __('daisy::components.blueprint_template.schema.relation'),
            'category' => __('daisy::components.blueprint_template.categories.schema'),
            'description' => __('daisy::components.blueprint_template.schema.relation_description'),
            'theme' => 'secondary',
            'icon' => 'FK',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Relation'],
            'inputs' => [['key' => 'from', 'label' => 'From', 'kind' => 'relation', 'type' => 'obj']],
            'controls' => [
                ['key' => 'kind', 'label' => 'Relation', 'type' => 'select', 'options' => ['belongsTo', 'hasMany', 'hasOne']],
            ],
            'defaults' => ['kind' => 'belongsTo'],
        ],
    ];

    $schemaValue ??= [
        'version' => 1,
        'nodes' => [
            ['id' => 'orders', 'type' => 'entity', 'label' => 'orders', 'position' => ['x' => 40, 'y' => 80], 'data' => ['table' => 'orders']],
            ['id' => 'customer-id', 'type' => 'field', 'label' => 'customer_id', 'position' => ['x' => 350, 'y' => 80], 'data' => ['name' => 'customer_id', 'type' => 'uuid']],
            ['id' => 'customers', 'type' => 'relation', 'label' => 'customers', 'position' => ['x' => 660, 'y' => 80], 'data' => ['kind' => 'belongsTo']],
        ],
        'edges' => [
            ['id' => 'schema-edge-1', 'source' => 'orders', 'sourcePort' => 'fields', 'target' => 'customer-id', 'targetPort' => 'entity', 'data' => []],
            ['id' => 'schema-edge-2', 'source' => 'customer-id', 'sourcePort' => 'relation', 'target' => 'customers', 'targetPort' => 'from', 'data' => []],
        ],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $minimalNodeTypes ??= collect($schemaNodeTypes)
        ->map(function ($type) {
            $type['display'] = 'minimal';
            $type['icon'] = $type['icon'] ?? strtoupper(substr((string) ($type['label'] ?? $type['type'] ?? 'N'), 0, 2));

            return $type;
        })
        ->all();

    $integrationNodeTypes ??= [
        [
            'type' => 'source',
            'label' => __('daisy::components.blueprint_template.integration.source'),
            'category' => __('daisy::components.blueprint_template.categories.integration'),
            'description' => __('daisy::components.blueprint_template.integration.source_description'),
            'theme' => 'accent',
            'icon' => 'API',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Source'],
            'outputs' => [['key' => 'rows', 'label' => 'Rows', 'kind' => 'dataset', 'type' => 'obj', 'multiple' => true]],
            'controls' => [
                ['key' => 'connector', 'label' => 'Connector', 'type' => 'select', 'options' => ['Stripe', 'Shopify', 'HubSpot']],
            ],
            'defaults' => ['connector' => 'Stripe'],
        ],
        [
            'type' => 'filter',
            'label' => __('daisy::components.blueprint_template.integration.filter'),
            'category' => __('daisy::components.blueprint_template.categories.integration'),
            'description' => __('daisy::components.blueprint_template.integration.filter_description'),
            'theme' => 'warning',
            'icon' => 'FLT',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Filter'],
            'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'dataset', 'type' => 'obj']],
            'outputs' => [['key' => 'out', 'label' => 'Out', 'kind' => 'dataset', 'type' => 'obj', 'multiple' => true]],
            'controls' => [
                ['key' => 'where', 'label' => 'Where', 'type' => 'textarea'],
            ],
            'defaults' => ['where' => 'status = paid'],
        ],
        [
            'type' => 'export',
            'label' => __('daisy::components.blueprint_template.integration.export'),
            'category' => __('daisy::components.blueprint_template.categories.integration'),
            'description' => __('daisy::components.blueprint_template.integration.export_description'),
            'theme' => 'success',
            'icon' => 'OUT',
            'nameStrategy' => ['mode' => 'auto', 'prefix' => 'Export'],
            'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'dataset', 'type' => 'obj']],
            'controls' => [
                ['key' => 'target', 'label' => 'Target', 'type' => 'text'],
            ],
            'defaults' => ['target' => 'warehouse.orders'],
        ],
    ];

    $integrationValue ??= [
        'version' => 1,
        'nodes' => [
            ['id' => 'source-1', 'type' => 'source', 'label' => __('daisy::components.blueprint_template.integration.stripe_events'), 'position' => ['x' => 40, 'y' => 90], 'data' => ['connector' => 'Stripe']],
            ['id' => 'filter-1', 'type' => 'filter', 'label' => __('daisy::components.blueprint_template.integration.paid_orders'), 'position' => ['x' => 360, 'y' => 90], 'data' => ['where' => 'status = paid']],
            ['id' => 'export-1', 'type' => 'export', 'label' => __('daisy::components.blueprint_template.integration.warehouse'), 'position' => ['x' => 680, 'y' => 90], 'data' => ['target' => 'warehouse.orders']],
        ],
        'edges' => [
            ['id' => 'integration-edge-1', 'source' => 'source-1', 'sourcePort' => 'rows', 'target' => 'filter-1', 'targetPort' => 'in', 'data' => []],
            ['id' => 'integration-edge-2', 'source' => 'filter-1', 'sourcePort' => 'out', 'target' => 'export-1', 'targetPort' => 'in', 'data' => []],
        ],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $featureGroups = collect(trans('daisy::components.blueprint_template.features.groups'));
    $contractGroups = collect(trans('daisy::components.blueprint_template.contract.groups'));
@endphp

<section {{ $attributes->merge(['class' => 'space-y-8']) }}>
    @if(($showHeader && ($title || $description)) || $showThemeStress)
        <header class="flex flex-wrap items-start justify-between gap-4">
            @if($showHeader && ($title || $description))
                <div class="min-w-0 space-y-2">
                    @if($title)
                        <h1 class="text-2xl font-semibold">{{ $title }}</h1>
                    @endif

                    @if($description)
                        <p class="max-w-3xl text-sm text-base-content/70">{{ $description }}</p>
                    @endif
                </div>
            @else
                <div class="min-w-0"></div>
            @endif

            @if($showThemeStress)
                <div class="shrink-0">
                    <x-daisy::ui.advanced.theme-controller
                        name="blueprint-theme-stress"
                        variant="dropdown"
                        size="sm"
                        :themes="$themeStressThemes"
                        :label="__('daisy::components.blueprint_template.theme_stress.label')"
                    />
                </div>
            @endif
        </header>
    @endif

    @if($showFeatureCoverage)
        <section class="space-y-3">
            <div class="space-y-1">
                <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.features.title') }}</h2>
                <p class="max-w-4xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.features.description') }}</p>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach($featureGroups as $group)
                    <div class="rounded-box border border-base-300 bg-base-100 p-4">
                        <h3 class="text-sm font-semibold">{{ $group['title'] ?? '' }}</h3>
                        <ul class="mt-3 space-y-2 text-sm text-base-content/70">
                            @foreach($group['items'] ?? [] as $item)
                                <li class="flex gap-2">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($showContract)
        <section class="space-y-3" data-blueprint-contract>
            <div class="space-y-1">
                <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.contract.title') }}</h2>
                <p class="max-w-4xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.contract.description') }}</p>
            </div>

            <div class="grid gap-3 lg:grid-cols-3">
                @foreach($contractGroups as $group)
                    <div class="rounded-box border border-base-300 bg-base-100 p-4">
                        <h3 class="text-sm font-semibold">{{ $group['title'] ?? '' }}</h3>
                        <ul class="mt-3 space-y-2 text-sm text-base-content/70">
                            @foreach($group['items'] ?? [] as $item)
                                <li class="flex gap-2">
                                    <code class="rounded bg-base-200 px-1.5 py-0.5 text-xs text-base-content">{{ $item['key'] ?? '' }}</code>
                                    <span>{{ $item['description'] ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($showThemeTokens)
        <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.theme_tokens.title') }}</h2>
                    <p class="max-w-3xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.theme_tokens.description') }}</p>
                </div>
                <span class="badge badge-neutral badge-outline">{{ __('daisy::components.blueprint_template.badges.theme_tokens') }}</span>
            </div>

            <x-daisy::ui.advanced.blueprint
                mode="view"
                height="360px"
                :toolbar="false"
                :palette="false"
                :details="false"
                :minimap="false"
                :auto-arrange="false"
                :fit-on-init="false"
                :history="false"
                :reroute="false"
                :node-types="$themeNodeTypes"
                :value="$themeValue"
            />
        </article>
    @endif

    <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="space-y-1">
                <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.workflow.title') }}</h2>
                <p class="max-w-3xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.workflow.description') }}</p>
            </div>
            <span class="badge badge-primary badge-outline">{{ __('daisy::components.blueprint_template.badges.editable') }}</span>
        </div>

        <x-daisy::ui.advanced.blueprint
            :name="$namePrefix.'_workflow'"
            :height="$workflowHeight"
            :node-types="$workflowNodeTypes"
            :value="$workflowValue"
        />
    </article>

    @if($showReadonly)
        <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.readonly.title') }}</h2>
                    <p class="max-w-3xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.readonly.description') }}</p>
                </div>
                <span class="badge badge-info badge-outline">{{ __('daisy::components.blueprint_template.badges.readonly') }}</span>
            </div>

            <x-daisy::ui.advanced.blueprint
                mode="view"
                :height="$exampleHeight"
                :node-types="$workflowNodeTypes"
                :value="$workflowValue"
                :details="false"
            />
        </article>
    @endif

    @if($showTyped)
        <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.typed.title') }}</h2>
                    <p class="max-w-3xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.typed.description') }}</p>
                </div>
                <span class="badge badge-warning badge-outline">{{ __('daisy::components.blueprint_template.badges.typed') }}</span>
            </div>

            <x-daisy::ui.advanced.blueprint
                :name="$namePrefix.'_typed'"
                :height="$exampleHeight"
                :node-types="$typedNodeTypes"
                :value="$typedValue"
            />
        </article>
    @endif

    <div class="grid gap-6 xl:grid-cols-2">
        @if($showSchema)
            <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.schema.title') }}</h2>
                        <p class="text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.schema.description') }}</p>
                    </div>
                    <span class="badge badge-success badge-outline">{{ __('daisy::components.blueprint_template.badges.schema') }}</span>
                </div>

                <x-daisy::ui.advanced.blueprint
                    mode="view"
                    :height="$exampleHeight"
                    :node-types="$schemaNodeTypes"
                    :value="$schemaValue"
                    :auto-arrange="false"
                />
            </article>
        @endif

        @if($showIntegration)
            <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1">
                        <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.integration.title') }}</h2>
                        <p class="text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.integration.description') }}</p>
                    </div>
                    <span class="badge badge-secondary badge-outline">{{ __('daisy::components.blueprint_template.badges.pipeline') }}</span>
                </div>

                <x-daisy::ui.advanced.blueprint
                    :name="$namePrefix.'_integration'"
                    :height="$exampleHeight"
                    :node-types="$integrationNodeTypes"
                    :value="$integrationValue"
                />
            </article>
        @endif
    </div>

    @if($showMinimal)
        <article class="space-y-3 rounded-box border border-base-300 bg-base-100 p-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h2 class="text-base font-semibold">{{ __('daisy::components.blueprint_template.examples.minimal.title') }}</h2>
                    <p class="max-w-3xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.minimal.description') }}</p>
                </div>
                <span class="badge badge-neutral badge-outline">{{ __('daisy::components.blueprint_template.badges.minimal') }}</span>
            </div>

            <x-daisy::ui.advanced.blueprint
                mode="view"
                :height="$exampleHeight"
                :toolbar="false"
                :palette="false"
                :details="false"
                :minimap="false"
                :auto-arrange="false"
                :history="false"
                :reroute="false"
                :node-types="$minimalNodeTypes"
                :value="$schemaValue"
            />
        </article>
    @endif
</section>

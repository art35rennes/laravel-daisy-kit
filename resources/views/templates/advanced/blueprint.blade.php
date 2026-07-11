{{--
    @template-name Blueprint workflows
    @template-description Focused examples for directed business workflows.
    @template-tags blueprint,workflow,graph,dagre
    @template-category advanced
    @template-route templates.advanced.blueprint
--}}

@props([
    'showHeader' => true,
    'showContract' => true,
    'namePrefix' => 'blueprint',
])

@php
    $integratorFields = [
        [
            'key' => 'owner',
            'type' => 'text',
            'label' => __('daisy::components.blueprint_template.fields.owner'),
            'section' => __('daisy::components.blueprint_template.fields.assignment'),
        ],
        [
            'key' => 'priority',
            'type' => 'select',
            'label' => __('daisy::components.blueprint_template.fields.priority'),
            'section' => __('daisy::components.blueprint_template.fields.assignment'),
            'options' => [
                ['value' => 'normal', 'label' => __('daisy::components.blueprint_template.fields.normal')],
                ['value' => 'high', 'label' => __('daisy::components.blueprint_template.fields.high')],
            ],
        ],
        [
            'key' => 'expedited',
            'type' => 'checkbox',
            'label' => __('daisy::components.blueprint_template.fields.expedited'),
            'section' => __('daisy::components.blueprint_template.fields.assignment'),
        ],
    ];
    $integratorDefaults = ['owner' => '', 'priority' => 'normal', 'expedited' => false];
    $nodeCategories = [
        ['value' => 'start', 'label' => __('daisy::components.blueprint_template.categories.start'), 'color' => 'info', 'defaults' => $integratorDefaults, 'fields' => $integratorFields],
        ['value' => 'work', 'label' => __('daisy::components.blueprint_template.categories.work'), 'color' => 'primary', 'defaults' => $integratorDefaults, 'fields' => $integratorFields],
        ['value' => 'approval', 'label' => __('daisy::components.blueprint_template.categories.approval'), 'color' => 'warning', 'defaults' => $integratorDefaults, 'fields' => $integratorFields],
        ['value' => 'done', 'label' => __('daisy::components.blueprint_template.categories.done'), 'color' => 'success', 'defaults' => $integratorDefaults, 'fields' => $integratorFields],
    ];
    $transitionCategories = [
        ['value' => 'progress', 'label' => __('daisy::components.blueprint_template.categories.progress'), 'color' => 'primary', 'shape' => 's', 'defaults' => ['notify' => false], 'fields' => [['key' => 'notify', 'type' => 'checkbox', 'label' => __('daisy::components.blueprint_template.fields.notify')]]],
        ['value' => 'approval', 'label' => __('daisy::components.blueprint_template.categories.approval'), 'color' => 'success', 'shape' => 'orthogonal', 'defaults' => ['notify' => true], 'fields' => [['key' => 'notify', 'type' => 'checkbox', 'label' => __('daisy::components.blueprint_template.fields.notify')]]],
        ['value' => 'return', 'label' => __('daisy::components.blueprint_template.categories.return'), 'color' => 'warning', 'shape' => 'curve', 'defaults' => ['notify' => true], 'fields' => [['key' => 'notify', 'type' => 'checkbox', 'label' => __('daisy::components.blueprint_template.fields.notify')]]],
    ];

    $approval = [
        'version' => 1,
        'nodes' => [
            ['id' => 'draft', 'label' => __('daisy::components.blueprint_template.workflow.draft'), 'description' => __('daisy::components.blueprint_template.workflow.draft_description'), 'category' => 'start', 'position' => ['x' => 60, 'y' => 110], 'data' => ['owner' => 'Editorial team', 'priority' => 'normal', 'expedited' => false]],
            ['id' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.review'), 'description' => __('daisy::components.blueprint_template.workflow.review_description'), 'category' => 'work', 'position' => ['x' => 500, 'y' => 110], 'data' => ['owner' => 'Reviewer', 'priority' => 'high', 'expedited' => false]],
            ['id' => 'approval', 'label' => __('daisy::components.blueprint_template.workflow.approval'), 'description' => __('daisy::components.blueprint_template.workflow.approval_description'), 'category' => 'approval', 'position' => ['x' => 940, 'y' => 110], 'data' => ['owner' => 'Decision maker', 'priority' => 'high', 'expedited' => true]],
            ['id' => 'published', 'label' => __('daisy::components.blueprint_template.workflow.published'), 'description' => __('daisy::components.blueprint_template.workflow.published_description'), 'category' => 'done', 'position' => ['x' => 1380, 'y' => 110], 'data' => ['owner' => 'Publisher', 'priority' => 'normal', 'expedited' => false]],
        ],
        'transitions' => [
            ['id' => 'submit', 'source' => 'draft', 'target' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.submit'), 'description' => '', 'category' => 'progress', 'data' => []],
            ['id' => 'request-approval', 'source' => 'review', 'target' => 'approval', 'label' => __('daisy::components.blueprint_template.workflow.request_approval'), 'description' => '', 'category' => 'approval', 'data' => []],
            ['id' => 'publish', 'source' => 'approval', 'target' => 'published', 'label' => __('daisy::components.blueprint_template.workflow.publish'), 'description' => '', 'category' => 'approval', 'data' => []],
        ],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $cycle = $approval;
    $cycle['nodes'] = collect($cycle['nodes'])
        ->map(fn ($node) => [...$node, 'position' => null])
        ->all();
    $cycle['transitions'][] = ['id' => 'return-review', 'source' => 'approval', 'target' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.return_review'), 'description' => __('daisy::components.blueprint_template.workflow.return_description'), 'category' => 'return', 'data' => []];
    $cycle['transitions'][] = ['id' => 'reopen-draft', 'source' => 'review', 'target' => 'draft', 'label' => __('daisy::components.blueprint_template.workflow.reopen'), 'description' => '', 'category' => 'return', 'data' => []];
    $cycle['transitions'][] = ['id' => 'self-review', 'source' => 'review', 'target' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.recheck'), 'description' => '', 'category' => 'work', 'data' => []];

    $denseNodes = collect(range(1, 14))->map(fn ($index) => [
        'id' => "step-{$index}",
        'label' => __('daisy::components.blueprint_template.workflow.step', ['number' => $index]),
        'description' => __('daisy::components.blueprint_template.workflow.dense_description'),
        'category' => $index === 1 ? 'start' : ($index === 14 ? 'done' : 'work'),
        'position' => null,
        'data' => ['external_reference' => "STEP-{$index}"],
    ])->all();
    $denseTransitions = collect(range(1, 13))->map(fn ($index) => [
        'id' => "dense-main-{$index}",
        'source' => "step-{$index}",
        'target' => 'step-'.($index + 1),
        'label' => __('daisy::components.blueprint_template.workflow.continue'),
        'description' => '',
        'category' => 'progress',
        'data' => [],
    ])->all();
    $denseTransitions = array_merge($denseTransitions, [
        ['id' => 'dense-branch-1', 'source' => 'step-2', 'target' => 'step-6', 'label' => __('daisy::components.blueprint_template.workflow.escalate'), 'description' => '', 'category' => 'approval', 'data' => []],
        ['id' => 'dense-branch-2', 'source' => 'step-4', 'target' => 'step-9', 'label' => __('daisy::components.blueprint_template.workflow.escalate'), 'description' => '', 'category' => 'approval', 'data' => []],
        ['id' => 'dense-return-1', 'source' => 'step-8', 'target' => 'step-3', 'label' => __('daisy::components.blueprint_template.workflow.return_review'), 'description' => '', 'category' => 'return', 'data' => []],
        ['id' => 'dense-return-2', 'source' => 'step-12', 'target' => 'step-7', 'label' => __('daisy::components.blueprint_template.workflow.return_review'), 'description' => '', 'category' => 'return', 'data' => []],
        ['id' => 'dense-parallel-1', 'source' => 'step-5', 'target' => 'step-10', 'label' => __('daisy::components.blueprint_template.workflow.fast_track'), 'description' => '', 'category' => 'progress', 'data' => []],
        ['id' => 'dense-parallel-2', 'source' => 'step-5', 'target' => 'step-10', 'label' => __('daisy::components.blueprint_template.workflow.manual_track'), 'description' => '', 'category' => 'approval', 'data' => []],
    ]);
    $dense = [
        'version' => 1,
        'nodes' => $denseNodes,
        'transitions' => $denseTransitions,
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];
@endphp

<section class="space-y-8">
    @if($showHeader)
        <header class="space-y-2">
            <h1 class="text-2xl font-semibold">{{ __('daisy::components.blueprint_template.title') }}</h1>
            <p class="max-w-3xl text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.description') }}</p>
        </header>
    @endif

    @if($showContract)
        <section class="rounded-box border border-base-300 bg-base-100 p-4" data-blueprint-contract>
            <h2 class="font-semibold">{{ __('daisy::components.blueprint_template.contract.title') }}</h2>
            <p class="mt-2 text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.contract.description') }}</p>
            <p class="mt-3 text-xs text-base-content/60">
                getValue · setValue · addNode · updateNode · removeNode · addTransition · updateTransition · removeTransition · arrange · fit · undo · redo · destroy
            </p>
            <p class="mt-1 text-xs text-base-content/60">
                daisy:blueprint:init · daisy:blueprint:change · daisy:blueprint:select · daisy:blueprint:error
            </p>
        </section>
    @endif

    <article class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold">{{ __('daisy::components.blueprint_template.examples.approval.title') }}</h2>
            <p class="text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.approval.description') }}</p>
        </div>
        <x-daisy::ui.advanced.blueprint
            :name="$namePrefix.'_approval'"
            :value="$approval"
            :node-categories="$nodeCategories"
            :transition-categories="$transitionCategories"
            height="480px"
        />
    </article>

    <article class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold">{{ __('daisy::components.blueprint_template.examples.cycle.title') }}</h2>
            <p class="text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.cycle.description') }}</p>
        </div>
        <x-daisy::ui.advanced.blueprint
            :name="$namePrefix.'_cycle'"
            :value="$cycle"
            :node-categories="$nodeCategories"
            :transition-categories="$transitionCategories"
            layout="radial"
            height="520px"
        />
    </article>

    <article class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold">{{ __('daisy::components.blueprint_template.examples.dense.title') }}</h2>
            <p class="text-sm text-base-content/70">{{ __('daisy::components.blueprint_template.examples.dense.description') }}</p>
        </div>
        <x-daisy::ui.advanced.blueprint
            :name="$namePrefix.'_dense'"
            :value="$dense"
            :node-categories="$nodeCategories"
            :transition-categories="$transitionCategories"
            direction="TB"
            layout="hierarchical"
            height="620px"
        />
    </article>
</section>

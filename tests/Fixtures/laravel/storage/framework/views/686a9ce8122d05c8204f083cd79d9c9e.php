

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'showHeader' => true,
    'showContract' => true,
    'namePrefix' => 'blueprint',
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
    'showHeader' => true,
    'showContract' => true,
    'namePrefix' => 'blueprint',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $nodeCategories = [
        ['value' => 'start', 'label' => __('daisy::components.blueprint_template.categories.start'), 'color' => 'info'],
        ['value' => 'work', 'label' => __('daisy::components.blueprint_template.categories.work'), 'color' => 'primary'],
        ['value' => 'approval', 'label' => __('daisy::components.blueprint_template.categories.approval'), 'color' => 'warning'],
        ['value' => 'done', 'label' => __('daisy::components.blueprint_template.categories.done'), 'color' => 'success'],
    ];
    $transitionCategories = [
        ['value' => 'progress', 'label' => __('daisy::components.blueprint_template.categories.progress'), 'color' => 'primary', 'shape' => 's'],
        ['value' => 'approval', 'label' => __('daisy::components.blueprint_template.categories.approval'), 'color' => 'success', 'shape' => 'orthogonal'],
        ['value' => 'return', 'label' => __('daisy::components.blueprint_template.categories.return'), 'color' => 'warning', 'shape' => 'curve'],
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
            ['id' => 'submit', 'source' => 'draft', 'target' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.submit'), 'description' => '', 'category' => 'progress'],
            ['id' => 'request-approval', 'source' => 'review', 'target' => 'approval', 'label' => __('daisy::components.blueprint_template.workflow.request_approval'), 'description' => '', 'category' => 'approval'],
            ['id' => 'publish', 'source' => 'approval', 'target' => 'published', 'label' => __('daisy::components.blueprint_template.workflow.publish'), 'description' => '', 'category' => 'approval'],
        ],
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];

    $cycle = $approval;
    $cycle['nodes'] = collect($cycle['nodes'])
        ->map(fn ($node) => [...$node, 'position' => null])
        ->all();
    $cycle['transitions'][] = ['id' => 'return-review', 'source' => 'approval', 'target' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.return_review'), 'description' => __('daisy::components.blueprint_template.workflow.return_description'), 'category' => 'return'];
    $cycle['transitions'][] = ['id' => 'reopen-draft', 'source' => 'review', 'target' => 'draft', 'label' => __('daisy::components.blueprint_template.workflow.reopen'), 'description' => '', 'category' => 'return'];
    $cycle['transitions'][] = ['id' => 'self-review', 'source' => 'review', 'target' => 'review', 'label' => __('daisy::components.blueprint_template.workflow.recheck'), 'description' => '', 'category' => 'work'];

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
    ])->all();
    $denseTransitions = array_merge($denseTransitions, [
        ['id' => 'dense-branch-1', 'source' => 'step-2', 'target' => 'step-6', 'label' => __('daisy::components.blueprint_template.workflow.escalate'), 'description' => '', 'category' => 'approval'],
        ['id' => 'dense-branch-2', 'source' => 'step-4', 'target' => 'step-9', 'label' => __('daisy::components.blueprint_template.workflow.escalate'), 'description' => '', 'category' => 'approval'],
        ['id' => 'dense-return-1', 'source' => 'step-8', 'target' => 'step-3', 'label' => __('daisy::components.blueprint_template.workflow.return_review'), 'description' => '', 'category' => 'return'],
        ['id' => 'dense-return-2', 'source' => 'step-12', 'target' => 'step-7', 'label' => __('daisy::components.blueprint_template.workflow.return_review'), 'description' => '', 'category' => 'return'],
        ['id' => 'dense-parallel-1', 'source' => 'step-5', 'target' => 'step-10', 'label' => __('daisy::components.blueprint_template.workflow.fast_track'), 'description' => '', 'category' => 'progress'],
        ['id' => 'dense-parallel-2', 'source' => 'step-5', 'target' => 'step-10', 'label' => __('daisy::components.blueprint_template.workflow.manual_track'), 'description' => '', 'category' => 'approval'],
    ]);
    $dense = [
        'version' => 1,
        'nodes' => $denseNodes,
        'transitions' => $denseTransitions,
        'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1],
    ];
?>

<section class="space-y-8">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showHeader): ?>
        <header class="space-y-2">
            <h1 class="text-2xl font-semibold"><?php echo e(__('daisy::components.blueprint_template.title')); ?></h1>
            <p class="max-w-3xl text-sm text-base-content/70"><?php echo e(__('daisy::components.blueprint_template.description')); ?></p>
        </header>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showContract): ?>
        <section class="rounded-box border border-base-300 bg-base-100 p-4" data-blueprint-contract>
            <h2 class="font-semibold"><?php echo e(__('daisy::components.blueprint_template.contract.title')); ?></h2>
            <p class="mt-2 text-sm text-base-content/70"><?php echo e(__('daisy::components.blueprint_template.contract.description')); ?></p>
            <p class="mt-3 text-xs text-base-content/60">
                getValue · setValue · addNode · updateNode · removeNode · addTransition · updateTransition · removeTransition · arrange · fit · undo · redo · openInspector · setInspectorDraft · commitInspector · cancelInspector · destroy
            </p>
            <p class="mt-1 text-xs text-base-content/60">
                daisy:blueprint:init · daisy:blueprint:change · daisy:blueprint:select · daisy:blueprint:inspector-open · daisy:blueprint:inspector-commit · daisy:blueprint:inspector-cancel · daisy:blueprint:error
            </p>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <article class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold"><?php echo e(__('daisy::components.blueprint_template.examples.approval.title')); ?></h2>
            <p class="text-sm text-base-content/70"><?php echo e(__('daisy::components.blueprint_template.examples.approval.description')); ?></p>
        </div>
        <?php if (isset($component)) { $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.blueprint','data' => ['name' => $namePrefix.'_approval','value' => $approval,'nodeCategories' => $nodeCategories,'transitionCategories' => $transitionCategories,'height' => '480px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.blueprint'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($namePrefix.'_approval'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($approval),'node-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nodeCategories),'transition-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($transitionCategories),'height' => '480px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('inspector', null, []); ?> 
                <?php echo $__env->make('daisy::templates.advanced.partials.blueprint-inspector', ['inspectorId' => $namePrefix.'-approval-inspector'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $attributes = $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $component = $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
    </article>

    <article class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold"><?php echo e(__('daisy::components.blueprint_template.examples.cycle.title')); ?></h2>
            <p class="text-sm text-base-content/70"><?php echo e(__('daisy::components.blueprint_template.examples.cycle.description')); ?></p>
        </div>
        <?php if (isset($component)) { $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.blueprint','data' => ['name' => $namePrefix.'_cycle','value' => $cycle,'nodeCategories' => $nodeCategories,'transitionCategories' => $transitionCategories,'layout' => 'radial','height' => '520px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.blueprint'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($namePrefix.'_cycle'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cycle),'node-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nodeCategories),'transition-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($transitionCategories),'layout' => 'radial','height' => '520px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('inspector', null, []); ?> 
                <?php echo $__env->make('daisy::templates.advanced.partials.blueprint-inspector', ['inspectorId' => $namePrefix.'-cycle-inspector'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $attributes = $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $component = $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
    </article>

    <article class="space-y-3">
        <div>
            <h2 class="text-lg font-semibold"><?php echo e(__('daisy::components.blueprint_template.examples.dense.title')); ?></h2>
            <p class="text-sm text-base-content/70"><?php echo e(__('daisy::components.blueprint_template.examples.dense.description')); ?></p>
        </div>
        <?php if (isset($component)) { $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.blueprint','data' => ['name' => $namePrefix.'_dense','value' => $dense,'nodeCategories' => $nodeCategories,'transitionCategories' => $transitionCategories,'direction' => 'TB','layout' => 'hierarchical','height' => '620px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.blueprint'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($namePrefix.'_dense'),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dense),'node-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($nodeCategories),'transition-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($transitionCategories),'direction' => 'TB','layout' => 'hierarchical','height' => '620px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('inspector', null, []); ?> 
                <?php echo $__env->make('daisy::templates.advanced.partials.blueprint-inspector', ['inspectorId' => $namePrefix.'-dense-inspector'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $attributes = $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $component = $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
    </article>
</section>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views//templates/advanced/blueprint.blade.php ENDPATH**/ ?>
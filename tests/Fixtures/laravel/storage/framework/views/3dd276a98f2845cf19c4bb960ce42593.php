<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'Editable dashboard',
    'theme' => null,
    'editable' => true,
    'columns' => 12,
    'cellHeight' => 112,
    'gap' => 16,
    'static' => false,
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
    'title' => 'Editable dashboard',
    'theme' => null,
    'editable' => true,
    'columns' => 12,
    'cellHeight' => 112,
    'gap' => 16,
    'static' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="space-y-5">
        <?php if (isset($component)) { $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.hero','data' => ['class' => 'rounded-box border border-base-content/10 bg-base-200/35']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'rounded-box border border-base-content/10 bg-base-200/35']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl space-y-2">
                    <h1 class="text-3xl font-semibold">Editable dashboard</h1>
                    <p class="text-sm leading-6 text-base-content/70">
                        Gridstack is isolated to this optional surface. The rest of the package keeps using the existing static grid system.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Optional surface <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'neutral']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'neutral']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e($editable && ! $static ? 'Editable' : 'Read only'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'accent']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'accent']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e($columns); ?> columns <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
                </div>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1)): ?>
<?php $attributes = $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1; ?>
<?php unset($__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal40312bcd153c4f1bbfbe6543713be4a1)): ?>
<?php $component = $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1; ?>
<?php unset($__componentOriginal40312bcd153c4f1bbfbe6543713be4a1); ?>
<?php endif; ?>

        <div class="rounded-box border border-base-content/10 bg-base-100 p-4 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Dashboard workspace</h2>
                    <p class="text-sm text-base-content/65">Move and resize widgets without clipping their content.</p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="badge badge-soft badge-primary"><?php echo e($columns); ?> columns</span>
                    <span class="badge badge-soft badge-neutral">Drag & resize</span>
                    <span class="badge badge-soft badge-accent">Compact layout</span>
                </div>
            </div>

            <?php if (isset($component)) { $__componentOriginalc45fd0387d378923b6d2ecb2f60ad25e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc45fd0387d378923b6d2ecb2f60ad25e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid','data' => ['editable' => $editable,'columns' => $columns,'cellHeight' => $cellHeight,'gap' => $gap,'static' => $static,'layout' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['editable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editable),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($columns),'cell-height' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cellHeight),'gap' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gap),'static' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($static),'layout' => 'compact']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'kpi-revenue','type' => 'stat','x' => 0,'y' => 0,'w' => 3,'h' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'kpi-revenue','type' => 'stat','x' => 0,'y' => 0,'w' => 3,'h' => 2]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Revenue</p>
                                <p class="mt-2 text-3xl font-semibold">$42.5k</p>
                            </div>
                            <span class="badge badge-soft badge-primary">MRR</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-base-content/60">
                                <span>Target</span>
                                <span>82%</span>
                            </div>
                            <progress class="progress progress-primary h-2" value="82" max="100"></progress>
                        </div>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'kpi-users','type' => 'stat','x' => 3,'y' => 0,'w' => 3,'h' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'kpi-users','type' => 'stat','x' => 3,'y' => 0,'w' => 3,'h' => 2]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Users</p>
                                <p class="mt-2 text-3xl font-semibold">1,284</p>
                            </div>
                            <span class="badge badge-soft badge-secondary">Active</span>
                        </div>
                        <p class="text-sm text-base-content/65">84 new accounts this week</p>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'kpi-sla','type' => 'stat','x' => 6,'y' => 0,'w' => 3,'h' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'kpi-sla','type' => 'stat','x' => 6,'y' => 0,'w' => 3,'h' => 2]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Ops</p>
                                <p class="mt-2 text-3xl font-semibold">2m 14s</p>
                            </div>
                            <span class="badge badge-soft badge-accent">SLA</span>
                        </div>
                        <p class="text-sm text-base-content/65">Response time under target</p>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'kpi-risk','type' => 'stat','x' => 9,'y' => 0,'w' => 3,'h' => 2]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'kpi-risk','type' => 'stat','x' => 9,'y' => 0,'w' => 3,'h' => 2]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col justify-between rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase text-base-content/55">Risk</p>
                                <p class="mt-2 text-3xl font-semibold">3</p>
                            </div>
                            <span class="badge badge-soft badge-warning">Open</span>
                        </div>
                        <p class="text-sm text-base-content/65">Two are waiting on review</p>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'team-priorities','type' => 'list','x' => 0,'y' => 2,'w' => 6,'h' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'team-priorities','type' => 'list','x' => 0,'y' => 2,'w' => 6,'h' => 3]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">Team priorities</h3>
                                <p class="text-sm text-base-content/60">This week</p>
                            </div>
                            <span class="badge badge-ghost">3 tasks</span>
                        </div>
                        <div class="min-h-0 flex-1 divide-y divide-base-content/10 overflow-hidden text-sm">
                            <div class="flex items-center justify-between gap-3 py-3">
                                <span>Ship editable layout demo</span>
                                <span class="badge badge-sm badge-success">Ready</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-3">
                                <span>Review persistence contract</span>
                                <span class="badge badge-sm badge-warning">Review</span>
                            </div>
                            <div class="flex items-center justify-between gap-3 py-3">
                                <span>Prepare form builder follow-up</span>
                                <span class="badge badge-sm badge-ghost">Queued</span>
                            </div>
                        </div>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'release-checklist','type' => 'list','x' => 6,'y' => 2,'w' => 3,'h' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'release-checklist','type' => 'list','x' => 6,'y' => 2,'w' => 3,'h' => 3]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col rounded-box border border-base-content/10 bg-base-100 p-4">
                        <h3 class="font-semibold">Release checklist</h3>
                        <div class="mt-3 flex min-h-0 flex-1 flex-col gap-3 text-sm">
                            <label class="flex items-center gap-3">
                                <input type="checkbox" checked class="checkbox checkbox-sm checkbox-success" disabled>
                                <span>Static mode verified</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="checkbox" checked class="checkbox checkbox-sm checkbox-success" disabled>
                                <span>Drag events serialized</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="checkbox" class="checkbox checkbox-sm" disabled>
                                <span>Docs screenshot updated</span>
                            </label>
                        </div>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>

                <?php if (isset($component)) { $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.editable-grid-item','data' => ['id' => 'layout-health','type' => 'chart','x' => 9,'y' => 2,'w' => 3,'h' => 3]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.editable-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'layout-health','type' => 'chart','x' => 9,'y' => 2,'w' => 3,'h' => 3]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <section class="flex h-full min-h-0 flex-col rounded-box border border-base-content/10 bg-base-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">Layout health</h3>
                                <p class="text-sm text-base-content/60">Widget fit score</p>
                            </div>
                            <span class="text-sm font-medium text-success">Stable</span>
                        </div>
                        <div class="flex flex-1 items-center justify-center">
                            <?php if (isset($component)) { $__componentOriginalb355cab2b2984b49b730ce467e13f652 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb355cab2b2984b49b730ce467e13f652 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.radial-progress','data' => ['value' => 92,'size' => '7rem','thickness' => '0.7rem','color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.radial-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 92,'size' => '7rem','thickness' => '0.7rem','color' => 'primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
92% <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb355cab2b2984b49b730ce467e13f652)): ?>
<?php $attributes = $__attributesOriginalb355cab2b2984b49b730ce467e13f652; ?>
<?php unset($__attributesOriginalb355cab2b2984b49b730ce467e13f652); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb355cab2b2984b49b730ce467e13f652)): ?>
<?php $component = $__componentOriginalb355cab2b2984b49b730ce467e13f652; ?>
<?php unset($__componentOriginalb355cab2b2984b49b730ce467e13f652); ?>
<?php endif; ?>
                        </div>
                    </section>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $attributes = $__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__attributesOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5)): ?>
<?php $component = $__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5; ?>
<?php unset($__componentOriginal8c9f3f7ea06a528330ca1cbf789392f5); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc45fd0387d378923b6d2ecb2f60ad25e)): ?>
<?php $attributes = $__attributesOriginalc45fd0387d378923b6d2ecb2f60ad25e; ?>
<?php unset($__attributesOriginalc45fd0387d378923b6d2ecb2f60ad25e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc45fd0387d378923b6d2ecb2f60ad25e)): ?>
<?php $component = $__componentOriginalc45fd0387d378923b6d2ecb2f60ad25e; ?>
<?php unset($__componentOriginalc45fd0387d378923b6d2ecb2f60ad25e); ?>
<?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $attributes = $__attributesOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__attributesOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $component = $__componentOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__componentOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/layout/editable-grid.blade.php ENDPATH**/ ?>
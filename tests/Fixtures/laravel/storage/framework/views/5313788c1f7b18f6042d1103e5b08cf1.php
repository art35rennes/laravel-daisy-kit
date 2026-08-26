<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'added', // added|changed|fixed|removed|security
    'category' => null, // Catégorie optionnelle
    'description' => '',
    'breaking' => false,
    'issues' => [], // [123, 456] ou [['number' => 123, 'url' => '...']]
    'contributors' => [], // ['username1', 'username2']
    'image' => null, // URL de l'image
    'migration' => false,
    'migrationGuide' => null,
    'cve' => null, // Numéro CVE
    'severity' => null, // high|medium|low
    'issueBaseUrl' => 'https://github.com/user/repo/issues', // Base URL pour les issues
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
    'type' => 'added', // added|changed|fixed|removed|security
    'category' => null, // Catégorie optionnelle
    'description' => '',
    'breaking' => false,
    'issues' => [], // [123, 456] ou [['number' => 123, 'url' => '...']]
    'contributors' => [], // ['username1', 'username2']
    'image' => null, // URL de l'image
    'migration' => false,
    'migrationGuide' => null,
    'cve' => null, // Numéro CVE
    'severity' => null, // high|medium|low
    'issueBaseUrl' => 'https://github.com/user/repo/issues', // Base URL pour les issues
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Mapping des types vers les couleurs daisyUI
    $typeColorMap = [
        'added' => 'success',
        'changed' => 'info',
        'fixed' => 'warning',
        'removed' => 'error',
        'security' => 'error',
    ];
    $typeColor = $typeColorMap[$type] ?? 'neutral';
    $typeLabel = __('daisy::changelog.'.$type);

    $normalizeUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $issueBaseUrl = $normalizeUrl($issueBaseUrl) ?? 'https://github.com/user/repo/issues';
    $migrationGuide = $normalizeUrl($migrationGuide);
    $image = $normalizeUrl($image);

    // Formatage des issues
    $formattedIssues = [];
    foreach ($issues as $issue) {
        if (is_array($issue)) {
            $issue['url'] = $normalizeUrl($issue['url'] ?? null);
            $formattedIssues[] = $issue;
        } else {
            $formattedIssues[] = [
                'number' => $issue,
                'url' => rtrim($issueBaseUrl, '/').'/'.$issue,
            ];
        }
    }
?>

<div class="changelog-change-item rounded-box card-border bg-base-100 p-4 shadow">
    
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => $typeColor,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($typeColor),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php echo e($typeLabel); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($breaking): ?>
            <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'error','size' => 'xs','variant' => 'soft']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error','size' => 'xs','variant' => 'soft']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('daisy::changelog.breaking_change')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($migration): ?>
            <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'warning','size' => 'xs','variant' => 'soft']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'warning','size' => 'xs','variant' => 'soft']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('daisy::changelog.migration_required')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cve): ?>
            <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'error','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error','size' => 'xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('daisy::changelog.cve')); ?>: <?php echo e($cve); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($severity): ?>
            <?php
                $severityColorMap = [
                    'high' => 'error',
                    'medium' => 'warning',
                    'low' => 'info',
                ];
                $severityColor = $severityColorMap[$severity] ?? 'neutral';
                $severityLabel = __('daisy::changelog.severity_'.$severity);
            ?>
            <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => $severityColor,'size' => 'xs','variant' => 'soft']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($severityColor),'size' => 'xs','variant' => 'soft']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e($severityLabel); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="mb-3">
        <p class="text-sm text-base-content/90 leading-relaxed">
            <?php echo e($description); ?>

        </p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category): ?>
            <p class="mt-1 text-xs text-base-content/60">
                <?php echo e(__('daisy::changelog.category_'.strtolower($category)) ?? $category); ?>

            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mt-3 space-y-2 text-sm text-base-content/80">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($formattedIssues)): ?>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs uppercase tracking-wide text-base-content/50"><?php echo e(__('daisy::changelog.issues')); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $formattedIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($issue['url'] ?? null): ?>
                        <a href="<?php echo e($issue['url']); ?>" target="_blank" rel="noopener noreferrer" class="link link-primary text-xs font-semibold">
                            #<?php echo e($issue['number']); ?>

                        </a>
                    <?php else: ?>
                        <span class="text-xs font-semibold">#<?php echo e($issue['number']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($migration && $migrationGuide): ?>
            <div class="flex items-center gap-2 text-xs">
                <span class="uppercase tracking-wide text-base-content/50"><?php echo e(__('daisy::changelog.migration_guide')); ?></span>
                <a href="<?php echo e($migrationGuide); ?>" target="_blank" rel="noopener noreferrer" class="link link-warning font-semibold">
                    <?php echo e(__('daisy::changelog.view_migration_guide')); ?>

                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($contributors)): ?>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="uppercase tracking-wide text-base-content/50"><?php echo e(__('daisy::changelog.contributors')); ?></span>
                <span><?php echo e(implode(', ', $contributors)); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>
        <div class="mt-3 rounded-box card-border bg-base-100">
            <?php if (isset($component)) { $__componentOriginal71eeb9913619c6fbc539de59e6844dee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal71eeb9913619c6fbc539de59e6844dee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.media.lightbox','data' => ['images' => [['src' => $image, 'thumb' => $image, 'alt' => $description, 'caption' => __('daisy::changelog.view_screenshot')]],'cols' => 'grid-cols-1','gap' => 'gap-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.media.lightbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['images' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['src' => $image, 'thumb' => $image, 'alt' => $description, 'caption' => __('daisy::changelog.view_screenshot')]]),'cols' => 'grid-cols-1','gap' => 'gap-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal71eeb9913619c6fbc539de59e6844dee)): ?>
<?php $attributes = $__attributesOriginal71eeb9913619c6fbc539de59e6844dee; ?>
<?php unset($__attributesOriginal71eeb9913619c6fbc539de59e6844dee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal71eeb9913619c6fbc539de59e6844dee)): ?>
<?php $component = $__componentOriginal71eeb9913619c6fbc539de59e6844dee; ?>
<?php unset($__componentOriginal71eeb9913619c6fbc539de59e6844dee); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/changelog/changelog-change-item.blade.php ENDPATH**/ ?>
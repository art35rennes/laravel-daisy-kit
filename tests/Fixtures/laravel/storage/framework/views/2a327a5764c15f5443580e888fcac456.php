<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'version' => null, // Version string
    'date' => null, // Date string
    'isCurrent' => false,
    'yanked' => false,
    'tagUrl' => null, // Lien vers le tag Git
    'compareUrl' => null, // Lien de comparaison
    'items' => [], // Array de changements (format enrichi)
    'changes' => [], // Array de changements (format simple: ['added' => [...], 'fixed' => [...]])
    'expandByDefault' => false,
    'highlightCurrent' => true,
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
    'version' => null, // Version string
    'date' => null, // Date string
    'isCurrent' => false,
    'yanked' => false,
    'tagUrl' => null, // Lien vers le tag Git
    'compareUrl' => null, // Lien de comparaison
    'items' => [], // Array de changements (format enrichi)
    'changes' => [], // Array de changements (format simple: ['added' => [...], 'fixed' => [...]])
    'expandByDefault' => false,
    'highlightCurrent' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use Carbon\Carbon;

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

    $tagUrl = $normalizeUrl($tagUrl);
    $compareUrl = $normalizeUrl($compareUrl);

    // Normaliser les données : convertir le format simple en format enrichi si nécessaire
    $normalizedItems = [];

    if (!empty($items)) {
        // Format enrichi déjà fourni
        $normalizedItems = $items;
    } elseif (!empty($changes)) {
        // Format simple : convertir en format enrichi
        foreach ($changes as $type => $changeList) {
            if (is_array($changeList)) {
                foreach ($changeList as $change) {
                    if (is_string($change)) {
                        $normalizedItems[] = [
                            'type' => $type,
                            'description' => $change,
                        ];
                    } elseif (is_array($change)) {
                        $normalizedItems[] = array_merge(['type' => $type], $change);
                    }
                }
            }
        }
    }

    // Formater la date
    $formattedDate = null;
    if ($date) {
        try {
            $formattedDate = Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            $formattedDate = $date;
        }
    }

    // Construire le titre du collapse
    $titleParts = [];
    if ($version) {
        $titleParts[] = __('daisy::changelog.version').' '.$version;
    }
    if ($formattedDate) {
        $titleParts[] = __('daisy::changelog.released_on').' '.$formattedDate;
    }
    $collapseTitle = implode(' - ', $titleParts);

    // Classes pour le collapse
    $collapseClasses = '';
    if ($yanked) {
        $collapseClasses .= ' opacity-60';
    }
?>

<div class="changelog-version-item <?php echo e($collapseClasses); ?> relative grid grid-cols-1 md:grid-cols-[auto_1fr] gap-4 md:gap-6" data-version="<?php echo e($version); ?>">
    
    <div class="timeline-column flex flex-row md:flex-col items-center md:items-start gap-3 md:gap-0 text-sm text-base-content/70 md:text-left">
        <span class="font-medium md:mb-3"><?php echo e($formattedDate); ?></span>
        <div class="relative hidden md:block">
            <span class="flex h-4 w-4 items-center justify-center rounded-full border border-primary bg-base-100 text-primary">
                <span class="h-1 w-1 rounded-full bg-primary"></span>
            </span>
            <span class="absolute left-1/2 top-4 h-full w-px -translate-x-1/2 bg-base-200"></span>
        </div>
    </div>

    
    <div class="space-y-3 rounded-box card-border bg-base-100 p-4 md:p-6 shadow transition hover:shadow">
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-start gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-xs uppercase tracking-wide text-base-content/60"><?php echo e(__('daisy::changelog.version')); ?></p>
                <h3 class="text-xl font-semibold text-base-content"><?php echo e($version ?? __('daisy::changelog.version')); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($collapseTitle): ?>
                    <p class="text-sm text-base-content/70 hidden md:block"><?php echo e($collapseTitle); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?>
                    <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'primary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary','size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php echo e(__('daisy::changelog.current_version')); ?>

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

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($yanked): ?>
                    <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'error','size' => 'sm','variant' => 'soft']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error','size' => 'sm','variant' => 'soft']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php echo e(__('daisy::changelog.yanked')); ?>

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

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tagUrl): ?>
                    <a href="<?php echo e($tagUrl); ?>" target="_blank" rel="noopener noreferrer" class="link link-primary font-semibold text-xs">
                        <?php echo e(__('daisy::changelog.view_tag')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($compareUrl): ?>
                    <a href="<?php echo e($compareUrl); ?>" target="_blank" rel="noopener noreferrer" class="link link-info font-semibold text-xs">
                        <?php echo e(__('daisy::changelog.compare_versions')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="space-y-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $normalizedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal2612d06129a98ab5e96ad425e3bf2af2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2612d06129a98ab5e96ad425e3bf2af2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.changelog.changelog-change-item','data' => ['type' => $item['type'] ?? 'added','category' => $item['category'] ?? null,'description' => $item['description'] ?? '','breaking' => (bool)($item['breaking'] ?? false),'issues' => $item['issues'] ?? [],'contributors' => $item['contributors'] ?? [],'image' => $item['image'] ?? null,'migration' => (bool)($item['migration'] ?? false),'migrationGuide' => $item['migrationGuide'] ?? null,'cve' => $item['cve'] ?? null,'severity' => $item['severity'] ?? null,'issueBaseUrl' => $item['issueBaseUrl'] ?? 'https://github.com/user/repo/issues']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.changelog.changelog-change-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['type'] ?? 'added'),'category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['category'] ?? null),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['description'] ?? ''),'breaking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool)($item['breaking'] ?? false)),'issues' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['issues'] ?? []),'contributors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['contributors'] ?? []),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['image'] ?? null),'migration' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool)($item['migration'] ?? false)),'migration-guide' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['migrationGuide'] ?? null),'cve' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['cve'] ?? null),'severity' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['severity'] ?? null),'issue-base-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['issueBaseUrl'] ?? 'https://github.com/user/repo/issues')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2612d06129a98ab5e96ad425e3bf2af2)): ?>
<?php $attributes = $__attributesOriginal2612d06129a98ab5e96ad425e3bf2af2; ?>
<?php unset($__attributesOriginal2612d06129a98ab5e96ad425e3bf2af2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2612d06129a98ab5e96ad425e3bf2af2)): ?>
<?php $component = $__componentOriginal2612d06129a98ab5e96ad425e3bf2af2; ?>
<?php unset($__componentOriginal2612d06129a98ab5e96ad425e3bf2af2); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-sm text-base-content/60"><?php echo e(__('daisy::changelog.no_results')); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/changelog/changelog-version-item.blade.php ENDPATH**/ ?>
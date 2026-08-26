<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::changelog.changelog'),
    'theme' => null,
    // Changelog data
    'versions' => [], // Array of version data
    'currentVersion' => null, // Current app version
    // Routes
    'rssUrl' => null, // RSS feed URL (optional)
    'atomUrl' => null, // Atom feed URL (optional)
    'documentationUrl' => null, // Optional CTA target
    // Options
    'showFilters' => true,
    'showSearch' => true,
    'showVersionBadge' => true,
    'showDocumentationCta' => null, // Auto-detect from documentationUrl when null
    'groupByMonth' => false, // Group versions by month
    'highlightCurrent' => true, // Highlight current version
    'expandLatest' => true, // Expand latest version by default
    'itemsPerPage' => 20, // If pagination enabled
    'pagination' => false, // Enable pagination
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
    'title' => __('daisy::changelog.changelog'),
    'theme' => null,
    // Changelog data
    'versions' => [], // Array of version data
    'currentVersion' => null, // Current app version
    // Routes
    'rssUrl' => null, // RSS feed URL (optional)
    'atomUrl' => null, // Atom feed URL (optional)
    'documentationUrl' => null, // Optional CTA target
    // Options
    'showFilters' => true,
    'showSearch' => true,
    'showVersionBadge' => true,
    'showDocumentationCta' => null, // Auto-detect from documentationUrl when null
    'groupByMonth' => false, // Group versions by month
    'highlightCurrent' => true, // Highlight current version
    'expandLatest' => true, // Expand latest version by default
    'itemsPerPage' => 20, // If pagination enabled
    'pagination' => false, // Enable pagination
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

    // Auto-détecter la version actuelle si non fournie
    if (is_null($currentVersion)) {
        $currentVersion = config('app.version');
    }

    $documentationUrl ??= Route::has('templates.documentation.changelog')
        ? route('templates.documentation.changelog')
        : null;

    $showDocumentationCta ??= filled($documentationUrl);

    // Normaliser les versions : déterminer isCurrent si non fourni
    $normalizedVersions = collect($versions)->map(function($version) use ($currentVersion) {
        if (!isset($version['isCurrent']) && $currentVersion) {
            $version['isCurrent'] = ($version['version'] ?? null) === $currentVersion;
        }
        return $version;
    })->values()->all();

    // Grouper par mois si demandé
    if ($groupByMonth && !empty($normalizedVersions)) {
        $groupedVersions = collect($normalizedVersions)->groupBy(function($version) {
            try {
                $date = $version['date'] ?? now();
                return Carbon::parse($date)->format('Y-m');
            } catch (\Exception $e) {
                return 'unknown';
            }
        })->sortKeysDesc()->all();
    } else {
        $groupedVersions = ['all' => $normalizedVersions];
    }

    // Déterminer quelle version doit être ouverte par défaut
    $latestVersionIndex = null;
    if ($expandLatest && !empty($normalizedVersions)) {
        $latestVersionIndex = 0; // La première version est la plus récente
    }
?>

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

    <?php if (isset($component)) { $__componentOriginald4aaa4b01baa94db46e38e4697384b0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.theme-selector','data' => ['position' => 'fixed','placement' => 'top-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.theme-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'fixed','placement' => 'top-right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $attributes = $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $component = $__componentOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
    <div class="changelog-container mx-auto max-w-5xl space-y-8 py-4">
        <section class="rounded-box bg-base-100/90 p-8 shadow">
            <div class="flex flex-wrap items-start gap-6">
                <div class="flex-1 min-w-0 space-y-3">
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary"><?php echo e(__('daisy::changelog.changelog')); ?></p>
                    <h1 class="text-4xl font-semibold text-base-content"><?php echo e($title); ?></h1>
                    <p class="text-base text-base-content/70">
                        <?php echo e(__('daisy::changelog.intro_description')); ?>

                    </p>
                </div>
                <div class="flex flex-col items-end gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showVersionBadge && $currentVersion): ?>
                        <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'primary','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'primary','size' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::changelog.current_version')); ?> <?php echo e($currentVersion); ?>

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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDocumentationCta && $documentationUrl): ?>
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['tag' => 'a','href' => $documentationUrl,'color' => 'primary','class' => 'btn-wide']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($documentationUrl),'color' => 'primary','class' => 'btn-wide']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::changelog.cta_get_template')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSearch || $showFilters): ?>
            <?php if (isset($component)) { $__componentOriginald3d270a1b9d3783f686e408d9bbd9e45 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald3d270a1b9d3783f686e408d9bbd9e45 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.changelog.changelog-toolbar','data' => ['showSearch' => $showSearch,'showFilters' => $showFilters]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.changelog.changelog-toolbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['showSearch' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showSearch),'showFilters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showFilters)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald3d270a1b9d3783f686e408d9bbd9e45)): ?>
<?php $attributes = $__attributesOriginald3d270a1b9d3783f686e408d9bbd9e45; ?>
<?php unset($__attributesOriginald3d270a1b9d3783f686e408d9bbd9e45); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald3d270a1b9d3783f686e408d9bbd9e45)): ?>
<?php $component = $__componentOriginald3d270a1b9d3783f686e408d9bbd9e45; ?>
<?php unset($__componentOriginald3d270a1b9d3783f686e408d9bbd9e45); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($normalizedVersions)): ?>
            <?php if (isset($component)) { $__componentOriginal35ede04184a85d1a23bc936778c668e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal35ede04184a85d1a23bc936778c668e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.empty-state','data' => ['title' => __('daisy::changelog.no_versions'),'message' => __('daisy::changelog.no_results'),'class' => 'rounded-box card-border bg-base-100/80']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::changelog.no_versions')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::changelog.no_results')),'class' => 'rounded-box card-border bg-base-100/80']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $attributes = $__attributesOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__attributesOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal35ede04184a85d1a23bc936778c668e2)): ?>
<?php $component = $__componentOriginal35ede04184a85d1a23bc936778c668e2; ?>
<?php unset($__componentOriginal35ede04184a85d1a23bc936778c668e2); ?>
<?php endif; ?>
        <?php else: ?>
            <div
                class="changelog-versions space-y-10"
                data-changelog-container
                <?php if($showSearch || $showFilters): ?> data-module="changelog-filter" data-all-types-label="<?php echo e(__('daisy::changelog.all_types')); ?>" <?php endif; ?>
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($groupByMonth): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $groupedVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $monthVersions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            try {
                                $monthDate = Carbon::createFromFormat('Y-m', $month);
                                $monthLabel = $monthDate->format('F Y');
                            } catch (\Exception $e) {
                                $monthLabel = $month;
                            }
                        ?>
                        <div class="changelog-month-group space-y-6">
                            <h2 class="text-lg font-semibold text-base-content/70">
                                <?php echo e($monthLabel); ?>

                            </h2>
                            <div class="space-y-8">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $monthVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php if (isset($component)) { $__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.changelog.changelog-version-item','data' => ['version' => $version['version'] ?? null,'date' => $version['date'] ?? null,'isCurrent' => (bool)($version['isCurrent'] ?? false),'yanked' => (bool)($version['yanked'] ?? false),'tagUrl' => $version['tagUrl'] ?? null,'compareUrl' => $version['compareUrl'] ?? null,'items' => $version['items'] ?? [],'changes' => $version['changes'] ?? [],'expandByDefault' => $expandLatest && $loop->first,'highlightCurrent' => $highlightCurrent]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.changelog.changelog-version-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['version' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['version'] ?? null),'date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['date'] ?? null),'isCurrent' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool)($version['isCurrent'] ?? false)),'yanked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool)($version['yanked'] ?? false)),'tagUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['tagUrl'] ?? null),'compareUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['compareUrl'] ?? null),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['items'] ?? []),'changes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['changes'] ?? []),'expandByDefault' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandLatest && $loop->first),'highlightCurrent' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($highlightCurrent)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36)): ?>
<?php $attributes = $__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36; ?>
<?php unset($__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36)): ?>
<?php $component = $__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36; ?>
<?php unset($__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36); ?>
<?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php else: ?>
                    <div class="space-y-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $normalizedVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $version): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.changelog.changelog-version-item','data' => ['version' => $version['version'] ?? null,'date' => $version['date'] ?? null,'isCurrent' => (bool)($version['isCurrent'] ?? false),'yanked' => (bool)($version['yanked'] ?? false),'tagUrl' => $version['tagUrl'] ?? null,'compareUrl' => $version['compareUrl'] ?? null,'items' => $version['items'] ?? [],'changes' => $version['changes'] ?? [],'expandByDefault' => $expandLatest && $index === 0,'highlightCurrent' => $highlightCurrent]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.changelog.changelog-version-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['version' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['version'] ?? null),'date' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['date'] ?? null),'isCurrent' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool)($version['isCurrent'] ?? false)),'yanked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((bool)($version['yanked'] ?? false)),'tagUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['tagUrl'] ?? null),'compareUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['compareUrl'] ?? null),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['items'] ?? []),'changes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($version['changes'] ?? []),'expandByDefault' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandLatest && $index === 0),'highlightCurrent' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($highlightCurrent)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36)): ?>
<?php $attributes = $__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36; ?>
<?php unset($__attributesOriginal365fc5f6a1b20ee4592fc6bf20f70e36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36)): ?>
<?php $component = $__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36; ?>
<?php unset($__componentOriginal365fc5f6a1b20ee4592fc6bf20f70e36); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="changelog-no-results text-center py-8 text-base-content/70" data-changelog-empty hidden>
                    <?php echo e(__('daisy::changelog.no_results')); ?>

                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pagination && isset($paginationData)): ?>
                <div class="flex justify-center">
                    <?php if (isset($component)) { $__componentOriginale55b5075277bb0c7550c33a521cdb8f9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale55b5075277bb0c7550c33a521cdb8f9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.pagination','data' => ['total' => $paginationData['total'] ?? 1,'current' => $paginationData['current'] ?? 1]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['total' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paginationData['total'] ?? 1),'current' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paginationData['current'] ?? 1)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale55b5075277bb0c7550c33a521cdb8f9)): ?>
<?php $attributes = $__attributesOriginale55b5075277bb0c7550c33a521cdb8f9; ?>
<?php unset($__attributesOriginale55b5075277bb0c7550c33a521cdb8f9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale55b5075277bb0c7550c33a521cdb8f9)): ?>
<?php $component = $__componentOriginale55b5075277bb0c7550c33a521cdb8f9; ?>
<?php unset($__componentOriginale55b5075277bb0c7550c33a521cdb8f9); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/changelog.blade.php ENDPATH**/ ?>
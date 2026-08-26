<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'wide', // slim|wide|auto|fit
    'collapsed' => false,
    'collapsible' => true,
    'expandOnHover' => false,
    'forceCollapsed' => null,
    'stickyAt' => 'lg',
    'collapseAt' => 'lg',
    'hasNavbar' => false,
    'end' => false,
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    'minWidth' => 'min-w-48',
    'maxWidth' => 'max-w-80',
    'storageKey' => null,
    'name' => null,
    'logo' => null,
    'logoAlt' => null,
    'brand' => null,
    'brandHref' => null,
    'brandUrl' => null,
    'brandCollapsed' => null,
    'showBrand' => true,
    'sections' => [],
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    'searchable' => false,
    'searchPlaceholder' => null,
    'searchEmptyLabel' => null,
    'searchResultsLabel' => null,
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
    'variant' => 'wide', // slim|wide|auto|fit
    'collapsed' => false,
    'collapsible' => true,
    'expandOnHover' => false,
    'forceCollapsed' => null,
    'stickyAt' => 'lg',
    'collapseAt' => 'lg',
    'hasNavbar' => false,
    'end' => false,
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    'minWidth' => 'min-w-48',
    'maxWidth' => 'max-w-80',
    'storageKey' => null,
    'name' => null,
    'logo' => null,
    'logoAlt' => null,
    'brand' => null,
    'brandHref' => null,
    'brandUrl' => null,
    'brandCollapsed' => null,
    'showBrand' => true,
    'sections' => [],
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    'searchable' => false,
    'searchPlaceholder' => null,
    'searchEmptyLabel' => null,
    'searchResultsLabel' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isSlim = $variant === 'slim';
    $isAuto = $variant === 'auto';
    $isFit = $variant === 'fit';
    $hoverExpandable = (bool) $expandOnHover && ! isset($forceCollapsed);
    $effectiveCollapsed = isset($forceCollapsed) ? (bool) $forceCollapsed : ($hoverExpandable || (bool) $collapsed);

    if ($expandedWidth) {
        $wideWidthClass = $expandedWidth;
        $widthStrategy = 'configured';
    } elseif ($sideClass) {
        $wideWidthClass = $sideClass;
        $widthStrategy = 'custom';
    } elseif ($isSlim) {
        $wideWidthClass = 'w-20';
        $widthStrategy = 'slim';
    } elseif ($isFit) {
        $wideWidthClass = trim("w-fit {$minWidth} {$maxWidth}");
        $widthStrategy = 'fit';
    } else {
        $wideWidthClass = 'w-64';
        $widthStrategy = $isAuto ? 'auto' : 'wide';
    }

    $collapsedWidthClass = $collapsedWidth ?: 'w-20';
    $widthClass = $effectiveCollapsed ? $collapsedWidthClass : $wideWidthClass;

    $stickyClasses = [
        'sm' => $hasNavbar ? 'sm:sticky sm:top-16' : 'sm:sticky sm:top-0 sm:h-screen',
        'md' => $hasNavbar ? 'md:sticky md:top-16' : 'md:sticky md:top-0 md:h-screen',
        'lg' => $hasNavbar ? 'lg:sticky lg:top-16' : 'lg:sticky lg:top-0 lg:h-screen',
        'xl' => $hasNavbar ? 'xl:sticky xl:top-16' : 'xl:sticky xl:top-0 xl:h-screen',
        '2xl' => $hasNavbar ? '2xl:sticky 2xl:top-16' : '2xl:sticky 2xl:top-0 2xl:h-screen',
    ];
    $toggleBreakpointClasses = [
        'sm' => 'hidden sm:flex',
        'md' => 'hidden md:flex',
        'lg' => 'hidden lg:flex',
        'xl' => 'hidden xl:flex',
        '2xl' => 'hidden 2xl:flex',
    ];
    $stickyClass = $stickyAt ? ($stickyClasses[$stickyAt] ?? $stickyClasses['lg']) : '';
    $toggleBreakpointClass = $collapseAt ? ($toggleBreakpointClasses[$collapseAt] ?? $toggleBreakpointClasses['lg']) : 'flex';

    $rootClasses = trim("bg-base-200 text-base-content min-h-full flex flex-col overflow-visible transition-[width] duration-200 {$stickyClass} {$widthClass}");
    $collapseLabel = __('daisy::components.sidebar_collapse');
    $expandLabel = __('daisy::components.sidebar_expand');
    $toggleLabel = $effectiveCollapsed ? $expandLabel : $collapseLabel;
    $expandIcon = $end ? 'chevron-double-left' : 'chevron-double-right';
    $collapseIcon = $end ? 'chevron-double-right' : 'chevron-double-left';
    $searchPlaceholder ??= __('daisy::components.sidebar_search_placeholder');
    $searchEmptyLabel ??= __('daisy::components.sidebar_search_empty');
    $searchResultsLabel ??= __('daisy::components.sidebar_search_results');

    $normalizeHref = function ($url): string {
        if (! is_string($url) && ! $url instanceof \Stringable) {
            return '#';
        }

        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return '#';
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : '#';
    };

    $isItemActive = function (array $item): bool {
        if (! empty($item['active'])) {
            return true;
        }

        $routeNames = array_filter((array) data_get($item, 'activeRoutes', []));

        if (data_get($item, 'activeRoute')) {
            $routeNames[] = data_get($item, 'activeRoute');
        }

        return $routeNames !== []
            && collect($routeNames)->contains(fn ($routeName) => \Illuminate\Support\Facades\Route::currentRouteNamed($routeName));
    };

    $hasActiveDescendant = function (array $items) use (&$hasActiveDescendant, $isItemActive): bool {
        foreach ($items as $item) {
            if (data_get($item, 'visible', true) === false) {
                continue;
            }

            if ($isItemActive($item) || $hasActiveDescendant((array) data_get($item, 'children', []))) {
                return true;
            }
        }

        return false;
    };

    $resolvedBrandHref = $normalizeHref($brandHref ?: $brandUrl);
    $hasBrandLink = $resolvedBrandHref !== '#';
    $hasCustomBrandSlot = $brand instanceof \Illuminate\View\ComponentSlot;
    $hasCustomCollapsedBrand = $brandCollapsed instanceof \Illuminate\View\ComponentSlot || filled($brandCollapsed);
    $hasCustomLogoSlot = $logo instanceof \Illuminate\View\ComponentSlot;
    $resolvedName = $name ?? ($hasCustomBrandSlot ? null : $brand) ?? config('app.name', 'App');
    $resolvedLogoAlt = $logoAlt ?? ($resolvedName ? (string) $resolvedName : '');
?>

<aside
    <?php echo e($attributes->merge(['class' => $rootClasses])); ?>

    data-sidebar-root
    data-sidebar-side="<?php echo e($end ? 'end' : 'start'); ?>"
    data-width-strategy="<?php echo e($widthStrategy); ?>"
    data-wide-class="<?php echo e($wideWidthClass); ?>"
    data-collapsed-class="<?php echo e($collapsedWidthClass); ?>"
    data-collapsed="<?php echo e($effectiveCollapsed ? '1' : '0'); ?>"
    data-collapse-at="<?php echo e($collapseAt ?? 'none'); ?>"
    data-expanded-label="<?php echo e($collapseLabel); ?>"
    data-collapsed-label="<?php echo e($expandLabel); ?>"
    <?php if($hoverExpandable): ?> data-expand-on-hover="1" <?php endif; ?>
    <?php if(isset($forceCollapsed)): ?> data-force-collapsed="<?php echo e($forceCollapsed ? '1' : '0'); ?>" <?php endif; ?>
    <?php if($storageKey): ?> data-storage-key="<?php echo e($storageKey); ?>" <?php endif; ?>
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showBrand): ?>
        <header class="flex h-14 w-full shrink-0 items-center gap-2 border-b border-base-content/10 px-3" data-sidebar-brand>
            <div class="flex min-w-0 flex-1 items-center justify-center gap-2" data-sidebar-brand-content>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBrandLink): ?>
                    <a href="<?php echo e($resolvedBrandHref); ?>" class="flex min-w-0 items-center gap-2" data-sidebar-brand-link>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCustomLogoSlot): ?>
                    <span class="shrink-0" data-sidebar-logo><?php echo e($logo); ?></span>
                <?php elseif(filled($logo)): ?>
                    <img src="<?php echo e($logo); ?>" alt="<?php echo e($resolvedLogoAlt); ?>" class="size-8 shrink-0 object-contain" data-sidebar-logo>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCustomBrandSlot): ?>
                    <span class="min-w-0 sidebar-label"><?php echo e($brand); ?></span>
                <?php elseif(filled($resolvedName)): ?>
                    <span class="min-w-0 truncate text-lg font-bold sidebar-label" data-sidebar-name><?php echo e($resolvedName); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBrandLink): ?>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCustomCollapsedBrand): ?>
                    <span class="hidden shrink-0" data-sidebar-brand-collapsed><?php echo e($brandCollapsed); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </header>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchable): ?>
        <div class="shrink-0 border-b border-base-content/10 p-2" data-sidebar-search-region>
            <label class="input input-sm w-full">
                <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'search','prefix' => $iconPrefix,'size' => 'sm','class' => 'opacity-50']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'size' => 'sm','class' => 'opacity-50']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                <input
                    type="search"
                    class="grow"
                    placeholder="<?php echo e($searchPlaceholder); ?>"
                    aria-label="<?php echo e($searchPlaceholder); ?>"
                    autocomplete="off"
                    data-sidebar-search
                >
            </label>
            <p class="sr-only" aria-live="polite" data-sidebar-search-status data-results-label="<?php echo e($searchResultsLabel); ?>"></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <ul class="menu min-h-0 w-full flex-1 flex-nowrap overflow-y-auto p-2" data-sidebar-menu data-sidebar-scroll-region <?php if($searchable): ?> data-menu-filter-target <?php endif; ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionIndex => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <li data-sidebar-section data-sidebar-section-id="<?php echo e($sectionIndex); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($section['label'])): ?>
                    <h2 class="menu-title sidebar-label" data-sidebar-section-title><?php echo e(__($section['label'])); ?></h2>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($section['items'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(data_get($item, 'visible', true) === false) continue; ?>
                        <?php echo $__env->make('daisy::components.ui.navigation.partials.sidebar-item', [
                            'item' => $item,
                            'depth' => 0,
                            'sectionId' => $sectionIndex,
                            'iconPrefix' => $iconPrefix,
                            'fallbackIcon' => $fallbackIcon,
                            'normalizeHref' => $normalizeHref,
                            'isItemActive' => $isItemActive,
                            'hasActiveDescendant' => $hasActiveDescendant,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </li>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php echo e($slot); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchable): ?>
        <p class="hidden shrink-0 px-3 py-4 text-center text-sm text-base-content/60" data-sidebar-search-empty><?php echo e($searchEmptyLabel); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer) || ($collapsible && ! isset($forceCollapsed) && ! $hoverExpandable)): ?>
        <footer class="flex shrink-0 flex-col items-center border-t border-base-content/10 p-2" data-sidebar-footer>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footer)): ?>
                <div class="mb-2 w-full text-center text-xs text-base-content/50 sidebar-label">
                    <?php echo e($footer); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($collapsible && ! isset($forceCollapsed) && ! $hoverExpandable): ?>
                <button
                    type="button"
                    class="btn btn-ghost btn-sm justify-center gap-2 sidebar-toggle <?php echo e($toggleBreakpointClass); ?>"
                    title="<?php echo e($toggleLabel); ?>"
                    aria-label="<?php echo e($toggleLabel); ?>"
                    aria-expanded="<?php echo e($effectiveCollapsed ? 'false' : 'true'); ?>"
                    data-sidebar-toggle
                >
                    <span class="sidebar-label" data-sidebar-toggle-label><?php echo e($toggleLabel); ?></span>
                    <span data-sidebar-icon-collapsed <?php if(! $effectiveCollapsed): ?> hidden <?php endif; ?>>
                        <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $expandIcon,'prefix' => $iconPrefix,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandIcon),'prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                    </span>
                    <span data-sidebar-icon-expanded <?php if($effectiveCollapsed): ?> hidden <?php endif; ?>>
                        <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $collapseIcon,'prefix' => $iconPrefix,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapseIcon),'prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                    </span>
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </footer>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</aside>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/sidebar.blade.php ENDPATH**/ ?>
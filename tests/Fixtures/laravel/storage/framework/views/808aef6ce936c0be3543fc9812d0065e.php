<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'theme' => null,
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    'collapsed' => false,
    'collapsible' => true,
    'forceCollapsed' => null,
    'expandOnHover' => false,
    'stickyAt' => 'lg',
    'collapseAt' => null,
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
    'searchable' => false,
    'searchPlaceholder' => null,
    'searchEmptyLabel' => null,
    'searchResultsLabel' => null,
    // Responsive drawer behavior
    'drawerId' => 'layout-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false, // sidebar on right
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    // Content container
    'container' => 'p-6',
    // Layout options
    'hasNavbar' => false,
    'showThemeController' => true,
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
    'themeLabel' => 'Theme',
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
    'title' => null,
    'theme' => null,
    // Sidebar options
    'variant' => 'wide', // slim|wide
    'sideClass' => null,
    'expandedWidth' => null,
    'collapsedWidth' => 'w-20',
    'collapsed' => false,
    'collapsible' => true,
    'forceCollapsed' => null,
    'expandOnHover' => false,
    'stickyAt' => 'lg',
    'collapseAt' => null,
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
    'searchable' => false,
    'searchPlaceholder' => null,
    'searchEmptyLabel' => null,
    'searchResultsLabel' => null,
    // Responsive drawer behavior
    'drawerId' => 'layout-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false, // sidebar on right
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    // Content container
    'container' => 'p-6',
    // Layout options
    'hasNavbar' => false,
    'showThemeController' => true,
    'themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],
    'themeLabel' => 'Theme',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $mobileOnlyClasses = [
        'sm' => 'sm:hidden',
        'md' => 'md:hidden',
        'lg' => 'lg:hidden',
        'xl' => 'xl:hidden',
        '2xl' => '2xl:hidden',
    ];
    $desktopOnlyClasses = [
        'sm' => 'hidden sm:flex',
        'md' => 'hidden md:flex',
        'lg' => 'hidden lg:flex',
        'xl' => 'hidden xl:flex',
        '2xl' => 'hidden 2xl:flex',
    ];
    $sidebarHeights = [
        'sm' => 'sm:h-[calc(100vh-4rem)]',
        'md' => 'md:h-[calc(100vh-4rem)]',
        'lg' => 'lg:h-[calc(100vh-4rem)]',
        'xl' => 'xl:h-[calc(100vh-4rem)]',
        '2xl' => '2xl:h-[calc(100vh-4rem)]',
    ];
    $responsiveBreakpoint = array_key_exists($responsiveOpen, $mobileOnlyClasses) ? $responsiveOpen : 'lg';
    $mobileOnlyClass = $mobileOnlyClasses[$responsiveBreakpoint];
    $desktopOnlyClass = $desktopOnlyClasses[$responsiveBreakpoint];
    $sidebarHeightClass = $sidebarHeights[$responsiveBreakpoint];
    $collapseAt ??= $responsiveBreakpoint;
?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div <?php echo e($attributes->merge(['class' => 'min-h-screen'])); ?>>
        <?php if (isset($component)) { $__componentOriginala94521fae691f5a72823b6a7ccfb859d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala94521fae691f5a72823b6a7ccfb859d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.drawer','data' => ['id' => $drawerId,'end' => $end,'responsiveOpen' => $responsiveOpen,'sideIsMenu' => false,'sideClass' => 'w-auto','class' => '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.drawer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($drawerId),'end' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($end),'responsiveOpen' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($responsiveOpen),'sideIsMenu' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'sideClass' => 'w-auto','class' => '']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('content', null, []); ?> 
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasNavbar): ?>
                    <div class="bg-base-100 px-4 h-14 flex items-center justify-between gap-4 lg:justify-end">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex items-center gap-2 <?php echo e($mobileOnlyClass); ?>">
                                <label for="<?php echo e($drawerId); ?>" aria-label="open sidebar" class="btn btn-square btn-ghost">
                                    <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $menuIcon,'size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($menuIcon),'size' => 'lg']); ?>
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
                                </label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                                    <div class="font-semibold"><?php echo e(__($title)); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($navbarHeading)): ?>
                                <div class="hidden min-w-0 max-w-xs flex-col justify-center leading-tight text-base-content md:flex lg:max-w-md xl:max-w-xl [&>h1]:truncate [&>h1]:text-sm [&>h1]:font-semibold [&>h1]:leading-tight [&>p]:truncate [&>p]:text-xs [&>p]:leading-tight [&>p]:text-base-content/70" data-navbar-heading>
                                    <?php echo e($navbarHeading); ?>

                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="shrink-0 items-center gap-2 <?php echo e($desktopOnlyClass); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showThemeController): ?>
                                <?php if (isset($component)) { $__componentOriginal9bb0178607a492116e5ecda2e9031c68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9bb0178607a492116e5ecda2e9031c68 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.theme-controller','data' => ['variant' => 'dropdown','themes' => $themes,'label' => $themeLabel,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.theme-controller'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'dropdown','themes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themes),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themeLabel),'size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9bb0178607a492116e5ecda2e9031c68)): ?>
<?php $attributes = $__attributesOriginal9bb0178607a492116e5ecda2e9031c68; ?>
<?php unset($__attributesOriginal9bb0178607a492116e5ecda2e9031c68); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9bb0178607a492116e5ecda2e9031c68)): ?>
<?php $component = $__componentOriginal9bb0178607a492116e5ecda2e9031c68; ?>
<?php unset($__componentOriginal9bb0178607a492116e5ecda2e9031c68); ?>
<?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php echo e($topbarRight ?? ''); ?>

                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNavbar && isset($topbar)): ?>
                    <?php echo e($topbar); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="min-w-0 <?php echo e($container); ?> <?php echo e($hasNavbar && ! isset($topbar) ? 'pt-16' : ''); ?>">
                    <?php echo e($slot); ?>

                </div>
             <?php $__env->endSlot(); ?>
             <?php $__env->slot('side', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginald20b33cb1c35ea0e8504db023e077668 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald20b33cb1c35ea0e8504db023e077668 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.sidebar','data' => ['variant' => $variant,'sideClass' => $sideClass,'expandedWidth' => $expandedWidth,'collapsedWidth' => $collapsedWidth,'collapsed' => $collapsed,'collapsible' => $collapsible,'forceCollapsed' => $forceCollapsed,'expandOnHover' => $expandOnHover,'stickyAt' => $stickyAt,'collapseAt' => $collapseAt,'hasNavbar' => $hasNavbar,'end' => $end,'storageKey' => $storageKey,'name' => $name,'logo' => $logo,'logoAlt' => $logoAlt,'brand' => $brand,'brandHref' => $brandHref,'brandUrl' => $brandUrl,'brandCollapsed' => $brandCollapsed,'showBrand' => $showBrand,'sections' => $sections,'searchable' => $searchable,'searchPlaceholder' => $searchPlaceholder,'searchEmptyLabel' => $searchEmptyLabel,'searchResultsLabel' => $searchResultsLabel,'iconPrefix' => $iconPrefix,'fallbackIcon' => $fallbackIcon,'class' => 'h-full '.e($hasNavbar ? $sidebarHeightClass : '').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($variant),'sideClass' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sideClass),'expandedWidth' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedWidth),'collapsedWidth' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapsedWidth),'collapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapsed),'collapsible' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapsible),'forceCollapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($forceCollapsed),'expandOnHover' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandOnHover),'stickyAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stickyAt),'collapseAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapseAt),'hasNavbar' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasNavbar),'end' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($end),'storageKey' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($storageKey),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'logo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logo),'logoAlt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logoAlt),'brand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brand),'brandHref' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandHref),'brandUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandUrl),'brandCollapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandCollapsed),'showBrand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showBrand),'sections' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sections),'searchable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchable),'searchPlaceholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchPlaceholder),'searchEmptyLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchEmptyLabel),'searchResultsLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchResultsLabel),'iconPrefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'fallbackIcon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fallbackIcon),'class' => 'h-full '.e($hasNavbar ? $sidebarHeightClass : '').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarFooter)): ?>
                         <?php $__env->slot('footer', null, []); ?> <?php echo e($sidebarFooter); ?> <?php $__env->endSlot(); ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald20b33cb1c35ea0e8504db023e077668)): ?>
<?php $attributes = $__attributesOriginald20b33cb1c35ea0e8504db023e077668; ?>
<?php unset($__attributesOriginald20b33cb1c35ea0e8504db023e077668); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald20b33cb1c35ea0e8504db023e077668)): ?>
<?php $component = $__componentOriginald20b33cb1c35ea0e8504db023e077668; ?>
<?php unset($__componentOriginald20b33cb1c35ea0e8504db023e077668); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala94521fae691f5a72823b6a7ccfb859d)): ?>
<?php $attributes = $__attributesOriginala94521fae691f5a72823b6a7ccfb859d; ?>
<?php unset($__attributesOriginala94521fae691f5a72823b6a7ccfb859d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala94521fae691f5a72823b6a7ccfb859d)): ?>
<?php $component = $__componentOriginala94521fae691f5a72823b6a7ccfb859d; ?>
<?php unset($__componentOriginala94521fae691f5a72823b6a7ccfb859d); ?>
<?php endif; ?>
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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/layout/sidebar-layout.blade.php ENDPATH**/ ?>
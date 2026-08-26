<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarContainer' => null,
    // Sidebar options (héritées de sidebar-layout)
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
    'drawerId' => 'layout-nav-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false,
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    // Content container
    'container' => 'p-6',
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
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarContainer' => null,
    // Sidebar options (héritées de sidebar-layout)
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
    'drawerId' => 'layout-nav-sidebar',
    'responsiveOpen' => 'lg',
    'end' => false,
    // Icon options
    'menuIcon' => 'list',
    'iconPrefix' => 'bi',
    'fallbackIcon' => 'circle',
    // Content container
    'container' => 'p-6',
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
    $responsiveBreakpoint = array_key_exists($responsiveOpen, $mobileOnlyClasses) ? $responsiveOpen : 'lg';
    $mobileOnlyClass = $mobileOnlyClasses[$responsiveBreakpoint];
    $collapseAt ??= $responsiveBreakpoint;
?>

<div class="min-h-screen">
    
    <?php if (isset($component)) { $__componentOriginalbd2165b0cec1ddd91acdd4cdee286435 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.sidebar-layout','data' => ['title' => $title,'theme' => $theme,'variant' => $variant,'sideClass' => $sideClass,'expandedWidth' => $expandedWidth,'collapsedWidth' => $collapsedWidth,'collapsed' => $collapsed,'collapsible' => $collapsible,'forceCollapsed' => $forceCollapsed,'expandOnHover' => $expandOnHover,'stickyAt' => $stickyAt,'collapseAt' => $collapseAt,'storageKey' => $storageKey,'name' => $name,'logo' => $logo,'logoAlt' => $logoAlt,'brand' => $brand,'brandHref' => $brandHref,'brandUrl' => $brandUrl,'brandCollapsed' => $brandCollapsed,'showBrand' => $showBrand,'sections' => $sections,'searchable' => $searchable,'searchPlaceholder' => $searchPlaceholder,'searchEmptyLabel' => $searchEmptyLabel,'searchResultsLabel' => $searchResultsLabel,'drawerId' => $drawerId,'responsiveOpen' => $responsiveOpen,'end' => $end,'menuIcon' => $menuIcon,'iconPrefix' => $iconPrefix,'fallbackIcon' => $fallbackIcon,'container' => $container,'hasNavbar' => true,'showThemeController' => $showThemeController,'themes' => $themes,'themeLabel' => $themeLabel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.sidebar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($variant),'sideClass' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sideClass),'expandedWidth' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandedWidth),'collapsedWidth' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapsedWidth),'collapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapsed),'collapsible' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapsible),'forceCollapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($forceCollapsed),'expandOnHover' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expandOnHover),'stickyAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stickyAt),'collapseAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($collapseAt),'storageKey' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($storageKey),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'logo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logo),'logoAlt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($logoAlt),'brand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brand),'brandHref' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandHref),'brandUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandUrl),'brandCollapsed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brandCollapsed),'showBrand' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showBrand),'sections' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sections),'searchable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchable),'searchPlaceholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchPlaceholder),'searchEmptyLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchEmptyLabel),'searchResultsLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchResultsLabel),'drawerId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($drawerId),'responsiveOpen' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($responsiveOpen),'end' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($end),'menuIcon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($menuIcon),'iconPrefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconPrefix),'fallbackIcon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fallbackIcon),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($container),'hasNavbar' => true,'showThemeController' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showThemeController),'themes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themes),'themeLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themeLabel)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sidebarFooter)): ?>
             <?php $__env->slot('sidebarFooter', null, []); ?> <?php echo e($sidebarFooter); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php $__env->slot('topbar', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal603245091fbcca7d0736436ae1a6a099 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal603245091fbcca7d0736436ae1a6a099 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.navbar','data' => ['bg' => $navbarBg,'text' => $navbarText,'shadow' => $navbarShadow,'fixed' => false,'container' => $navbarContainer,'dataNavbarSidebarTopbar' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.navbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bg' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarBg),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarText),'shadow' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarShadow),'fixed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarContainer),'data-navbar-sidebar-topbar' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                 <?php $__env->slot('start', null, []); ?> 
                    <label for="<?php echo e($drawerId); ?>" aria-label="open sidebar" class="btn btn-square btn-ghost <?php echo e($mobileOnlyClass); ?>">
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
                    <?php echo e($navbarStart ?? ($brand ?? '')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($navbarHeading)): ?>
                        <div class="ms-3 hidden min-w-0 max-w-xs flex-col justify-center leading-tight text-base-content sm:flex lg:max-w-md xl:max-w-xl [&>h1]:truncate [&>h1]:text-sm [&>h1]:font-semibold [&>h1]:leading-tight [&>p]:truncate [&>p]:text-xs [&>p]:leading-tight [&>p]:text-base-content/70" data-navbar-heading>
                            <?php echo e($navbarHeading); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('center', null, []); ?> 
                    <?php echo e($navbarCenter ?? ($nav ?? '')); ?>

                 <?php $__env->endSlot(); ?>
                 <?php $__env->slot('end', null, []); ?> 
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
                    <?php echo e($navbarEnd ?? ($actions ?? '')); ?>

                 <?php $__env->endSlot(); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal603245091fbcca7d0736436ae1a6a099)): ?>
<?php $attributes = $__attributesOriginal603245091fbcca7d0736436ae1a6a099; ?>
<?php unset($__attributesOriginal603245091fbcca7d0736436ae1a6a099); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal603245091fbcca7d0736436ae1a6a099)): ?>
<?php $component = $__componentOriginal603245091fbcca7d0736436ae1a6a099; ?>
<?php unset($__componentOriginal603245091fbcca7d0736436ae1a6a099); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
        <?php echo e($slot); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435)): ?>
<?php $attributes = $__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435; ?>
<?php unset($__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbd2165b0cec1ddd91acdd4cdee286435)): ?>
<?php $component = $__componentOriginalbd2165b0cec1ddd91acdd4cdee286435; ?>
<?php unset($__componentOriginalbd2165b0cec1ddd91acdd4cdee286435); ?>
<?php endif; ?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/layout/navbar-sidebar-layout.blade.php ENDPATH**/ ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'theme' => null,
    // Couleurs et styles de la navbar
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Classe container du contenu principal
    'container' => 'container mx-auto p-6',
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
    // Couleurs et styles de la navbar
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top', // top|bottom
    // Classe container du contenu principal
    'container' => 'container mx-auto p-6',
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginal603245091fbcca7d0736436ae1a6a099 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal603245091fbcca7d0736436ae1a6a099 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.navbar','data' => ['bg' => $navbarBg,'text' => $navbarText,'shadow' => $navbarShadow,'fixed' => $navbarFixed,'fixedPosition' => $navbarFixedPosition]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.navbar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bg' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarBg),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarText),'shadow' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarShadow),'fixed' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarFixed),'fixedPosition' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($navbarFixedPosition)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('start', null, []); ?> 
            <?php echo e($navbarStart ?? ($brand ?? null)); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($navbarHeading)): ?>
                <div class="ms-3 hidden min-w-0 max-w-xs flex-col justify-center leading-tight text-base-content sm:flex lg:max-w-md xl:max-w-xl [&>h1]:truncate [&>h1]:text-sm [&>h1]:font-semibold [&>h1]:leading-tight [&>p]:truncate [&>p]:text-xs [&>p]:leading-tight [&>p]:text-base-content/70" data-navbar-heading>
                    <?php echo e($navbarHeading); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('center', null, []); ?> 
            <?php echo e($navbarCenter ?? ($nav ?? null)); ?>

         <?php $__env->endSlot(); ?>
         <?php $__env->slot('end', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal9bb0178607a492116e5ecda2e9031c68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9bb0178607a492116e5ecda2e9031c68 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.theme-controller','data' => ['variant' => 'dropdown','themes' => ['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter'],'label' => 'Theme','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.theme-controller'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'dropdown','themes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['light', 'dark', 'cupcake', 'bumblebee', 'emerald', 'corporate', 'synthwave', 'retro', 'cyberpunk', 'valentine', 'halloween', 'garden', 'forest', 'aqua', 'lofi', 'pastel', 'fantasy', 'wireframe', 'black', 'luxury', 'dracula', 'cmyk', 'autumn', 'business', 'acid', 'lemonade', 'night', 'coffee', 'winter']),'label' => 'Theme','size' => 'sm']); ?>
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
            <?php echo e($navbarEnd ?? ($actions ?? null)); ?>

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

    <main class="<?php echo e($container); ?> pt-24">
        <?php echo e($slot); ?>

    </main>

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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/layout/navbar-layout.blade.php ENDPATH**/ ?>
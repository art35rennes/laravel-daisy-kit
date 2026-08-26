

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'theme' => null,
    // Navbar options
    'navbarBg' => 'base-100',
    'navbarText' => null,
    'navbarShadow' => 'sm',
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top',
    // Grid options
    'gap' => 4,
    'align' => 'start', // start|center|end
    'container' => 'container mx-auto p-6',
    // Footer options
    'footerBg' => 'base-200',
    'footerText' => 'base-content',
    'footerPadding' => 'p-10',
    'footerCenter' => false,
    'footerHorizontal' => false,
    'footerHorizontalAt' => null,
    'footerColumns' => [],
    'footerLogo' => null,
    'footerBrandText' => null,
    'footerBrandDescription' => null,
    'footerCopyright' => null,
    'footerCopyrightYear' => null,
    'footerCopyrightText' => null,
    'footerSocialLinks' => [],
    'footerNewsletter' => false,
    'footerNewsletterTitle' => null,
    'footerNewsletterDescription' => null,
    'footerNewsletterAction' => null,
    'footerNewsletterMethod' => 'POST',
    'footerShowDivider' => true,
    'footerDividerColor' => null,
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
    'navbarFixed' => true,
    'navbarFixedPosition' => 'top',
    // Grid options
    'gap' => 4,
    'align' => 'start', // start|center|end
    'container' => 'container mx-auto p-6',
    // Footer options
    'footerBg' => 'base-200',
    'footerText' => 'base-content',
    'footerPadding' => 'p-10',
    'footerCenter' => false,
    'footerHorizontal' => false,
    'footerHorizontalAt' => null,
    'footerColumns' => [],
    'footerLogo' => null,
    'footerBrandText' => null,
    'footerBrandDescription' => null,
    'footerCopyright' => null,
    'footerCopyrightYear' => null,
    'footerCopyrightText' => null,
    'footerSocialLinks' => [],
    'footerNewsletter' => false,
    'footerNewsletterTitle' => null,
    'footerNewsletterDescription' => null,
    'footerNewsletterAction' => null,
    'footerNewsletterMethod' => 'POST',
    'footerShowDivider' => true,
    'footerDividerColor' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $slot = $slot ?? '';
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

    
    <main class="<?php echo e($container); ?> <?php echo e($navbarFixed ? 'pt-24' : ''); ?> min-h-screen">
        <?php if (isset($component)) { $__componentOriginala8292fbc6719c22f60e6bbff9e345811 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8292fbc6719c22f60e6bbff9e345811 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.grid-layout','data' => ['gap' => $gap,'align' => $align]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.grid-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['gap' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gap),'align' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($align)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php echo e($slot); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $attributes = $__attributesOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $component = $__componentOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__componentOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
    </main>

    
    <?php if (isset($component)) { $__componentOriginale8fe76f5c0a00b92936146899c13d917 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale8fe76f5c0a00b92936146899c13d917 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.footer-layout','data' => ['bg' => $footerBg,'text' => $footerText,'padding' => $footerPadding,'center' => $footerCenter,'horizontal' => $footerHorizontal,'horizontalAt' => $footerHorizontalAt,'columns' => $footerColumns,'logo' => $footerLogo,'brandText' => $footerBrandText,'brandDescription' => $footerBrandDescription,'copyright' => $footerCopyright,'copyrightYear' => $footerCopyrightYear,'copyrightText' => $footerCopyrightText,'socialLinks' => $footerSocialLinks,'newsletter' => $footerNewsletter,'newsletterTitle' => $footerNewsletterTitle,'newsletterDescription' => $footerNewsletterDescription,'newsletterAction' => $footerNewsletterAction,'newsletterMethod' => $footerNewsletterMethod,'showDivider' => $footerShowDivider,'dividerColor' => $footerDividerColor]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.footer-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bg' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerBg),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerText),'padding' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerPadding),'center' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerCenter),'horizontal' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerHorizontal),'horizontalAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerHorizontalAt),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerColumns),'logo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerLogo),'brandText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerBrandText),'brandDescription' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerBrandDescription),'copyright' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerCopyright),'copyrightYear' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerCopyrightYear),'copyrightText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerCopyrightText),'socialLinks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerSocialLinks),'newsletter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerNewsletter),'newsletterTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerNewsletterTitle),'newsletterDescription' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerNewsletterDescription),'newsletterAction' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerNewsletterAction),'newsletterMethod' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerNewsletterMethod),'showDivider' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerShowDivider),'dividerColor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($footerDividerColor)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($columns) && $columns instanceof \Illuminate\View\ComponentSlot): ?>
             <?php $__env->slot('columns', null, []); ?> <?php echo e($columns); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($copyright) && $copyright instanceof \Illuminate\View\ComponentSlot): ?>
             <?php $__env->slot('copyright', null, []); ?> <?php echo e($copyright); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footerBottom) && $footerBottom instanceof \Illuminate\View\ComponentSlot): ?>
             <?php $__env->slot('footerBottom', null, []); ?> <?php echo e($footerBottom); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale8fe76f5c0a00b92936146899c13d917)): ?>
<?php $attributes = $__attributesOriginale8fe76f5c0a00b92936146899c13d917; ?>
<?php unset($__attributesOriginale8fe76f5c0a00b92936146899c13d917); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale8fe76f5c0a00b92936146899c13d917)): ?>
<?php $component = $__componentOriginale8fe76f5c0a00b92936146899c13d917; ?>
<?php unset($__componentOriginale8fe76f5c0a00b92936146899c13d917); ?>
<?php endif; ?>
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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/layout/navbar-grid-footer.blade.php ENDPATH**/ ?>
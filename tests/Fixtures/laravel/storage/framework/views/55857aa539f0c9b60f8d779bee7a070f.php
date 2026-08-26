    <!doctype html>
    <html lang="en">
        <head>
            <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'nonce-smoke-nonce'; style-src 'self' 'nonce-smoke-nonce'">
            <?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->yieldPushContent('styles'); ?>
        </head>
        <body>
            <?php if (isset($component)) { $__componentOriginal40312bcd153c4f1bbfbe6543713be4a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40312bcd153c4f1bbfbe6543713be4a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.hero','data' => ['imageUrl' => '/img/example.jpg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['image-url' => '/img/example.jpg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb355cab2b2984b49b730ce467e13f652)): ?>
<?php $attributes = $__attributesOriginalb355cab2b2984b49b730ce467e13f652; ?>
<?php unset($__attributesOriginalb355cab2b2984b49b730ce467e13f652); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb355cab2b2984b49b730ce467e13f652)): ?>
<?php $component = $__componentOriginalb355cab2b2984b49b730ce467e13f652; ?>
<?php unset($__componentOriginalb355cab2b2984b49b730ce467e13f652); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal10ad05071a6832c005a49ec6f828332a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10ad05071a6832c005a49ec6f828332a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.range','data' => ['noFill' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['no-fill' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10ad05071a6832c005a49ec6f828332a)): ?>
<?php $attributes = $__attributesOriginal10ad05071a6832c005a49ec6f828332a; ?>
<?php unset($__attributesOriginal10ad05071a6832c005a49ec6f828332a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10ad05071a6832c005a49ec6f828332a)): ?>
<?php $component = $__componentOriginal10ad05071a6832c005a49ec6f828332a; ?>
<?php unset($__componentOriginal10ad05071a6832c005a49ec6f828332a); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal0ce53333fd9bce9077254ba1f15363cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0ce53333fd9bce9077254ba1f15363cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.media.embed','data' => ['src' => '/frame']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.media.embed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => '/frame']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0ce53333fd9bce9077254ba1f15363cf)): ?>
<?php $attributes = $__attributesOriginal0ce53333fd9bce9077254ba1f15363cf; ?>
<?php unset($__attributesOriginal0ce53333fd9bce9077254ba1f15363cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0ce53333fd9bce9077254ba1f15363cf)): ?>
<?php $component = $__componentOriginal0ce53333fd9bce9077254ba1f15363cf; ?>
<?php unset($__componentOriginal0ce53333fd9bce9077254ba1f15363cf); ?>
<?php endif; ?>
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

            <?php if (isset($component)) { $__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.breadcrumbs','data' => ['jsonLd' => true,'items' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Current'],
            ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['json-ld' => true,'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Current'],
            ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf)): ?>
<?php $attributes = $__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf; ?>
<?php unset($__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf)): ?>
<?php $component = $__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf; ?>
<?php unset($__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf); ?>
<?php endif; ?>

            <?php echo $__env->yieldPushContent('scripts'); ?>
        </body>
    </html><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/ba519716dcba108eb7ca5adb0ab298ec.blade.php ENDPATH**/ ?>
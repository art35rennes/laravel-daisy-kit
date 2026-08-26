    <?php if (isset($component)) { $__componentOriginal09971076e99c1c5834bc3fb747811213 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal09971076e99c1c5834bc3fb747811213 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.profile.profile-edit','data' => ['showPhone' => true,'showLocation' => true,'showWebsite' => true,'profile' => [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '+33123456789',
            'location' => 'Rennes',
            'website' => 'https://example.com',
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.profile.profile-edit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['show-phone' => true,'show-location' => true,'show-website' => true,'profile' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '+33123456789',
            'location' => 'Rennes',
            'website' => 'https://example.com',
        ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal09971076e99c1c5834bc3fb747811213)): ?>
<?php $attributes = $__attributesOriginal09971076e99c1c5834bc3fb747811213; ?>
<?php unset($__attributesOriginal09971076e99c1c5834bc3fb747811213); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal09971076e99c1c5834bc3fb747811213)): ?>
<?php $component = $__componentOriginal09971076e99c1c5834bc3fb747811213; ?>
<?php unset($__componentOriginal09971076e99c1c5834bc3fb747811213); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/132b9dfcf1f81ae567b3fd2ee9a8eda6.blade.php ENDPATH**/ ?>
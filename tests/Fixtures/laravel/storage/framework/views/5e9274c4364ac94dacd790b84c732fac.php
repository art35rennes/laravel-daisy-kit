    <?php if (isset($component)) { $__componentOriginald20b33cb1c35ea0e8504db023e077668 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald20b33cb1c35ea0e8504db023e077668 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.sidebar','data' => ['brand' => 'Acme','brandUrl' => '/dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['brand' => 'Acme','brand-url' => '/dashboard']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald20b33cb1c35ea0e8504db023e077668)): ?>
<?php $attributes = $__attributesOriginald20b33cb1c35ea0e8504db023e077668; ?>
<?php unset($__attributesOriginald20b33cb1c35ea0e8504db023e077668); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald20b33cb1c35ea0e8504db023e077668)): ?>
<?php $component = $__componentOriginald20b33cb1c35ea0e8504db023e077668; ?>
<?php unset($__componentOriginald20b33cb1c35ea0e8504db023e077668); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/6d438633588f7f898a73c627366bd772.blade.php ENDPATH**/ ?>
    <?php if (isset($component)) { $__componentOriginal11a0739b0dc1eccfd965e70a6223062a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11a0739b0dc1eccfd965e70a6223062a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.color-picker','data' => ['id' => 'brand-color','name' => 'brand_color','value' => '#123456','dropdown' => true,'swatches' => [['#123456', '#abcdef']],'showAlpha' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.color-picker'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'brand-color','name' => 'brand_color','value' => '#123456','dropdown' => true,'swatches' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['#123456', '#abcdef']]),'show-alpha' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11a0739b0dc1eccfd965e70a6223062a)): ?>
<?php $attributes = $__attributesOriginal11a0739b0dc1eccfd965e70a6223062a; ?>
<?php unset($__attributesOriginal11a0739b0dc1eccfd965e70a6223062a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11a0739b0dc1eccfd965e70a6223062a)): ?>
<?php $component = $__componentOriginal11a0739b0dc1eccfd965e70a6223062a; ?>
<?php unset($__componentOriginal11a0739b0dc1eccfd965e70a6223062a); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/52977c7ed89bec2e73a8c64bdda74d70.blade.php ENDPATH**/ ?>
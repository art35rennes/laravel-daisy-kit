    <?php if (isset($component)) { $__componentOriginal07986fd896a426bae91d5734bd48976f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal07986fd896a426bae91d5734bd48976f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.choice-card-group','data' => ['name' => 'profile','items' => [
            ['value' => 'individual', 'label' => 'Particulier', 'icon' => 'person'],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.choice-card-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'profile','items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['value' => 'individual', 'label' => 'Particulier', 'icon' => 'person'],
        ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal07986fd896a426bae91d5734bd48976f)): ?>
<?php $attributes = $__attributesOriginal07986fd896a426bae91d5734bd48976f; ?>
<?php unset($__attributesOriginal07986fd896a426bae91d5734bd48976f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal07986fd896a426bae91d5734bd48976f)): ?>
<?php $component = $__componentOriginal07986fd896a426bae91d5734bd48976f; ?>
<?php unset($__componentOriginal07986fd896a426bae91d5734bd48976f); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/d390d6a07529d06109b57bebfc1933b1.blade.php ENDPATH**/ ?>
    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['rowKey' => 'id','columns' => [['key' => 'actions', 'type' => 'actions']],'rows' => [['id' => 'user-1', 'actions' => ['action' => 'remove&amp;quot; onclick=&amp;quot;alert(1)', 'label' => '&lt;script&gt;alert(1)&lt;/script&gt;', 'variant' => 'unknown']]]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['row-key' => 'id','columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['key' => 'actions', 'type' => 'actions']]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['id' => 'user-1', 'actions' => ['action' => 'remove&amp;quot; onclick=&amp;quot;alert(1)', 'label' => '&lt;script&gt;alert(1)&lt;/script&gt;', 'variant' => 'unknown']]])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal60cd61a283b1a188e2653a28cf5f25be)): ?>
<?php $attributes = $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be; ?>
<?php unset($__attributesOriginal60cd61a283b1a188e2653a28cf5f25be); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal60cd61a283b1a188e2653a28cf5f25be)): ?>
<?php $component = $__componentOriginal60cd61a283b1a188e2653a28cf5f25be; ?>
<?php unset($__componentOriginal60cd61a283b1a188e2653a28cf5f25be); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/7f838993aee271df6932d09920d83dfd.blade.php ENDPATH**/ ?>
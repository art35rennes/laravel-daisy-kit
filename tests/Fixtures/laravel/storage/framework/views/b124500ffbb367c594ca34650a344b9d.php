    <?php if (isset($component)) { $__componentOriginalcbcbb17075ca6a7d41dffb83a751129b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.navbar-sidebar-layout','data' => ['themes' => ['light', 'dark'],'themeLabel' => 'Appearance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.navbar-sidebar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['themes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['light', 'dark']),'theme-label' => 'Appearance']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b)): ?>
<?php $attributes = $__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b; ?>
<?php unset($__attributesOriginalcbcbb17075ca6a7d41dffb83a751129b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbcbb17075ca6a7d41dffb83a751129b)): ?>
<?php $component = $__componentOriginalcbcbb17075ca6a7d41dffb83a751129b; ?>
<?php unset($__componentOriginalcbcbb17075ca6a7d41dffb83a751129b); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/7b7e27c583f1afbb5447ed44c4075608.blade.php ENDPATH**/ ?>
    <?php if (isset($component)) { $__componentOriginalbd2165b0cec1ddd91acdd4cdee286435 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.sidebar-layout','data' => ['title' => 'Dashboard','variant' => 'slim','forceCollapsed' => true,'collapsible' => false,'sections' => [['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.sidebar-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dashboard','variant' => 'slim','force-collapsed' => true,'collapsible' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'sections' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        Content
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435)): ?>
<?php $attributes = $__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435; ?>
<?php unset($__attributesOriginalbd2165b0cec1ddd91acdd4cdee286435); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbd2165b0cec1ddd91acdd4cdee286435)): ?>
<?php $component = $__componentOriginalbd2165b0cec1ddd91acdd4cdee286435; ?>
<?php unset($__componentOriginalbd2165b0cec1ddd91acdd4cdee286435); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/e04e991419b923ea8ade041c805cde9d.blade.php ENDPATH**/ ?>
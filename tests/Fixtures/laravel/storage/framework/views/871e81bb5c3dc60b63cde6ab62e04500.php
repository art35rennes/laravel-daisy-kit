<?php if (isset($component)) { $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.blueprint','data' => ['name' => 'workflow','mode' => 'edit','direction' => 'TB','height' => '640px','layout' => 'radial','transitionShape' => 'orthogonal','transitionColor' => 'accent','nodeColor' => 'neutral','inspectorMode' => 'sidebar','autosave' => true,'nodeCategories' => [[
        'value' => 'approval',
        'label' => 'Approbation',
        'color' => 'success',
        'defaults' => ['required_approvals' => 2],
        'fields' => [[
            'key' => 'owner_uuid',
            'type' => 'select',
            'label' => 'Responsable',
            'required' => true,
            'options' => [['value' => 'ada', 'label' => 'Ada']],
        ]],
    ]],'transitionCategories' => [[
        'value' => 'return',
        'label' => 'Retour',
        'shape' => 's',
        'color' => 'warning',
    ]],'value' => [
        'nodes' => [['id' => 'review', 'label' => 'Révision']],
        'transitions' => [],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.blueprint'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'workflow','mode' => 'edit','direction' => 'TB','height' => '640px','layout' => 'radial','transition-shape' => 'orthogonal','transition-color' => 'accent','node-color' => 'neutral','inspector-mode' => 'sidebar','autosave' => true,'node-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([[
        'value' => 'approval',
        'label' => 'Approbation',
        'color' => 'success',
        'defaults' => ['required_approvals' => 2],
        'fields' => [[
            'key' => 'owner_uuid',
            'type' => 'select',
            'label' => 'Responsable',
            'required' => true,
            'options' => [['value' => 'ada', 'label' => 'Ada']],
        ]],
    ]]),'transition-categories' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([[
        'value' => 'return',
        'label' => 'Retour',
        'shape' => 's',
        'color' => 'warning',
    ]]),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        'nodes' => [['id' => 'review', 'label' => 'Révision']],
        'transitions' => [],
    ])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('inspector', null, []); ?> <div data-host-inspector>Host content</div> <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $attributes = $__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__attributesOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1)): ?>
<?php $component = $__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1; ?>
<?php unset($__componentOriginale09d0f4dbd8d6c0a54ee3a5ca28478c1); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/e0720838ab6040be33bb4b1434597fe1.blade.php ENDPATH**/ ?>
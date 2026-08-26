    <?php if (isset($component)) { $__componentOriginal60cd61a283b1a188e2653a28cf5f25be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60cd61a283b1a188e2653a28cf5f25be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.table','data' => ['mode' => 'client','rowKey' => 'id','searchMode' => 'includes','subRowsKey' => 'children','columnResizing' => true,'editable' => true,'editEndpoint' => '/users/edit','editMethod' => 'PUT','editMode' => 'row','editableColumns' => ['name'],'editPolicy' => ['required' => ['name']],'columns' => [
            ['key' => 'name', 'label' => 'Name', 'size' => 160, 'minSize' => 80, 'maxSize' => 320],
            ['key' => 'status', 'label' => 'Status'],
        ],'rows' => [
            ['id' => 'user-1', 'name' => 'Jane', 'status' => 'draft', 'children' => []],
        ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => 'client','row-key' => 'id','search-mode' => 'includes','sub-rows-key' => 'children','column-resizing' => true,'editable' => true,'edit-endpoint' => '/users/edit','edit-method' => 'PUT','edit-mode' => 'row','editable-columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['name']),'edit-policy' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['required' => ['name']]),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['key' => 'name', 'label' => 'Name', 'size' => 160, 'minSize' => 80, 'maxSize' => 320],
            ['key' => 'status', 'label' => 'Status'],
        ]),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            ['id' => 'user-1', 'name' => 'Jane', 'status' => 'draft', 'children' => []],
        ])]); ?>
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
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/0f0bae7c947d3436c186bcd3e620a0db.blade.php ENDPATH**/ ?>
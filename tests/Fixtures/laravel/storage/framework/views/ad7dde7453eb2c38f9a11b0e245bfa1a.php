<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <button
        type="button"
        class="btn btn-xs <?php echo e(\Art35rennes\DaisyKit\Support\DaisyTableActions::Variants[$action['variant']]); ?>"
        data-table-row-action="<?php echo e($action['action']); ?>"
        data-table-row-id="<?php echo e($rowId); ?>"
        data-table-column-id="<?php echo e($columnId); ?>"
        <?php if($action['ariaLabel'] !== ''): ?> aria-label="<?php echo e($action['ariaLabel']); ?>" <?php endif; ?>
        <?php if($action['disabled']): echo 'disabled'; endif; ?>
        <?php if($action['disabled']): ?> aria-disabled="true" <?php endif; ?>
    ><?php echo e($action['label']); ?></button>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/partials/table-actions.blade.php ENDPATH**/ ?>
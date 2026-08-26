    <?php if (isset($component)) { $__componentOriginale6131ca26c0be8f9950e53cc775de81a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6131ca26c0be8f9950e53cc775de81a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.destructive-confirm','data' => ['title' => 'Supprimer le document','message' => 'La suppression est définitive.','detail' => 'Les fichiers et empreintes associées seront retirés.','color' => 'warning','confirmText' => 'Supprimer','confirmVariant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.destructive-confirm'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Supprimer le document','message' => 'La suppression est définitive.','detail' => 'Les fichiers et empreintes associées seront retirés.','color' => 'warning','confirm-text' => 'Supprimer','confirm-variant' => 'outline']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <button type="button">Open</button>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6131ca26c0be8f9950e53cc775de81a)): ?>
<?php $attributes = $__attributesOriginale6131ca26c0be8f9950e53cc775de81a; ?>
<?php unset($__attributesOriginale6131ca26c0be8f9950e53cc775de81a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6131ca26c0be8f9950e53cc775de81a)): ?>
<?php $component = $__componentOriginale6131ca26c0be8f9950e53cc775de81a; ?>
<?php unset($__componentOriginale6131ca26c0be8f9950e53cc775de81a); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/f65082cc086744eab95c6e465cdcdea7.blade.php ENDPATH**/ ?>
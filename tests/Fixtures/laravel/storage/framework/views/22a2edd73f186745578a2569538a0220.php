<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,          // Required to bind errors/old values
    'for' => '__name',       // Optional explicit HTML id targeted by the label (`null` disables `for`)
    'label' => null,         // Label text (can be overridden by slot: label)
    'labelClass' => null,    // Extra classes on label
    'error' => null,         // Force an error message (overrides Laravel $errors)
    'hint' => null,          // Help text (can be overridden by slot: hint)
    'hintMode' => 'text',    // text|icon
    'required' => false,     // Display required asterisk on label
    'srOnly' => false,       // Screen-reader only label
    'as' => 'div',           // Wrapper tag
    'full' => true,          // Apply w-full on wrapper
    'class' => '',           // Extra classes on wrapper
    'id' => null,            // Explicit control id; defaults from name
    'gap' => 'gap-1',        // Vertical spacing between label, control, hint, and error
    'labelWrap' => 'truncate', // truncate|wrap|normal label overflow behavior
    'controlClass' => null,  // Extra classes on the control containment wrapper
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name' => null,          // Required to bind errors/old values
    'for' => '__name',       // Optional explicit HTML id targeted by the label (`null` disables `for`)
    'label' => null,         // Label text (can be overridden by slot: label)
    'labelClass' => null,    // Extra classes on label
    'error' => null,         // Force an error message (overrides Laravel $errors)
    'hint' => null,          // Help text (can be overridden by slot: hint)
    'hintMode' => 'text',    // text|icon
    'required' => false,     // Display required asterisk on label
    'srOnly' => false,       // Screen-reader only label
    'as' => 'div',           // Wrapper tag
    'full' => true,          // Apply w-full on wrapper
    'class' => '',           // Extra classes on wrapper
    'id' => null,            // Explicit control id; defaults from name
    'gap' => 'gap-1',        // Vertical spacing between label, control, hint, and error
    'labelWrap' => 'truncate', // truncate|wrap|normal label overflow behavior
    'controlClass' => null,  // Extra classes on the control containment wrapper
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Determine final wrapper classes.
    $wrapperClasses = trim(($full ? 'w-full ' : '')."daisy-form-field min-w-0 max-w-full flex flex-col {$gap} {$class}");
    $controlClasses = trim('daisy-form-field-control w-full min-w-0 max-w-full '.$controlClass);
    $labelClasses = trim('daisy-form-field-label '.$labelClass);
    $resolvedLabelWrap = in_array((string) $labelWrap, ['truncate', 'wrap', 'normal'], true) ? (string) $labelWrap : 'truncate';
    $resolvedHintMode = $hintMode === 'icon' ? 'icon' : 'text';
    $fieldId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $labelFor = $for === '__name' ? $fieldId : $for;

    // Resolve message and state from Laravel validation bag if name is provided.
    $laravelErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $laravelMessage = null;
    if ($name) {
        $laravelMessage = $laravelErrors->first($name);
    }
    $message = $error ?? $laravelMessage;
    $hasError = filled($message);

    // Expose commonly used values to the slot content.
    // - $hasError: boolean for conditional classes
    // - $errorMessage: string|null
    // - $oldValue: previous submitted value for this field
    // - $errorClassInput: class string for input error (empty if no error)
    // - $errorClassSelect: class string for select error (empty if no error)
    // - $errorClassTextarea: class string for textarea error (empty if no error)
    $errorMessage = $message;
    $oldValue = $name ? old($name) : null;
    $errorClassInput = $hasError ? 'input-error' : '';
    $errorClassSelect = $hasError ? 'select-error' : '';
    $errorClassTextarea = $hasError ? 'textarea-error' : '';
    $hintId = $fieldId ? $fieldId.'-hint' : null;
    $errorId = $fieldId ? $fieldId.'-error' : null;
    $describedBy = collect([$hint || isset($hintSlot) ? $hintId : null, $errorMessage ? $errorId : null])
        ->filter()
        ->implode(' ');
?>

<<?php echo e($as); ?> <?php echo e($attributes->merge(['class' => $wrapperClasses])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($label || isset($labelSlot)): ?>
        <?php if (isset($component)) { $__componentOriginalb71d880671c75daf17af1766dc1e0f68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb71d880671c75daf17af1766dc1e0f68 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.label','data' => ['for' => $labelFor,'srOnly' => $srOnly,'class' => ''.e($labelClasses).'','dataLabelWrap' => ''.e($resolvedLabelWrap).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($labelFor),'srOnly' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($srOnly),'class' => ''.e($labelClasses).'','data-label-wrap' => ''.e($resolvedLabelWrap).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php ($labelText = $label); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($labelSlot)): ?>
                <?php echo e($labelSlot); ?>

            <?php else: ?>
                <?php echo e($labelText); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?>
                    <span aria-hidden="true" class="text-error ml-1">*</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedHintMode === 'icon' && (isset($hintSlot) || $hint)): ?>
                <span class="daisy-form-field-hint-icon tooltip tooltip-top ms-1 inline-flex align-middle" tabindex="0">
                    <span class="tooltip-content">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($hintSlot)): ?>
                            <?php echo e($hintSlot); ?>

                        <?php else: ?>
                            <?php echo e($hint); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-info-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['aria-hidden' => 'true','class' => 'size-4 shrink-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb71d880671c75daf17af1766dc1e0f68)): ?>
<?php $attributes = $__attributesOriginalb71d880671c75daf17af1766dc1e0f68; ?>
<?php unset($__attributesOriginalb71d880671c75daf17af1766dc1e0f68); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb71d880671c75daf17af1766dc1e0f68)): ?>
<?php $component = $__componentOriginalb71d880671c75daf17af1766dc1e0f68; ?>
<?php unset($__componentOriginalb71d880671c75daf17af1766dc1e0f68); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    <div class="<?php echo e($controlClasses); ?>" <?php if($describedBy): ?> role="group" aria-describedby="<?php echo e($describedBy); ?>" <?php endif; ?>>
        <?php echo e($slot); ?>

    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($hintSlot) || $hint): ?>
        <p <?php if($hintId): ?> id="<?php echo e($hintId); ?>" <?php endif; ?> class="<?php echo e($resolvedHintMode === 'icon' ? 'sr-only' : 'mt-1 text-sm text-base-content/70'); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($hintSlot)): ?>
                <?php echo e($hintSlot); ?>

            <?php else: ?>
                <?php echo e($hint); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessage): ?>
        <?php if (isset($component)) { $__componentOriginal74c4edaaf4ed25d31bc679c0cadc9c83 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74c4edaaf4ed25d31bc679c0cadc9c83 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.validator','data' => ['state' => 'error','message' => $errorMessage,'full' => false,'as' => 'div','class' => 'mt-1','id' => $errorId]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.validator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['state' => 'error','message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errorMessage),'full' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'as' => 'div','class' => 'mt-1','id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errorId)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74c4edaaf4ed25d31bc679c0cadc9c83)): ?>
<?php $attributes = $__attributesOriginal74c4edaaf4ed25d31bc679c0cadc9c83; ?>
<?php unset($__attributesOriginal74c4edaaf4ed25d31bc679c0cadc9c83); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74c4edaaf4ed25d31bc679c0cadc9c83)): ?>
<?php $component = $__componentOriginal74c4edaaf4ed25d31bc679c0cadc9c83; ?>
<?php unset($__componentOriginal74c4edaaf4ed25d31bc679c0cadc9c83); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</<?php echo e($as); ?>>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/partials/form-field.blade.php ENDPATH**/ ?>
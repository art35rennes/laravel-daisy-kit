<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'provider' => 'cally',
    'mode' => 'date',
    'months' => 1,
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'placeholder' => null,
    'firstDay' => 1,
    'type' => null,
    'options' => [],
    'valueSeparator' => ',',
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
    'provider' => 'cally',
    'mode' => 'date',
    'months' => 1,
    'showPrevNext' => true,
    'inputId' => null,
    'value' => null,
    'min' => null,
    'max' => null,
    'locale' => null,
    'placeholder' => null,
    'firstDay' => 1,
    'type' => null,
    'options' => [],
    'valueSeparator' => ',',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($provider === 'native'): ?>
    <?php if (isset($component)) { $__componentOriginal108ca0bdfa5fc0168ed6d0f15176655b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal108ca0bdfa5fc0168ed6d0f15176655b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.calendar-native','data' => ['inputId' => $inputId,'value' => $value,'min' => $min,'max' => $max,'placeholder' => $placeholder,'attributes' => $attributes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.calendar-native'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['inputId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputId),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($min),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($max),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal108ca0bdfa5fc0168ed6d0f15176655b)): ?>
<?php $attributes = $__attributesOriginal108ca0bdfa5fc0168ed6d0f15176655b; ?>
<?php unset($__attributesOriginal108ca0bdfa5fc0168ed6d0f15176655b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal108ca0bdfa5fc0168ed6d0f15176655b)): ?>
<?php $component = $__componentOriginal108ca0bdfa5fc0168ed6d0f15176655b; ?>
<?php unset($__componentOriginal108ca0bdfa5fc0168ed6d0f15176655b); ?>
<?php endif; ?>
<?php elseif($provider === 'vanilla'): ?>
    <?php if (isset($component)) { $__componentOriginalf5b5708075e5d2b0e6d319e1f82be35b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf5b5708075e5d2b0e6d319e1f82be35b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.calendar-vanilla','data' => ['inputId' => $inputId,'mode' => $mode,'months' => $months,'showPrevNext' => $showPrevNext,'value' => $value,'min' => $min,'max' => $max,'locale' => $locale,'firstDay' => $firstDay,'type' => $type,'options' => $options,'valueSeparator' => $valueSeparator,'attributes' => $attributes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.calendar-vanilla'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['inputId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inputId),'mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode),'months' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($months),'showPrevNext' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showPrevNext),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($min),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($max),'locale' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($locale),'firstDay' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($firstDay),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($type),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($options),'valueSeparator' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($valueSeparator),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf5b5708075e5d2b0e6d319e1f82be35b)): ?>
<?php $attributes = $__attributesOriginalf5b5708075e5d2b0e6d319e1f82be35b; ?>
<?php unset($__attributesOriginalf5b5708075e5d2b0e6d319e1f82be35b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf5b5708075e5d2b0e6d319e1f82be35b)): ?>
<?php $component = $__componentOriginalf5b5708075e5d2b0e6d319e1f82be35b; ?>
<?php unset($__componentOriginalf5b5708075e5d2b0e6d319e1f82be35b); ?>
<?php endif; ?>
<?php else: ?>
    <?php if (isset($component)) { $__componentOriginal52bfe01e0ae702a264ac9f86cf160e01 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal52bfe01e0ae702a264ac9f86cf160e01 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.calendar-cally','data' => ['mode' => $mode,'months' => $months,'showPrevNext' => $showPrevNext,'value' => $value,'min' => $min,'max' => $max,'locale' => $locale,'attributes' => $attributes]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.calendar-cally'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mode),'months' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($months),'showPrevNext' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showPrevNext),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($min),'max' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($max),'locale' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($locale),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($previous)): ?>
             <?php $__env->slot('previous', null, []); ?> <?php echo e($previous); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($next)): ?>
             <?php $__env->slot('next', null, []); ?> <?php echo e($next); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($heading)): ?>
             <?php $__env->slot('heading', null, []); ?> <?php echo e($heading); ?> <?php $__env->endSlot(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo e($slot); ?>

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal52bfe01e0ae702a264ac9f86cf160e01)): ?>
<?php $attributes = $__attributesOriginal52bfe01e0ae702a264ac9f86cf160e01; ?>
<?php unset($__attributesOriginal52bfe01e0ae702a264ac9f86cf160e01); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal52bfe01e0ae702a264ac9f86cf160e01)): ?>
<?php $component = $__componentOriginal52bfe01e0ae702a264ac9f86cf160e01; ?>
<?php unset($__componentOriginal52bfe01e0ae702a264ac9f86cf160e01); ?>
<?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/calendar.blade.php ENDPATH**/ ?>
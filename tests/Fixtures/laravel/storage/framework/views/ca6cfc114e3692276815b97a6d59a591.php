

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::form.builder.title'),
    'description' => __('daisy::form.builder.description'),
    'schema' => null,
    'value' => [],
    'errors' => [],
    'schemaName' => 'schema',
    'fieldTypes' => null,
    'functionCatalog' => null,
    'viewerSubmitMode' => null,
    'preview' => true,
    'jsonEditor' => false,
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
    'title' => __('daisy::form.builder.title'),
    'description' => __('daisy::form.builder.description'),
    'schema' => null,
    'value' => [],
    'errors' => [],
    'schemaName' => 'schema',
    'fieldTypes' => null,
    'functionCatalog' => null,
    'viewerSubmitMode' => null,
    'preview' => true,
    'jsonEditor' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section <?php echo e($attributes->merge(['class' => 'space-y-6'])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title || $description): ?>
        <header class="space-y-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
                <h1 class="text-2xl font-semibold"><?php echo e($title); ?></h1>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description): ?>
                <p class="max-w-3xl text-sm text-base-content/70"><?php echo e($description); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </header>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-base font-semibold"><?php echo e(__('daisy::form.builder.surface')); ?></h2>
            <span class="badge badge-outline"><?php echo e(__('daisy::form.builder.schema_version', ['version' => '1.0'])); ?></span>
        </div>

        <?php if (isset($component)) { $__componentOriginal65da8be3abf0702790c6a63da305415d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65da8be3abf0702790c6a63da305415d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.forms.builder','data' => ['schema' => $schema,'fieldTypes' => $fieldTypes,'functionCatalog' => $functionCatalog,'preview' => $preview,'jsonEditor' => $jsonEditor,'name' => $schemaName,'value' => $value,'errors' => $errors,'viewerSubmitMode' => $viewerSubmitMode]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::forms.builder'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['schema' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($schema),'field-types' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fieldTypes),'function-catalog' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($functionCatalog),'preview' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($preview),'json-editor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jsonEditor),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($schemaName),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($value),'errors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors),'viewer-submit-mode' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($viewerSubmitMode)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65da8be3abf0702790c6a63da305415d)): ?>
<?php $attributes = $__attributesOriginal65da8be3abf0702790c6a63da305415d; ?>
<?php unset($__attributesOriginal65da8be3abf0702790c6a63da305415d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65da8be3abf0702790c6a63da305415d)): ?>
<?php $component = $__componentOriginal65da8be3abf0702790c6a63da305415d; ?>
<?php unset($__componentOriginal65da8be3abf0702790c6a63da305415d); ?>
<?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views//templates/form/builder.blade.php ENDPATH**/ ?>
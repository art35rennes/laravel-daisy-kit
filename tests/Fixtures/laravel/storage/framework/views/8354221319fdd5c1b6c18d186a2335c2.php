
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'schema' => null,
    'fieldTypes' => null,
    'functionCatalog' => null,
    'preview' => true,
    'jsonEditor' => true,
    'name' => null,
    'value' => [],
    'errors' => [],
    'viewerSubmitMode' => null,
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
    'schema' => null,
    'fieldTypes' => null,
    'functionCatalog' => null,
    'preview' => true,
    'jsonEditor' => true,
    'name' => null,
    'value' => [],
    'errors' => [],
    'viewerSubmitMode' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $schema = is_string($schema) ? (json_decode($schema, true) ?: []) : ($schema ?? []);
    $fieldTypes = is_string($fieldTypes) ? (json_decode($fieldTypes, true) ?: []) : ($fieldTypes ?? []);
    $functionCatalog = $functionCatalog ?? config('daisy-kit.forms.jsonata.function_catalog', []);
    $functionCatalog = is_string($functionCatalog) ? (json_decode($functionCatalog, true) ?: []) : (array) $functionCatalog;
    $value = is_string($value) ? (json_decode($value, true) ?: []) : (array) $value;
    $errors = is_string($errors) ? (json_decode($errors, true) ?: []) : (array) $errors;
?>

<div <?php echo e($attributes->merge(['class' => 'daisy-form-builder-shell'])); ?>>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('daisy.form-builder', [
        'schema' => $schema,
        'fieldTypes' => $fieldTypes,
        'functionCatalog' => $functionCatalog ?? config('daisy-kit.forms.jsonata.function_catalog', []),
        'preview' => (bool) $preview,
        'jsonEditor' => (bool) $jsonEditor,
        'name' => $name,
        'value' => $value,
        'errors' => $errors,
        'viewerSubmitMode' => $viewerSubmitMode,
    ]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1597392324-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/forms/builder.blade.php ENDPATH**/ ?>
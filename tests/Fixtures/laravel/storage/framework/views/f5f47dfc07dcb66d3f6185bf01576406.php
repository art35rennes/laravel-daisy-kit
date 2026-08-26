<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'showSearch' => true,
    'showFilters' => true,
    'searchPlaceholder' => __('daisy::changelog.search_placeholder'),
    'filterName' => 'changelog-filter',
    'filterItems' => [], // ['added', 'changed', 'fixed', 'removed', 'security']
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
    'showSearch' => true,
    'showFilters' => true,
    'searchPlaceholder' => __('daisy::changelog.search_placeholder'),
    'filterName' => 'changelog-filter',
    'filterItems' => [], // ['added', 'changed', 'fixed', 'removed', 'security']
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Si filterItems est vide, utiliser les types par défaut
    if (empty($filterItems)) {
        $filterItems = ['added', 'changed', 'fixed', 'removed', 'security'];
    }

    // Formater les items pour le composant filter
    $formattedFilterItems = array_map(function($type) {
        return [
            'label' => __('daisy::changelog.'.$type),
            'checked' => false,
        ];
    }, $filterItems);

    // Ajouter "Tous les types" en premier
    array_unshift($formattedFilterItems, [
        'label' => __('daisy::changelog.all_types'),
        'checked' => true,
    ]);
?>

<div class="changelog-toolbar flex flex-col gap-4 rounded-box card-border bg-base-100 p-6 shadow">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSearch): ?>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-base-content/40">
                <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => 'search','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
            </span>
            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['type' => 'text','placeholder' => $searchPlaceholder,'class' => 'w-full pl-10','dataChangelogSearch' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($searchPlaceholder),'class' => 'w-full pl-10','data-changelog-search' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showFilters): ?>
        <div class="flex flex-wrap gap-2">
            <?php if (isset($component)) { $__componentOriginal33c62a204d8d34ecbe1758bb3892fe2d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal33c62a204d8d34ecbe1758bb3892fe2d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.filter','data' => ['name' => $filterName,'items' => $formattedFilterItems,'useForm' => false,'resetLabel' => __('daisy::changelog.all_types'),'class' => 'filter rounded-full bg-base-200/60 p-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.filter'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filterName),'items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($formattedFilterItems),'useForm' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'resetLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::changelog.all_types')),'class' => 'filter rounded-full bg-base-200/60 p-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal33c62a204d8d34ecbe1758bb3892fe2d)): ?>
<?php $attributes = $__attributesOriginal33c62a204d8d34ecbe1758bb3892fe2d; ?>
<?php unset($__attributesOriginal33c62a204d8d34ecbe1758bb3892fe2d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal33c62a204d8d34ecbe1758bb3892fe2d)): ?>
<?php $component = $__componentOriginal33c62a204d8d34ecbe1758bb3892fe2d; ?>
<?php unset($__componentOriginal33c62a204d8d34ecbe1758bb3892fe2d); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/changelog/changelog-toolbar.blade.php ENDPATH**/ ?>
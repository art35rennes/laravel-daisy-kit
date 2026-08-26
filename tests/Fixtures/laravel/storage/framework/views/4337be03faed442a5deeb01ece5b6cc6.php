<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Variant: secondary|success|warning|danger (map vers DaisyUI info/success/warning/error)
    'variant' => 'secondary',
    // Couleur Tailwind optionnelle (ex: blue, emerald...) → on délègue au class utilisateur
    'color' => null,
    // Icône (SVG Blade Icons recommandé)
    'icon' => null,
    // Heading/text raccourcis
    'heading' => null,
    'text' => null,
    // Actions inline
    'inline' => false,
    // Déplacer l'icône dans le heading
    'iconInHeading' => false,
    // Responsive: vertical par défaut, horizontal à partir du breakpoint donné
    'horizontalAt' => 'sm',
    'dismissible' => false,
    'closeLabel' => 'Close alert',
    'autoDismiss' => false,
    'autoDismissAfter' => null,
    'autoDismissMs' => null,
    'showDismissProgress' => true,
    'showDismissRemaining' => false,
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
    // Variant: secondary|success|warning|danger (map vers DaisyUI info/success/warning/error)
    'variant' => 'secondary',
    // Couleur Tailwind optionnelle (ex: blue, emerald...) → on délègue au class utilisateur
    'color' => null,
    // Icône (SVG Blade Icons recommandé)
    'icon' => null,
    // Heading/text raccourcis
    'heading' => null,
    'text' => null,
    // Actions inline
    'inline' => false,
    // Déplacer l'icône dans le heading
    'iconInHeading' => false,
    // Responsive: vertical par défaut, horizontal à partir du breakpoint donné
    'horizontalAt' => 'sm',
    'dismissible' => false,
    'closeLabel' => 'Close alert',
    'autoDismiss' => false,
    'autoDismissAfter' => null,
    'autoDismissMs' => null,
    'showDismissProgress' => true,
    'showDismissRemaining' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Mapping variant→color DaisyUI
    $variantToColor = [
        'secondary' => 'info',
        'success' => 'success',
        'warning' => 'warning',
        'danger' => 'error',
    ];
    $alertColor = $variantToColor[$variant] ?? 'info';
    $classes = trim('callout '.($color ? 'callout-'.$color : ''));
?>

<?php if (isset($component)) { $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.alert','data' => ['color' => $alertColor,'inline' => $inline,'icon' => $icon,'iconInHeading' => $iconInHeading,'heading' => $heading,'text' => $text,'horizontalAt' => $horizontalAt,'dismissible' => $dismissible,'closeLabel' => $closeLabel,'autoDismiss' => $autoDismiss,'autoDismissAfter' => $autoDismissAfter,'autoDismissMs' => $autoDismissMs,'showDismissProgress' => $showDismissProgress,'showDismissRemaining' => $showDismissRemaining,'attributes' => $attributes->merge(['class' => $classes])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($alertColor),'inline' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($inline),'icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'iconInHeading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($iconInHeading),'heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($heading),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($text),'horizontalAt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($horizontalAt),'dismissible' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dismissible),'closeLabel' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($closeLabel),'autoDismiss' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoDismiss),'autoDismissAfter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoDismissAfter),'autoDismissMs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoDismissMs),'showDismissProgress' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDismissProgress),'showDismissRemaining' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showDismissRemaining),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attributes->merge(['class' => $classes]))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($text === null): ?>
        <?php echo e($slot); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
         <?php $__env->slot('actions', null, []); ?> <?php echo e($actions); ?> <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($controls)): ?>
         <?php $__env->slot('controls', null, []); ?> <?php echo e($controls); ?> <?php $__env->endSlot(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $attributes = $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $component = $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/feedback/callout.blade.php ENDPATH**/ ?>
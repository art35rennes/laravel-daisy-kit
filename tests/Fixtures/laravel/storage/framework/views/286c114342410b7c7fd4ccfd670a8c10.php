<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'text' => null,
    'maxWidth' => 'max-w-full',
    'tag' => 'span',
    'tooltip' => true,
    'reveal' => null,
    'position' => null,
    'tooltipPosition' => 'top',
    'popoverPosition' => null,
    'onlyWhenTruncated' => null,
    'tooltipOnlyWhenTruncated' => true,
    'actionHint' => false,
    'lines' => 1,
    'title' => null,
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
    'text' => null,
    'maxWidth' => 'max-w-full',
    'tag' => 'span',
    'tooltip' => true,
    'reveal' => null,
    'position' => null,
    'tooltipPosition' => 'top',
    'popoverPosition' => null,
    'onlyWhenTruncated' => null,
    'tooltipOnlyWhenTruncated' => true,
    'actionHint' => false,
    'lines' => 1,
    'title' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $content = (string) $text;
    $tooltipText = (string) ($title ?? $content);
    $validRevealModes = ['tooltip', 'popover', 'both', 'none'];
    $legacyRevealMode = filter_var($tooltip, FILTER_VALIDATE_BOOLEAN) ? 'tooltip' : 'none';
    $revealMode = in_array($reveal, $validRevealModes, true) ? $reveal : $legacyRevealMode;
    $hasTooltip = in_array($revealMode, ['tooltip', 'both'], true);
    $hasPopover = in_array($revealMode, ['popover', 'both'], true);
    $overflowOnly = is_null($onlyWhenTruncated)
        ? filter_var($tooltipOnlyWhenTruncated, FILTER_VALIDATE_BOOLEAN)
        : filter_var($onlyWhenTruncated, FILTER_VALIDATE_BOOLEAN);
    $lineCount = max(1, (int) $lines);
    $validTags = ['span', 'p', 'div', 'strong', 'em', 'small', 'code', 'time'];
    $elementTag = in_array($tag, $validTags, true) ? $tag : 'span';
    $validPositions = ['top', 'right', 'bottom', 'left'];
    $resolvedPosition = $position ?? $tooltipPosition;
    $tooltipPlacement = in_array($resolvedPosition, $validPositions, true) ? $resolvedPosition : 'top';
    $resolvedPopoverPosition = $popoverPosition ?? $tooltipPlacement;
    $popoverPlacement = in_array($resolvedPopoverPosition, $validPositions, true) ? $resolvedPopoverPosition : 'top';
    $truncateClass = $lineCount === 1 ? 'truncate' : "line-clamp-{$lineCount}";
    $customClasses = $attributes->get('class');
    $actionHintClasses = filter_var($actionHint, FILTER_VALIDATE_BOOLEAN)
        ? 'cursor-pointer decoration-dotted underline underline-offset-2 decoration-base-content/40 hover:decoration-base-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary'
        : '';
    $contentClass = implode(' ', array_filter(['min-w-0', $maxWidth, $truncateClass, $actionHintClasses, $customClasses ?? null]));
    $contentAttributes = $attributes->except('class')->merge([
        'class' => $contentClass,
        'aria-label' => $content,
    ]);
    $usesMeasuredTooltip = $hasTooltip && $overflowOnly;
    $usesStaticTooltip = $hasTooltip && ! $usesMeasuredTooltip;
    $usesTruncateModule = $usesMeasuredTooltip || $hasPopover;
    $popoverClasses = [
        'top' => 'bottom-full left-1/2 mb-2 -translate-x-1/2',
        'right' => 'left-full top-1/2 ml-2 -translate-y-1/2',
        'bottom' => 'top-full left-1/2 mt-2 -translate-x-1/2',
        'left' => 'right-full top-1/2 mr-2 -translate-y-1/2',
    ][$popoverPlacement];
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usesTruncateModule): ?>
    <span
        <?php if($hasTooltip): ?> data-tip="<?php echo e($tooltipText); ?>" <?php endif; ?>
        class="<?php echo e(trim('relative inline-block max-w-full align-middle '.($hasTooltip ? "tooltip tooltip-{$tooltipPlacement} before:!delay-0 after:!delay-0 before:!duration-75 after:!duration-75" : ''))); ?>"
    >
        <<?php echo e($elementTag); ?>

            <?php echo e($contentAttributes->merge([
                'data-module' => 'truncate-text',
                'data-truncate-text-title' => $tooltipText,
                'data-truncate-text-position' => $tooltipPlacement,
                'data-truncate-text-reveal' => $revealMode,
                'data-truncate-text-only-when-truncated' => $overflowOnly ? 'true' : 'false',
            ])); ?>

        ><?php echo e($content); ?></<?php echo e($elementTag); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasPopover): ?>
            <span
                class="daisy-truncate-popover pointer-events-auto absolute z-50 hidden <?php echo e($popoverClasses); ?>"
                role="dialog"
                aria-hidden="true"
            >
                <span class="block max-w-xs select-text whitespace-normal break-words rounded-box bg-base-100 px-3 py-2 text-sm leading-relaxed text-base-content shadow-lg ring-1 ring-base-content/10">
                    <?php echo e($tooltipText); ?>

                </span>
            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
    <?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php elseif($usesStaticTooltip): ?>
    <?php if (isset($component)) { $__componentOriginal483ee062796518568c43f5ca7224edd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal483ee062796518568c43f5ca7224edd9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.overlay.tooltip','data' => ['text' => $tooltipText,'position' => $tooltipPlacement]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.overlay.tooltip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tooltipText),'position' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tooltipPlacement)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <<?php echo e($elementTag); ?> <?php echo e($contentAttributes->merge(['tabindex' => '0'])); ?>><?php echo e($content); ?></<?php echo e($elementTag); ?>>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal483ee062796518568c43f5ca7224edd9)): ?>
<?php $attributes = $__attributesOriginal483ee062796518568c43f5ca7224edd9; ?>
<?php unset($__attributesOriginal483ee062796518568c43f5ca7224edd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal483ee062796518568c43f5ca7224edd9)): ?>
<?php $component = $__componentOriginal483ee062796518568c43f5ca7224edd9; ?>
<?php unset($__componentOriginal483ee062796518568c43f5ca7224edd9); ?>
<?php endif; ?>
<?php else: ?>
    <<?php echo e($elementTag); ?> <?php echo e($contentAttributes->merge(['title' => $tooltipText])); ?>><?php echo e($content); ?></<?php echo e($elementTag); ?>>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/utilities/truncate-text.blade.php ENDPATH**/ ?>
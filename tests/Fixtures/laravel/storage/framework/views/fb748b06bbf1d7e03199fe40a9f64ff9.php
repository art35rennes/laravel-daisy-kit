<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'variant' => 'solid', // solid|soft|outline|dash
    'icon' => null,
    'title' => null,
    // API "callout" friendly
    'heading' => null,     // alias de title
    'text' => null,        // contenu texte (alias du slot)
    'inline' => false,     // actions en ligne
    'iconInHeading' => false, // placer l'icône dans le heading
    // Orientation
    'vertical' => null,       // bool|null
    'horizontal' => null,     // bool|null
    'horizontalAt' => null,   // ex: 'sm' → alert-vertical sm:alert-horizontal
    'role' => null,
    'sessionKey' => null,
    'showErrors' => false,
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
    'color' => 'neutral', // neutral|primary|secondary|accent|info|success|warning|error
    'variant' => 'solid', // solid|soft|outline|dash
    'icon' => null,
    'title' => null,
    // API "callout" friendly
    'heading' => null,     // alias de title
    'text' => null,        // contenu texte (alias du slot)
    'inline' => false,     // actions en ligne
    'iconInHeading' => false, // placer l'icône dans le heading
    // Orientation
    'vertical' => null,       // bool|null
    'horizontal' => null,     // bool|null
    'horizontalAt' => null,   // ex: 'sm' → alert-vertical sm:alert-horizontal
    'role' => null,
    'sessionKey' => null,
    'showErrors' => false,
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
    $classes = 'alert';
    // Color (supporte alias danger→error pour compat callout)
    $colorKey = $color === 'danger' ? 'error' : $color;
    $classes .= ' alert-'.$colorKey;
    // Variant
    $variantMap = [
        'soft' => 'alert-soft',
        'outline' => 'alert-outline',
        'dash' => 'alert-dash',
    ];
    if (isset($variantMap[$variant])) {
        $classes .= ' '.$variantMap[$variant];
    }

    // Orientation
    if ($horizontalAt) {
        $classes .= ' alert-vertical '.($horizontalAt).':alert-horizontal';
    } elseif (! is_null($vertical) || ! is_null($horizontal)) {
        if ($vertical) {
            $classes .= ' alert-vertical';
        }
        if ($horizontal) {
            $classes .= ' alert-horizontal';
        }
    }

    // Inline actions → aligner verticalement au centre
    if ($inline) {
        $classes .= ' items-center';
    }

    $resolvedRole = $role ?: (in_array($colorKey, ['error', 'warning'], true) ? 'alert' : 'status');

    $headingText = $heading ?? $title;
    $sessionMessage = $sessionKey ? session($sessionKey) : null;
    $laravelErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $errorMessages = $showErrors && method_exists($laravelErrors, 'any') && $laravelErrors->any()
        ? $laravelErrors->all()
        : [];
    $bodyText = filled($text) ? $text : (filled($sessionMessage) ? $sessionMessage : null);
    $progressClass = [
        'neutral' => 'progress-neutral',
        'primary' => 'progress-primary',
        'secondary' => 'progress-secondary',
        'accent' => 'progress-accent',
        'info' => 'progress-info',
        'success' => 'progress-success',
        'warning' => 'progress-warning',
        'error' => 'progress-error',
    ][$colorKey] ?? 'progress-neutral';

    $hasSlotContent = isset($slot)
        && (method_exists($slot, 'isEmpty') ? ! $slot->isEmpty() : filled(trim((string) $slot)));

    $hasContent = filled($headingText)
        || filled($bodyText)
        || $errorMessages !== []
        || $hasSlotContent;

    $autoDismissDelay = null;
    if (is_numeric($autoDismissMs) && (int) $autoDismissMs > 0) {
        $autoDismissDelay = (int) $autoDismissMs;
    } elseif (is_numeric($autoDismissAfter) && (float) $autoDismissAfter > 0) {
        $autoDismissDelay = (int) round((float) $autoDismissAfter * 1000);
    } elseif (is_numeric($autoDismiss) && (float) $autoDismiss > 0) {
        $autoDismissDelay = (int) round((float) $autoDismiss * 1000);
    } elseif ($autoDismiss === true) {
        $autoDismissDelay = 5000;
    }

    $isAutoDismissible = ! is_null($autoDismissDelay);
    if ($isAutoDismissible) {
        $classes .= ' relative overflow-hidden';
    }

    // Gestion de l'icône pour éviter l'erreur BladeUI\Icons\Svg
    $iconHtml = null;
    if ($icon) {
        if (is_string($icon)) {
            $iconHtml = $icon;
        } elseif (is_object($icon) && method_exists($icon, 'toHtml')) {
            $iconHtml = $icon->toHtml();
        } elseif (is_object($icon) && method_exists($icon, '__toString')) {
            $iconHtml = (string) $icon;
        } else {
            $iconHtml = '';
        }
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasContent): ?>
    <div
        <?php echo e($attributes->merge(['role' => $resolvedRole, 'class' => $classes])); ?>

        <?php if($dismissible || $isAutoDismissible): ?> data-module="alert-dismiss" <?php endif; ?>
        <?php if($isAutoDismissible): ?> data-alert-auto-dismiss="<?php echo e($autoDismissDelay); ?>" <?php endif; ?>
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconHtml && ! $iconInHeading): ?>
            <span class="shrink-0"><?php echo $iconHtml; ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="flex-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($headingText): ?>
                <h3 class="font-medium flex items-center gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconHtml && $iconInHeading): ?>
                        <span class="shrink-0"><?php echo $iconHtml; ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span><?php echo e($headingText); ?></span>
                </h3>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="text-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errorMessages !== []): ?>
                    <ul class="list-disc list-inside">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $errorMessage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><?php echo e($errorMessage); ?></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                <?php elseif($bodyText !== null): ?>
                    <?php echo e($bodyText); ?>

                <?php else: ?>
                    <?php echo e($slot); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
            <div class="flex items-center gap-2 flex-wrap justify-start sm:justify-end"><?php echo e($actions); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($controls)): ?>
            <div class="ms-2"><?php echo e($controls); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dismissible): ?>
            <button
                type="button"
                class="btn btn-ghost btn-xs btn-square ms-2"
                aria-label="<?php echo e($closeLabel); ?>"
                data-alert-dismiss
            >
                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => 'bi-x'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
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
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAutoDismissible && $showDismissRemaining): ?>
            <span class="text-xs tabular-nums opacity-70" aria-live="polite" data-alert-remaining>
                <?php echo e((int) ceil($autoDismissDelay / 1000)); ?>s
            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAutoDismissible && $showDismissProgress): ?>
            <progress
                class="progress <?php echo e($progressClass); ?> absolute inset-x-0 bottom-0 h-1 w-full rounded-none"
                max="100"
                value="100"
                aria-hidden="true"
                data-alert-progress
            ></progress>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/feedback/alert.blade.php ENDPATH**/ ?>
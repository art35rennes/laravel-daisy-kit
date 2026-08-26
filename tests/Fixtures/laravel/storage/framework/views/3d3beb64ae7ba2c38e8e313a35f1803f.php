<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [],
    'size' => 'sm',
    'as' => 'nav',
    'label' => __('daisy::components.breadcrumbs'),
    'truncate' => false,
    'ellipsisLabel' => '...',
    'schema' => false,
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
    'items' => [],
    'size' => 'sm',
    'as' => 'nav',
    'label' => __('daisy::components.breadcrumbs'),
    'truncate' => false,
    'ellipsisLabel' => '...',
    'schema' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizeMap = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];

    $wrapperClasses = 'breadcrumbs '.($sizeMap[$size] ?? 'text-sm');
    $tag = in_array($as, ['div', 'nav'], true) ? $as : 'nav';
    $wrapperAttributes = $attributes->merge(['class' => $wrapperClasses]);

    if ($tag === 'nav') {
        $wrapperAttributes = $wrapperAttributes->merge(['aria-label' => $label]);
    }

    $items = collect($items)->values();
    $slotContent = isset($slot) ? trim((string) $slot) : '';
    $usesSlot = $slotContent !== '';
    $shouldTruncate = filter_var($truncate, FILTER_VALIDATE_BOOLEAN) && $items->count() > 2;

    $renderIcon = static function (mixed $icon): ?string {
        if ($icon instanceof \Illuminate\Contracts\Support\Htmlable) {
            return $icon->toHtml();
        }

        if (is_string($icon) && $icon !== '') {
            return e($icon);
        }

        return null;
    };

    $renderTrustedIconHtml = static function (mixed $icon): ?string {
        if ($icon instanceof \Illuminate\Contracts\Support\Htmlable) {
            return $icon->toHtml();
        }

        return is_string($icon) && $icon !== '' ? $icon : null;
    };

    $breadcrumbSchema = null;

    if (! $usesSlot && filter_var($schema, FILTER_VALIDATE_BOOLEAN)) {
        $schemaItems = $items
            ->reject(fn (mixed $item): bool => (bool) data_get($item, 'separator', false))
            ->values()
            ->map(function (mixed $item, int $index): array {
                $entry = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => (string) data_get($item, 'label', ''),
                ];

                $href = data_get($item, 'href');
                $href = is_string($href) || $href instanceof \Stringable ? trim((string) $href) : '';

                if ($href !== '') {
                    $entry['item'] = url($href);
                }

                return $entry;
            })
            ->all();

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $schemaItems,
        ];
    }
?>


<<?php echo e($tag); ?> <?php echo e($wrapperAttributes); ?>>
    <ul>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usesSlot): ?>
            <?php echo e($slot); ?>

        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $labelText = (string) data_get($item, 'label', '');
                    $href = data_get($item, 'href');
                    $href = is_string($href) || $href instanceof \Stringable ? (string) $href : null;
                    $hasHref = is_string($href) && trim($href) !== '';
                    $isDisabled = (bool) data_get($item, 'disabled', false);
                    $isSeparator = (bool) data_get($item, 'separator', false);
                    $isCurrent = (bool) data_get($item, 'current', false) || (! $hasHref && ! $isDisabled && ! $isSeparator && $index === $items->count() - 1);
                    $iconName = data_get($item, 'iconName');
                    $iconName = is_string($iconName) || $iconName instanceof \Stringable ? (string) $iconName : null;
                    $iconHtml = data_get($item, 'iconHtml');
                    $icon = $iconHtml !== null ? $renderTrustedIconHtml($iconHtml) : $renderIcon(data_get($item, 'icon'));
                    $hasVisualIcon = $iconName || $icon;
                    $itemClasses = $hasVisualIcon ? 'inline-flex items-center gap-2' : '';
                    $spanClasses = trim($itemClasses.' '.($isCurrent ? 'font-medium' : '').' '.($isDisabled ? 'opacity-60 cursor-not-allowed' : ''));
                    $shouldCollapseMiddle = $shouldTruncate && $index > 0 && $index < $items->count() - 1;
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shouldTruncate && $index === 1): ?>
                    <li class="sm:hidden" aria-hidden="true"><span><?php echo e($ellipsisLabel); ?></span></li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <li <?php if($shouldCollapseMiddle): ?> class="hidden sm:list-item" <?php endif; ?> <?php if($isSeparator): ?> aria-hidden="true" <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasHref && ! $isCurrent && ! $isDisabled && ! $isSeparator): ?>
                        <a href="<?php echo e($href); ?>" <?php if($itemClasses !== ''): ?> class="<?php echo e($itemClasses); ?>" <?php endif; ?>>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconName): ?>
                                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $iconName] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 shrink-0']); ?>
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
                            <?php elseif($icon): ?>
                                <?php echo $icon; ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><?php echo e($labelText); ?></span>
                        </a>
                    <?php else: ?>
                        <span <?php if($spanClasses !== ''): ?> class="<?php echo e($spanClasses); ?>" <?php endif; ?> <?php if($isCurrent): ?> aria-current="page" <?php endif; ?> <?php if($isDisabled): ?> aria-disabled="true" <?php endif; ?>>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconName): ?>
                                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $iconName] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 shrink-0']); ?>
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
                            <?php elseif($icon): ?>
                                <?php echo $icon; ?>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span><?php echo e($labelText); ?></span>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
 </<?php echo e($tag); ?>>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($breadcrumbSchema): ?>
    <script type="application/ld+json"<?php echo \Art35rennes\DaisyKit\Support\PackageAsset::nonceAttribute(); ?>><?php echo json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/breadcrumbs.blade.php ENDPATH**/ ?>
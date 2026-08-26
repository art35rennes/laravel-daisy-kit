<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => [], // [ { when,title,content, iconName?, iconHtml?, icon?, boxOn: 'start'|'end'|null, hrBefore?:bool, hrAfter?, startHtml?, endHtml? } ]
    'orientation' => 'vertical', // vertical|horizontal (daisyUI: horizontal par défaut)
    'responsiveAt' => 'lg', // used when orientation is responsive
    'compact' => false,
    'snapIcon' => false, // timeline-snap-icon (icône alignée sur start)
    'showIcons' => true,
    'side' => 'both', // both|start|end|alternate
    'lineClass' => null,
    'iconClass' => 'h-5 w-5',
    // Valeur par défaut pour appliquer timeline-box sur un côté (item.boxOn a priorité)
    'boxOn' => 'end', // start|end|null
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
    'items' => [], // [ { when,title,content, iconName?, iconHtml?, icon?, boxOn: 'start'|'end'|null, hrBefore?:bool, hrAfter?, startHtml?, endHtml? } ]
    'orientation' => 'vertical', // vertical|horizontal (daisyUI: horizontal par défaut)
    'responsiveAt' => 'lg', // used when orientation is responsive
    'compact' => false,
    'snapIcon' => false, // timeline-snap-icon (icône alignée sur start)
    'showIcons' => true,
    'side' => 'both', // both|start|end|alternate
    'lineClass' => null,
    'iconClass' => 'h-5 w-5',
    // Valeur par défaut pour appliquer timeline-box sur un côté (item.boxOn a priorité)
    'boxOn' => 'end', // start|end|null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Construction des classes CSS selon l'orientation et les options (compact, snapIcon).
    $classes = 'timeline';
    $classes .= match ($orientation) {
        'horizontal' => ' timeline-horizontal',
        'responsive' => ' timeline-vertical '.$responsiveAt.':timeline-horizontal',
        default => ' timeline-vertical',
    };
    if ($compact) $classes .= ' timeline-compact';
    if ($snapIcon) $classes .= ' timeline-snap-icon';

    $itemValue = static function (array $item, array $keys, mixed $default = null): mixed {
        foreach ($keys as $key) {
            if (array_key_exists($key, $item)) {
                return $item[$key];
            }
        }

        return $default;
    };

    $hasVisibleValue = static fn (mixed $value): bool => $value !== null && $value !== '';
?>

<ul <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <li>
            <?php
                // Détermination de l'application de timeline-box : priorité à item.boxOn, sinon boxOn global.
                $applyBox = $item['boxOn'] ?? $boxOn;
                $itemSide = $item['side'] ?? $side;
                if ($itemSide === 'alternate') {
                    $itemSide = $index % 2 === 0 ? 'start' : 'end';
                }

                $rendersStart = $itemSide !== 'end';
                $rendersEnd = $itemSide !== 'start';
                $rendersIcon = array_key_exists('showIcon', $item) ? (bool) $item['showIcon'] : (bool) $showIcons;

                $startClasses = 'timeline-start'.($applyBox === 'start' ? ' timeline-box' : '');
                $endClasses = 'timeline-end'.($applyBox === 'end' ? ' timeline-box' : '');
                // Détection du dernier item pour la gestion des séparateurs.
                $isLast = $index === (count($items) - 1);
                // Logique des séparateurs : hrBefore explicite OU automatique si index > 0.
                $hrBefore = array_key_exists('hrBefore', $item) ? (bool)$item['hrBefore'] : ($index > 0);
                // hrAfter explicite OU automatique si ce n'est pas le dernier item.
                $hrAfter = array_key_exists('hrAfter', $item) ? (bool)$item['hrAfter'] : (!$isLast);
                $beforeClass = $item['hrBeforeClass'] ?? $item['lineClass'] ?? $lineClass;
                $afterClass = $item['hrAfterClass'] ?? $item['lineClass'] ?? $lineClass;
                $itemIconClass = trim($item['iconClass'] ?? $iconClass);

                $startHtml = $item['startHtml'] ?? null;
                $endHtml = $item['endHtml'] ?? null;
                $startText = $itemValue($item, ['start', 'when']);
                $endText = $itemValue($item, ['end', 'title', 'content']);

                if ($itemSide === 'start' && ! $hasVisibleValue($startHtml) && ! $hasVisibleValue($startText)) {
                    $startHtml = $endHtml;
                    $startText = $endText;
                }

                if ($itemSide === 'end' && ! $hasVisibleValue($endHtml) && ! $hasVisibleValue($endText)) {
                    $endHtml = $startHtml;
                    $endText = $startText;
                }
            ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hrBefore): ?>
                <hr <?php if($beforeClass): ?> class="<?php echo e($beforeClass); ?>" <?php endif; ?> />
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rendersStart): ?>
                <div class="<?php echo e($startClasses); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVisibleValue($startHtml)): ?>
                        <?php echo $startHtml; ?>

                    <?php else: ?>
                        <?php echo e($startText ?? ''); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rendersIcon): ?>
                <div class="timeline-middle">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['iconName'])): ?>
                        <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $item['iconName']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($itemIconClass)]); ?>
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
                    <?php elseif(!empty($item['iconHtml'])): ?>
                        <?php echo $item['iconHtml']; ?>

                    <?php elseif(!empty($item['icon']) && $item['icon'] instanceof \Illuminate\Contracts\Support\Htmlable): ?>
                        <?php echo $item['icon']->toHtml(); ?>

                    <?php elseif(!empty($item['icon'])): ?>
                        <?php echo e($item['icon']); ?>

                    <?php else: ?>
                        
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="<?php echo e($itemIconClass); ?>">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rendersEnd): ?>
                <div class="<?php echo e($endClasses); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasVisibleValue($endHtml)): ?>
                        <?php echo $endHtml; ?>

                    <?php elseif($itemSide === 'both'): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['title'])): ?>
                            <div class="text-lg font-black"><?php echo e($item['title']); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($item['content'])): ?>
                            <div><?php echo e($item['content']); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <?php echo e($endText ?? ''); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hrAfter): ?>
                <hr <?php if($afterClass): ?> class="<?php echo e($afterClass); ?>" <?php endif; ?> />
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </li>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</ul>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/data-display/timeline.blade.php ENDPATH**/ ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'total' => 1,
    'current' => 1,
    'size' => null, // xs|sm|md|lg|xl
    'edges' => true,
    'maxButtons' => 7,
    'prevLabel' => '«',
    'nextLabel' => '»',
    'equalPrevNext' => false,
    'outlinePrevNext' => false,
    'responsive' => true,
    'mobileLabel' => null,
    'color' => null, // primary|secondary|accent|neutral|info|success|warning|error
    'outline' => false,
    'paginator' => null,
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
    'total' => 1,
    'current' => 1,
    'size' => null, // xs|sm|md|lg|xl
    'edges' => true,
    'maxButtons' => 7,
    'prevLabel' => '«',
    'nextLabel' => '»',
    'equalPrevNext' => false,
    'outlinePrevNext' => false,
    'responsive' => true,
    'mobileLabel' => null,
    'color' => null, // primary|secondary|accent|neutral|info|success|warning|error
    'outline' => false,
    'paginator' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator) {
        $current = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : $current;
        $total = method_exists($paginator, 'lastPage') ? $paginator->lastPage() : $total;
    }

    $total = max(1, (int) $total);
    $current = max(1, min((int) $current, $total));

    $sizeSuffix = in_array($size, ['xs', 'sm', 'md', 'lg', 'xl'], true) ? ' btn-'.$size : '';
    $colorSuffix = in_array($color, ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'], true) ? ' btn-'.$color : '';
    $outlineSuffix = $outline ? ' btn-outline' : '';

    // Shared segment classes for join items (DaisyUI: join + btn + join-item on each control).
    $segmentClass = 'btn join-item'.$sizeSuffix.$colorSuffix.$outlineSuffix;

    $resolvedMobileLabel = $mobileLabel ?: __('daisy::components.pagination_page', ['current' => $current, 'total' => $total]);
    $mobileInfo = str_replace([':current', ':total'], [$current, $total], (string) $resolvedMobileLabel);
    $prevUrl = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($paginator, 'previousPageUrl') ? $paginator->previousPageUrl() : null;
    $nextUrl = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($paginator, 'nextPageUrl') ? $paginator->nextPageUrl() : null;
    $previousAriaLabel = __('daisy::common.previous');
    $nextAriaLabel = __('daisy::common.next');

    $maxButtons = max(3, (int) $maxButtons);
    $pages = [];
    if ($total <= $maxButtons) {
        for ($i = 1; $i <= $total; $i++) {
            $pages[] = $i;
        }
    } else {
        $pages[] = 1;
        $window = $maxButtons - 2;
        $half = (int) floor($window / 2);
        $start = max(2, $current - $half);
        $end = min($total - 1, $start + $window - 1);
        $start = max(2, $end - $window + 1);
        if ($start > 2) {
            $pages[] = null;
        }
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        if ($end < $total - 1) {
            $pages[] = null;
        }
        $pages[] = $total;
    }
?>

<nav aria-label="<?php echo e(__('daisy::components.pagination')); ?>" <?php echo e($attributes->class('inline-block w-full max-w-full')); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($equalPrevNext): ?>
        <div class="join join-horizontal w-full">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prevUrl): ?>
                <a
                    href="<?php echo e($prevUrl); ?>"
                    class="btn join-item flex-1<?php echo e($sizeSuffix); ?><?php echo e($colorSuffix); ?><?php echo e($outlinePrevNext ? ' btn-outline' : ''); ?>"
                    aria-label="<?php echo e($previousAriaLabel); ?>"
                ><?php echo e($prevLabel); ?></a>
            <?php else: ?>
                <button
                    type="button"
                    class="btn join-item flex-1<?php echo e($sizeSuffix); ?><?php echo e($colorSuffix); ?><?php echo e($outlinePrevNext ? ' btn-outline' : ''); ?>"
                    <?php if($current === 1): echo 'disabled'; endif; ?>
                ><?php echo e($prevLabel); ?></button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nextUrl): ?>
                <a
                    href="<?php echo e($nextUrl); ?>"
                    class="btn join-item flex-1<?php echo e($sizeSuffix); ?><?php echo e($colorSuffix); ?><?php echo e($outlinePrevNext ? ' btn-outline' : ''); ?>"
                    aria-label="<?php echo e($nextAriaLabel); ?>"
                ><?php echo e($nextLabel); ?></a>
            <?php else: ?>
                <button
                    type="button"
                    class="btn join-item flex-1<?php echo e($sizeSuffix); ?><?php echo e($colorSuffix); ?><?php echo e($outlinePrevNext ? ' btn-outline' : ''); ?>"
                    <?php if($current === $total): echo 'disabled'; endif; ?>
                ><?php echo e($nextLabel); ?></button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto max-w-full">
            <div class="join join-horizontal min-w-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($edges): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prevUrl): ?>
                        <a
                            href="<?php echo e($prevUrl); ?>"
                            class="<?php echo e($segmentClass); ?>"
                            aria-label="<?php echo e($previousAriaLabel); ?>"
                        ><?php echo e($prevLabel); ?></a>
                    <?php else: ?>
                        <button
                            type="button"
                            class="<?php echo e($segmentClass); ?>"
                            <?php if($current === 1): echo 'disabled'; endif; ?>
                            aria-label="<?php echo e($previousAriaLabel); ?>"
                        ><?php echo e($prevLabel); ?></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($responsive): ?>
                    <span class="btn join-item<?php echo e($sizeSuffix); ?> btn-ghost pointer-events-none cursor-default sm:hidden" role="status"><?php echo e($mobileInfo); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $resolvedUrl = $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && method_exists($paginator, 'url') && is_int($pageNumber) ? $paginator->url($pageNumber) : null;
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pageNumber === null): ?>
                        <span
                            class="<?php echo e($segmentClass); ?> btn-disabled pointer-events-none select-none hidden sm:inline-flex"
                            aria-hidden="true"
                        >&hellip;</span>
                    <?php elseif($resolvedUrl): ?>
                        <a
                            href="<?php echo e($resolvedUrl); ?>"
                            class="<?php echo e($segmentClass); ?> <?php echo e($pageNumber === $current ? 'btn-active' : ''); ?> hidden sm:inline-flex"
                            <?php if($pageNumber === $current): ?> aria-current="page" <?php endif; ?>
                        ><?php echo e($pageNumber); ?></a>
                    <?php else: ?>
                        <button
                            type="button"
                            class="<?php echo e($segmentClass); ?> <?php echo e($pageNumber === $current ? 'btn-active' : ''); ?> hidden sm:inline-flex"
                            <?php if($pageNumber === $current): ?> aria-current="page" <?php endif; ?>
                        ><?php echo e($pageNumber); ?></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($edges): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nextUrl): ?>
                        <a
                            href="<?php echo e($nextUrl); ?>"
                            class="<?php echo e($segmentClass); ?>"
                            aria-label="<?php echo e($nextAriaLabel); ?>"
                        ><?php echo e($nextLabel); ?></a>
                    <?php else: ?>
                        <button
                            type="button"
                            class="<?php echo e($segmentClass); ?>"
                            <?php if($current === $total): echo 'disabled'; endif; ?>
                            aria-label="<?php echo e($nextAriaLabel); ?>"
                        ><?php echo e($nextLabel); ?></button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</nav>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/pagination.blade.php ENDPATH**/ ?>
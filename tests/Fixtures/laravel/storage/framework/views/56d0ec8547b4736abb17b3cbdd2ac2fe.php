<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    /**
     * items: [
     *   ['label' => 'Inputs', 'children' => [
     *      ['label' => 'Button', 'href' => '/docs/inputs/button'],
     *   ]],
     * ]
     */
    'items' => [],
    'current' => null,
    // Activer le filtrage du menu
    'searchable' => true,
    // Placeholder pour le champ de recherche
    'searchPlaceholder' => 'Rechercher...',
    // Module override
    'module' => 'menu-filter',
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
    /**
     * items: [
     *   ['label' => 'Inputs', 'children' => [
     *      ['label' => 'Button', 'href' => '/docs/inputs/button'],
     *   ]],
     * ]
     */
    'items' => [],
    'current' => null,
    // Activer le filtrage du menu
    'searchable' => true,
    // Placeholder pour le champ de recherche
    'searchPlaceholder' => 'Rechercher...',
    // Module override
    'module' => 'menu-filter',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentPath = '/'.ltrim((string) ($current ?? request()->path()), '/');

    $normalizeHref = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if ($url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : null;
    };

    $isActive = function (?string $href) use ($currentPath): bool {
        if (!$href) {
            return false;
        }

        // Normaliser les chemins (supprimer les slashes finaux)
        $normalizedHref = rtrim($href, '/');
        $normalizedCurrent = rtrim($currentPath, '/');

        // Match exact
        if ($normalizedCurrent === $normalizedHref) {
            return true;
        }

        // Match avec sous-chemin : vérifier que le href est un préfixe exact (suivi de / ou fin de chaîne)
        if (str_starts_with($normalizedCurrent, $normalizedHref)) {
            $nextChar = substr($normalizedCurrent, strlen($normalizedHref), 1);
            // Le caractère suivant doit être '/' ou la fin de la chaîne
            return $nextChar === '' || $nextChar === '/';
        }

        return false;
    };

    $hasActive = function (array $node) use (&$hasActive, $isActive): bool {
        $href = $node['href'] ?? null;
        if (is_string($href) && $isActive($href)) {
            return true;
        }

        foreach (($node['children'] ?? []) as $child) {
            if (is_array($child) && $hasActive($child)) {
                return true;
            }
        }

        return false;
    };
?>

<nav aria-label="Navigation de la documentation" class="w-full">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchable): ?>
        <div class="mb-4" data-module="<?php echo e($module); ?>">
            <input 
                type="text" 
                data-menu-filter-input
                placeholder="<?php echo e($searchPlaceholder); ?>"
                class="input input-sm w-full"
                aria-label="Rechercher dans le menu"
            />
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php
        $menuAttributes = $searchable ? ['data-menu-filter-target' => ''] : [];
    ?>
    <?php if (isset($component)) { $__componentOriginal91b7ec410a681c4e92d3f108a8f492ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.menu','data' => ['bg' => false,'rounded' => false,'size' => 'sm','class' => 'w-full','attributes' => new \Illuminate\View\ComponentAttributeBag($menuAttributes)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bg' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'rounded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'size' => 'sm','class' => 'w-full','attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(new \Illuminate\View\ComponentAttributeBag($menuAttributes))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $label = (string) ($node['label'] ?? '');
                $href = $normalizeHref($node['href'] ?? null);
                $children = is_array($node['children'] ?? null) ? $node['children'] : [];
                $nodeHasChildren = !empty($children);

                $nodeIsActive = $isActive($href);
                $nodeHasActive = $nodeHasChildren ? $hasActive($node) : $nodeIsActive;
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nodeHasChildren): ?>
                <li class="w-full">
                    <details <?php echo e($nodeHasActive ? 'open' : ''); ?> class="w-full">
                        <summary class="text-sm font-medium opacity-70 w-full cursor-pointer py-1.5 px-2"><?php echo e($label); ?></summary>
                        <?php if (isset($component)) { $__componentOriginal91b7ec410a681c4e92d3f108a8f492ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.menu','data' => ['bg' => false,'rounded' => false,'size' => 'xs','class' => 'pl-2 w-full mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bg' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'rounded' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'size' => 'xs','class' => 'pl-2 w-full mt-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $childLabel = (string) ($child['label'] ?? '');
                                    $childHref = $normalizeHref($child['href'] ?? null);
                                    $childIsActive = $isActive($childHref);
                                ?>

                                <li class="w-full">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($childHref): ?>
                                        <a href="<?php echo e($childHref); ?>" class="block w-full <?php echo e($childIsActive ? 'menu-active font-semibold' : ''); ?>"><?php echo e($childLabel); ?></a>
                                    <?php else: ?>
                                        <span class="block w-full opacity-70"><?php echo e($childLabel); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad)): ?>
<?php $attributes = $__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad; ?>
<?php unset($__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91b7ec410a681c4e92d3f108a8f492ad)): ?>
<?php $component = $__componentOriginal91b7ec410a681c4e92d3f108a8f492ad; ?>
<?php unset($__componentOriginal91b7ec410a681c4e92d3f108a8f492ad); ?>
<?php endif; ?>
                    </details>
                </li>
            <?php else: ?>
                <li class="w-full">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($href): ?>
                        <a href="<?php echo e($href); ?>" class="block w-full <?php echo e($nodeIsActive ? 'menu-active font-semibold' : ''); ?>"><?php echo e($label); ?></a>
                    <?php else: ?>
                        <span class="block w-full opacity-70"><?php echo e($label); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad)): ?>
<?php $attributes = $__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad; ?>
<?php unset($__attributesOriginal91b7ec410a681c4e92d3f108a8f492ad); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91b7ec410a681c4e92d3f108a8f492ad)): ?>
<?php $component = $__componentOriginal91b7ec410a681c4e92d3f108a8f492ad; ?>
<?php unset($__componentOriginal91b7ec410a681c4e92d3f108a8f492ad); ?>
<?php endif; ?>
</nav>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/navigation/sidebar-navigation.blade.php ENDPATH**/ ?>
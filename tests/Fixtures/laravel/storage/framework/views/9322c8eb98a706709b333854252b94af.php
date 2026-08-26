<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Données
    'source' => [],          // [[ 'data' => 'Label', 'disabled' => false, 'checked' => false, 'customId' => null ], ...]
    'target' => [],
    // Options
    'oneWay' => false,
    'pagination' => false,
    'elementsPerPage' => 5,
    'search' => false,
    'selectAll' => true,
    'noDataText' => 'No Data',
    // Personnalisation des textes
    'titleSource' => 'Source',
    'titleTarget' => 'Target',
    'selectAllTextSource' => 'Sélectionner tout',
    'selectAllTextTarget' => 'Sélectionner tout',
    'searchPlaceholderSource' => null,
    'searchPlaceholderTarget' => null,
    'toTargetButtonText' => null,
    'toSourceButtonText' => null,
    'toTargetArrow' => '→',
    'toSourceArrow' => '←',
    // Classes
    'class' => '',
    // Style des boutons de transfert
    'buttonsColor' => 'primary',   // primary | secondary | accent | neutral | info | success | warning | error
    'buttonsSize' => 'md',         // sm | md | lg
    'buttonsVariant' => 'solid',   // solid | outline | ghost
    'buttonsMode' => 'text',       // text | icon | both
    'tooltip' => true,             // true => tooltip DaisyUI au survol (utile en mode icon)
    'tooltipPlacement' => 'top',   // top | right | bottom | left
    // Responsivité et overflow
    'stackOn' => 'md',             // sm | md | lg | xl (breakpoint de passage en colonnes)
    'listOverflow' => 'y',         // y | x | both | none
    'listMaxHeight' => 'max-h-64', // classe Tailwind appliquée quand overflow-y
    'sortable' => false,
    'dragAndDrop' => false,
    'dropPlaceholder' => 'Déplacer ici',
    'keepButtons' => true,
    'handle' => false,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
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
    // Données
    'source' => [],          // [[ 'data' => 'Label', 'disabled' => false, 'checked' => false, 'customId' => null ], ...]
    'target' => [],
    // Options
    'oneWay' => false,
    'pagination' => false,
    'elementsPerPage' => 5,
    'search' => false,
    'selectAll' => true,
    'noDataText' => 'No Data',
    // Personnalisation des textes
    'titleSource' => 'Source',
    'titleTarget' => 'Target',
    'selectAllTextSource' => 'Sélectionner tout',
    'selectAllTextTarget' => 'Sélectionner tout',
    'searchPlaceholderSource' => null,
    'searchPlaceholderTarget' => null,
    'toTargetButtonText' => null,
    'toSourceButtonText' => null,
    'toTargetArrow' => '→',
    'toSourceArrow' => '←',
    // Classes
    'class' => '',
    // Style des boutons de transfert
    'buttonsColor' => 'primary',   // primary | secondary | accent | neutral | info | success | warning | error
    'buttonsSize' => 'md',         // sm | md | lg
    'buttonsVariant' => 'solid',   // solid | outline | ghost
    'buttonsMode' => 'text',       // text | icon | both
    'tooltip' => true,             // true => tooltip DaisyUI au survol (utile en mode icon)
    'tooltipPlacement' => 'top',   // top | right | bottom | left
    // Responsivité et overflow
    'stackOn' => 'md',             // sm | md | lg | xl (breakpoint de passage en colonnes)
    'listOverflow' => 'y',         // y | x | both | none
    'listMaxHeight' => 'max-h-64', // classe Tailwind appliquée quand overflow-y
    'sortable' => false,
    'dragAndDrop' => false,
    'dropPlaceholder' => 'Déplacer ici',
    'keepButtons' => true,
    'handle' => false,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $resolvedPagination = (bool) $pagination;
    $resolvedDragAndDrop = (bool) $dragAndDrop && ! $resolvedPagination;
    $resolvedSortable = ((bool) $sortable || $resolvedDragAndDrop) && ! $resolvedPagination;
    $resolvedHandle = (bool) $handle;

    // Préparation des attributs data-* pour l'initialisation JavaScript du module transfer.
    $wrapAttrs = [
        'data-transfer' => '1',
        'data-one-way' => $oneWay ? 'true' : 'false',
        'data-pagination' => $resolvedPagination ? 'true' : 'false',
        'data-elements-per-page' => (string) $elementsPerPage,
        'data-search' => $search ? 'true' : 'false',
        'data-select-all' => $selectAll ? 'true' : 'false',
        'data-no-data-text' => $noDataText,
        'data-stack-on' => $stackOn,
        'data-list-overflow' => $listOverflow,
        'data-sortable' => $resolvedSortable ? 'true' : 'false',
        'data-drag-and-drop' => $resolvedDragAndDrop ? 'true' : 'false',
        'data-drop-placeholder' => $dropPlaceholder,
        'data-handle' => $resolvedHandle ? 'true' : 'false',
    ];
    
    // Génération des textes par défaut pour les placeholders et boutons (si non fournis).
    $defaultSearchPlaceholderSource = $searchPlaceholderSource ?? 'Rechercher dans ' . $titleSource;
    $defaultSearchPlaceholderTarget = $searchPlaceholderTarget ?? 'Rechercher dans ' . $titleTarget;
    $defaultToTargetButtonText = $toTargetButtonText ?? $titleSource . ' ' . $toTargetArrow . ' ' . $titleTarget;
    $defaultToSourceButtonText = $toSourceButtonText ?? $titleTarget . ' ' . $toSourceArrow . ' ' . $titleSource;

    // Génération d'IDs uniques pour les cases "tout sélectionner" (accessibilité et ciblage JS).
    $sourceSelectAllId = 'select-all-source-'.uniqid();
    $targetSelectAllId = 'select-all-target-'.uniqid();

    // Construction des classes CSS pour les boutons de transfert (couleur, taille, variant).
    $btnColor = in_array($buttonsColor, ['primary','secondary','accent','neutral','info','success','warning','error']) ? $buttonsColor : 'primary';
    $btnSize = in_array($buttonsSize, ['sm','md','lg']) ? $buttonsSize : 'md';
    $btnVariantClass = $buttonsVariant === 'outline' ? ' btn-outline' : ($buttonsVariant === 'ghost' ? ' btn-ghost' : '');
    $useText = ($buttonsMode === 'text') || ($buttonsMode === 'both');
    $useIcon = ($buttonsMode === 'icon') || ($buttonsMode === 'both');
    $btnBase = 'btn btn-'.$btnColor.' btn-'.$btnSize.$btnVariantClass.($useIcon && !$useText ? ' btn-circle' : '');
    $tooltipClass = 'tooltip tooltip-'.(in_array($tooltipPlacement, ['top','right','bottom','left']) ? $tooltipPlacement : 'top');

    // Détermination des classes d'overflow pour les listes (scroll horizontal, vertical, ou les deux).
    $overflowClasses = match($listOverflow) {
        'x' => 'overflow-x-auto whitespace-nowrap',
        'both' => 'overflow-auto whitespace-nowrap '.$listMaxHeight,
        'none' => 'overflow-visible',
        default => 'overflow-y-auto '.$listMaxHeight,
    };

    // Breakpoint Tailwind pour le passage en colonnes (responsive : stack vertical → horizontal).
    $break = in_array($stackOn, ['sm','md','lg','xl']) ? $stackOn : 'md';
?>

<div <?php echo e($attributes->merge(['class' => trim('w-full '.$class), 'data-module' => ($module ?? 'transfer')])->merge($wrapAttrs)); ?>>
    <div class="grid grid-cols-1 <?php echo e($break); ?>:grid-cols-12 gap-4 items-stretch w-full">
        
        <div class="card bg-base-100 card-border h-full min-w-0 col-span-12 <?php echo e($break); ?>:col-span-5">
            <div class="card-body gap-3 p-4">
                
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectAll): ?>
                            <label class="label cursor-pointer justify-start gap-3 p-0">
                                <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="source" id="<?php echo e($sourceSelectAllId); ?>">
                                <span class="min-w-0 leading-tight">
                                    <span class="card-title text-base leading-tight"><?php echo e($titleSource); ?></span>
                                    <span class="block truncate text-xs text-base-content/60"><?php echo e($selectAllTextSource); ?></span>
                                </span>
                            </label>
                        <?php else: ?>
                            <h3 class="card-title text-base leading-tight"><?php echo e($titleSource); ?></h3>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="badge badge-ghost badge-sm shrink-0 font-mono" data-transfer-count="source"></span>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                    <div class="join w-full">
                        <input type="text" class="input input-sm join-item w-full" placeholder="<?php echo e($defaultSearchPlaceholderSource); ?>" data-transfer-search="source" />
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <ul class="menu menu-sm w-full bg-base-100 rounded-box card-border p-2 <?php echo e($overflowClasses); ?>" data-transfer-list="source">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $source; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            // Extraction des propriétés de l'item (support array ou string simple).
                            $label = is_array($it) ? ($it['data'] ?? (string)$i) : (string)$it;
                            $disabled = is_array($it) ? !empty($it['disabled']) : false;
                            $checked = is_array($it) ? !empty($it['checked']) : false;
                            $customId = is_array($it) ? ($it['customId'] ?? null) : null;
                        ?>
                        <li data-transfer-item data-id="<?php echo e($customId ?? ('s-'.$i)); ?>" data-label="<?php echo e($label); ?>" data-disabled="<?php echo e($disabled ? 'true' : 'false'); ?>" data-checked="<?php echo e($checked ? 'true' : 'false'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedSortable && $resolvedHandle): ?>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="btn btn-ghost btn-xs btn-square daisy-drag-handle cursor-grab" data-transfer-handle aria-label="Reorder <?php echo e($label); ?>" <?php if($disabled): echo 'disabled'; endif; ?>>
                                        <span aria-hidden="true">⋮⋮</span>
                                    </button>
                                    <label class="flex min-w-0 flex-1 cursor-pointer items-center gap-3 p-0">
                                        <input type="checkbox" class="checkbox checkbox-sm" <?php if($checked): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> />
                                        <span class="min-w-0 flex-1 truncate"><?php echo e($label); ?></span>
                                    </label>
                                </div>
                            <?php else: ?>
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" class="checkbox checkbox-sm" <?php if($checked): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> />
                                    <span class="min-w-0 flex-1 truncate"><?php echo e($label); ?></span>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pagination): ?>
                    <div class="join justify-center" data-transfer-pager="source">
                        <button type="button" class="btn btn-xs join-item" data-transfer-page="prev">«</button>
                        <span class="btn btn-xs join-item" data-transfer-page="info">1/1</span>
                        <button type="button" class="btn btn-xs join-item" data-transfer-page="next">»</button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="col-span-12 <?php echo e($break); ?>:col-span-2 flex flex-col justify-center items-center gap-3">
            <?php 
                $toTargetContent = '';
                if ($useIcon) {
                    $toTargetContent .= '<span class="text-lg inline '.$break.':hidden">↓</span>';
                    $toTargetContent .= '<span class="text-lg hidden '.$break.':inline">'.e($toTargetArrow).'</span>';
                }
                if ($useText) $toTargetContent .= '<span class="'.($useIcon ? 'ml-2 ' : '').' whitespace-nowrap">'.e($defaultToTargetButtonText).'</span>';
                $toTargetBtn = (
                    '<button type="button" class="'.$btnBase.'" data-transfer-move="toTarget" aria-label="'.e($defaultToTargetButtonText).'">'
                    .$toTargetContent
                    .'</button>'
                ); 
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($keepButtons): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tooltip && !$useText && $useIcon): ?>
                    <div class="<?php echo e($tooltipClass); ?>">
                        <span class="tooltip-content"><?php echo e($defaultToTargetButtonText); ?></span>
                        <?php echo $toTargetBtn; ?>

                    </div>
                <?php else: ?>
                    <?php echo $toTargetBtn; ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$oneWay && $keepButtons): ?>
                <?php 
                    $toSourceContent = '';
                    if ($useIcon) {
                        $toSourceContent .= '<span class="text-lg inline '.$break.':hidden">↑</span>';
                        $toSourceContent .= '<span class="text-lg hidden '.$break.':inline">'.e($toSourceArrow).'</span>';
                    }
                    if ($useText) $toSourceContent .= '<span class="'.($useIcon ? 'ml-2 ' : '').' whitespace-nowrap">'.e($defaultToSourceButtonText).'</span>';
                    $toSourceBtn = (
                        '<button type="button" class="'.$btnBase.'" data-transfer-move="toSource" aria-label="'.e($defaultToSourceButtonText).'">'
                        .$toSourceContent
                        .'</button>'
                    ); 
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tooltip && !$useText && $useIcon): ?>
                    <div class="<?php echo e($tooltipClass); ?>">
                        <span class="tooltip-content"><?php echo e($defaultToSourceButtonText); ?></span>
                        <?php echo $toSourceBtn; ?>

                    </div>
                <?php else: ?>
                    <?php echo $toSourceBtn; ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="card bg-base-100 card-border h-full min-w-0 col-span-12 <?php echo e($break); ?>:col-span-5">
            <div class="card-body gap-3 p-4">
                
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectAll): ?>
                            <label class="label cursor-pointer justify-start gap-3 p-0">
                                <input type="checkbox" class="checkbox checkbox-sm" data-transfer-selectall="target" id="<?php echo e($targetSelectAllId); ?>">
                                <span class="min-w-0 leading-tight">
                                    <span class="card-title text-base leading-tight"><?php echo e($titleTarget); ?></span>
                                    <span class="block truncate text-xs text-base-content/60"><?php echo e($selectAllTextTarget); ?></span>
                                </span>
                            </label>
                        <?php else: ?>
                            <h3 class="card-title text-base leading-tight"><?php echo e($titleTarget); ?></h3>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <span class="badge badge-ghost badge-sm shrink-0 font-mono" data-transfer-count="target"></span>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                    <div class="join w-full">
                        <input type="text" class="input input-sm join-item w-full" placeholder="<?php echo e($defaultSearchPlaceholderTarget); ?>" data-transfer-search="target" />
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <ul class="menu menu-sm w-full bg-base-100 rounded-box card-border p-2 <?php echo e($overflowClasses); ?>" data-transfer-list="target">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $target; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            // Extraction des propriétés de l'item (support array ou string simple).
                            $label = is_array($it) ? ($it['data'] ?? (string)$i) : (string)$it;
                            $disabled = is_array($it) ? !empty($it['disabled']) : false;
                            $checked = is_array($it) ? !empty($it['checked']) : false;
                            $customId = is_array($it) ? ($it['customId'] ?? null) : null;
                        ?>
                        <li data-transfer-item data-id="<?php echo e($customId ?? ('t-'.$i)); ?>" data-label="<?php echo e($label); ?>" data-disabled="<?php echo e($disabled ? 'true' : 'false'); ?>" data-checked="<?php echo e($checked ? 'true' : 'false'); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($resolvedSortable && $resolvedHandle): ?>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="btn btn-ghost btn-xs btn-square daisy-drag-handle cursor-grab" data-transfer-handle aria-label="Reorder <?php echo e($label); ?>" <?php if($disabled): echo 'disabled'; endif; ?>>
                                        <span aria-hidden="true">⋮⋮</span>
                                    </button>
                                    <label class="flex min-w-0 flex-1 cursor-pointer items-center gap-3 p-0">
                                        <input type="checkbox" class="checkbox checkbox-sm" <?php if($checked): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> />
                                        <span class="min-w-0 flex-1 truncate"><?php echo e($label); ?></span>
                                    </label>
                                </div>
                            <?php else: ?>
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input type="checkbox" class="checkbox checkbox-sm" <?php if($checked): echo 'checked'; endif; ?> <?php if($disabled): echo 'disabled'; endif; ?> />
                                    <span class="min-w-0 flex-1 truncate"><?php echo e($label); ?></span>
                                </label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pagination): ?>
                    <div class="join justify-center" data-transfer-pager="target">
                        <button type="button" class="btn btn-xs join-item" data-transfer-page="prev">«</button>
                        <span class="btn btn-xs join-item" data-transfer-page="info">1/1</span>
                        <button type="button" class="btn btn-xs join-item" data-transfer-page="next">»</button>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/transfer.blade.php ENDPATH**/ ?>
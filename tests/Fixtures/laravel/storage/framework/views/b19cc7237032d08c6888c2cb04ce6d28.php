<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::form.tabs.title'),
    'action' => '#',
    'method' => 'POST',
    'tabs' => [], // [['id' => 'general', 'label' => 'Général']]
    'activeTab' => null,
    'tabsStyle' => 'box', // box|border|lift
    'tabsPlacement' => 'top', // top|bottom
    'highlightErrors' => true,
    'showErrorBadges' => true,
    'persistActiveTabField' => '_active_tab',
    'fieldToTabMap' => [], // Mapping des champs vers les onglets pour le comptage d'erreurs
    'autoRefreshCsrf' => true,
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
    'title' => __('daisy::form.tabs.title'),
    'action' => '#',
    'method' => 'POST',
    'tabs' => [], // [['id' => 'general', 'label' => 'Général']]
    'activeTab' => null,
    'tabsStyle' => 'box', // box|border|lift
    'tabsPlacement' => 'top', // top|bottom
    'highlightErrors' => true,
    'showErrorBadges' => true,
    'persistActiveTabField' => '_active_tab',
    'fieldToTabMap' => [], // Mapping des champs vers les onglets pour le comptage d'erreurs
    'autoRefreshCsrf' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use Art35rennes\DaisyKit\Helpers\TabErrorBag;
    
    // Déterminer l'onglet actif
    $currentActiveTab = $activeTab ?? old($persistActiveTabField) ?? ($tabs[0]['id'] ?? null);
    
    // Compter les erreurs par onglet si nécessaire
    $errorCountsByTab = [];
    $errorsBag = $errors ?? new \Illuminate\Support\MessageBag();
    if ($showErrorBadges && $highlightErrors && !empty($fieldToTabMap) && $errorsBag->any()) {
        $errorCountsByTab = TabErrorBag::countErrorsByTab($fieldToTabMap, $errorsBag);
    } elseif ($showErrorBadges && $highlightErrors && $errorsBag->any()) {
        // Fallback : utiliser les préfixes si aucun mapping n'est fourni
        $tabIds = array_column($tabs, 'id');
        $errorCountsByTab = TabErrorBag::countErrorsByTabPrefix($tabIds, $errorsBag);
    }
    
    // Construire les items pour le composant tabs
    $tabItems = [];
    foreach ($tabs as $tab) {
        $tabId = $tab['id'] ?? null;
        $tabLabel = $tab['label'] ?? 'Tab';
        $isActive = $tabId === $currentActiveTab;
        
        // Ajouter un badge d'erreur si nécessaire
        $errorCount = $errorCountsByTab[$tabId] ?? 0;
        
        $tabItems[] = [
            'id' => $tabId,
            'label' => $tabLabel,
            'errorCount' => $errorCount,
            'active' => $isActive,
        ];
    }
    
    // Déterminer le style des tabs
    $tabsVariant = match($tabsStyle) {
        'box' => 'box',
        'border' => 'border',
        'lift' => 'lifted',
        default => 'box',
    };
?>

<?php
    // Générer un ID unique pour cette instance si non fourni
    $instanceId = $attributes->get('id') ?? 'form-tabs-'.uniqid();
    $formMethod = strtoupper($method);
    $isGet = $formMethod === 'GET';
    $htmlMethod = $isGet ? 'GET' : 'POST';
?>

<form 
    id="<?php echo e($instanceId); ?>"
    action="<?php echo e($action); ?>" 
    method="<?php echo e($htmlMethod); ?>" 
    data-module="tabs"
    data-tabs-instance-id="<?php echo e($instanceId); ?>"
    data-persist-field="<?php echo e($persistActiveTabField); ?>"
    <?php echo e($attributes->except(['id'])); ?>

>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isGet): ?>
        <?php echo csrf_field(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isGet && $formMethod !== 'POST'): ?>
        <?php echo method_field($formMethod); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <input type="hidden" name="<?php echo e($persistActiveTabField); ?>" value="<?php echo e($currentActiveTab); ?>" />
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($autoRefreshCsrf && !$isGet): ?>
        <?php if (isset($component)) { $__componentOriginal11d192a26b600b197a9d17a6b06c2323 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11d192a26b600b197a9d17a6b06c2323 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.utilities.csrf-keeper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.utilities.csrf-keeper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11d192a26b600b197a9d17a6b06c2323)): ?>
<?php $attributes = $__attributesOriginal11d192a26b600b197a9d17a6b06c2323; ?>
<?php unset($__attributesOriginal11d192a26b600b197a9d17a6b06c2323); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11d192a26b600b197a9d17a6b06c2323)): ?>
<?php $component = $__componentOriginal11d192a26b600b197a9d17a6b06c2323; ?>
<?php unset($__componentOriginal11d192a26b600b197a9d17a6b06c2323); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title): ?>
        <h2 class="text-2xl font-semibold mb-6"><?php echo e($title); ?></h2>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <div class="space-y-6">
        <?php
            $tabsRadioName = 'form-tabs-'.uniqid();
        ?>
        
        
        <div class="tabs <?php echo e($tabsVariant === 'box' ? 'tabs-box' : ($tabsVariant === 'border' ? 'tabs-border' : 'tabs-lift')); ?> <?php echo e($tabsPlacement === 'bottom' ? 'tabs-bottom' : 'tabs-top'); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $tabId = $tab['id'] ?? null;
                    $tabLabel = $tab['label'] ?? 'Tab';
                    $isActive = $tabId === $currentActiveTab;
                    $errorCount = $errorCountsByTab[$tabId] ?? 0;
                ?>
                <input 
                    type="radio" 
                    name="<?php echo e($tabsRadioName); ?>" 
                    class="tab" 
                    aria-label="<?php echo e($tabLabel); ?>"
                    <?php if($isActive): echo 'checked'; endif; ?>
                    data-tab-id="<?php echo e($tabId); ?>"
                />
                <div class="tab-content border-base-300 bg-base-100 p-6" data-tab-content-id="<?php echo e($tabId); ?>" role="tabpanel">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showErrorBadges && $errorCount > 0): ?>
                        <div class="flex items-center gap-2 mb-4">
                            <h3 class="text-lg font-semibold"><?php echo e($tabLabel); ?></h3>
                            <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => 'error','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error','size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e($errorCount); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset(${'tab_'.$tabId})): ?>
                        <?php echo ${'tab_'.$tabId}; ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset(${'tab_'.$tabId.'_footer'})): ?>
                        <div class="mt-6 pt-6 border-t">
                            <?php echo ${'tab_'.$tabId.'_footer'}; ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($actions)): ?>
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <?php echo $actions; ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</form>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views//templates/form/form-with-tabs.blade.php ENDPATH**/ ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'drawer-toggle',
    'open' => false,
    'sideClass' => 'w-80',
    'end' => false, // drawer-end
    'responsiveOpen' => null, // ex: 'lg' -> lg:drawer-open
    // Quand true, la zone côté est un <ul class="menu ...">. Sinon, on rend le contenu brut.
    'sideIsMenu' => true,
    // Contrôle de la hauteur du contenu: par défaut plein écran, peut être désactivé pour les démos compactes
    'fullHeight' => true,
    // Classes supplémentaires injectées dans la zone contenu
    'contentClass' => '',
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
    'id' => 'drawer-toggle',
    'open' => false,
    'sideClass' => 'w-80',
    'end' => false, // drawer-end
    'responsiveOpen' => null, // ex: 'lg' -> lg:drawer-open
    // Quand true, la zone côté est un <ul class="menu ...">. Sinon, on rend le contenu brut.
    'sideIsMenu' => true,
    // Contrôle de la hauteur du contenu: par défaut plein écran, peut être désactivé pour les démos compactes
    'fullHeight' => true,
    // Classes supplémentaires injectées dans la zone contenu
    'contentClass' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $responsiveOpenClasses = [
        'sm' => 'sm:drawer-open',
        'md' => 'md:drawer-open',
        'lg' => 'lg:drawer-open',
        'xl' => 'xl:drawer-open',
        '2xl' => '2xl:drawer-open',
    ];
    $rootClasses = 'drawer';
    if ($end) $rootClasses .= ' drawer-end';
    if ($responsiveOpen) $rootClasses .= ' '.($responsiveOpenClasses[$responsiveOpen] ?? $responsiveOpenClasses['lg']);

    // Classes pour la zone de contenu principal.
    $contentClasses = 'drawer-content min-w-0';
    // Hauteur pleine écran par défaut (peut être désactivée pour des layouts compacts).
    if ($fullHeight) $contentClasses .= ' min-h-screen';
    if (!empty($contentClass)) $contentClasses .= ' '.$contentClass;
?>


<div <?php echo e($attributes->merge(['class' => $rootClasses])); ?>>
  
  <input id="<?php echo e($id); ?>" type="checkbox" class="drawer-toggle" <?php if($open): echo 'checked'; endif; ?> />
  
  <div class="<?php echo e($contentClasses); ?>">
    <?php echo e($content ?? $slot); ?>

  </div>
  
  <div class="drawer-side overflow-visible">
    
    <label for="<?php echo e($id); ?>" aria-label="close sidebar" class="drawer-overlay"></label>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sideIsMenu): ?>
      
      <ul class="menu p-4 bg-base-200 text-base-content h-full overflow-y-auto border-r border-base-content/10 <?php echo e($sideClass); ?>">
        <?php echo e($side ?? ''); ?>

      </ul>
    <?php else: ?>
      
      <div class="bg-base-200 text-base-content h-full overflow-visible border-r border-base-content/10 <?php echo e($sideClass); ?>">
        <?php echo e($side ?? ''); ?>

      </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
  </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/overlay/drawer.blade.php ENDPATH**/ ?>
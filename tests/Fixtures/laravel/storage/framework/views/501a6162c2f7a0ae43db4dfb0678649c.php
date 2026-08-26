<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'overlay' => false,
    'imageUrl' => null,
    'minH' => 'min-h-[24rem]',
    // Plein écran
    'fullScreen' => false,
    // Disposition
    'row' => false,
    'reverse' => false,
    // Couleurs/texte
    'text' => null,         // ex: neutral-content
    'bg' => null,           // ex: base-200
    'overlayClass' => 'bg-black/60',
    // Classes supplémentaires
    'contentMax' => 'max-w-md',
    'contentClass' => null,
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
    'overlay' => false,
    'imageUrl' => null,
    'minH' => 'min-h-[24rem]',
    // Plein écran
    'fullScreen' => false,
    // Disposition
    'row' => false,
    'reverse' => false,
    // Couleurs/texte
    'text' => null,         // ex: neutral-content
    'bg' => null,           // ex: base-200
    'overlayClass' => 'bg-black/60',
    // Classes supplémentaires
    'contentMax' => 'max-w-md',
    'contentClass' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $normalizeImageUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $imageUrl = $normalizeImageUrl($imageUrl);

    $rootClasses = 'hero relative overflow-hidden ' . ($fullScreen ? 'min-h-screen' : $minH);
    if ($bg) {
        $rootClasses .= ' bg-'.$bg;
    }

    $contentClasses = 'hero-content';
    if ($row) $contentClasses .= ' flex-col lg:flex-row';
    if ($reverse) $contentClasses .= ' lg:flex-row-reverse';

    // Texte: si explicitement fourni, on l'utilise; sinon si image/overlay → neutral-content, sinon rien
    $textClass = $text ? ' text-'.$text : (($imageUrl || $overlay) ? ' text-neutral-content' : '');
    $contentClasses .= ' '.$textClass;

    if ($contentClass) $contentClasses .= ' '.$contentClass;
?>

<div <?php echo e($attributes->merge(['class' => $rootClasses])); ?>>
  <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($imageUrl): ?>
    <img src="<?php echo e($imageUrl); ?>" alt="" aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full object-cover">
  <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
  <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overlay): ?>
    <div class="hero-overlay <?php echo e($overlayClass); ?>"></div>
  <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
  <div class="<?php echo e($contentClasses); ?> relative z-10">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($figure)): ?>
      <div class="w-full max-w-sm">
        <?php echo e($figure); ?>

      </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="<?php echo e($contentMax); ?> w-full">
      <?php echo e($slot); ?>

    </div>
  </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/hero.blade.php ENDPATH**/ ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // type d'élément embarqué: iframe|video|object|embed|div (fallback)
    'tag' => 'iframe',
    // ratio prédéfini: 1x1|4x3|16x9|21x9 ou null
    'ratio' => '16x9',
    // ratio personnalisé via pourcentage (ex: '50%' => 2x1)
    'ratioPercent' => null,
    // classes supplémentaires appliquées au wrapper
    'wrapperClass' => '',
    // attributs HTML spécifiques (src, allow, allowfullscreen, etc.)
    'src' => null,
    'allow' => null,
    'allowfullscreen' => true,
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
    // type d'élément embarqué: iframe|video|object|embed|div (fallback)
    'tag' => 'iframe',
    // ratio prédéfini: 1x1|4x3|16x9|21x9 ou null
    'ratio' => '16x9',
    // ratio personnalisé via pourcentage (ex: '50%' => 2x1)
    'ratioPercent' => null,
    // classes supplémentaires appliquées au wrapper
    'wrapperClass' => '',
    // attributs HTML spécifiques (src, allow, allowfullscreen, etc.)
    'src' => null,
    'allow' => null,
    'allowfullscreen' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $ratioClass = null;

    if (!empty($ratioPercent)) {
        $ratioPercentValue = trim((string) $ratioPercent);

        if (preg_match('/^(\d+(?:\.\d+)?)%$/', $ratioPercentValue, $matches) === 1) {
            $ratioToken = (int) round((float) $matches[1]);
            $ratioClass = $ratioToken >= 1 && $ratioToken <= 300 ? 'daisy-embed-ratio-percent-'.$ratioToken : null;
        }
    } elseif ($ratio) {
        $ratioClasses = [
            '1x1' => 'daisy-embed-ratio-1x1',
            '4x3' => 'daisy-embed-ratio-4x3',
            '16x9' => 'daisy-embed-ratio-16x9',
            '21x9' => 'daisy-embed-ratio-21x9',
        ];
        $ratioClass = $ratioClasses[$ratio] ?? null;
    }

    $wrapperClasses = trim('relative w-full '.$ratioClass.' '.$wrapperClass);
?>

<div <?php echo e($attributes->merge(['class' => $wrapperClasses])); ?>>
    <div class="daisy-embed-aspect block w-full">
        <div class="absolute inset-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tag === 'iframe'): ?>
                <iframe src="<?php echo e($src); ?>" class="h-full w-full" <?php if($allow): ?> allow="<?php echo e($allow); ?>" <?php endif; ?> <?php if($allowfullscreen): ?> allowfullscreen <?php endif; ?>></iframe>
            <?php elseif($tag === 'video'): ?>
                <video class="h-full w-full" <?php if($src): ?> src="<?php echo e($src); ?>" <?php endif; ?> controls></video>
            <?php elseif($tag === 'object'): ?>
                <object class="h-full w-full" <?php if($src): ?> data="<?php echo e($src); ?>" <?php endif; ?>></object>
            <?php elseif($tag === 'embed'): ?>
                <embed class="h-full w-full" <?php if($src): ?> src="<?php echo e($src); ?>" <?php endif; ?> />
            <?php else: ?>
                <div class="h-full w-full"><?php echo e($slot); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/media/embed.blade.php ENDPATH**/ ?>
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Vue initiale: month | week | day | year | list
    'view' => 'month',
    // Vues disponibles pour le switcher
    'views' => ['year','month','week','day','list'],
    // Date ISO (YYYY-MM-DD) de départ. Null = aujourd'hui
    'initialDate' => null,
    // Données d'évènements: tableau PHP encodé en JSON côté data-attr
    'events' => null,
    // URL JSON pour chargement AJAX (params fournis: start, end)
    'eventsUrl' => null,
    // Premier jour de la semaine (0=Dimanche .. 6=Samedi). FR par défaut
    'firstDay' => 1,
    // Plage horaire visible pour les vues week/day (ex: 6..22)
    'hourStart' => 6,
    'hourEnd' => 22,
    // Hauteur: auto | fixed(px)
    'height' => 'auto',
    // Détail par défaut: none | modal (clic événement). none = évènement personnalisé uniquement
    'detail' => 'modal',
    'module' => null,
    'label' => null,
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
    // Vue initiale: month | week | day | year | list
    'view' => 'month',
    // Vues disponibles pour le switcher
    'views' => ['year','month','week','day','list'],
    // Date ISO (YYYY-MM-DD) de départ. Null = aujourd'hui
    'initialDate' => null,
    // Données d'évènements: tableau PHP encodé en JSON côté data-attr
    'events' => null,
    // URL JSON pour chargement AJAX (params fournis: start, end)
    'eventsUrl' => null,
    // Premier jour de la semaine (0=Dimanche .. 6=Samedi). FR par défaut
    'firstDay' => 1,
    // Plage horaire visible pour les vues week/day (ex: 6..22)
    'hourStart' => 6,
    'hourEnd' => 22,
    // Hauteur: auto | fixed(px)
    'height' => 'auto',
    // Détail par défaut: none | modal (clic événement). none = évènement personnalisé uniquement
    'detail' => 'modal',
    'module' => null,
    'label' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $heightClass = null;
    if ($height !== 'auto') {
        $heightValue = trim((string) $height);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $heightValue, $matches) === 1) {
            $token = (int) round((float) $matches[1]);
            $heightClass = $token >= 1 && $token <= 1200 ? 'daisy-calendar-min-height-px-'.$token : null;
        } elseif (is_numeric($heightValue)) {
            $token = (int) round((float) $heightValue);
            $heightClass = $token >= 1 && $token <= 1200 ? 'daisy-calendar-min-height-px-'.$token : null;
        }
    }

    $data = [
        'view' => $view,
        'views' => array_values($views ?? []),
        'initialDate' => $initialDate,
        'firstDay' => (int) $firstDay,
        'hourStart' => (int) $hourStart,
        'hourEnd' => (int) $hourEnd,
        'height' => $height,
        'detail' => $detail,
    ];
?>

<div
    data-module="<?php echo e($module ?? 'calendar-full'); ?>"
    data-calendar-full="1"
    data-options='<?php echo json_encode($data, 15, 512) ?>'
    role="region"
    aria-label="<?php echo e($label ?? __('daisy::calendar.calendar')); ?>"
    <?php if($events): ?> data-events='<?php echo json_encode($events, 15, 512) ?>' <?php endif; ?>
    <?php if($eventsUrl): ?> data-events-url="<?php echo e($eventsUrl); ?>" <?php endif; ?>
    <?php echo e($attributes->merge(['class' => trim('calendar-full card card-border block w-full bg-base-100 shadow-sm '.$heightClass)])); ?>

></div>

<?php echo $__env->make('daisy::components.ui.partials.calendar-event', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('daisy::components.partials.assets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/advanced/calendar-full.blade.php ENDPATH**/ ?>
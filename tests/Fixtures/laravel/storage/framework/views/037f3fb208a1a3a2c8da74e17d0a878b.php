<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Intervalle de rafraîchissement en millisecondes (optionnel, calculé automatiquement si non fourni)
    'refreshInterval' => null,
    // Ratio de sécurité pour le calcul automatique (0.8 = 80% du lifetime de session)
    'refreshRatio' => 0.8,
    // Endpoint pour rafraîchir le token (route du package)
    'endpoint' => null,
    // Override du nom du module JS
    'module' => 'csrf-keeper',
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
    // Intervalle de rafraîchissement en millisecondes (optionnel, calculé automatiquement si non fourni)
    'refreshInterval' => null,
    // Ratio de sécurité pour le calcul automatique (0.8 = 80% du lifetime de session)
    'refreshRatio' => 0.8,
    // Endpoint pour rafraîchir le token (route du package)
    'endpoint' => null,
    // Override du nom du module JS
    'module' => 'csrf-keeper',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Calculer l'intervalle si non fourni
    if ($refreshInterval === null) {
        $sessionLifetime = (int) config('session.lifetime', 120); // minutes
        $refreshInterval = (int) ($sessionLifetime * 60 * 1000 * $refreshRatio); // convertit en ms et applique le ratio
    }
    
    // Déterminer l'endpoint
    if ($endpoint === null) {
        $routeEnabled = (bool) config('daisy-kit.csrf_refresh.enabled', true);

        if ($routeEnabled) {
            $routeName = (string) config('daisy-kit.csrf_refresh.name', 'daisy-kit.csrf-token');

            try {
                $endpoint = route($routeName);
            } catch (\Exception $e) {
                $endpoint = '/'.ltrim((string) config('daisy-kit.csrf_refresh.path', 'daisy-kit/csrf-token.json'), '/');
            }
        }
    }
?>

<div 
    data-module="<?php echo e($module); ?>"
    data-refresh-interval="<?php echo e($refreshInterval); ?>"
    <?php if(filled($endpoint)): ?> data-endpoint="<?php echo e($endpoint); ?>" <?php endif; ?>
    <?php echo e($attributes); ?>

></div>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/utilities/csrf-keeper.blade.php ENDPATH**/ ?>
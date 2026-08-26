<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'size' => 'md',         // xs | sm | md | lg | xl
    'variant' => null,      // null | ghost
    'color' => null,        // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
    // Modes avancés
    'search' => false,              // Active le mode "search" (filtre local des options existantes)
    'autocomplete' => false,        // Active le mode "autocomplete" (requête distante)
    'searchable' => true,           // Autorise la saisie libre dans un select enrichi
    // Options autocomplete
    'endpoint' => null,             // URL de l'endpoint qui renvoie les options [{ value, label, disabled? }]
    'param' => 'q',                 // Nom du paramètre de recherche (par défaut: q)
    'debounce' => 500,              // Délais de debounce en ms pour la saisie
    'minChars' => 3,                // Nombre minimal de caractères avant de déclencher l'appel
    'default' => null,              // Données par défaut (array d'items {value,label,disabled?,subtitle?,avatar?}) quand vide en remote
    'fetchOnEmpty' => true,         // Si true, quand input vide en remote, on interroge endpoint avec q=''
    'placeholder' => null,          // Placeholder à utiliser pour l'input unifié (sinon 1ère option vide ou défaut)
    'listSize' => 5,                // Nombre maximal d'options visibles avant défilement (1 à 20)
    // Surcharge éventuelle du nom du module
    'module' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
    'options' => [],
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
    'size' => 'md',         // xs | sm | md | lg | xl
    'variant' => null,      // null | ghost
    'color' => null,        // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
    // Modes avancés
    'search' => false,              // Active le mode "search" (filtre local des options existantes)
    'autocomplete' => false,        // Active le mode "autocomplete" (requête distante)
    'searchable' => true,           // Autorise la saisie libre dans un select enrichi
    // Options autocomplete
    'endpoint' => null,             // URL de l'endpoint qui renvoie les options [{ value, label, disabled? }]
    'param' => 'q',                 // Nom du paramètre de recherche (par défaut: q)
    'debounce' => 500,              // Délais de debounce en ms pour la saisie
    'minChars' => 3,                // Nombre minimal de caractères avant de déclencher l'appel
    'default' => null,              // Données par défaut (array d'items {value,label,disabled?,subtitle?,avatar?}) quand vide en remote
    'fetchOnEmpty' => true,         // Si true, quand input vide en remote, on interroge endpoint avec q=''
    'placeholder' => null,          // Placeholder à utiliser pour l'input unifié (sinon 1ère option vide ou défaut)
    'listSize' => 5,                // Nombre maximal d'options visibles avant défilement (1 à 20)
    // Surcharge éventuelle du nom du module
    'module' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
    'options' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizeMap = [
        'xs' => 'select-xs',
        'sm' => 'select-sm',
        'md' => 'select-md',
        'lg' => 'select-lg',
        'xl' => 'select-xl',
    ];
    $enhancedInputSizeMap = [
        'xs' => 'input-xs',
        'sm' => 'input-sm',
        'md' => 'input-md',
        'lg' => 'input-lg',
        'xl' => 'input-xl',
    ];

    $classes = 'select w-full';

    if ($variant === 'ghost') {
        $classes .= ' select-ghost';
    }

    if ($color) {
        $classes .= ' select-'.$color;
    }

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first($name) : null);
    $hasError = filled($errorMessage);

    if ($hasError) {
        $classes .= ' select-error';
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    $selectId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $oldInput = $name ? data_get(session()->get('_old_input', []), $name, old($name, $value)) : $value;
    $selectedValue = $bindOld && $name ? $oldInput : $value;
    $slotContent = $slot ?? '';
    $semanticSwatches = ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'];
    $semanticSwatchClasses = [
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'accent' => 'bg-accent',
        'neutral' => 'bg-neutral',
        'info' => 'bg-info',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'error' => 'bg-error',
    ];
    $normalizeSwatch = static function (mixed $swatch) use ($semanticSwatches): string {
        $swatch = trim((string) $swatch);

        return in_array($swatch, $semanticSwatches, true) ? $swatch : '';
    };
    $normalizedOptions = collect(is_iterable($options) ? $options : [])
        ->map(function ($option) use ($normalizeSwatch): array {
            if (is_array($option)) {
                return [
                    'value' => $option['value'] ?? $option['id'] ?? '',
                    'label' => trim((string) ($option['label'] ?? $option['name'] ?? $option['value'] ?? '')),
                    'disabled' => (bool) ($option['disabled'] ?? false),
                    'swatch' => $normalizeSwatch($option['swatch'] ?? ''),
                ];
            }

            return [
                'value' => $option,
                'label' => trim((string) $option),
                'disabled' => false,
                'swatch' => '',
            ];
        })
        ->values();

    $selectedOption = $normalizedOptions->first(fn (array $option): bool => (string) $selectedValue === (string) $option['value']);
    $selectedSwatch = $selectedOption['swatch'] ?? '';
    $resolvedListSize = is_numeric($listSize) ? min(20, max(1, (int) $listSize)) : 5;

    // Attributs data pour initialiser le module JS quand nécessaire
    $dataAttributes = [];
    $shouldEnhance = $search || $autocomplete || $endpoint || $normalizedOptions->contains(fn (array $option): bool => $option['swatch'] !== '');
    if ($shouldEnhance) {
        $dataAttributes['data-module'] = $module ?: 'select';
        // Options communes
        $dataAttributes['data-debounce'] = (string) (is_numeric($debounce) ? $debounce : 500);
        $dataAttributes['data-min-chars'] = (string) (is_numeric($minChars) ? $minChars : 3);
        $dataAttributes['data-searchable'] = $searchable ? 'true' : 'false';
        $dataAttributes['data-list-size'] = (string) $resolvedListSize;
        // Options spécifiques à l'autocomplete
        if ($endpoint) {
            $dataAttributes['data-endpoint'] = (string) $endpoint;
            $dataAttributes['data-param'] = (string) ($param ?: 'q');
            $dataAttributes['data-fetch-on-empty'] = $fetchOnEmpty ? 'true' : 'false';
        }
        if ($endpoint && !is_null($default)) {
            try {
                $dataAttributes['data-default'] = json_encode($default, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } catch (\Throwable $e) {
                // En cas d'échec d'encodage, ignorer silencieusement pour ne pas casser le rendu.
            }
        }
        if (!is_null($placeholder)) {
            $dataAttributes['data-placeholder'] = (string) $placeholder;
        }
    }

    $selectAttributes = $attributes
        ->merge(array_merge(['class' => $classes], $dataAttributes))
        ->merge(array_filter([
            'id' => $selectId,
            'name' => $name,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $describedBy,
        ], static fn ($attributeValue) => ! is_null($attributeValue)));

    $wrapperAttributes = $attributes
        ->except(['id', 'name'])
        ->class('dropdown w-full')
        ->merge($dataAttributes);

    $nativeSelectAttributes = $selectAttributes->except(array_keys($dataAttributes));
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shouldEnhance): ?>
    <div <?php echo e($wrapperAttributes); ?>>
        <label class="input <?php echo e($enhancedInputSizeMap[$size] ?? $enhancedInputSizeMap['md']); ?> flex w-full items-center gap-2">
            <span data-role="swatch" class="h-3 w-3 shrink-0 rounded-full <?php echo e($semanticSwatchClasses[$selectedSwatch] ?? 'hidden'); ?>" aria-hidden="true"></span>
            <input type="text"
                   data-role="input"
                   class="grow"
                   autocomplete="off"
                   <?php if(!$searchable): echo 'readonly'; endif; ?>
                   placeholder="<?php echo e(is_string($placeholder ?? null) ? $placeholder : 'Tapez pour rechercher...'); ?>" />
        </label>
        <ul
            class="daisy-select-list dropdown-content z-10 menu hidden w-full overflow-y-auto overscroll-contain rounded-box bg-base-100 shadow"
            role="listbox"
            data-role="list"
            data-select-list-size="<?php echo e($resolvedListSize); ?>"
        ></ul>
        <select data-role="native" <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($nativeSelectAttributes->merge(['hidden' => true])); ?>>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $normalizedOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($option['value']); ?>" <?php if((string) $selectedValue === (string) $option['value']): echo 'selected'; endif; ?> <?php if($option['disabled']): echo 'disabled'; endif; ?> <?php if($option['swatch'] !== ''): ?> data-swatch="<?php echo e($option['swatch']); ?>" <?php endif; ?>><?php echo e($option['label']); ?></option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php echo e($slotContent); ?>

        </select>
    </div>
<?php else: ?>
    <select <?php if($disabled): echo 'disabled'; endif; ?> <?php echo e($selectAttributes); ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $normalizedOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <option value="<?php echo e($option['value']); ?>" <?php if((string) $selectedValue === (string) $option['value']): echo 'selected'; endif; ?> <?php if($option['disabled']): echo 'disabled'; endif; ?>><?php echo e($option['label']); ?></option>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php echo e($slotContent); ?>

        
        
    </select>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/inputs/select.blade.php ENDPATH**/ ?>
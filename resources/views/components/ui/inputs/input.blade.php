@props([
    'type' => 'text',
    'size' => 'md',        // xs | sm | md | lg | xl
    'variant' => null,     // null | ghost
    'color' => null,       // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
    // Obfuscation (JS input-mask: data-obfuscate, data-obfuscate-char, data-obfuscate-keep-end)
    'obfuscate' => false,
    'obfuscateChar' => null,      // ex: '*', '•'
    'obfuscateKeepEnd' => null,   // ex: 4
    // Pattern mask (hérité) – active [data-inputmask="1"] si défini
    'inputMask' => false,         // true pour forcer l'initialisation du mask
    'mask' => null,               // pattern (ex: '99-99') → data-mask
    'maskCharPlaceholder' => null, // → data-char-placeholder
    'maskPlaceholder' => null,     // bool → data-mask-placeholder
    'inputPlaceholder' => null,    // bool → data-input-placeholder
    'clearIncomplete' => null,     // bool → data-clear-incomplete
    'customMask' => null,          // liste de tokens personnalisés (ex: "#,%,@") → data-custom-mask
    'customValidator' => null,     // liste de regex correspondantes → data-custom-validator
    'name' => null,
    'id' => null,
    'value' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
])

@php
    $sizeMap = [
        'xs' => 'input-xs',
        'sm' => 'input-sm',
        'md' => 'input-md',
        'lg' => 'input-lg',
        'xl' => 'input-xl',
    ];

    $classes = 'input w-full';
    $nativePickerClasses = [
        'date' => 'daisy-native-picker-date',
        'datetime-local' => 'daisy-native-picker-datetime',
        'month' => 'daisy-native-picker-month',
        'time' => 'daisy-native-picker-time',
        'week' => 'daisy-native-picker-week',
    ];

    if ($variant === 'ghost') {
        $classes .= ' input-ghost';
    }

    if ($color) {
        $classes .= ' input-'.$color;
    }

    $sharedErrors = view()->shared('errors');
    $localErrors = $errors ?? null;
    $laravelErrors = $localErrors instanceof \Illuminate\Support\ViewErrorBag && $localErrors->any()
        ? $localErrors
        : ($sharedErrors instanceof \Illuminate\Support\ViewErrorBag ? $sharedErrors : new \Illuminate\Support\ViewErrorBag());
    $errorMessage = $error ?? ($name && method_exists($laravelErrors, 'first') ? $laravelErrors->first($name) : null);
    $hasError = filled($errorMessage);

    if ($hasError) {
        $classes .= ' input-error';
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    if (isset($nativePickerClasses[$type])) {
        $classes .= ' '.$nativePickerClasses[$type];
    }

    $inputId = $id ?: ($name ? preg_replace('/[^A-Za-z0-9_-]+/', '-', trim((string) $name, '[]')) : null);
    $canBindValue = ! in_array($type, ['password', 'file'], true);
    $oldInput = $name ? data_get(session()->get('_old_input', []), $name, old($name, $value)) : $value;
    $inputValue = $canBindValue ? ($bindOld && $name ? $oldInput : $value) : null;
    $resolvedDescribedBy = $describedBy ?: ($hasError && $inputId ? $inputId.'-error' : null);

    // Mapping des props → data-attributes pour l'init JS (obfuscation / mask)
    $dataAttrs = [];
    // Obfuscation
    if ($obfuscate) {
        $dataAttrs['data-obfuscate'] = '1';
        if (!is_null($obfuscateChar)) $dataAttrs['data-obfuscate-char'] = (string) $obfuscateChar;
        if (!is_null($obfuscateKeepEnd)) $dataAttrs['data-obfuscate-keep-end'] = (string) (int) $obfuscateKeepEnd;
    }
    // Pattern mask (hérité)
    $enableMask = $inputMask || !is_null($mask) || !is_null($customMask) || !is_null($customValidator);
    if ($enableMask) {
        $dataAttrs['data-inputmask'] = '1';
        if (!is_null($mask)) $dataAttrs['data-mask'] = (string) $mask;
        if (!is_null($maskCharPlaceholder)) $dataAttrs['data-char-placeholder'] = (string) $maskCharPlaceholder;
        if (!is_null($maskPlaceholder)) $dataAttrs['data-mask-placeholder'] = $maskPlaceholder ? 'true' : 'false';
        if (!is_null($inputPlaceholder)) $dataAttrs['data-input-placeholder'] = $inputPlaceholder ? 'true' : 'false';
        if (!is_null($clearIncomplete)) $dataAttrs['data-clear-incomplete'] = $clearIncomplete ? 'true' : 'false';
        if (!is_null($customMask)) $dataAttrs['data-custom-mask'] = (string) $customMask;
        if (!is_null($customValidator)) $dataAttrs['data-custom-validator'] = (string) $customValidator;
    }

    $inputAttributes = $attributes
        ->merge(['class' => $classes])
        ->merge($dataAttrs)
        ->merge(array_filter([
            'id' => $inputId,
            'name' => $name,
            'value' => $inputValue,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $resolvedDescribedBy,
        ], static fn ($value) => ! is_null($value)));
@endphp

<input type="{{ $type }}" @disabled($disabled) {{ $inputAttributes }} />

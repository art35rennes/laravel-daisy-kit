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

    if ($variant === 'ghost') {
        $classes .= ' input-ghost';
    }

    if ($color) {
        $classes .= ' input-'.$color;
    }

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

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
@endphp

<input type="{{ $type }}" @disabled($disabled) {{ $attributes->merge(['class' => $classes])->merge($dataAttrs) }} />



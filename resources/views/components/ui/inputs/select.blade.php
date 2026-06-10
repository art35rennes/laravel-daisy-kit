@props([
    'size' => 'md',         // xs | sm | md | lg | xl
    'variant' => null,      // null | ghost
    'color' => null,        // primary | secondary | accent | info | success | warning | error | neutral
    'disabled' => false,
    // Modes avancés
    'search' => false,              // Active le mode "search" (filtre local des options existantes)
    'autocomplete' => false,        // Active le mode "autocomplete" (requête distante)
    // Options autocomplete
    'endpoint' => null,             // URL de l'endpoint qui renvoie les options [{ value, label, disabled? }]
    'param' => 'q',                 // Nom du paramètre de recherche (par défaut: q)
    'debounce' => 500,              // Délais de debounce en ms pour la saisie
    'minChars' => 3,                // Nombre minimal de caractères avant de déclencher l'appel
    'default' => null,              // Données par défaut (array d'items {value,label,disabled?,subtitle?,avatar?}) quand vide en remote
    'fetchOnEmpty' => true,         // Si true, quand input vide en remote, on interroge endpoint avec q=''
    'placeholder' => null,          // Placeholder à utiliser pour l'input unifié (sinon 1ère option vide ou défaut)
    // Surcharge éventuelle du nom du module
    'module' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'bindOld' => true,
    'error' => null,
    'describedBy' => null,
    'options' => [],
])

@php
    $sizeMap = [
        'xs' => 'select-xs',
        'sm' => 'select-sm',
        'md' => 'select-md',
        'lg' => 'select-lg',
        'xl' => 'select-xl',
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
    $normalizedOptions = collect(is_iterable($options) ? $options : [])
        ->map(function ($option): array {
            if (is_array($option)) {
                return [
                    'value' => $option['value'] ?? $option['id'] ?? '',
                    'label' => $option['label'] ?? $option['name'] ?? $option['value'] ?? '',
                    'disabled' => (bool) ($option['disabled'] ?? false),
                ];
            }

            return [
                'value' => $option,
                'label' => $option,
                'disabled' => false,
            ];
        })
        ->values();

    // Attributs data pour initialiser le module JS quand nécessaire
    $dataAttributes = [];
    $shouldEnhance = $search || $autocomplete || $endpoint;
    if ($shouldEnhance) {
        $dataAttributes['data-module'] = $module ?: 'select';
        // Options communes
        $dataAttributes['data-debounce'] = (string) (is_numeric($debounce) ? $debounce : 500);
        $dataAttributes['data-min-chars'] = (string) (is_numeric($minChars) ? $minChars : 3);
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
@endphp

@if($shouldEnhance)
    <div class="dropdown w-full" {{ $attributes->class('w-full')->only('class')->merge($dataAttributes) }}>
        <label class="input flex w-full items-center gap-2">
            <input type="text"
                   data-role="input"
                   class="grow"
                   autocomplete="off"
                   placeholder="{{ is_string($placeholder ?? null) ? $placeholder : 'Tapez pour rechercher...' }}" />
        </label>
        <ul class="dropdown-content z-10 menu bg-base-100 rounded-box w-full shadow hidden" role="listbox" data-role="list"></ul>
        <select data-role="native" @disabled($disabled) {{ $selectAttributes->merge(['hidden' => true]) }}>
            @foreach($normalizedOptions as $option)
                <option value="{{ $option['value'] }}" @selected((string) $selectedValue === (string) $option['value']) @disabled($option['disabled'])>
                    {{ $option['label'] }}
                </option>
            @endforeach
            {{ $slotContent }}
        </select>
    </div>
@else
    <select @disabled($disabled) {{ $selectAttributes }}>
        @foreach($normalizedOptions as $option)
            <option value="{{ $option['value'] }}" @selected((string) $selectedValue === (string) $option['value']) @disabled($option['disabled'])>
                {{ $option['label'] }}
            </option>
        @endforeach
        {{ $slotContent }}
        {{-- Expecting <option> children --}}
        {{-- Example: <x-ui.select><option>One</option></x-ui.select> --}}
    </select>
@endif

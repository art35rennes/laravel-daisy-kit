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
    'debounce' => 300,              // Délais de debounce en ms pour la saisie
    'minChars' => 2,                // Nombre minimal de caractères avant de déclencher l'appel
    'default' => null,              // Données par défaut (array d'items {value,label,disabled?,subtitle?,avatar?}) quand vide en remote
    'fetchOnEmpty' => true,         // Si true, quand input vide en remote, on interroge endpoint avec q=''
    'placeholder' => null,          // Placeholder à utiliser pour l'input unifié (sinon 1ère option vide ou défaut)
    // Surcharge éventuelle du nom du module
    'module' => null,
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

    if (isset($sizeMap[$size])) {
        $classes .= ' '.$sizeMap[$size];
    }

    // Attributs data pour initialiser le module JS quand nécessaire
    $dataAttributes = [];
    $shouldEnhance = $search || $autocomplete || $endpoint;
    if ($shouldEnhance) {
        $dataAttributes['data-module'] = $module ?: 'select';
        // Options communes
        $dataAttributes['data-debounce'] = (string) (is_numeric($debounce) ? $debounce : 300);
        $dataAttributes['data-min-chars'] = (string) (is_numeric($minChars) ? $minChars : 2);
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
@endphp

@if($shouldEnhance)
    <div class="dropdown w-full" {{ $attributes->class('w-full')->only('class')->merge($dataAttributes) }}>
        <label class="input input-bordered flex items-center gap-2 w-full">
            <input type="text"
                   data-role="input"
                   class="grow"
                   autocomplete="off"
                   placeholder="{{ is_string($placeholder ?? null) ? $placeholder : 'Tapez pour rechercher...' }}" />
        </label>
        <ul class="dropdown-content z-10 menu bg-base-100 rounded-box w-full shadow hidden" role="listbox" data-role="list"></ul>
        <select data-role="native" @disabled($disabled) class="{{ $classes }}" hidden>
            {{ $slot }}
        </select>
    </div>
@else
    <select @disabled($disabled) {{ $attributes->merge(array_merge(['class' => $classes], $dataAttributes)) }}>
        {{ $slot }}
        {{-- Expecting <option> children --}}
        {{-- Example: <x-ui.select><option>One</option></x-ui.select> --}}
    </select>
@endif



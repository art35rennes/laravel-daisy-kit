@props([
    // items: tableau d'onglets
    // ex: [ ['label' => 'Tab 1', 'active' => true, 'disabled' => false, 'href' => '#', 'content' => '...'], ... ]
    'items' => [],
    // Styles: box|boxed|border|bordered|lifted
    'variant' => null,
    // Tailles: xs|sm|md|lg|xl (classe sur le conteneur)
    'size' => null,
    // Placement: top|bottom
    'placement' => 'top',
    // Mode radio + contenu juste après chaque tab
    'radioName' => null, // si fourni, rend <input type="radio" class="tab"> + <div class="tab-content"> pour chaque item
    'contentClass' => 'border-base-300 bg-base-100 p-6',
    'errorBag' => null,
])

@php
    // Construction des classes CSS selon la variante, la taille et le placement.
    $classes = 'tabs';
    if ($variant) {
        // Mapping des variantes (support des alias : box/boxed, border/bordered).
        $map = [
            'box' => 'tabs-box',
            'boxed' => 'tabs-box',
            'border' => 'tabs-border',
            'bordered' => 'tabs-border',
            'lifted' => 'tabs-lift',
        ];
        $classes .= ' '.($map[$variant] ?? '');
    }
    if (in_array($size, ['xs','sm','md','lg','xl'], true)) {
        $classes .= ' tabs-'.$size;
    }
    // Placement des onglets : top (défaut) ou bottom.
    if ($placement === 'bottom') {
        $classes .= ' tabs-bottom';
    } else {
        $classes .= ' tabs-top';
    }

    // Mode radio : si radioName est fourni, utilise des inputs radio + contenu associé (pattern daisyUI).
    $isRadio = !empty($radioName);
    $generatedRadio = $isRadio ? $radioName : null;
    // Génération d'un nom unique si radioName est null mais que le mode radio est activé.
    if (!$generatedRadio && $radioName !== null) {
        $generatedRadio = uniqid('tabs_', false);
    }
    $resolvedErrorBag = $errorBag instanceof \Illuminate\Support\ViewErrorBag
        ? $errorBag
        : (view()->shared('errors') instanceof \Illuminate\Support\ViewErrorBag ? view()->shared('errors') : new \Illuminate\Support\ViewErrorBag());
    $errorLabel = __('daisy::components.tab_error');
@endphp

@if(!$isRadio)
    {{-- Mode standard : liste d'onglets (liens ou boutons) sans contenu intégré --}}
    <div role="tablist" {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $tab)
            @php
                if (data_get($tab, 'visible', true) === false) {
                    continue;
                }
                // Extraction des propriétés de l'onglet.
                $isActive = (bool) data_get($tab, 'active', false);
                $isDisabled = (bool) data_get($tab, 'disabled', false);
                $label = data_get($tab, 'label', __('daisy::components.tab'));
                $href = data_get($tab, 'href');
                $iconName = data_get($tab, 'iconName');
                $errorKey = data_get($tab, 'errorKey');
                $hasError = is_string($errorKey) && $resolvedErrorBag->has($errorKey);
                // Construction des classes : tab de base + états (active, disabled).
                $tabClasses = 'tab'.($isActive ? ' tab-active' : '').($isDisabled ? ' tab-disabled' : '').($hasError ? ' text-error' : '');
            @endphp
            {{-- Rendu conditionnel : lien si href fourni, sinon bouton --}}
            @if($href)
                <a role="tab" href="{{ $href }}" class="{{ $tabClasses }}" aria-selected="{{ $isActive ? 'true' : 'false' }}">
                    @if($iconName)<x-icon :name="$iconName" class="w-4 h-4" />@endif
                    <span>{{ $label }}</span>
                    @if($hasError)<span aria-hidden="true">•</span><span class="sr-only">{{ $errorLabel }}</span>@endif
                </a>
            @else
                <button role="tab" class="{{ $tabClasses }}" @disabled($isDisabled) aria-selected="{{ $isActive ? 'true' : 'false' }}">
                    @if($iconName)<x-icon :name="$iconName" class="w-4 h-4" />@endif
                    <span>{{ $label }}</span>
                    @if($hasError)<span aria-hidden="true">•</span><span class="sr-only">{{ $errorLabel }}</span>@endif
                </button>
            @endif
        @endforeach
    </div>
@else
    {{-- Mode radio : chaque onglet est un input radio suivi de son contenu (pattern daisyUI tabs-box) --}}
    <div {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $index => $tab)
            @php
                if (data_get($tab, 'visible', true) === false) {
                    continue;
                }
                $label = data_get($tab, 'label', __('daisy::components.tab'));
                // L'onglet est checked s'il est explicitement actif OU si c'est le premier (index 0).
                $checked = array_key_exists('active', $tab) ? (bool) data_get($tab, 'active') : ($index === 0);
                $isDisabled = (bool) data_get($tab, 'disabled', false);
                $errorKey = data_get($tab, 'errorKey');
                $hasError = is_string($errorKey) && $resolvedErrorBag->has($errorKey);
            @endphp
            {{-- Input radio : contrôle l'affichage du contenu associé via CSS (pattern daisyUI) --}}
            <input type="radio" name="{{ $generatedRadio }}" class="tab {{ $hasError ? 'text-error' : '' }}" aria-label="{{ $label }}" @checked($checked) @disabled($isDisabled) />
            {{-- Contenu de l'onglet : visible uniquement si le radio correspondant est checked --}}
            <div class="tab-content {{ $contentClass }}">{!! $tab['content'] ?? '' !!}</div>
        @endforeach
    </div>
@endif

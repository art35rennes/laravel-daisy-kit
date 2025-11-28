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
@endphp

@if(!$isRadio)
    {{-- Mode standard : liste d'onglets (liens ou boutons) sans contenu intégré --}}
    <div role="tablist" {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $tab)
            @php
                // Extraction des propriétés de l'onglet.
                $isActive = !empty($tab['active']);
                $isDisabled = !empty($tab['disabled']);
                $label = $tab['label'] ?? 'Tab';
                $href = $tab['href'] ?? null;
                // Construction des classes : tab de base + états (active, disabled).
                $tabClasses = 'tab'.($isActive ? ' tab-active' : '').($isDisabled ? ' tab-disabled' : '');
            @endphp
            {{-- Rendu conditionnel : lien si href fourni, sinon bouton --}}
            @if($href)
                <a role="tab" href="{{ $href }}" class="{{ $tabClasses }}" aria-selected="{{ $isActive ? 'true' : 'false' }}">{!! $label !!}</a>
            @else
                <button role="tab" class="{{ $tabClasses }}" @disabled($isDisabled) aria-selected="{{ $isActive ? 'true' : 'false' }}">{!! $label !!}</button>
            @endif
        @endforeach
    </div>
@else
    {{-- Mode radio : chaque onglet est un input radio suivi de son contenu (pattern daisyUI tabs-box) --}}
    <div {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $index => $tab)
            @php
                $label = $tab['label'] ?? 'Tab';
                // L'onglet est checked s'il est explicitement actif OU si c'est le premier (index 0).
                $checked = array_key_exists('active', $tab) ? (bool) $tab['active'] : ($index === 0);
                $isDisabled = !empty($tab['disabled']);
            @endphp
            {{-- Input radio : contrôle l'affichage du contenu associé via CSS (pattern daisyUI) --}}
            <input type="radio" name="{{ $generatedRadio }}" class="tab" aria-label="{{ $label }}" @checked($checked) @disabled($isDisabled) />
            {{-- Contenu de l'onglet : visible uniquement si le radio correspondant est checked --}}
            <div class="tab-content {{ $contentClass }}">{!! $tab['content'] ?? '' !!}</div>
        @endforeach
    </div>
@endif

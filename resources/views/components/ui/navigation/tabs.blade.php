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
    $classes = 'tabs';
    if ($variant) {
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
    if ($placement === 'bottom') {
        $classes .= ' tabs-bottom';
    } else {
        $classes .= ' tabs-top';
    }

    $isRadio = !empty($radioName);
    $generatedRadio = $isRadio ? $radioName : null;
    if (!$generatedRadio && $radioName !== null) {
        $generatedRadio = uniqid('tabs_', false);
    }
@endphp

@if(!$isRadio)
    <div role="tablist" {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $tab)
            @php
                $isActive = !empty($tab['active']);
                $isDisabled = !empty($tab['disabled']);
                $label = $tab['label'] ?? 'Tab';
                $href = $tab['href'] ?? null;
                $tabClasses = 'tab'.($isActive ? ' tab-active' : '').($isDisabled ? ' tab-disabled' : '');
            @endphp
            @if($href)
                <a role="tab" href="{{ $href }}" class="{{ $tabClasses }}">{{ $label }}</a>
            @else
                <button role="tab" class="{{ $tabClasses }}" @disabled($isDisabled)>{{ $label }}</button>
            @endif
        @endforeach
    </div>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $index => $tab)
            @php
                $label = $tab['label'] ?? 'Tab';
                $checked = array_key_exists('active', $tab) ? (bool) $tab['active'] : ($index === 0);
                $isDisabled = !empty($tab['disabled']);
            @endphp
            <input type="radio" name="{{ $generatedRadio }}" class="tab" aria-label="{{ $label }}" @checked($checked) @disabled($isDisabled) />
            <div class="tab-content {{ $contentClass }}">{!! $tab['content'] ?? '' !!}</div>
        @endforeach
    </div>
@endif

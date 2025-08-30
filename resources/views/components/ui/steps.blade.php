@props([
    'items' => [],
    'current' => 0,
    'vertical' => false,
    'horizontal' => false,
    'horizontalAt' => null,
    'color' => 'primary',
    'allowClickNav' => false,
    'rootId' => null,
])

@php
    // Configuration des classes d'orientation
    $orientationClasses = match(true) {
        $horizontalAt => "steps-vertical {$horizontalAt}:steps-horizontal",
        $vertical => 'steps-vertical',
        $horizontal => 'steps-horizontal',
        default => '',
    };

    $validColors = ['neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'];
    $defaultColor = in_array($color, $validColors) ? $color : 'primary';

    // Fonction helper pour extraire les propriétés d'un item
    $extractItemData = function($item, $index) use ($defaultColor, $current, $validColors, $allowClickNav, $rootId) {
        $data = [
            'label' => is_array($item) ? ($item['label'] ?? '') : (string) $item,
            'icon' => is_array($item) ? ($item['icon'] ?? null) : null,
            'disabled' => is_array($item) ? (bool) ($item['disabled'] ?? false) : false,
            'invalid' => is_array($item) ? (bool) ($item['invalid'] ?? false) : false,
            'stepIndex' => is_array($item) ? ($item['index'] ?? ($index + 1)) : ($index + 1),
        ];
        
        $data['isDone'] = ($data['stepIndex'] <= $current) && !$data['disabled'];
        
        // Gestion des couleurs
        $itemColor = is_array($item) ? ($item['color'] ?? null) : null;
        if ($itemColor && in_array($itemColor, $validColors)) {
            $data['colorClass'] = "step-{$itemColor}";
        } elseif ($data['isDone']) {
            $data['colorClass'] = "step-{$defaultColor}";
        } else {
            $data['colorClass'] = '';
        }
        
        // Classes CSS
        $classes = ['step'];
        if ($data['colorClass']) $classes[] = $data['colorClass'];
        if ($data['invalid']) $classes[] = 'step-error';
        if ($data['disabled']) $classes[] = 'pointer-events-none opacity-50';
        $data['classes'] = implode(' ', $classes);
        
        // Attributs d'accessibilité
        $data['attributes'] = [];
        if ($allowClickNav && !$data['disabled']) {
            $data['attributes']['tabindex'] = '0';
            $data['attributes']['role'] = 'button';
        }
        if ($rootId) {
            $data['attributes']['id'] = "{$rootId}-header-{$data['stepIndex']}";
            $data['attributes']['aria-controls'] = "{$rootId}-panel-{$data['stepIndex']}";
        }
        $data['attributes']['data-step-index'] = $data['stepIndex'];
        
        return $data;
    };
@endphp

<ul {{ $attributes->merge(['class' => "steps {$orientationClasses}"]) }}>
    @foreach($items as $index => $item)
        @php $stepData = $extractItemData($item, $index); @endphp
        
        <li class="{{ $stepData['classes'] }}" 
            @foreach($stepData['attributes'] as $attr => $value)
                {{ $attr }}="{{ $value }}"
            @endforeach>
            
            @if($stepData['icon'])
                <span class="step-icon">{!! $stepData['icon'] !!}</span>
            @endif
            
            {{ $stepData['label'] }}
        </li>
    @endforeach
</ul>
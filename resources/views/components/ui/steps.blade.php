@props([
    // items: tableau de chaînes ou d'objets
    // ex: ['Register', 'Choose plan'] ou
    // [ ['label' => 'Register', 'icon' => '😕', 'color' => 'neutral'], ...]
    'items' => [],
    // index 1-based des étapes complétées (les index <= current sont marqués)
    'current' => 0,
    // Orientation
    'vertical' => false,          // force steps-vertical
    'horizontal' => false,        // force steps-horizontal
    'horizontalAt' => null,       // ex: 'lg' → steps-vertical lg:steps-horizontal
    // Couleur par défaut des étapes complétées si aucune couleur par item fournie
    'color' => 'primary',         // neutral|primary|secondary|accent|info|success|warning|error
])

@php
    $classes = 'steps';
    // Orientation
    if ($horizontalAt) {
        $classes .= ' steps-vertical '.($horizontalAt).':steps-horizontal';
    } elseif ($vertical) {
        $classes .= ' steps-vertical';
    } elseif ($horizontal) {
        $classes .= ' steps-horizontal';
    }

    $validColors = ['neutral','primary','secondary','accent','info','success','warning','error'];
    $defaultDoneColor = in_array($color, $validColors, true) ? $color : 'primary';
@endphp

<ul {{ $attributes->merge(['class' => $classes]) }}>
    @foreach($items as $index => $item)
        @php
            $isDone = ($index + 1) <= $current;
            $label = is_array($item) ? ($item['label'] ?? '') : $item;
            $icon = is_array($item) ? ($item['icon'] ?? null) : null;
            $itemColor = is_array($item) ? ($item['color'] ?? null) : null;
            $colorClass = '';
            if ($itemColor && in_array($itemColor, $validColors, true)) {
                $colorClass = ' step-'.$itemColor;
            } elseif ($isDone) {
                $colorClass = ' step-'.$defaultDoneColor;
            }
        @endphp
        <li class="step{{ $colorClass }}">
            @if(!is_null($icon))
                <span class="step-icon">{!! $icon !!}</span>
            @endif
            {{ $label }}
        </li>
    @endforeach
    </ul>

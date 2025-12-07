@props([
    /**
     * Position du menu flottant
     * left|right|top|bottom
     */
    'position' => 'left',
    /**
     * Orientation des boutons
     * vertical|horizontal
     */
    'orientation' => 'vertical',
    /**
     * Taille des boutons
     * xs|sm|md|lg|xl
     */
    'buttonSize' => 'md',
    /**
     * Variant des boutons
     * solid|outline|ghost|link|soft|dash
     */
    'buttonVariant' => 'ghost',
    /**
     * Couleur des boutons
     * primary|secondary|accent|info|success|warning|error|neutral
     */
    'buttonColor' => null,
    /**
     * Groupes de boutons
     * [
     *   [
     *     'items' => [
     *       ['icon' => 'pencil', 'label' => 'Edit', 'active' => false, 'href' => '#', 'onclick' => '...'],
     *       ['icon' => 'eye', 'label' => 'Preview', 'active' => true],
     *     ]
     *   ],
     *   [
     *     'items' => [...]
     *   ]
     * ]
     */
    'groups' => [],
    /**
     * Espacement entre les groupes (en rem)
     */
    'groupSpacing' => 1.5,
    /**
     * Espacement entre les boutons dans un groupe (en rem)
     */
    'itemSpacing' => 0.5,
    /**
     * Fond du menu
     */
    'bg' => true,
    /**
     * Bordure arrondie
     */
    'rounded' => true,
    /**
     * Ombre
     */
    'shadow' => true,
    /**
     * Padding du conteneur
     */
    'padding' => 'p-2',
])

@php
    // Classes de position
    $positionClasses = match($position) {
        'left' => 'left-4 top-1/2 -translate-y-1/2',
        'right' => 'right-4 top-1/2 -translate-y-1/2',
        'top' => 'top-4 left-1/2 -translate-x-1/2',
        'bottom' => 'bottom-4 left-1/2 -translate-x-1/2',
        default => 'left-4 top-1/2 -translate-y-1/2',
    };

    // Classes de flex selon l'orientation
    $flexClasses = $orientation === 'horizontal' 
        ? 'flex-row' 
        : 'flex-col';

    // Classes de conteneur
    $containerClasses = 'fixed z-50 flex ' . $flexClasses;
    
    if ($bg) {
        $containerClasses .= ' bg-base-100';
    }
    
    if ($rounded) {
        $containerClasses .= ' rounded-box';
    }
    
    if ($shadow) {
        $containerClasses .= ' shadow';
    }

    $containerClasses .= ' ' . $padding;
@endphp

<div {{ $attributes->merge(['class' => $containerClasses . ' ' . $positionClasses]) }}>
    @forelse($groups as $groupIndex => $group)
        <div 
            class="flex {{ $flexClasses }}"
            style="gap: {{ $itemSpacing }}rem;"
        >
            @foreach($group['items'] ?? [] as $item)
                @php
                    $isActive = $item['active'] ?? false;
                    $icon = $item['icon'] ?? null;
                    $label = $item['label'] ?? null;
                    $href = $item['href'] ?? null;
                    $onclick = $item['onclick'] ?? null;
                    $tag = $href ? 'a' : 'button';
                    
                    // Construction des classes de bouton
                    $buttonClasses = 'btn btn-' . $buttonSize . ' btn-' . $buttonVariant;
                    if ($isActive) {
                        $buttonClasses .= ' btn-active';
                    }
                    if ($buttonColor) {
                        $buttonClasses .= ' btn-' . $buttonColor;
                    }
                    $buttonClasses .= ' btn-square';
                @endphp

                <{{ $tag }}
                    @if($href) href="{{ $href }}" @endif
                    @if($onclick) onclick="{{ $onclick }}" @endif
                    @if($tag === 'button') type="button" @endif
                    class="{{ $buttonClasses }}"
                    @if($label) aria-label="{{ $label }}" title="{{ $label }}" @endif
                >
                    @if($icon)
                        <x-daisy::ui.advanced.icon :name="$icon" :size="$buttonSize" />
                    @endif
                </{{ $tag }}>
            @endforeach
        </div>

        @if($groupIndex < count($groups) - 1)
            @php
                $dividerDirection = $orientation === 'horizontal' ? 'vertical' : 'horizontal';
                $dividerMargin = $orientation === 'horizontal' ? '0 ' . $groupSpacing . 'rem' : $groupSpacing . 'rem 0';
            @endphp
            <div 
                class="divider divider-{{ $dividerDirection }}"
                style="margin: {{ $dividerMargin }};"
            ></div>
        @endif
    @empty
        {{ $slot }}
    @endforelse
</div>


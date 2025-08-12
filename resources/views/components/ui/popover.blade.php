@props([
    // Déclencheur: click | hover | focus
    'trigger' => 'click',
    // Position: top | right | bottom | left
    'position' => 'top',
    // Ouverture forcée par défaut (sera fermé par JS si interactions)
    'open' => false,
    // Titre simple si pas de slot header
    'title' => null,
    // Classe du panneau (taille)
    'panelClass' => 'w-64',
    // Afficher une flèche directionnelle
    'arrow' => false,
])

@php
    $rootAttrs = $attributes->class('relative inline-block');
    $posMap = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    ];
    $panelPos = $posMap[$position] ?? $posMap['top'];
    $panelHidden = $open ? '' : 'hidden';
@endphp

<span {{ $rootAttrs->merge([
    'data-popover' => true,
    'data-trigger' => $trigger,
    'data-position' => $position,
]) }}>
    <span class="popover-trigger inline-flex items-center" tabindex="0">
        {{ $triggerSlot ?? ($triggerContent ?? $slot) }}
    </span>
    <div class="popover-panel absolute z-50 {{ $panelPos }} {{ $panelHidden }}">
        <div class="rounded-box bg-base-100 shadow border border-base-200 p-4 {{ $panelClass }}">
            @if($arrow)
                @php
                    $arrowBase = 'absolute w-3 h-3 rotate-45 bg-base-100 border border-base-200';
                    $arrowPos = [
                        'top' => 'left-1/2 -translate-x-1/2 -bottom-1 border-t-0 border-l-0',
                        'right' => '-left-1 -translate-y-1/2 top-1/2 border-t-0 border-r-0',
                        'bottom' => 'left-1/2 -translate-x-1/2 -top-1 border-b-0 border-r-0',
                        'left' => '-right-1 -translate-y-1/2 top-1/2 border-b-0 border-l-0',
                    ][$position] ?? 'left-1/2 -translate-x-1/2 -bottom-1 border-t-0 border-l-0';
                @endphp
                <span class="{{ $arrowBase }} {{ $arrowPos }}"></span>
            @endif

            @if(!empty($title) || isset($header))
                <div class="mb-2 font-medium text-base-content/90">
                    @isset($header)
                        {{ $header }}
                    @else
                        {{ $title }}
                    @endisset
                </div>
            @endif

            <div class="text-sm leading-relaxed">
                @isset($content)
                    {{ $content }}
                @else
                    {{ $slot }}
                @endisset
            </div>

            @isset($footer)
                <div class="mt-3 pt-3 border-t border-base-200">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</span>



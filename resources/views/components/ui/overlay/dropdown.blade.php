@props([
    'label' => null,
    'end' => false,
    'hover' => false,
    // Classes du bouton trigger (par défaut adapté à la navbar)
    'buttonClass' => 'btn btn-ghost',
    // Afficher le trigger en bouton circulaire (utile en navbar pour avatar/icone)
    'buttonCircle' => false,
    // Type de contenu: 'menu' (UL/menu) ou 'card' (helper dropdown)
    'type' => 'menu', // menu | card
    // Classes du contenu (prioritaire). Si null, on déduit selon type
    'contentClass' => null,
    // Compat: classes du menu (héritage v1)
    'menuClass' => 'menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52',
    // Classes pour le helper dropdown (carte)
    'cardClass' => 'card card-sm dropdown-content bg-base-100 rounded-box z-[1] w-64 shadow',
    'cardBodyClass' => 'card-body',
])

@php
    // Construction des classes CSS selon les options (placement, hover).
    $root = 'dropdown';
    // Placement : dropdown-end pour aligner à droite (défaut : gauche).
    if ($end) $root .= ' dropdown-end';
    // Mode hover : ouverture au survol au lieu du clic (dropdown-hover).
    if ($hover) $root .= ' dropdown-hover';

    // Déduction des classes de contenu selon le type si non fourni explicitement.
    $resolvedContentClass = $contentClass ?? ($type === 'card' ? $cardClass : $menuClass);
@endphp

{{-- Dropdown : menu déroulant ou carte (pattern daisyUI) --}}
<div {{ $attributes->merge(['class' => $root]) }}>
    {{-- Trigger : bouton qui ouvre le dropdown (clic ou hover selon configuration) --}}
    <div tabindex="0" role="button" class="{{ $buttonClass }}{{ $buttonCircle ? ' btn-circle' : '' }}">
        @isset($trigger)
            {{ $trigger }}
        @else
            {{ $label ?? 'Open' }}
        @endisset
    </div>
    @if($type === 'card')
        {{-- Type card : dropdown avec structure de carte (utile pour des contenus complexes) --}}
        <div tabindex="0" class="{{ $resolvedContentClass }}">
            <div class="{{ $cardBodyClass }}">
                @isset($content)
                    {{ $content }}
                @else
                    {{ $slot }}
                @endisset
            </div>
        </div>
    @else
        {{-- Type menu (défaut) : dropdown avec structure de menu (liste d'items) --}}
        <ul tabindex="0" class="{{ $resolvedContentClass }}">
            @isset($content)
                {{ $content }}
            @else
                {{ $slot }}
            @endisset
        </ul>
    @endif
 </div>



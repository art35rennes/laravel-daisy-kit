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
    $root = 'dropdown';
    if ($end) $root .= ' dropdown-end';
    if ($hover) $root .= ' dropdown-hover';

    // Déduction des classes de contenu selon le type si non fourni
    $resolvedContentClass = $contentClass ?? ($type === 'card' ? $cardClass : $menuClass);
@endphp

<div {{ $attributes->merge(['class' => $root]) }}>
    <div tabindex="0" role="button" class="{{ $buttonClass }}{{ $buttonCircle ? ' btn-circle' : '' }}">
        @isset($trigger)
            {{ $trigger }}
        @else
            {{ $label ?? 'Open' }}
        @endisset
    </div>
    @if($type === 'card')
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
        <ul tabindex="0" class="{{ $resolvedContentClass }}">
            @isset($content)
                {{ $content }}
            @else
                {{ $slot }}
            @endisset
        </ul>
    @endif
 </div>



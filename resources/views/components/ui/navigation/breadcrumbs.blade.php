@props([
    'items' => [], // [['label' => 'Home', 'href' => '/', 'icon' => '<svg.../>'], ...]
    // Taille du texte: sm|md|lg (utilise text-*)
    'size' => 'sm',
    // Balise wrapper: div|nav
    'as' => 'div',
    // aria-label si as=nav
    'label' => 'Breadcrumb',
])

@php
    // Mapping des tailles vers les classes Tailwind text-*.
    $sizeMap = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];
    $wrapperClasses = 'breadcrumbs '.($sizeMap[$size] ?? 'text-sm');
    // Choix du tag wrapper : nav pour accessibilité (avec aria-label), div par défaut.
    $tag = in_array($as, ['div','nav'], true) ? $as : 'div';
@endphp

{{-- Breadcrumbs : fil d'Ariane pour la navigation (pattern daisyUI) --}}
<{{ $tag }} @if($tag==='nav') aria-label="{{ $label }}" @endif {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    <ul>
        @foreach($items as $item)
            <li>
                @php $hasIcon = !empty($item['icon']); @endphp
                {{-- Item cliquable : lien si href fourni --}}
                @if(!empty($item['href']))
                    <a href="{{ $item['href'] }}" class="{{ $hasIcon ? 'inline-flex items-center gap-2' : '' }}">
                        @if($hasIcon){!! $item['icon'] !!}@endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @else
                    {{-- Item actuel : span avec style font-medium (non cliquable) --}}
                    <span class="inline-flex items-center gap-2 font-medium">
                        @if($hasIcon){!! $item['icon'] !!}@endif
                        <span>{{ $item['label'] }}</span>
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
 </{{ $tag }}>



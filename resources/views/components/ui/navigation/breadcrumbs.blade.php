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
    $sizeMap = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];
    $wrapperClasses = 'breadcrumbs '.($sizeMap[$size] ?? 'text-sm');
    $tag = in_array($as, ['div','nav'], true) ? $as : 'div';
@endphp

<{{ $tag }} @if($tag==='nav') aria-label="{{ $label }}" @endif {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    <ul>
        @foreach($items as $item)
            <li>
                @php $hasIcon = !empty($item['icon']); @endphp
                @if(!empty($item['href']))
                    <a href="{{ $item['href'] }}" class="{{ $hasIcon ? 'inline-flex items-center gap-2' : '' }}">
                        @if($hasIcon){!! $item['icon'] !!}@endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 font-medium">
                        @if($hasIcon){!! $item['icon'] !!}@endif
                        <span>{{ $item['label'] }}</span>
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
 </{{ $tag }}>



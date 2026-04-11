@props([
    'as' => 'figure',
    'maxWidth' => 'max-w-60',
    'images' => [],
])

@php
    $tag = in_array($as, ['figure', 'div'], true) ? $as : 'figure';
    $baseClass = trim('hover-gallery'.(($maxWidth ?? '') !== '' ? ' '.$maxWidth : ''));
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $baseClass]) }}>
    @if(count($images) > 0)
        @foreach($images as $image)
            @php
                $src = is_array($image) ? ($image['src'] ?? '') : $image;
                $alt = is_array($image) ? ($image['alt'] ?? '') : '';
            @endphp
            @if($src !== '')
                <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" />
            @endif
        @endforeach
    @else
        {{ $slot }}
    @endif
</{{ $tag }}>

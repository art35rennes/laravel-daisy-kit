@props([
    // type d'élément embarqué: iframe|video|object|embed|div (fallback)
    'tag' => 'iframe',
    // ratio prédéfini: 1x1|4x3|16x9|21x9 ou null
    'ratio' => '16x9',
    // ratio personnalisé via pourcentage (ex: '50%' => 2x1)
    'ratioPercent' => null,
    // classes supplémentaires appliquées au wrapper
    'wrapperClass' => '',
    // attributs HTML spécifiques (src, allow, allowfullscreen, etc.)
    'src' => null,
    'allow' => null,
    'allowfullscreen' => true,
])

@php
    // wrapper avec aspect-ratio via variable CSS
    $style = '';
    if (!empty($ratioPercent)) {
        $style = "--ar: {$ratioPercent};";
    } elseif ($ratio) {
        $map = [
            '1x1' => '100%',
            '4x3' => 'calc(3/4*100%)',
            '16x9' => 'calc(9/16*100%)',
            '21x9' => 'calc(9/21*100%)',
        ];
        $style = isset($map[$ratio]) ? "--ar: {$map[$ratio]};" : '';
    }

    $wrapperClasses = trim('relative w-full '.$wrapperClass);
@endphp

<div {{ $attributes->merge(['class' => $wrapperClasses]) }} style="{{ $style }}">
    <div class="block w-full" style="position:relative; padding-bottom: var(--ar, 56.25%); height:0;">
        <div class="absolute inset-0">
            @if($tag === 'iframe')
                <iframe src="{{ $src }}" class="h-full w-full" @if($allow) allow="{{ $allow }}" @endif @if($allowfullscreen) allowfullscreen @endif></iframe>
            @elseif($tag === 'video')
                <video class="h-full w-full" @if($src) src="{{ $src }}" @endif controls></video>
            @elseif($tag === 'object')
                <object class="h-full w-full" @if($src) data="{{ $src }}" @endif></object>
            @elseif($tag === 'embed')
                <embed class="h-full w-full" @if($src) src="{{ $src }}" @endif />
            @else
                <div class="h-full w-full">{{ $slot }}</div>
            @endif
        </div>
    </div>
</div>



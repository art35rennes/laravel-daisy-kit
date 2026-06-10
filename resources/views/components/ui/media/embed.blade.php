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
    $ratioClass = null;

    if (!empty($ratioPercent)) {
        $ratioPercentValue = trim((string) $ratioPercent);

        if (preg_match('/^(\d+(?:\.\d+)?)%$/', $ratioPercentValue, $matches) === 1) {
            $ratioToken = (int) round((float) $matches[1]);
            $ratioClass = $ratioToken >= 1 && $ratioToken <= 300 ? 'daisy-embed-ratio-percent-'.$ratioToken : null;
        }
    } elseif ($ratio) {
        $ratioClasses = [
            '1x1' => 'daisy-embed-ratio-1x1',
            '4x3' => 'daisy-embed-ratio-4x3',
            '16x9' => 'daisy-embed-ratio-16x9',
            '21x9' => 'daisy-embed-ratio-21x9',
        ];
        $ratioClass = $ratioClasses[$ratio] ?? null;
    }

    $wrapperClasses = trim('relative w-full '.$ratioClass.' '.$wrapperClass);
@endphp

<div {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    <div class="daisy-embed-aspect block w-full">
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

@props([
    // images: [['src' => 'large.jpg', 'thumb' => 'thumb.jpg', 'alt' => '...'], ...]
    'images' => [],
    // Activation des vignettes: click | mouseenter
    'activation' => 'click',
    // Effet de zoom sur l'image principale au survol
    'zoomEffect' => false,
    // Forcer la hauteur auto sur l'image principale
    'autoHeight' => false,
    // Position des vignettes: bottom | right | top | left
    'position' => 'bottom',
    // Nombre d'items visibles (style) pour les vignettes (affecte la taille)
    'thumbsPerRow' => 4,
    // Aspect ratio du conteneur principal quand autoHeight = false (ex: aspect-video)
    'aspectClass' => 'aspect-video',
])

@php
    $id = $attributes->get('id') ?? 'media-gallery-'.uniqid();
    $imgs = collect($images ?? [])->map(function($it){
        return [
            'src' => is_array($it) ? ($it['src'] ?? '') : (string)$it,
            'thumb' => is_array($it) ? ($it['thumb'] ?? ($it['src'] ?? '')) : (string)$it,
            'alt' => is_array($it) ? ($it['alt'] ?? '') : '',
        ];
    })->values()->all();

    $vertical = in_array($position, ['left','right'], true);
    $thumbWrapClasses = $vertical ? 'flex flex-col gap-2 w-24' : 'grid gap-2 mt-2 grid-cols-'.$thumbsPerRow;
@endphp

<div id="{{ $id }}"
     data-media-gallery="1"
     data-activation="{{ $activation }}"
     data-zoom="{{ $zoomEffect ? 'true' : 'false' }}"
     data-auto-height="{{ $autoHeight ? 'true' : 'false' }}"
     data-position="{{ $position }}"
      {{ $attributes->merge(['class' => 'media-gallery block']) }}>
    <div class="flex gap-3 @if($position==='right') flex-row-reverse @elseif($position==='left') flex-row @elseif($position==='top') flex-col-reverse @else flex-col @endif">
        @if($vertical)
            <div class="{{ $thumbWrapClasses }}" data-thumbs>
                @foreach($imgs as $i => $img)
                    <button type="button" class="rounded-box overflow-hidden border hover:border-primary @if($i===0) border-primary @else border-base-300 @endif" data-thumb data-index="{{ $i }}">
                        <img src="{{ $img['thumb'] }}" alt="{{ $img['alt'] }}" class="object-cover w-full h-20" loading="lazy" />
                    </button>
                @endforeach
            </div>
        @endif

        <div class="flex-1">
            <div class="relative rounded-box overflow-hidden bg-base-200 @if(!$autoHeight) {{ $aspectClass }} @endif" data-main-wrapper>
                <img data-main src="{{ $imgs[0]['src'] ?? '' }}" alt="{{ $imgs[0]['alt'] ?? '' }}" class="w-full h-auto @if(!$autoHeight) absolute inset-0 h-full object-contain @endif" />
            </div>

            @if(!$vertical)
                <div class="{{ $thumbWrapClasses }}" data-thumbs>
                    @foreach($imgs as $i => $img)
                        <button type="button" class="rounded-box overflow-hidden border hover:border-primary @if($i===0) border-primary @else border-base-300 @endif" data-thumb data-index="{{ $i }}">
                            <img src="{{ $img['thumb'] }}" alt="{{ $img['alt'] }}" class="object-cover w-full h-20" loading="lazy" />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        @if($vertical && $position==='right')
            <!-- already rendered above for vertical/right alignment -->
        @endif
    </div>

    <template data-items>{{ json_encode($imgs) }}</template>
</div>

@include('daisy::components.partials.assets')
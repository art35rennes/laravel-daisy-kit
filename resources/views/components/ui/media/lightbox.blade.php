@props([
    // Album d'images: [['src' => '...', 'thumb' => '...', 'alt' => '...', 'caption' => '...']]
    'images' => [],
    // Mise en page du grid
    'cols' => 'grid-cols-3', // ex: 'sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
    'gap' => 'gap-3',
    // Options de viewer
    'loop' => true,
    'keyboard' => true,
    'zoom' => true,
    'fullscreen' => true,
    // Texte/labels
    'labelClose' => 'Fermer',
    'labelPrev' => 'Précédent',
    'labelNext' => 'Suivant',
    'labelZoomIn' => 'Zoom +',
    'labelZoomOut' => 'Zoom −',
    'labelZoomReset' => 'Réinitialiser le zoom',
    'labelFullscreen' => 'Plein écran',
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $id = $attributes->get('id') ?? 'lightbox-'.uniqid();
    $imgs = collect($images ?? [])->map(function($it){
        return [
            'src' => is_array($it) ? ($it['src'] ?? '') : (string)$it,
            'thumb' => is_array($it) ? ($it['thumb'] ?? ($it['src'] ?? '')) : (string)$it,
            'alt' => is_array($it) ? ($it['alt'] ?? '') : '',
            'caption' => is_array($it) ? ($it['caption'] ?? '') : '',
        ];
    })->values()->all();
@endphp

<div id="{{ $id }}" data-module="{{ $module ?? 'lightbox' }}" data-lightbox="1" data-loop="{{ $loop ? 'true' : 'false' }}" data-keyboard="{{ $keyboard ? 'true' : 'false' }}" data-zoom="{{ $zoom ? 'true' : 'false' }}" data-fullscreen="{{ $fullscreen ? 'true' : 'false' }}" class="space-y-2">
    <div class="grid {{ $cols }} {{ $gap }}">
        @foreach($imgs as $i => $img)
            <button type="button" class="group relative overflow-hidden rounded-box aspect-video bg-base-200" data-index="{{ $i }}" data-item>
                <img src="{{ $img['thumb'] }}" alt="{{ $img['alt'] }}" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]" loading="lazy" />
                @if(!empty($img['caption']))
                    <div class="absolute inset-x-0 bottom-0 bg-black/40 text-white text-xs px-2 py-1 truncate">{{ $img['caption'] }}</div>
                @endif
            </button>
        @endforeach
    </div>

    <!-- Viewer overlay -->
    <div class="fixed inset-0 z-50 hidden" data-overlay role="dialog" aria-modal="true" aria-label="Lightbox" aria-hidden="true">
        <div class="absolute inset-0 bg-base-content/90"></div>
        <div class="relative h-full w-full flex items-center justify-center">
            <!-- Toolbar top-left: close -->
            <div class="absolute top-3 left-3 flex items-center gap-2">
                <button type="button" class="btn btn-sm btn-ghost" data-close title="{{ $labelClose }}" aria-label="{{ $labelClose }}">
                    <x-bi-x class="size-5" />
                </button>
            </div>

            <!-- Arrows -->
            <div class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2">
                <button type="button" class="btn btn-circle btn-ghost" data-prev title="{{ $labelPrev }}" aria-label="{{ $labelPrev }}">
                    <x-bi-chevron-left class="size-6" />
                </button>
            </div>
            <div class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2">
                <button type="button" class="btn btn-circle btn-ghost" data-next title="{{ $labelNext }}" aria-label="{{ $labelNext }}">
                    <x-bi-chevron-right class="size-6" />
                </button>
            </div>

            <!-- Stage -->
            <div class="relative max-w-[96vw] max-h-[86vh] w-[96vw] h-[86vh] select-none" data-stage>
                <img data-image alt="" class="absolute inset-0 m-auto max-w-full max-h-full object-contain will-change-transform" />
                <div class="absolute bottom-0 left-0 right-0 text-center text-base-100/90 bg-base-100/5 py-2 px-3">
                    <div class="text-sm" data-caption></div>
                </div>
            </div>

            <!-- Toolbar bottom -->
            <div class="absolute bottom-3 inset-x-0 flex items-center justify-center gap-2">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm" data-zoom-in title="{{ $labelZoomIn }}" aria-label="{{ $labelZoomIn }}">
                        <x-bi-plus class="size-5" />
                    </button>
                    <button type="button" class="btn btn-sm" data-zoom-out title="{{ $labelZoomOut }}" aria-label="{{ $labelZoomOut }}">
                        <x-bi-dash class="size-5" />
                    </button>
                    <button type="button" class="btn btn-sm" data-zoom-reset title="{{ $labelZoomReset }}" aria-label="{{ $labelZoomReset }}">
                        <x-bi-arrow-clockwise class="size-5" />
                    </button>
                    <button type="button" class="btn btn-sm" data-fullscreen title="{{ $labelFullscreen }}" aria-label="{{ $labelFullscreen }}">
                        <x-bi-arrows-fullscreen class="size-5" />
                    </button>
                </div>
                <div class="ml-3 text-xs text-base-100/80" data-counter>1 / 1</div>
            </div>
        </div>
        <template data-items>{{ json_encode($imgs) }}</template>
    </div>
</div>

@include('daisy::components.partials.assets')

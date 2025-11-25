@props([
    // Taille du canvas (largeur)
    'width' => 400,
    // Hauteur du canvas
    'height' => 200,
    // Couleur de l'encre
    'penColor' => '#000000',
    // Épaisseur du trait
    'minWidth' => 0.5,
    'maxWidth' => 2.5,
    // Vitesse de dessin (0-1)
    'velocityFilterWeight' => 0.7,
    // Mode responsive (ajuste automatiquement la taille)
    'responsive' => true,
    // Désactivé
    'disabled' => false,
    // Afficher les boutons d'action (effacer, télécharger)
    'showActions' => true,
    // Label pour le bouton effacer
    'clearLabel' => null,
    // Label pour le bouton télécharger
    'downloadLabel' => null,
    // Format de téléchargement (png, jpg, svg)
    'downloadFormat' => 'png',
    // Nom du fichier de téléchargement
    'downloadFilename' => 'signature',
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $id = $attributes->get('id') ?? 'sign-'.uniqid();
    $clearLabel = $clearLabel ?? __('common.clear');
    $downloadLabel = $downloadLabel ?? __('common.download');
@endphp

<div id="{{ $id }}" 
     data-module="{{ $module ?? 'sign' }}" 
     data-sign="1"
     data-width="{{ (int)$width }}"
     data-height="{{ (int)$height }}"
     data-pen-color="{{ $penColor }}"
     data-min-width="{{ (float)$minWidth }}"
     data-max-width="{{ (float)$maxWidth }}"
     data-velocity-filter-weight="{{ (float)$velocityFilterWeight }}"
     data-responsive="{{ $responsive ? 'true' : 'false' }}"
     data-disabled="{{ $disabled ? 'true' : 'false' }}"
     data-show-actions="{{ $showActions ? 'true' : 'false' }}"
     data-clear-label="{{ $clearLabel }}"
     data-download-label="{{ $downloadLabel }}"
     data-download-format="{{ $downloadFormat }}"
     data-download-filename="{{ $downloadFilename }}"
     {{ $attributes->merge(['class' => 'sign-container']) }}>
    
    <div class="card card-border bg-base-100">
        <div class="card-body p-4">
            <div class="relative w-full overflow-hidden rounded-box border border-base-300 bg-base-200" 
                 data-sign-canvas-wrapper
                 style="max-width: 100%;">
                <canvas data-sign-canvas 
                        class="w-full h-auto touch-none"
                        style="display: block; max-width: 100%; height: auto;"></canvas>
            </div>
            
            @if($showActions)
                <div class="card-actions justify-end mt-4 gap-2 flex-wrap">
                    <button type="button" 
                            class="btn btn-sm btn-ghost" 
                            data-sign-clear
                            @disabled($disabled)>
                        <x-bi-eraser class="size-4" />
                        {{ $clearLabel }}
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-primary" 
                            data-sign-download
                            @disabled($disabled)>
                        <x-bi-download class="size-4" />
                        {{ $downloadLabel }}
                    </button>
                </div>
            @endif
            
            <input type="hidden" 
                   name="{{ $attributes->get('name', 'signature') }}" 
                   data-sign-input
                   value="" />
        </div>
    </div>
</div>

@include('daisy::components.partials.assets')


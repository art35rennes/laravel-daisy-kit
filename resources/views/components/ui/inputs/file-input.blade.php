@props([
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'multiple' => false,
    'accept' => null,
    'disabled' => false,
    // Drag & Drop + Preview
    'dragdrop' => false,
    'preview' => false,
    // Taille max de la zone (classes)
    'dropZoneClass' => 'border border-dashed rounded-box p-4',
    'dropzoneText' => null,
    'helpText' => null,
    'browseText' => null,
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $sizeMap = [
        'xs' => 'file-input-xs',
        'sm' => 'file-input-sm',
        'md' => 'file-input-md',
        'lg' => 'file-input-lg',
        'xl' => 'file-input-xl',
    ];

    $classes = 'file-input w-full';
    if ($variant === 'ghost') $classes .= ' file-input-ghost';
    if ($color) $classes .= ' file-input-'.$color;
    if (isset($sizeMap[$size])) $classes .= ' '.$sizeMap[$size];

    $isMultiple = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
    $dropzoneText ??= $isMultiple
        ? 'Glissez-déposez vos fichiers ici'
        : 'Glissez-déposez votre fichier ici';
    $helpText ??= $isMultiple
        ? 'Vous pouvez sélectionner plusieurs fichiers.'
        : 'Un seul fichier sera conservé.';
    $browseText ??= 'Parcourir';

    $inputAttributes = $attributes;
    if ($accept !== null && ! $inputAttributes->has('accept')) {
        $inputAttributes = $inputAttributes->merge(['accept' => $accept]);
    }
@endphp
@php
    $id = $attributes->get('id') ?? 'file-'.uniqid();
@endphp

@if(!$dragdrop && !$preview)
    <input type="file" id="{{ $id }}" @multiple($isMultiple) @disabled($disabled) {{ $inputAttributes->merge(['class' => $classes]) }} />
@else
    <div id="{{ $id }}-wrap" data-module="{{ $module ?? 'file-input' }}" data-fileinput="1" data-preview="{{ $preview ? 'true' : 'false' }}" data-multiple="{{ $isMultiple ? 'true' : 'false' }}" class="space-y-2">
        <input type="file" id="{{ $id }}" @multiple($isMultiple) @disabled($disabled) {{ $inputAttributes->merge(['class' => $classes.' hidden']) }} />
        <div class="{{ $dropZoneClass }} bg-base-100 flex flex-col items-center justify-center gap-2 text-center text-sm" data-dropzone>
            <div class="flex items-center justify-center gap-2">
                <x-bi-cloud-arrow-up class="size-5 opacity-70" />
                <span class="opacity-70">{{ $dropzoneText }}</span>
            </div>
            <span class="btn btn-ghost btn-xs">{{ $browseText }}</span>
            @if($helpText)
                <span class="text-xs text-base-content/60">{{ $helpText }}</span>
            @endif
        </div>
        @if($preview)
            <div class="{{ $isMultiple ? 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4' : 'grid-cols-1 max-w-sm' }} grid gap-2" data-previews></div>
        @endif
    </div>
@endif

@include('daisy::components.partials.assets')

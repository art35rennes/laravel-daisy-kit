@props([
    'size' => 'md', // xs|sm|md|lg|xl
    'variant' => null, // null|ghost
    'color' => null, // primary|secondary|accent|info|success|warning|error|neutral
    'multiple' => false,
    'disabled' => false,
    // Drag & Drop + Preview
    'dragdrop' => false,
    'preview' => false,
    // Taille max de la zone (classes)
    'dropZoneClass' => 'border-2 border-dashed border-base-300 rounded-box p-4',
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
@endphp
@php
    $id = $attributes->get('id') ?? 'file-'.uniqid();
@endphp

@if(!$dragdrop && !$preview)
    <input type="file" id="{{ $id }}" @multiple($multiple) @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }} />
@else
    <div id="{{ $id }}-wrap" data-fileinput="1" data-preview="{{ $preview ? 'true' : 'false' }}" data-multiple="{{ $multiple ? 'true' : 'false' }}" class="space-y-2">
        <input type="file" id="{{ $id }}" @multiple($multiple) @disabled($disabled) {{ $attributes->merge(['class' => $classes.' hidden']) }} />
        <div class="{{ $dropZoneClass }} bg-base-100 flex items-center justify-center gap-2 text-sm" data-dropzone>
            <x-heroicon-o-cloud-arrow-up class="size-5 opacity-70" />
            <span class="opacity-70">Glissez-déposez vos fichiers ici ou cliquez pour parcourir</span>
        </div>
        @if($preview)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2" data-previews></div>
        @endif
    </div>
@endif

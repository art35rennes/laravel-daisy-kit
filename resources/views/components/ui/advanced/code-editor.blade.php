@props([
    'language' => 'javascript', // obligatoire pour highlighting
    'value' => '',
    'readonly' => false,
    'showToolbar' => true,
    'showFoldAll' => true,
    'showUnfoldAll' => true,
    'showFormat' => true, // pour JSON: prettify natif
    'showCopy' => true,
    'height' => '320px',
    'width' => '100%',
    'fontSize' => '0.9rem',
    'tabSize' => 2,
    'theme' => null, // 'dark'|'light'|null (auto)
    'options' => [], // tableau d'options supplémentaires
    'name' => null,  // si présent, synchronise une <textarea name="...">
    // Surcharge du nom de module JS (optionnel)
    'module' => null,
])

@php
    $id = $attributes->get('id') ?? 'code-'.uniqid();
    $classes = 'bg-base-100 border border-base-300 rounded-box overflow-hidden';
    $toolbar = [
        'fold' => $showFoldAll,
        'unfold' => $showUnfoldAll,
        'format' => $showFormat,
        'copy' => $showCopy,
    ];
@endphp

<div class="code-editor {{ $classes }}" id="{{ $id }}" data-module="{{ $module ?? 'code-editor' }}" data-language="{{ $language }}" data-readonly="{{ $readonly ? 'true' : 'false' }}" data-theme="{{ $theme ?? '' }}" data-tab-size="{{ (int)$tabSize }}" style="width: {{ $width }};">
    @if($showToolbar)
        <div class="flex items-center justify-between gap-2 border-b border-base-300 px-2 py-1 bg-base-200">
            <div class="text-xs opacity-70">{{ strtoupper($language) }}</div>
            <div class="flex items-center gap-1">
                @if($toolbar['fold'])
                    <button type="button" class="btn btn-xs" data-action="fold-all" title="Plier tout">Fold</button>
                @endif
                @if($toolbar['unfold'])
                    <button type="button" class="btn btn-xs" data-action="unfold-all" title="Déplier tout">Unfold</button>
                @endif
                @if($toolbar['format'])
                    <button type="button" class="btn btn-xs" data-action="format" title="Formatter">Format</button>
                @endif
                @if($toolbar['copy'])
                    <button type="button" class="btn btn-xs" data-action="copy" title="Copier">Copy</button>
                @endif
            </div>
        </div>
    @endif
    <div class="cm-host" style="height: {{ $height }}; font-size: {{ $fontSize }}"></div>
    <textarea class="hidden" data-sync @if($name) name="{{ $name }}" @endif>{{ $value }}</textarea>
    <script type="application/json" data-options>{{ json_encode($options) }}</script>
    <script type="application/json" data-initial>@json(['value' => $value])</script>
</div>

@pushOnce('styles')
<style>
/* Composant code-editor: hauteur et scroll internes */
.code-editor .cm-editor { height: 100%; }
.code-editor .cm-scroller { overflow: auto; }
</style>
@endPushOnce


@include('daisy::components.partials.assets')
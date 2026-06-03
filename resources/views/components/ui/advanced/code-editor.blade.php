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
    $classes = 'bg-base-100 card-border rounded-box overflow-hidden';
    $toolbar = [
        'fold' => $showFoldAll,
        'unfold' => $showUnfoldAll,
        'format' => $showFormat,
        'copy' => $showCopy,
    ];
    $i18n = [
        'Fold line' => __('daisy::components.code_editor.codemirror.fold_line'),
        'Unfold line' => __('daisy::components.code_editor.codemirror.unfold_line'),
        'Folded lines' => __('daisy::components.code_editor.codemirror.folded_lines'),
        'Unfolded lines' => __('daisy::components.code_editor.codemirror.unfolded_lines'),
        'folded code' => __('daisy::components.code_editor.codemirror.folded_code'),
        'unfold' => __('daisy::components.code_editor.codemirror.unfold'),
        'to' => __('daisy::components.code_editor.codemirror.to'),
        'Find' => __('daisy::components.code_editor.codemirror.find'),
        'Replace' => __('daisy::components.code_editor.codemirror.replace'),
        'next' => __('daisy::components.code_editor.codemirror.next'),
        'previous' => __('daisy::components.code_editor.codemirror.previous'),
        'all' => __('daisy::components.code_editor.codemirror.all'),
        'match case' => __('daisy::components.code_editor.codemirror.match_case'),
        'regexp' => __('daisy::components.code_editor.codemirror.regexp'),
        'by word' => __('daisy::components.code_editor.codemirror.by_word'),
        'replace' => __('daisy::components.code_editor.codemirror.replace_action'),
        'replace all' => __('daisy::components.code_editor.codemirror.replace_all'),
        'close' => __('daisy::components.code_editor.codemirror.close'),
        'Go to line' => __('daisy::components.code_editor.codemirror.go_to_line'),
        'go' => __('daisy::components.code_editor.codemirror.go'),
        'current match' => __('daisy::components.code_editor.codemirror.current_match'),
        'on line' => __('daisy::components.code_editor.codemirror.on_line'),
        'replaced match on line $' => __('daisy::components.code_editor.codemirror.replaced_match_on_line'),
        'replaced $ matches' => __('daisy::components.code_editor.codemirror.replaced_matches'),
        'Completions' => __('daisy::components.code_editor.codemirror.completions'),
        'Diagnostics' => __('daisy::components.code_editor.codemirror.diagnostics'),
        'No diagnostics' => __('daisy::components.code_editor.codemirror.no_diagnostics'),
        'Control character' => __('daisy::components.code_editor.codemirror.control_character'),
        'Selection deleted' => __('daisy::components.code_editor.codemirror.selection_deleted'),
        'Copied!' => __('daisy::components.code_editor.actions.copied'),
    ];
@endphp

<div
    {{ $attributes
        ->except('id')
        ->class(['code-editor', $classes])
        ->merge([
            'id' => $id,
            'data-module' => $module ?? 'code-editor',
            'data-language' => $language,
            'data-readonly' => $readonly ? 'true' : 'false',
            'data-theme' => $theme ?? '',
            'data-tab-size' => (int) $tabSize,
            'style' => 'width: '.$width.';',
        ]) }}
>
    @if($showToolbar)
        <div class="flex items-center justify-between gap-2 border-b px-2 py-1 bg-base-200">
            <div class="text-xs opacity-70">{{ strtoupper($language) }}</div>
            <div class="flex items-center gap-1">
                @if($toolbar['fold'])
                    <button type="button" class="btn btn-xs" data-action="fold-all" title="{{ __('daisy::components.code_editor.actions.fold_all_title') }}">{{ __('daisy::components.code_editor.actions.fold_all') }}</button>
                @endif
                @if($toolbar['unfold'])
                    <button type="button" class="btn btn-xs" data-action="unfold-all" title="{{ __('daisy::components.code_editor.actions.unfold_all_title') }}">{{ __('daisy::components.code_editor.actions.unfold_all') }}</button>
                @endif
                @if($toolbar['format'])
                    <button type="button" class="btn btn-xs" data-action="format" title="{{ __('daisy::components.code_editor.actions.format_title') }}">{{ __('daisy::components.code_editor.actions.format') }}</button>
                @endif
                @if($toolbar['copy'])
                    <button type="button" class="btn btn-xs" data-action="copy" title="{{ __('daisy::components.code_editor.actions.copy_title') }}">{{ __('daisy::components.code_editor.actions.copy') }}</button>
                @endif
            </div>
        </div>
    @endif
    <div class="cm-host" style="height: {{ $height }}; font-size: {{ $fontSize }}"></div>
    <textarea class="hidden" data-sync @if($name) name="{{ $name }}" @endif>{{ $value }}</textarea>
    <script type="application/json" data-options>{{ json_encode($options) }}</script>
    <script type="application/json" data-initial>@json(['value' => $value])</script>
    <script type="application/json" data-i18n>@json($i18n)</script>
</div>

@pushOnce('styles')
<style>
/* Composant code-editor: hauteur et scroll internes */
.code-editor .cm-editor { height: 100%; }
.code-editor .cm-scroller { overflow: auto; }
</style>
@endPushOnce


@include('daisy::components.partials.assets')

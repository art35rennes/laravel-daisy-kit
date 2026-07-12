@props([
    'language' => 'javascript', // obligatoire pour highlighting
    'value' => '',
    'readonly' => false,
    'showToolbar' => true,
    'showFoldAll' => true,
    'showFoldOthers' => true,
    'showUnfoldAll' => true,
    'showFormat' => true, // pour JSON: prettify natif
    'showCopy' => true,
    'showExpand' => true,
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
    $dimensionClass = function ($value, string $prefix, int $remMultiplier = 100) {
        if (! is_string($value) && ! $value instanceof \Stringable && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= 1200 ? "{$prefix}-px-{$token}" : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)rem$/', $value, $matches) === 1) {
            $token = (int) round(((float) $matches[1]) * $remMultiplier);

            return $token >= 1 && $token <= 400 ? "{$prefix}-rem-{$token}" : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)%$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 1 && $token <= 100 ? "{$prefix}-percent-{$token}" : null;
        }

        return null;
    };

    $id = $attributes->get('id') ?? 'code-'.uniqid();
    $expandModalId = $id.'-expand-modal';
    $widthClass = $width === '100%' ? null : $dimensionClass($width, 'daisy-code-editor-width');
    $heightClass = $height === '320px' ? null : $dimensionClass($height, 'daisy-code-editor-height');
    $fontSizeClass = $fontSize === '0.9rem' ? null : $dimensionClass($fontSize, 'daisy-code-editor-font-size');
    $classes = trim('bg-base-100 card-border rounded-box overflow-hidden '.$widthClass);
    $toolbar = [
        'fold' => $showFoldAll,
        'foldOthers' => $showFoldOthers,
        'unfold' => $showUnfoldAll,
        'format' => $showFormat,
        'copy' => $showCopy,
        'expand' => $showExpand,
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
            'data-expand-modal-id' => $expandModalId,
            'data-expand-label' => __('daisy::components.code_editor.actions.expand'),
            'data-expand-title' => __('daisy::components.code_editor.actions.expand_title'),
            'data-reduce-label' => __('daisy::components.code_editor.actions.reduce'),
            'data-reduce-title' => __('daisy::components.code_editor.actions.reduce_title'),
        ]) }}
>
    @if($showToolbar)
        <div class="flex items-center justify-between gap-2 border-b px-2 py-1 bg-base-200">
            <div class="text-xs opacity-70">{{ strtoupper($language) }}</div>
            <div class="flex items-center gap-1">
                @if($toolbar['fold'])
                    <button type="button" class="btn btn-xs" data-action="fold-all" title="{{ __('daisy::components.code_editor.actions.fold_all_title') }}">{{ __('daisy::components.code_editor.actions.fold_all') }}</button>
                @endif
                @if($toolbar['foldOthers'])
                    <button type="button" class="btn btn-xs" data-action="fold-others" title="{{ __('daisy::components.code_editor.actions.fold_others_title') }}">{{ __('daisy::components.code_editor.actions.fold_others') }}</button>
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
                @if($toolbar['expand'])
                    <button type="button" class="btn btn-xs" data-action="expand" data-code-editor-expand-button title="{{ __('daisy::components.code_editor.actions.expand_title') }}" aria-label="{{ __('daisy::components.code_editor.actions.expand_title') }}">{{ __('daisy::components.code_editor.actions.expand') }}</button>
                @endif
            </div>
        </div>
    @endif
    <div class="cm-host {{ $heightClass }} {{ $fontSizeClass }}"></div>
    <textarea class="hidden" data-sync @if($name) name="{{ $name }}" @endif>{{ $value }}</textarea>
    <template data-options>{{ json_encode($options) }}</template>
    <template data-initial>@json(['value' => $value])</template>
    <template data-i18n>@json($i18n)</template>
</div>

@if($showExpand)
    <dialog id="{{ $expandModalId }}" class="modal" data-code-editor-expand-modal>
        <div class="modal-box h-[100svh] max-h-none w-screen max-w-none rounded-none p-4">
            <div data-code-editor-expand-host class="h-full min-h-0"></div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>{{ __('daisy::components.code_editor.actions.reduce') }}</button>
        </form>
    </dialog>
@endif

@include('daisy::components.partials.assets')

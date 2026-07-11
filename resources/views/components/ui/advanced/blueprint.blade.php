@props([
    'value' => [],
    'name' => null,
    'mode' => 'edit',
    'height' => '520px',
    'direction' => 'LR',
    'layout' => 'hierarchical',
    'transitionShape' => 'curve',
    'transitionColor' => 'primary',
    'nodeColor' => 'primary',
    'inspectorMode' => 'modal',
    'autosave' => false,
    'nodeCategories' => [],
    'transitionCategories' => [],
])

@php
    $dimensionClass = function ($value, string $prefix) {
        if (! is_string($value) && ! $value instanceof \Stringable && ! is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^(\d+(?:\.\d+)?)px$/', $value, $matches) === 1) {
            $token = (int) round((float) $matches[1]);

            return $token >= 10 && $token <= 1200 ? "{$prefix}-px-{$token}" : null;
        }

        if (preg_match('/^(\d+(?:\.\d+)?)rem$/', $value, $matches) === 1) {
            $token = (int) round(((float) $matches[1]) * 100);

            return $token >= 1 && $token <= 400 ? "{$prefix}-rem-{$token}" : null;
        }

        return null;
    };

    $transitionShapes = ['straight', 'curve', 's', 'orthogonal'];
    $transitionColors = ['primary', 'secondary', 'accent', 'neutral', 'info', 'success', 'warning', 'error'];
    $normalizeCategories = function ($categories, bool $withColor = false, bool $withShape = false) use ($transitionShapes, $transitionColors) {
        return collect(is_array($categories) ? $categories : [])
            ->map(function ($category) use ($withColor, $withShape, $transitionShapes, $transitionColors) {
                if (is_string($category)) {
                    return ['value' => $category, 'label' => $category];
                }

                if (! is_array($category) || blank($category['value'] ?? null)) {
                    return null;
                }

                $normalized = [
                    'value' => (string) $category['value'],
                    'label' => (string) ($category['label'] ?? $category['value']),
                ];

                if (is_array($category['defaults'] ?? null)) {
                    $normalized['defaults'] = $category['defaults'];
                }

                if (is_array($category['fields'] ?? null)) {
                    $normalized['fields'] = collect($category['fields'])
                        ->filter(fn ($field): bool => is_array($field))
                        ->values()
                        ->all();
                }

                if ($withShape && in_array($category['shape'] ?? null, $transitionShapes, true)) {
                    $normalized['shape'] = $category['shape'];
                }

                if ($withColor && in_array($category['color'] ?? null, $transitionColors, true)) {
                    $normalized['color'] = $category['color'];
                }

                return $normalized;
            })
            ->filter()
            ->values()
            ->all();
    };

    $id = $attributes->get('id') ?? 'blueprint-'.uniqid();
    $resolvedMode = $mode === 'view' ? 'view' : 'edit';
    $resolvedDirection = $direction === 'TB' ? 'TB' : 'LR';
    $resolvedLayout = in_array($layout, ['hierarchical', 'tree', 'radial'], true) ? $layout : 'hierarchical';
    $resolvedTransitionShape = in_array($transitionShape, $transitionShapes, true) ? $transitionShape : 'curve';
    $resolvedTransitionColor = in_array($transitionColor, $transitionColors, true) ? $transitionColor : 'primary';
    $resolvedNodeColor = in_array($nodeColor, $transitionColors, true) ? $nodeColor : 'primary';
    $resolvedInspectorMode = $inspectorMode === 'sidebar' ? 'sidebar' : 'modal';
    $resolvedAutosave = filter_var($autosave, FILTER_VALIDATE_BOOLEAN);
    $heightClass = $height === '520px' ? null : $dimensionClass($height, 'daisy-blueprint-height');
    $resolvedNodeCategories = $normalizeCategories($nodeCategories, true);
    $resolvedTransitionCategories = $normalizeCategories($transitionCategories, true, true);
    $i18n = [
        'newNode' => __('daisy::components.blueprint_editor.new_node'),
        'newTransition' => __('daisy::components.blueprint_editor.new_transition'),
        'node' => __('daisy::components.blueprint_editor.node'),
        'transition' => __('daisy::components.blueprint_editor.transition'),
        'empty' => __('daisy::components.blueprint_editor.empty'),
        'unnamed' => __('daisy::components.blueprint_editor.unnamed'),
        'selectTarget' => __('daisy::components.blueprint_editor.select_target'),
        'selectConnectionTarget' => __('daisy::components.blueprint_editor.select_connection_target'),
        'validationError' => __('daisy::components.blueprint_editor.validation_error'),
    ];
@endphp

<div
    {{ $attributes
        ->except('id')
        ->class([
            'daisy-blueprint w-full overflow-hidden rounded-box border border-base-300 bg-base-100',
            $heightClass,
        ])
        ->merge([
            'id' => $id,
            'data-module' => 'blueprint',
            'data-blueprint' => '1',
            'data-mode' => $resolvedMode,
            'data-direction' => $resolvedDirection,
            'data-layout' => $resolvedLayout,
            'data-transition-shape' => $resolvedTransitionShape,
            'data-transition-color' => $resolvedTransitionColor,
            'data-node-color' => $resolvedNodeColor,
            'data-inspector-mode' => $resolvedInspectorMode,
            'data-autosave' => $resolvedAutosave ? 'true' : 'false',
        ]) }}
>
    <div class="daisy-blueprint-toolbar flex flex-wrap items-center gap-2 border-b border-base-300 bg-base-100 px-3 py-2">
        @if($resolvedMode === 'edit')
            <button type="button" class="btn btn-primary btn-sm" data-blueprint-action="add-node">
                {{ __('daisy::components.blueprint_editor.actions.add_node') }}
            </button>
        @endif

        <label class="input input-sm min-w-44 flex-1 sm:max-w-64">
            <span class="sr-only">{{ __('daisy::components.blueprint_editor.actions.search') }}</span>
            <svg class="h-4 w-4 opacity-60" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m20 20-3.5-3.5"></path>
            </svg>
            <input type="search" data-blueprint-search placeholder="{{ __('daisy::components.blueprint_editor.actions.search') }}">
        </label>

        <div class="join ms-auto">
            @if($resolvedMode === 'edit')
                <button type="button" class="btn btn-sm join-item" data-blueprint-action="undo" disabled>
                    {{ __('daisy::components.blueprint_editor.actions.undo') }}
                </button>
                <button type="button" class="btn btn-sm join-item" data-blueprint-action="redo" disabled>
                    {{ __('daisy::components.blueprint_editor.actions.redo') }}
                </button>
            @endif
            <button type="button" class="btn btn-sm join-item" data-blueprint-action="arrange">
                {{ __('daisy::components.blueprint_editor.actions.arrange') }}
            </button>
            <button type="button" class="btn btn-sm join-item" data-blueprint-action="fit">
                {{ __('daisy::components.blueprint_editor.actions.fit') }}
            </button>
        </div>
    </div>

    <div class="daisy-blueprint-layout relative min-h-0" data-blueprint-layout>
        <div
            class="daisy-blueprint-canvas relative overflow-hidden bg-base-200"
            data-blueprint-canvas
            tabindex="0"
            role="application"
            aria-label="{{ __('daisy::components.blueprint_editor.canvas') }}"
        >
            <div class="daisy-blueprint-world absolute start-0 top-0" data-blueprint-world>
                <svg class="daisy-blueprint-edges absolute start-0 top-0 overflow-visible" data-blueprint-edges>
                    <defs>
                        <marker id="{{ $id }}-arrow" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">
                            <path d="M 0 0 L 10 5 L 0 10 z"></path>
                        </marker>
                    </defs>
                    <g data-blueprint-transition-layer></g>
                    <g data-blueprint-transition-label-layer></g>
                </svg>
                <div class="daisy-blueprint-nodes absolute start-0 top-0" data-blueprint-nodes></div>
            </div>
            <p class="daisy-blueprint-empty pointer-events-none absolute inset-0 grid place-items-center text-sm opacity-60" data-blueprint-empty hidden>
                {{ __('daisy::components.blueprint_editor.empty') }}
            </p>
            <p class="sr-only" data-blueprint-connection-status aria-live="polite"></p>
        </div>

        @if($resolvedMode === 'edit')
            <dialog
                class="daisy-blueprint-inspector modal {{ $resolvedInspectorMode === 'sidebar' ? 'modal-end' : 'modal-middle' }} hidden"
                data-blueprint-inspector
                aria-labelledby="{{ $id }}-inspector-title"
            >
                <div class="modal-box {{ $resolvedInspectorMode === 'sidebar' ? 'h-full max-h-none w-full max-w-sm rounded-none' : 'w-full max-w-xl' }} overflow-y-auto p-4">
                    <div class="mb-4 flex items-start justify-between gap-2">
                    <div class="grid min-w-0 gap-2">
                        <h2 id="{{ $id }}-inspector-title" class="font-semibold" data-blueprint-inspector-title></h2>
                        <span class="badge badge-warning badge-sm hidden" data-blueprint-dirty-indicator>
                            {{ __('daisy::components.blueprint_editor.unsaved_changes') }}
                        </span>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm" data-blueprint-action="close-inspector" aria-label="{{ __('daisy::components.blueprint_editor.actions.close') }}">×</button>
                </div>

                    <form class="grid gap-4" data-blueprint-inspector-form>
                    <label class="form-control grid gap-1">
                        <span class="label-text text-sm font-medium">{{ __('daisy::components.blueprint_editor.fields.name') }}</span>
                        <input type="text" class="input input-bordered w-full" name="label" maxlength="160">
                    </label>
                    <label class="form-control grid gap-1">
                        <span class="label-text text-sm font-medium">{{ __('daisy::components.blueprint_editor.fields.description') }}</span>
                        <textarea class="textarea textarea-bordered min-h-24 w-full" name="description" maxlength="500"></textarea>
                    </label>
                    <label class="form-control grid gap-1">
                        <span class="label-text text-sm font-medium">{{ __('daisy::components.blueprint_editor.fields.category') }}</span>
                        <select class="select select-bordered w-full" name="category"></select>
                    </label>

                    <div class="grid gap-4" data-blueprint-integrator-fields></div>

                    <fieldset class="grid gap-2 rounded-box border border-base-300 p-3" data-blueprint-node-transition>
                        <legend class="px-1 text-sm font-medium">{{ __('daisy::components.blueprint_editor.create_transition') }}</legend>
                        <label class="sr-only" for="{{ $id }}-target">{{ __('daisy::components.blueprint_editor.select_target') }}</label>
                        <select id="{{ $id }}-target" class="select select-bordered w-full" data-blueprint-transition-target></select>
                        <button type="button" class="btn btn-outline btn-sm" data-blueprint-action="add-transition">
                            {{ __('daisy::components.blueprint_editor.actions.add_transition') }}
                        </button>
                    </fieldset>

                    <div class="flex justify-between gap-2 border-t border-base-300 pt-4">
                        <button type="button" class="btn btn-error btn-outline" data-blueprint-action="delete">
                            {{ __('daisy::components.blueprint_editor.actions.delete') }}
                        </button>
                        @if($resolvedAutosave)
                            <span class="self-center text-xs text-base-content/60">
                                {{ __('daisy::components.blueprint_editor.autosave') }}
                            </span>
                        @else
                            <button type="submit" class="btn btn-primary" data-blueprint-action="save">
                                {{ __('daisy::components.blueprint_editor.actions.save') }}
                            </button>
                        @endif
                    </div>
                    </form>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button
                        type="button"
                        data-blueprint-inspector-backdrop
                        data-blueprint-action="close-inspector"
                        aria-label="{{ __('daisy::components.blueprint_editor.actions.close') }}"
                    ></button>
                </form>
            </dialog>

            <x-daisy::ui.overlay.modal
                :id="$id.'-discard-confirm'"
                :title="__('daisy::components.blueprint_editor.discard.title')"
                size="sm"
                :backdrop="false"
                :close-button="false"
                :teleport="false"
                initial-focus="[data-blueprint-action='keep-editing']"
                data-blueprint-discard-dialog
            >
                <p>{{ __('daisy::components.blueprint_editor.discard.message') }}</p>

                <x-slot:actions>
                    <button type="button" class="btn" data-blueprint-action="keep-editing">
                        {{ __('daisy::components.blueprint_editor.discard.keep_editing') }}
                    </button>
                    <button type="button" class="btn btn-error" data-blueprint-action="discard-changes">
                        {{ __('daisy::components.blueprint_editor.discard.confirm') }}
                    </button>
                </x-slot:actions>
            </x-daisy::ui.overlay.modal>
        @endif

        <div class="daisy-blueprint-mobile-list hidden overflow-y-auto bg-base-200 p-3" data-blueprint-mobile-list></div>
    </div>

    <textarea class="hidden" data-blueprint-sync @if($name) name="{{ $name }}" @endif></textarea>
    <textarea class="hidden" hidden readonly data-blueprint-value>@json($value)</textarea>
    <textarea class="hidden" hidden readonly data-blueprint-node-categories>@json($resolvedNodeCategories)</textarea>
    <textarea class="hidden" hidden readonly data-blueprint-transition-categories>@json($resolvedTransitionCategories)</textarea>
    <textarea class="hidden" hidden readonly data-blueprint-i18n>@json($i18n)</textarea>
</div>

@include('daisy::components.partials.assets')

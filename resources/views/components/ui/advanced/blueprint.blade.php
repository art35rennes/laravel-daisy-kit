@props([
    'value' => [],
    'nodeTypes' => [],
    'mode' => 'workflow',
    'readonly' => false,
    'name' => null,
    'height' => '520px',
    'toolbar' => true,
    'palette' => true,
    'details' => true,
    'detailsMode' => 'panel',
    'dock' => false,
    'fullscreen' => true,
    'autoLink' => true,
    'minimap' => true,
    'autoArrange' => true,
    'fitOnInit' => true,
    'history' => true,
    'reroute' => true,
    'module' => null,
])

{{--
    Public Blueprint contract:
    - value.version, value.nodes[], value.edges[], value.viewport are persisted by the host application.
    - nodeTypes[] defines allowed types, typed ports, DaisyUI semantic theme tokens, controls, display, icon, and naming.
    - Mutations are emitted through daisy:blueprint:* events and synchronized into the hidden textarea when name is set.
--}}

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

    $id = $attributes->get('id') ?? 'blueprint-'.uniqid();
    $resolvedMode = in_array($mode, ['view', 'edit', 'workflow'], true) ? $mode : 'workflow';
    $isReadonly = (bool) $readonly || $resolvedMode === 'view';
    $heightClass = $height === '520px' ? null : $dimensionClass($height, 'daisy-blueprint-height');
    $classes = trim('daisy-blueprint w-full bg-base-100 card-border rounded-box overflow-hidden '.$heightClass);
    $resolvedNodeTypes = filled($nodeTypes) ? $nodeTypes : [[
        'type' => 'task',
        'label' => __('daisy::components.blueprint_editor.default_node'),
        'category' => __('daisy::components.blueprint_editor.general_category'),
        'inputs' => [['key' => 'in', 'label' => 'In', 'kind' => 'flow']],
        'outputs' => [['key' => 'out', 'label' => 'Out', 'kind' => 'flow', 'multiple' => true]],
        'defaults' => [],
    ]];
    $nodeTypeGroups = collect($resolvedNodeTypes)
        ->filter(fn ($type) => is_array($type) && filled($type['type'] ?? null))
        ->groupBy(fn ($type) => filled($type['category'] ?? null) ? (string) $type['category'] : __('daisy::components.blueprint_editor.general_category'));
    $showsPalette = (bool) $palette && ! $isReadonly;
    $showsDetails = (bool) $details;
    $resolvedDetailsMode = in_array($detailsMode, ['panel', 'modal'], true) ? $detailsMode : 'panel';
    $themeBadgeClasses = [
        'primary' => 'badge-primary',
        'secondary' => 'badge-secondary',
        'accent' => 'badge-accent',
        'neutral' => 'badge-neutral',
        'info' => 'badge-info',
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'error' => 'badge-error',
        'trigger' => 'badge-primary',
        'schema' => 'badge-secondary',
        'data' => 'badge-accent',
        'function' => 'badge-info',
        'action' => 'badge-success',
        'condition' => 'badge-warning',
    ];
    $i18n = [
        'noSelection' => __('daisy::components.blueprint_editor.no_selection'),
        'noProperties' => __('daisy::components.blueprint_editor.no_properties'),
        'invalidConnection' => __('daisy::components.blueprint_editor.invalid_connection'),
        'applyNode' => __('daisy::components.blueprint_editor.actions.apply'),
        'applySuccess' => __('daisy::components.blueprint_editor.apply_success'),
        'applyError' => __('daisy::components.blueprint_editor.apply_error'),
        'errorDetails' => __('daisy::components.blueprint_editor.error_details'),
        'copyError' => __('daisy::components.blueprint_editor.actions.copy_error'),
        'deleteNode' => __('daisy::components.blueprint_editor.actions.delete'),
    ];
@endphp

<div
    {{ $attributes
        ->except('id')
        ->class([$classes])
        ->merge([
            'id' => $id,
            'data-module' => $module ?? 'blueprint',
            'data-blueprint' => '1',
            'data-mode' => $resolvedMode,
            'data-readonly' => $isReadonly ? 'true' : 'false',
            'data-palette' => $palette ? 'true' : 'false',
            'data-details' => $showsDetails ? 'true' : 'false',
            'data-details-mode' => $resolvedDetailsMode,
            'data-dock' => $dock ? 'true' : 'false',
            'data-fullscreen' => $fullscreen ? 'true' : 'false',
            'data-auto-link' => $autoLink ? 'true' : 'false',
            'data-minimap' => $minimap ? 'true' : 'false',
            'data-auto-arrange' => $autoArrange ? 'true' : 'false',
            'data-fit-on-init' => $fitOnInit ? 'true' : 'false',
            'data-history' => $history ? 'true' : 'false',
            'data-reroute' => $reroute ? 'true' : 'false',
        ]) }}
>
    @if($toolbar)
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-base-300 bg-base-200 px-3 py-2">
            <div class="flex min-w-0 flex-wrap items-center gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">{{ __('daisy::components.blueprint_editor.title') }}</p>
                    <p class="text-xs opacity-70">{{ __('daisy::components.blueprint_editor.subtitle') }}</p>
                </div>

                @if($showsPalette)
                    <x-daisy::ui.overlay.dropdown
                        :label="__('daisy::components.blueprint_editor.actions.add_node')"
                        buttonClass="btn btn-sm btn-primary"
                        type="card"
                        contentClass="dropdown-content z-50 mt-2 max-h-96 overflow-y-auto rounded-box border border-base-300 bg-base-100 shadow"
                        cardBodyClass="p-3"
                        data-blueprint-palette-menu
                    >
                        <div class="grid gap-1">
                            @forelse(collect($nodeTypeGroups)->flatten(1) as $type)
                                @php
                                    $theme = filled($type['theme'] ?? null) ? (string) $type['theme'] : 'default';
                                    $badgeClass = $themeBadgeClasses[$theme] ?? 'badge-neutral';
                                    $portSummary = collect($type['inputs'] ?? [])
                                        ->merge($type['outputs'] ?? [])
                                        ->pluck('type')
                                        ->filter()
                                        ->unique()
                                        ->implode(' · ');
                                @endphp
                                <button
                                    type="button"
                                    class="rounded-btn px-2 py-1.5 text-left text-sm hover:bg-base-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary"
                                    data-blueprint-add-node="{{ $type['type'] }}"
                                    @if(filled($type['description'] ?? null)) title="{{ $type['description'] }}" @endif
                                >
                                    <span class="flex min-w-0 items-center justify-between gap-2">
                                        <span class="truncate font-medium">{{ $type['label'] ?? $type['type'] }}</span>
                                        <span @class(['badge badge-xs shrink-0', $badgeClass])>{{ $theme }}</span>
                                    </span>
                                    @if(filled($type['description'] ?? null) || filled($portSummary))
                                        <span class="mt-0.5 grid min-w-0 gap-0.5">
                                            @if(filled($type['description'] ?? null))
                                                <span class="line-clamp-2 text-xs font-normal opacity-60">{{ $type['description'] }}</span>
                                            @endif
                                            @if(filled($portSummary))
                                                <span class="text-[0.6875rem] font-normal uppercase tracking-wide opacity-50">{{ $portSummary }}</span>
                                            @endif
                                        </span>
                                    @endif
                                </button>
                            @empty
                                <p class="text-sm opacity-70">{{ __('daisy::components.blueprint_editor.empty_palette') }}</p>
                            @endforelse
                        </div>
                    </x-daisy::ui.overlay.dropdown>
                @endif
            </div>

            <div class="join">
                @if($history && ! $isReadonly)
                    <button type="button" class="btn btn-xs join-item" data-blueprint-action="undo">{{ __('daisy::components.blueprint_editor.actions.undo') }}</button>
                    <button type="button" class="btn btn-xs join-item" data-blueprint-action="redo">{{ __('daisy::components.blueprint_editor.actions.redo') }}</button>
                @endif
                @if($autoArrange)
                    <button type="button" class="btn btn-xs join-item" data-blueprint-action="arrange">{{ __('daisy::components.blueprint_editor.actions.arrange') }}</button>
                @endif
                <button type="button" class="btn btn-xs join-item" data-blueprint-action="fit">{{ __('daisy::components.blueprint_editor.actions.fit') }}</button>
                @if($fullscreen)
                    <button type="button" class="btn btn-xs join-item" data-blueprint-action="fullscreen">{{ __('daisy::components.blueprint_editor.actions.fullscreen') }}</button>
                @endif
            </div>
        </div>
    @endif

    <div class="relative grid min-h-0 grid-cols-1" data-blueprint-layout>
        <div class="daisy-blueprint-canvas-wrap min-w-0 bg-base-200">
            <div class="daisy-blueprint-canvas" data-blueprint-canvas></div>
        </div>

        @if($showsDetails)
            <button
                type="button"
                class="absolute inset-0 z-30 hidden bg-base-300/45 backdrop-blur-[1px]"
                data-blueprint-details-backdrop
                aria-label="{{ __('daisy::components.blueprint_editor.actions.close_properties') }}"
            ></button>

            @if($resolvedDetailsMode === 'modal')
                <div
                    class="modal hidden"
                    data-blueprint-details-panel="true"
                >
                    <div class="modal-box flex max-h-[calc(100dvh-3rem)] w-11/12 max-w-6xl flex-col p-0">
                        <div class="flex items-center justify-between gap-2 border-b border-base-300 px-5 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide opacity-70">{{ __('daisy::components.blueprint_editor.properties') }}</p>
                            <button type="button" class="btn btn-ghost btn-xs" data-blueprint-details-close>{{ __('daisy::components.blueprint_editor.actions.close') }}</button>
                        </div>
                        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4" data-blueprint-properties></div>
                    </div>
                    <button type="button" class="modal-backdrop" data-blueprint-details-backdrop>{{ __('daisy::components.blueprint_editor.actions.close_properties') }}</button>
                </div>
            @else
                <aside
                    class="daisy-blueprint-panel daisy-blueprint-details-panel absolute inset-y-0 end-0 z-40 hidden w-full max-w-sm border-s border-base-300 bg-base-100 p-3 shadow-xl"
                    data-blueprint-details-panel="true"
                >
                    <div class="mb-3 flex items-center justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-wide opacity-70">{{ __('daisy::components.blueprint_editor.properties') }}</p>
                        <button type="button" class="btn btn-ghost btn-xs" data-blueprint-details-close>{{ __('daisy::components.blueprint_editor.actions.close') }}</button>
                    </div>
                    <div data-blueprint-properties></div>
                </aside>
            @endif
        @endif
    </div>

    <textarea class="hidden" data-blueprint-sync @if($name) name="{{ $name }}" @endif></textarea>
    <textarea class="hidden" hidden readonly data-blueprint-value>@json($value)</textarea>
    <textarea class="hidden" hidden readonly data-blueprint-node-types>@json($resolvedNodeTypes)</textarea>
    <textarea class="hidden" hidden readonly data-blueprint-i18n>@json($i18n)</textarea>
</div>

@include('daisy::components.partials.assets')

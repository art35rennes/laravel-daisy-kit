<div
    class="daisy-form-builder space-y-3"
    wire:click="clearSelection"
    data-module="form-builder"
    data-form-builder-livewire
    data-schema-id="{{ $canonicalSchema['id'] ?? 'daisy-form-schema' }}"
>
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-box border border-base-300 bg-base-100 p-3">
        <div class="flex flex-wrap items-center gap-2">
            <div wire:click.stop>
                <x-daisy::ui.overlay.dropdown
                    :label="__('daisy::form.builder.add_element')"
                    buttonClass="btn btn-sm btn-primary"
                    type="card"
                    contentClass="dropdown-content z-20 mt-2 max-h-96 overflow-y-auto rounded-box border border-base-300 bg-base-100 shadow"
                    cardBodyClass="p-3"
                    data-builder-palette
                >
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($fieldGroups as $group)
                            <section class="min-w-0">
                                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-base-content/50">{{ $group['label'] }}</h3>
                                <div class="grid gap-1">
                                    @foreach($group['fields'] as $fieldType)
                                        <button type="button" class="rounded-btn px-2 py-1.5 text-left text-sm hover:bg-base-200" wire:click.stop="addField('{{ $fieldType['type'] }}')" data-builder-add="{{ $fieldType['type'] }}">
                                            <span class="block truncate">{{ $fieldType['label'] ?? $fieldType['type'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </x-daisy::ui.overlay.dropdown>
            </div>

            <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="secondary" wire:click.stop="addStep">
                {{ __('daisy::form.builder.add_step') }}
            </x-daisy::ui.inputs.button>

            <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="neutral" wire:click="collapseAllFields" data-builder-collapse-all>
                <x-slot:icon>
                    <x-bi-arrows-collapse class="size-3.5" />
                </x-slot:icon>
                {{ __('daisy::form.builder.collapse_all') }}
            </x-daisy::ui.inputs.button>

            <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="neutral" wire:click="expandAllFields" data-builder-expand-all>
                <x-slot:icon>
                    <x-bi-arrows-expand class="size-3.5" />
                </x-slot:icon>
                {{ __('daisy::form.builder.expand_all') }}
            </x-daisy::ui.inputs.button>

            <details class="dropdown" data-builder-schema-settings>
                <summary class="btn btn-sm btn-outline btn-info list-none">
                    {{ __('daisy::form.builder.schema_settings') }}
                </summary>

                <div class="dropdown-content z-20 mt-2 rounded-box border border-base-300 bg-base-100 p-3 shadow">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold">{{ __('daisy::form.builder.schema_settings') }}</h3>
                        <x-daisy::ui.inputs.button type="button" size="xs" variant="ghost" color="neutral" square data-builder-close-menu aria-label="{{ __('daisy::form.builder.close_menu') }}">
                            <x-bi-x class="size-4" />
                        </x-daisy::ui.inputs.button>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <x-daisy::ui.partials.form-field :label="__('daisy::form.builder.schema_id')">
                            <x-daisy::ui.inputs.input size="sm" value="{{ $canonicalSchema['id'] ?? '' }}" wire:change="updateSchemaKey('id', $event.target.value)" />
                        </x-daisy::ui.partials.form-field>

                        <x-daisy::ui.partials.form-field :label="__('daisy::form.builder.schema_title')">
                            <x-daisy::ui.inputs.input size="sm" value="{{ data_get($canonicalSchema, 'meta.title', '') }}" wire:change="updateSchemaKey('meta.title', $event.target.value)" />
                        </x-daisy::ui.partials.form-field>

                        <x-daisy::ui.partials.form-field :label="__('daisy::form.builder.schema_description')" class="md:col-span-2">
                            <x-daisy::ui.inputs.textarea rows="2" size="sm" wire:change="updateSchemaKey('meta.description', $event.target.value)">{{ data_get($canonicalSchema, 'meta.description', '') }}</x-daisy::ui.inputs.textarea>
                        </x-daisy::ui.partials.form-field>

                        <x-daisy::ui.partials.form-field :label="__('daisy::form.builder.layout_type')">
                            <x-daisy::ui.inputs.select size="sm" wire:change="updateSchemaKey('layout.type', $event.target.value)">
                                @foreach(['one-page', 'sections', 'multi-step'] as $layoutType)
                                    <option value="{{ $layoutType }}" @selected(data_get($canonicalSchema, 'layout.type') === $layoutType)>{{ __("daisy::form.builder.layout_types.{$layoutType}") }}</option>
                                @endforeach
                            </x-daisy::ui.inputs.select>
                        </x-daisy::ui.partials.form-field>

                        <x-daisy::ui.partials.form-field :label="__('daisy::form.builder.submit_mode')">
                            <x-daisy::ui.inputs.select size="sm" wire:change="updateSchemaKey('submit.mode', $event.target.value)">
                                @foreach(['event', 'html', 'fetch', 'none'] as $submitMode)
                                    <option value="{{ $submitMode }}" @selected(data_get($canonicalSchema, 'submit.mode') === $submitMode)>{{ __("daisy::form.builder.submit_modes.{$submitMode}") }}</option>
                                @endforeach
                            </x-daisy::ui.inputs.select>
                        </x-daisy::ui.partials.form-field>

                        <x-daisy::ui.partials.form-field :label="__('daisy::form.builder.submit_label')">
                            <x-daisy::ui.inputs.input size="sm" value="{{ data_get($canonicalSchema, 'submit.label', '') }}" wire:change="updateSchemaKey('submit.label', $event.target.value)" />
                        </x-daisy::ui.partials.form-field>
                    </div>
                </div>
            </details>

            <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="neutral" wire:click="undo" :disabled="count($undoStack) === 0" data-builder-undo>
                <x-slot:icon>
                    <x-bi-arrow-counterclockwise class="size-3.5" />
                </x-slot:icon>
                {{ __('daisy::form.builder.undo') }}
            </x-daisy::ui.inputs.button>

            <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="neutral" wire:click="redo" :disabled="count($redoStack) === 0" data-builder-redo>
                <x-slot:icon>
                    <x-bi-arrow-clockwise class="size-3.5" />
                </x-slot:icon>
                {{ __('daisy::form.builder.redo') }}
            </x-daisy::ui.inputs.button>

            <x-daisy::ui.inputs.button
                type="button"
                size="sm"
                variant="outline"
                color="success"
                data-builder-export
            >
                <x-slot:icon>
                    <x-bi-download class="size-3.5" />
                </x-slot:icon>
                {{ __('daisy::form.builder.export_json') }}
            </x-daisy::ui.inputs.button>

            @if(count($functionCatalog) > 0)
                <x-daisy::ui.overlay.dropdown
                    :label="__('daisy::form.builder.functions')"
                    buttonClass="btn btn-sm btn-ghost"
                    contentClass="dropdown-content z-20 mt-2 w-72 rounded-box border border-base-300 bg-base-100 p-3 shadow"
                    type="card"
                >
                    <ul class="space-y-1 text-xs" data-builder-functions>
                        @foreach($functionCatalog as $definition)
                            <li class="rounded-box bg-base-200 px-2 py-1">
                                {{ trim(($definition['name'] ?? '').' '.($definition['signature'] ?? '')) }}
                            </li>
                        @endforeach
                    </ul>
                </x-daisy::ui.overlay.dropdown>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-daisy::ui.inputs.input
                size="sm"
                class="w-56"
                placeholder="{{ __('daisy::form.builder.search_fields') }}"
                wire:model.live.debounce.250ms="fieldSearch"
                data-builder-search
            />
            @if(count($diagnostics) > 0)
                <span class="badge badge-error badge-outline">{{ trans_choice('daisy::form.builder.errors_count', count($diagnostics), ['count' => count($diagnostics)]) }}</span>
            @else
                <span class="badge badge-success badge-outline">{{ __('daisy::form.builder.valid_schema') }}</span>
            @endif
            <span class="badge badge-outline">{{ trans_choice('daisy::form.builder.fields_count', count($allFields), ['count' => count($allFields)]) }}</span>
        </div>
    </div>

    <div class="tabs tabs-box lg:grid lg:grid-cols-[minmax(0,1.25fr)_minmax(28rem,1fr)] lg:items-start lg:gap-4">
        <input type="radio" name="daisy-form-builder-mobile-tabs" class="tab lg:hidden" aria-label="{{ __('daisy::form.builder.builder_tab') }}" checked />
        <section class="tab-content border-base-300 bg-base-100 p-3 lg:!block lg:border-0 lg:bg-transparent lg:p-0" data-builder-authoring>
            <div class="min-w-0 rounded-box border border-base-300 bg-base-100 p-4">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold">{{ __('daisy::form.builder.surface') }}</h2>
                        <p class="text-sm text-base-content/60">{{ __('daisy::form.builder.surface_help') }}</p>
                    </div>
                </div>

                <div class="tabs tabs-border">
                    <input type="radio" name="daisy-form-builder-authoring-tabs" class="tab" aria-label="{{ __('daisy::form.builder.visual_tab') }}" checked />
                    <div class="tab-content pt-4">
                        <div class="min-w-0 overflow-x-auto rounded-box border border-base-300" data-builder-outline>
                            <table class="table table-xs daisy-form-builder-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('daisy::form.builder.columns.element') }}</th>
                                        <th>{{ __('daisy::form.builder.columns.type') }}</th>
                                        <th>{{ __('daisy::form.builder.columns.state') }}</th>
                                        <th class="text-center">{{ __('daisy::form.builder.columns.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $fieldParents = collect($flatFields)
                                            ->mapWithKeys(fn (array $field): array => [(string) ($field['id'] ?? '') => $field['_parent'] ?? null])
                                            ->all();
                                        $fieldDescendants = [];

                                        foreach (array_keys($fieldParents) as $fieldId) {
                                            $fieldDescendants[$fieldId] = [];
                                        }

                                        $fieldSiblingsByParent = [];

                                        foreach ($fieldParents as $fieldId => $parentId) {
                                            $parentKey = $parentId ?? '__root';
                                            $fieldSiblingsByParent[$parentKey] ??= [];
                                            $fieldSiblingsByParent[$parentKey][] = $fieldId;

                                            while ($parentId) {
                                                $fieldDescendants[$parentId][] = $fieldId;
                                                $parentId = $fieldParents[$parentId] ?? null;
                                            }
                                        }
                                    @endphp
                                    @forelse($flatFields as $field)
                                        @php
                                            $isSelected = $selectedId === ($field['id'] ?? null);
                                            $fieldDiagnostics = $diagnosticsByField[$field['id'] ?? ''] ?? [];
                                            $isContainer = is_array($field['fields'] ?? null);
                                            $fieldId = (string) ($field['id'] ?? '');
                                            $parentId = $fieldParents[$fieldId] ?? null;
                                            $parentKey = $parentId ?? '__root';
                                            $siblings = $fieldSiblingsByParent[$parentKey] ?? [];
                                            $siblingIndex = array_search($fieldId, $siblings, true);
                                            $siblingIndex = is_int($siblingIndex) ? $siblingIndex : 0;
                                            $previousSiblingId = $siblings[$siblingIndex - 1] ?? null;
                                            $isLastDirectSibling = $fieldId === end($siblings);
                                            $hasExpandableChildren = $isContainer && count((array) ($field['fields'] ?? [])) > 0;
                                            $isCollapsed = $collapsedFieldIds[$field['id'] ?? ''] ?? false;
                                            $depth = max(0, (int) ($field['_depth'] ?? 0));
                                            $status = count($fieldDiagnostics) > 0
                                                ? ['label' => __('daisy::form.builder.state_invalid'), 'class' => 'badge-error text-error']
                                                : ((array_key_exists('visibleWhen', $field) || array_key_exists('computed', $field))
                                                    ? ['label' => __('daisy::form.builder.state_conditional'), 'class' => 'badge-warning text-warning']
                                                    : ['label' => __('daisy::form.builder.state_valid'), 'class' => 'badge-success text-success']);
                                            $type = (string) ($field['type'] ?? 'text');
                                            $typeBadgeClass = match ($type) {
                                                'text', 'email', 'tel', 'url', 'password', 'number', 'textarea', 'color' => 'badge-info text-info',
                                                'select', 'radio', 'checkbox', 'toggle', 'range' => 'badge-secondary text-secondary',
                                                'date', 'time', 'datetime-local', 'month' => 'badge-warning text-warning',
                                                'file', 'signature' => 'badge-accent text-accent',
                                                'staticText' => 'badge-success text-success',
                                                'section', 'tabs', 'wizardStep' => 'badge-primary text-primary',
                                                'hidden' => 'badge-neutral text-neutral',
                                                default => 'badge-outline text-base-content',
                                            };
                                        @endphp
                                        <tr class="daisy-form-builder-drop-row" data-builder-drop-row>
                                            <td colspan="4">
                                                <button
                                                    type="button"
                                                    class="daisy-form-builder-drop-zone daisy-form-builder-drop-zone-position"
                                                    data-builder-drop-depth="{{ min($depth, 12) }}"
                                                    data-builder-drop-tone="{{ $depth % 16 }}"
                                                    data-builder-drop-target="{{ $fieldId }}"
                                                    data-builder-drop-descendants='@json($fieldDescendants[$fieldId] ?? [])'
                                                    data-builder-drop-kind="position"
                                                    data-builder-drop-action="before"
                                                    data-builder-drop-parent="{{ $parentKey }}"
                                                    data-builder-drop-index="{{ $siblingIndex }}"
                                                    data-builder-drop-previous="{{ $previousSiblingId ?? '' }}"
                                                    data-builder-drop-zone="before"
                                                    aria-label="{{ __('daisy::form.builder.drop_position') }}"
                                                >
                                                    <span class="daisy-form-builder-drop-icon">+</span>
                                                    <span>{{ __('daisy::form.builder.drop_position') }}</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr
                                            class="group relative {{ $isSelected ? 'bg-primary/5' : '' }}"
                                            data-builder-field="{{ $field['id'] ?? '' }}"
                                            data-builder-field-depth="{{ $depth }}"
                                            @if($isSelected) data-builder-selected="true" @endif
                                            wire:click.stop="selectField('{{ $field['id'] }}')"
                                        >
                                            <td>
                                                <div
                                                    class="daisy-form-builder-field-main min-w-0"
                                                >
                                                    <span
                                                        class="daisy-form-builder-drag-handle relative z-10 flex size-10 cursor-grab select-none items-center justify-center rounded-btn border border-base-300 bg-base-100 text-base-content/55 transition hover:border-primary hover:bg-primary/10 hover:text-primary active:cursor-grabbing group-hover:text-base-content"
                                                        data-builder-drag-handle
                                                        data-builder-drag-field="{{ $fieldId }}"
                                                        data-builder-drag-descendants='@json($fieldDescendants[$fieldId] ?? [])'
                                                        data-builder-drag-parent="{{ $parentKey }}"
                                                        data-builder-drag-index="{{ $siblingIndex }}"
                                                        data-builder-stop-propagation
                                                        aria-label="{{ __('daisy::form.builder.drag_handle') }}"
                                                    >
                                                        <x-bi-grip-vertical class="size-3.5 pointer-events-none" />
                                                    </span>
                                                    <div class="daisy-form-builder-field-label min-w-0">
                                                        @if($hasExpandableChildren)
                                                            <button
                                                                type="button"
                                                                class="btn btn-ghost btn-xs !h-6 !min-h-6 !w-5 p-0 text-base-content/55"
                                                                wire:click.stop="toggleFieldCollapsed('{{ $field['id'] }}')"
                                                                data-builder-collapse="{{ $isCollapsed ? 'closed' : 'open' }}"
                                                                aria-label="{{ $isCollapsed ? __('daisy::form.builder.expand') : __('daisy::form.builder.collapse') }}"
                                                            >
                                                                @if($isCollapsed)
                                                                    <x-bi-chevron-right class="size-3" />
                                                                @else
                                                                    <x-bi-chevron-down class="size-3" />
                                                                @endif
                                                            </button>
                                                        @else
                                                            <span></span>
                                                        @endif
                                                        <button
                                                            type="button"
                                                            class="min-w-0 text-left"
                                                            wire:click.stop="selectField('{{ $field['id'] }}')"
                                                            data-builder-select
                                                            @if($isSelected) aria-current="true" @endif
                                                        >
                                                            <span class="block truncate text-sm font-medium">{{ $field['label'] ?? $field['id'] }}</span>
                                                            <span class="block truncate text-xs text-base-content/60">{{ $field['id'] ?? '' }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-sm badge-outline font-mono daisy-form-builder-type-badge {{ $typeBadgeClass }}" data-builder-type-badge="{{ $type }}">{{ $type }}</span></td>
                                            <td><span class="badge badge-sm badge-outline {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                            <td>
                                                <div class="flex justify-center gap-1.5">
                                                    <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="info" square class="!min-h-9 !h-9 !w-9" wire:click.stop="editField('{{ $field['id'] }}')" data-builder-edit aria-label="{{ __('daisy::form.builder.edit_field') }}">
                                                        <x-bi-pencil class="size-4" />
                                                    </x-daisy::ui.inputs.button>
                                                    <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="neutral" square class="!min-h-9 !h-9 !w-9" wire:click.stop="moveField('{{ $field['id'] }}', -1)" data-builder-move="up" aria-label="{{ __('daisy::form.builder.move_up') }}">
                                                        <x-bi-arrow-up class="size-4" />
                                                    </x-daisy::ui.inputs.button>
                                                    <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="neutral" square class="!min-h-9 !h-9 !w-9" wire:click.stop="moveField('{{ $field['id'] }}', 1)" data-builder-move="down" aria-label="{{ __('daisy::form.builder.move_down') }}">
                                                        <x-bi-arrow-down class="size-4" />
                                                    </x-daisy::ui.inputs.button>
                                                    <x-daisy::ui.inputs.button type="button" size="sm" variant="outline" color="error" square class="!min-h-9 !h-9 !w-9" wire:click.stop="removeField('{{ $field['id'] }}')" data-builder-delete aria-label="{{ __('daisy::form.builder.remove') }}">
                                                        <x-bi-x class="size-5" />
                                                    </x-daisy::ui.inputs.button>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($isContainer && (! $hasExpandableChildren || $isCollapsed))
                                            <tr class="daisy-form-builder-drop-row daisy-form-builder-drop-row-inside" data-builder-drop-row>
                                                <td colspan="4">
                                                    <button
                                                        type="button"
                                                        class="daisy-form-builder-drop-zone daisy-form-builder-drop-zone-position"
                                                        data-builder-drop-depth="{{ min($depth + 1, 12) }}"
                                                        data-builder-drop-tone="{{ ($depth + 1) % 16 }}"
                                                        data-builder-drop-target="{{ $fieldId }}"
                                                        data-builder-drop-descendants='@json($fieldDescendants[$fieldId] ?? [])'
                                                        data-builder-drop-kind="inside"
                                                        data-builder-drop-action="inside"
                                                        data-builder-drop-parent="{{ $fieldId }}"
                                                        data-builder-drop-index="{{ count((array) ($field['fields'] ?? [])) }}"
                                                        data-builder-drop-zone="inside"
                                                        aria-label="{{ __('daisy::form.builder.drop_inside') }}"
                                                    >
                                                        <span class="daisy-form-builder-drop-icon">+</span>
                                                        <span>{{ __('daisy::form.builder.drop_inside') }}</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($isLastDirectSibling)
                                            <tr class="daisy-form-builder-drop-row" data-builder-drop-row>
                                                <td colspan="4">
                                                    <button
                                                        type="button"
                                                        class="daisy-form-builder-drop-zone daisy-form-builder-drop-zone-position"
                                                        data-builder-drop-depth="{{ min($depth, 12) }}"
                                                        data-builder-drop-tone="{{ $depth % 16 }}"
                                                        data-builder-drop-target="{{ $fieldId }}"
                                                        data-builder-drop-descendants='@json($fieldDescendants[$fieldId] ?? [])'
                                                        data-builder-drop-kind="position"
                                                        data-builder-drop-action="after"
                                                        data-builder-drop-parent="{{ $parentKey }}"
                                                        data-builder-drop-index="{{ count($siblings) }}"
                                                        data-builder-drop-previous="{{ $fieldId }}"
                                                        data-builder-drop-zone="after"
                                                        aria-label="{{ __('daisy::form.builder.drop_position_end') }}"
                                                    >
                                                        <span class="daisy-form-builder-drop-icon">+</span>
                                                        <span>{{ __('daisy::form.builder.drop_position_end') }}</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-sm text-base-content/60">
                                                {{ __('daisy::form.builder.no_fields_match') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($jsonEditor)
                        <input type="radio" name="daisy-form-builder-authoring-tabs" class="tab" aria-label="{{ __('daisy::form.builder.json_tab') }}" />
                        <div class="tab-content pt-4">
                            <x-daisy::ui.advanced.code-editor
                                language="json"
                                :value="$canonicalJson"
                                height="28rem"
                                font-size="0.85rem"
                                :show-fold-all="true"
                                :show-unfold-all="true"
                                :show-format="true"
                                :show-copy="true"
                                wire:ignore
                                wire:key="daisy-form-builder-schema-json-{{ md5($canonicalJson) }}"
                                data-builder-json
                                data-builder-json-debounce="700"
                            />
                        </div>
                    @endif
                </div>

                @if($name)
                    <textarea name="{{ $name }}" class="hidden" data-builder-hidden>{{ $canonicalJson }}</textarea>
                @endif
                <script type="application/json" data-builder-export-json>@json($canonicalSchema)</script>
            </div>
        </section>

        <input type="radio" name="daisy-form-builder-mobile-tabs" class="tab lg:hidden" aria-label="{{ __('daisy::form.builder.preview_tab') }}" />
        <section class="tab-content border-base-300 bg-base-100 p-3 lg:!block lg:border-0 lg:bg-transparent lg:p-0" data-builder-preview-panel>
            <div class="space-y-4 lg:sticky lg:top-4">
                @if($preview)
                    <div class="rounded-box border border-base-300 bg-base-100 p-4" data-builder-stop-propagation data-builder-preview>
                        <h2 class="mb-3 font-semibold">{{ __('daisy::form.builder.preview') }}</h2>
                        <div wire:key="daisy-form-builder-viewer-{{ md5($canonicalJson) }}">
                            <x-daisy::forms.viewer
                                :schema="$canonicalSchema"
                                :value="$value"
                                :errors="$errors"
                                :submit-mode="$viewerSubmitMode"
                            />
                        </div>
                    </div>
                @endif

                <div class="rounded-box border border-error/40 bg-error/5 p-3 {{ count($diagnostics) === 0 ? 'hidden' : '' }}" data-builder-diagnostics-panel>
                    <h2 class="mb-2 font-semibold text-error">{{ __('daisy::form.builder.diagnostics') }}</h2>
                    <ul class="list-disc space-y-1 ps-4 text-sm text-error" data-builder-diagnostics>
                        @foreach($diagnostics as $diagnostic)
                            <li>{{ $diagnostic['message'] ?? $diagnostic['code'] ?? 'Invalid schema.' }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    </div>

    @if($selectedField)
        <x-daisy::ui.overlay.modal
            id="daisy-form-builder-field-editor"
            :open="$fieldEditorOpen"
            :backdrop="false"
            :close-button="false"
            :teleport="false"
            size="4xl"
            vertical="middle"
            boxClass="border border-base-300 shadow-xl"
            data-builder-editor-modal
        >
            <div class="daisy-form-builder-editor" wire:click.stop>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-base-content/60">{{ __('daisy::form.builder.edit_field') }}</p>
                        <h3 class="text-lg font-semibold">{{ $selectedField['label'] ?? $selectedField['id'] }}</h3>
                        <p class="text-sm text-base-content/60">{{ $selectedField['id'] ?? '' }} · {{ $selectedField['type'] ?? 'text' }}</p>
                    </div>
                    <x-daisy::ui.inputs.button type="button" size="sm" variant="ghost" color="neutral" square wire:click.stop="cancelFieldEditor" aria-label="{{ __('daisy::form.builder.cancel_editor') }}">×</x-daisy::ui.inputs.button>
                </div>

                @include('daisy::livewire.form-builder-field-properties', [
                    'selectedField' => $selectedField,
                    'propertyGroups' => $propertyGroups,
                ])
            </div>

            <x-slot:actions>
                <x-daisy::ui.inputs.button type="button" variant="outline" color="error" wire:click.stop="cancelFieldEditor" data-builder-editor-cancel>
                    {{ __('daisy::form.builder.cancel_editor') }}
                </x-daisy::ui.inputs.button>
                <x-daisy::ui.inputs.button type="button" color="success" wire:click.stop="closeFieldEditor" data-builder-editor-confirm>
                    {{ __('daisy::form.builder.confirm_editor') }}
                </x-daisy::ui.inputs.button>
            </x-slot:actions>
        </x-daisy::ui.overlay.modal>
    @endif
</div>

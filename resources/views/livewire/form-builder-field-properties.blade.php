@props([
    'selectedField',
    'propertyGroups',
])

<div class="space-y-5" data-builder-field-editor>
    @php
        $properties = collect($propertyGroups)
            ->flatMap(fn (array $group): array => $group['properties'] ?? [])
            ->values();
        $propertiesByPath = $properties->keyBy('path');
        $fieldName = (string) ($selectedField['name'] ?? '');
        $fieldId = (string) ($selectedField['id'] ?? '');
        $hasPayloadName = $propertiesByPath->has('name');
        $hasCustomId = ! $hasPayloadName || $fieldName === '' || $fieldId !== $fieldName;
        $editorTabsId = 'daisy-form-builder-field-editor-tabs-'.($fieldId !== '' ? $fieldId : 'field');
        $componentPaths = $properties
            ->pluck('path')
            ->filter(fn (string $path): bool => str_starts_with($path, 'attrs.') || (str_starts_with($path, 'ui.') && $path !== 'ui.width'))
            ->values()
            ->all();
        $dataPaths = $hasPayloadName ? ['default', 'options', 'rules'] : [];
        $editorSections = collect([
            [
                'id' => 'general',
                'label' => __('daisy::form.builder.editor_tabs.general'),
                'help' => __('daisy::form.builder.editor_tabs_help.general'),
                'paths' => ['label', 'description', 'text'],
                'always' => true,
            ],
            [
                'id' => 'data',
                'label' => __('daisy::form.builder.editor_tabs.data'),
                'help' => __('daisy::form.builder.editor_tabs_help.data'),
                'paths' => $dataPaths,
            ],
            [
                'id' => 'display',
                'label' => __('daisy::form.builder.editor_tabs.display'),
                'help' => __('daisy::form.builder.editor_tabs_help.display'),
                'paths' => array_values(array_merge(['ui.width'], $componentPaths)),
            ],
            [
                'id' => 'logic',
                'label' => __('daisy::form.builder.editor_tabs.logic'),
                'help' => __('daisy::form.builder.editor_tabs_help.logic'),
                'paths' => ['visibleWhen', 'computed'],
            ],
        ])
            ->filter(fn (array $section): bool => ($section['always'] ?? false)
                || collect($section['paths'])->contains(fn (string $path): bool => $propertiesByPath->has($path)))
            ->values()
            ->all();
    @endphp

    <div
        class="tabs tabs-border daisy-form-builder-editor-tabs"
        data-builder-editor-tabs
    >
        @foreach($editorSections as $sectionIndex => $section)
            <input
                type="radio"
                name="{{ $editorTabsId }}"
                class="tab"
                aria-label="{{ $section['label'] }}"
                @checked($sectionIndex === 0)
            />

            <section class="tab-content space-y-4 pt-4" data-builder-editor-tab-panel="{{ $section['id'] }}">
                <div class="rounded-box border border-base-300 bg-base-200/40 p-3">
                    <h4 class="text-sm font-semibold">{{ $section['label'] }}</h4>
                    <p class="mt-1 text-xs text-base-content/60">{{ $section['help'] }}</p>
                </div>

                @if($section['id'] === 'general')
                    <div class="grid gap-4 lg:grid-cols-2">
                        @if($hasPayloadName && $propertiesByPath->has('name'))
                            @php
                                $property = $propertiesByPath->get('name');
                            @endphp
                            <x-daisy::ui.partials.form-field :label="$property['label']">
                                <x-daisy::ui.inputs.input
                                    type="text"
                                    size="sm"
                                    value="{{ $fieldName }}"
                                    data-builder-field-name
                                />
                                <x-slot:hintSlot>
                                    {{ $property['help'] ?? 'name' }}
                                    <code class="kbd kbd-xs ms-1">name</code>
                                </x-slot:hintSlot>
                            </x-daisy::ui.partials.form-field>

                            <div class="space-y-3 rounded-box border border-base-300 p-3">
                                <label class="flex items-start gap-3 text-sm">
                                    <x-daisy::ui.inputs.toggle :checked="$hasCustomId" data-builder-custom-id />
                                    <span>
                                        <span class="block font-medium">{{ __('daisy::form.builder.custom_field_id') }}</span>
                                        <span class="block text-xs text-base-content/60">{{ __('daisy::form.builder.custom_field_id_help') }}</span>
                                    </span>
                                </label>

                                @php
                                    $property = $propertiesByPath->get('id');
                                @endphp
                                <div data-builder-custom-id-panel @if(! $hasCustomId) hidden @endif>
                                    <x-daisy::ui.partials.form-field :label="$property['label']">
                                        <x-daisy::ui.inputs.input type="text" size="sm" value="{{ $fieldId }}" wire:change="updateSelectedPath('id', $event.target.value)" />
                                        <x-slot:hintSlot>
                                            {{ $property['help'] ?? 'id' }}
                                            <code class="kbd kbd-xs ms-1">id</code>
                                        </x-slot:hintSlot>
                                    </x-daisy::ui.partials.form-field>
                                </div>
                            </div>
                        @elseif($propertiesByPath->has('id'))
                            @php
                                $property = $propertiesByPath->get('id');
                            @endphp
                            <x-daisy::ui.partials.form-field :label="$property['label']">
                                <x-daisy::ui.inputs.input type="text" size="sm" value="{{ $fieldId }}" wire:change="updateSelectedPath('id', $event.target.value)" />
                                <x-slot:hintSlot>
                                    {{ $property['help'] ?? 'id' }}
                                    <code class="kbd kbd-xs ms-1">id</code>
                                </x-slot:hintSlot>
                            </x-daisy::ui.partials.form-field>
                        @endif
                    </div>
                @endif

                @foreach($section['paths'] as $path)
                    @continue(! $propertiesByPath->has($path))
                    @php
                        $property = $propertiesByPath->get($path);
                        $path = $property['path'];
                        $control = $property['control'];
                        $current = data_get($selectedField, $path);
                    @endphp

                @if($control === 'options')
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="text-sm font-medium">{{ $property['label'] }}</span>
                                <p class="text-xs text-base-content/60">{{ $property['help'] ?? $path }}</p>
                            </div>
                            <x-daisy::ui.inputs.button type="button" size="xs" variant="outline" color="primary" wire:click="addSelectedOption">{{ __('daisy::form.builder.add_option') }}</x-daisy::ui.inputs.button>
                        </div>

                        @foreach(array_values((array) ($selectedField['options'] ?? [])) as $index => $option)
                            <div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] gap-2">
                                <x-daisy::ui.inputs.input size="sm" value="{{ $option['label'] ?? '' }}" wire:change="updateSelectedOption({{ $index }}, 'label', $event.target.value)" aria-label="{{ __('daisy::form.builder.option_label') }}" />
                                <x-daisy::ui.inputs.input size="sm" value="{{ $option['value'] ?? '' }}" wire:change="updateSelectedOption({{ $index }}, 'value', $event.target.value)" aria-label="{{ __('daisy::form.builder.option_value') }}" />
                                <x-daisy::ui.inputs.button type="button" size="sm" variant="ghost" color="error" square wire:click="removeSelectedOption({{ $index }})" aria-label="{{ __('daisy::form.builder.remove_option') }}">×</x-daisy::ui.inputs.button>
                            </div>
                        @endforeach
                    </div>
                @elseif($control === 'select')
                    <x-daisy::ui.partials.form-field :label="$property['label']" :hint="$property['help'] ?? $path">
                        <x-daisy::ui.inputs.select size="sm" wire:change="updateSelectedPath('{{ $path }}', $event.target.value)">
                            @foreach($property['options'] ?? [] as $option)
                                <option value="{{ $option }}" @selected((string) ($current ?? '') === (string) $option)>{{ $option === '' ? __('daisy::form.builder.default_option') : $option }}</option>
                            @endforeach
                        </x-daisy::ui.inputs.select>
                        <x-slot:hintSlot>
                            {{ $property['help'] ?? $path }}
                            <code class="kbd kbd-xs ms-1">{{ $path }}</code>
                        </x-slot:hintSlot>
                    </x-daisy::ui.partials.form-field>
                @elseif($control === 'boolean')
                    <label class="flex items-center gap-2 text-sm">
                        <x-daisy::ui.inputs.toggle :checked="(bool) $current" wire:change="updateSelectedPath('{{ $path }}', $event.target.checked)" />
                        <span>
                            <span class="block">{{ $property['label'] }}</span>
                            <span class="block text-xs text-base-content/60">{{ $property['help'] ?? $path }} <code class="kbd kbd-xs">{{ $path }}</code></span>
                        </span>
                    </label>
                @elseif($control === 'textarea')
                    <x-daisy::ui.partials.form-field :label="$property['label']">
                        <x-daisy::ui.inputs.textarea rows="3" size="sm" wire:change="updateSelectedPath('{{ $path }}', $event.target.value)">{{ is_scalar($current) ? $current : '' }}</x-daisy::ui.inputs.textarea>
                        <x-slot:hintSlot>
                            {{ $property['help'] ?? $path }}
                            <code class="kbd kbd-xs ms-1">{{ $path }}</code>
                        </x-slot:hintSlot>
                    </x-daisy::ui.partials.form-field>
                @elseif($control === 'json')
                    <x-daisy::ui.partials.form-field :label="$property['label']">
                        <x-daisy::ui.advanced.code-editor
                            language="json"
                            :value="json_encode($current ?? ($path === 'default' ? null : []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)"
                            height="11rem"
                            font-size="0.78rem"
                            :show-fold-all="false"
                            :show-unfold-all="false"
                            :show-format="true"
                            :show-copy="true"
                            wire:ignore
                            wire:key="daisy-form-builder-field-json-{{ $selectedField['id'] ?? 'field' }}-{{ str_replace('.', '-', $path) }}"
                            data-builder-json-property="{{ $path }}"
                            data-builder-json-debounce="500"
                        />
                        <x-slot:hintSlot>
                            {{ $property['help'] ?? $path }}
                            <code class="kbd kbd-xs ms-1">{{ $path }}</code>
                        </x-slot:hintSlot>
                    </x-daisy::ui.partials.form-field>
                @else
                    <x-daisy::ui.partials.form-field :label="$property['label']">
                        <x-daisy::ui.inputs.input type="{{ $control === 'number' ? 'number' : 'text' }}" size="sm" value="{{ is_scalar($current) ? $current : '' }}" wire:change="updateSelectedPath('{{ $path }}', $event.target.value)" />
                        <x-slot:hintSlot>
                            {{ $property['help'] ?? $path }}
                            <code class="kbd kbd-xs ms-1">{{ $path }}</code>
                        </x-slot:hintSlot>
                    </x-daisy::ui.partials.form-field>
                @endif
                @endforeach
            </section>
        @endforeach
    </div>
</div>

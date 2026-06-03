@props([
    'selectedField',
    'propertyGroups',
])

<div class="space-y-5" data-builder-field-editor>
    @foreach($propertyGroups as $group)
        <section class="space-y-3">
            <h4 class="border-b border-base-300 pb-2 text-xs font-semibold uppercase tracking-wide text-base-content/60">{{ $group['label'] }}</h4>

            @foreach($group['properties'] as $property)
                @php
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
                            x-data
                            x-on:code:change.debounce.500ms="$wire.updateSelectedJson('{{ $path }}', $event.detail.value)"
                            data-builder-json-property="{{ $path }}"
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

{{--
    Recursive field renderer aligning Daisy UI atoms with JSON schema fragments.

    Emits `data-form-field`, `data-form-input`, and `data-form-errors` hooks consumed by `form-kit/runtime.js`.
--}}
@props([
    'field',
    'value' => [],
    'errors' => [],
    'readonly' => false,
])

@php
    $allErrors = $errors;
    $type = $field['type'] ?? 'text';
    $id = $field['id'] ?? uniqid('field-');
    $name = $field['name'] ?? $id;
    $label = $field['label'] ?? $name;
    $description = $field['description'] ?? null;
    $fieldValue = data_get($value, $name, data_get($value, $id, $field['default'] ?? null));
    $fieldErrors = array_values((array) data_get($allErrors, $name, []));
    $hasError = count($fieldErrors) > 0;
    // Hidden computed values still participate in payloads while staying out of the visible layout.
    $isComputedHidden = ($field['computed']['mode'] ?? null) === 'hidden';
    $isReadonly = (bool) $readonly || (($field['computed']['mode'] ?? null) === 'readonly');
    $options = array_values((array) ($field['options'] ?? []));
@endphp

@if($type === 'staticText')
    <div data-form-field="{{ $id }}" class="prose max-w-none text-base-content">
        <p>{{ $field['text'] ?? $label }}</p>
    </div>
@elseif($isComputedHidden || $type === 'hidden')
    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}"
        data-form-input="{{ $id }}"
    />
@elseif(in_array($type, ['section', 'tabs', 'wizardStep'], true))
    <fieldset data-form-field="{{ $id }}" class="space-y-4 rounded-box border border-base-300 p-4">
        <legend class="px-2 font-medium">{{ $label }}</legend>
        @foreach((array) ($field['fields'] ?? []) as $child)
            @include('daisy::components.forms.partials.field', [
                'field' => $child,
                'value' => $value,
                'errors' => $allErrors,
                'readonly' => $readonly,
            ])
        @endforeach
    </fieldset>
@else
    @php
        $errors = new \Illuminate\Support\ViewErrorBag();
    @endphp
    <x-daisy::ui.partials.form-field
        :name="$name"
        :label="$label"
        :hint="$description"
        :error="$fieldErrors[0] ?? null"
        data-form-field="{{ $id }}"
    >
        @if($type === 'textarea')
            <x-daisy::ui.inputs.textarea
                name="{{ $name }}"
                data-form-input="{{ $id }}"
                :disabled="$readonly"
                @class([$hasError ? 'textarea-error' : null])
            >{{ is_scalar($fieldValue) ? $fieldValue : '' }}</x-daisy::ui.inputs.textarea>
        @elseif($type === 'select')
            <x-daisy::ui.inputs.select
                name="{{ $name }}"
                data-form-input="{{ $id }}"
                :disabled="$readonly"
                @class([$hasError ? 'select-error' : null])
            >
                <option value=""></option>
                @foreach($options as $option)
                    @php
                        $optionValue = (string) ($option['value'] ?? $option['label'] ?? '');
                        $optionLabel = (string) ($option['label'] ?? $optionValue);
                    @endphp
                    <option value="{{ $optionValue }}" @selected((string) $fieldValue === $optionValue) @disabled((bool) ($option['disabled'] ?? false))>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </x-daisy::ui.inputs.select>
        @elseif($type === 'radio')
            <div class="flex flex-wrap gap-3">
                @foreach($options as $option)
                    @php
                        $optionValue = (string) ($option['value'] ?? $option['label'] ?? '');
                        $optionLabel = (string) ($option['label'] ?? $optionValue);
                    @endphp
                    <label class="inline-flex items-center gap-2">
                        <x-daisy::ui.inputs.radio
                            :name="$name"
                            :value="$optionValue"
                            :checked="(string) $fieldValue === $optionValue"
                            :disabled="$readonly"
                            data-form-input="{{ $id }}"
                        />
                        <span>{{ $optionLabel }}</span>
                    </label>
                @endforeach
            </div>
        @elseif($type === 'checkbox')
            <label class="inline-flex items-center gap-2">
                <x-daisy::ui.inputs.checkbox
                    name="{{ $name }}"
                    value="1"
                    :checked="(bool) $fieldValue"
                    :disabled="$readonly"
                    data-form-input="{{ $id }}"
                />
                <span>{{ $label }}</span>
            </label>
        @elseif($type === 'toggle')
            <label class="inline-flex items-center gap-2">
                <x-daisy::ui.inputs.toggle
                    name="{{ $name }}"
                    value="1"
                    :checked="(bool) $fieldValue"
                    :disabled="$readonly"
                    data-form-input="{{ $id }}"
                />
                <span>{{ $label }}</span>
            </label>
        @elseif($type === 'range')
            <x-daisy::ui.inputs.range
                name="{{ $name }}"
                :value="$fieldValue"
                :disabled="$readonly"
                data-form-input="{{ $id }}"
            />
        @elseif($type === 'file')
            <x-daisy::ui.inputs.file-input
                name="{{ $name }}"
                :disabled="$readonly"
                data-form-input="{{ $id }}"
            />
        @elseif($type === 'signature')
            <x-daisy::ui.inputs.sign
                name="{{ $name }}"
                :disabled="$readonly"
                data-form-input="{{ $id }}"
            />
        @else
            <x-daisy::ui.inputs.input
                type="{{ in_array($type, ['email', 'tel', 'url', 'password', 'number', 'date', 'time', 'datetime-local', 'month', 'color'], true) ? $type : 'text' }}"
                name="{{ $name }}"
                value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}"
                :disabled="$readonly"
                data-form-input="{{ $id }}"
                @class([$hasError ? 'input-error' : null])
            />
        @endif

        <p class="mt-1 hidden text-sm text-error" data-form-errors="{{ $id }}"></p>
    </x-daisy::ui.partials.form-field>
@endif

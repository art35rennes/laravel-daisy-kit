{{--
    Recursive field renderer aligning Daisy UI atoms with JSON schema fragments.

    Emits `data-form-field`, `data-form-input`, and `data-form-errors` hooks consumed by `form-kit/runtime.js`.
--}}
@props([
    'field',
    'value' => [],
    'errors' => [],
    'readonly' => false,
    'formId' => null,
])

@php
    $allErrors = $errors;
    $type = $field['type'] ?? 'text';
    $id = $field['id'] ?? uniqid('field-');
    $name = $field['name'] ?? $id;
    $label = $field['label'] ?? $name;
    $description = $field['description'] ?? null;
    $safeDomSeed = \Illuminate\Support\Str::slug((string) (($formId ?: 'daisy-form-viewer').'-'.$id));
    $fieldDomId = $safeDomSeed !== '' ? $safeDomSeed : 'daisy-form-field-'.uniqid();
    $controlId = $fieldDomId.'-control';
    $fieldValue = data_get($value, $name, data_get($value, $id, $field['default'] ?? null));
    $fieldErrors = array_values((array) data_get($allErrors, $name, []));
    $hasError = count($fieldErrors) > 0;
    // Hidden computed values still participate in payloads while staying out of the visible layout.
    $isComputedHidden = ($field['computed']['mode'] ?? null) === 'hidden';
    $isReadonly = (bool) $readonly || (($field['computed']['mode'] ?? null) === 'readonly');
    $options = array_values((array) ($field['options'] ?? []));
    $attrs = new \Illuminate\View\ComponentAttributeBag((array) ($field['attrs'] ?? []));
    $ui = (array) ($field['ui'] ?? []);
    $size = $ui['size'] ?? 'md';
    $color = $ui['color'] ?? null;
    $widthClass = match ($ui['width'] ?? 'full') {
        '1/4' => 'col-span-12 md:col-span-3',
        '1/3' => 'col-span-12 md:col-span-4',
        '1/2' => 'col-span-12 md:col-span-6',
        '2/3' => 'col-span-12 md:col-span-8',
        '3/4' => 'col-span-12 md:col-span-9',
        default => 'col-span-12',
    };
    $firstOption = $options[0] ?? null;
    $firstOptionValue = (string) (($firstOption['value'] ?? $firstOption['label'] ?? '') ?: '0');
    $firstOptionSuffix = \Illuminate\Support\Str::slug($firstOptionValue) ?: '0';
    $labelFor = match ($type) {
        'signature' => false,
        'radio' => count($options) > 0 ? $controlId.'-'.$firstOptionSuffix : null,
        default => $controlId,
    };
@endphp

@if($type === 'staticText')
    <div data-form-field="{{ $id }}" class="{{ $widthClass }} prose max-w-none text-base-content">
        <p>{{ $field['text'] ?? $label }}</p>
    </div>
@elseif($isComputedHidden || $type === 'hidden')
    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}"
        data-form-input="{{ $id }}"
    />
@elseif($type === 'tabs')
    @php
        $tabs = array_values((array) ($field['fields'] ?? []));
        $tabRadioName = 'daisy-form-tabs-'.\Illuminate\Support\Str::slug((string) $id);
    @endphp
    <section data-form-field="{{ $id }}" class="{{ $widthClass }} space-y-3">
        <div class="space-y-1">
            <h3 class="font-medium">{{ $label }}</h3>
            @if($description)
                <p class="text-sm text-base-content/70">{{ $description }}</p>
            @endif
        </div>

        <div class="tabs tabs-box tabs-top w-full">
            @forelse($tabs as $index => $tab)
                @php
                    $tabId = $tab['id'] ?? "{$id}-{$index}";
                    $tabLabel = $tab['label'] ?? $tabId;
                    $tabFields = array_values((array) ($tab['fields'] ?? []));
                @endphp
                <input
                    type="radio"
                    name="{{ $tabRadioName }}"
                    class="tab"
                    aria-label="{{ $tabLabel }}"
                    @checked($index === 0)
                />
                <div class="tab-content border-base-300 bg-base-100 p-4">
                    <div data-form-field="{{ $tabId }}" class="grid grid-cols-12 gap-4">
                        @if(($tab['description'] ?? null) && count($tabFields) > 0)
                            <p class="text-sm text-base-content/70">{{ $tab['description'] }}</p>
                        @endif

                        @if(count($tabFields) > 0)
                            @foreach($tabFields as $child)
                                @include('daisy::components.forms.partials.field', [
                                    'field' => $child,
                                    'value' => $value,
                                    'errors' => $allErrors,
                                    'readonly' => $readonly,
                                    'formId' => $formId,
                                ])
                            @endforeach
                        @elseif(($tab['type'] ?? null) !== 'tabs')
                            @include('daisy::components.forms.partials.field', [
                                'field' => $tab,
                                'value' => $value,
                                'errors' => $allErrors,
                                'readonly' => $readonly,
                                'formId' => $formId,
                            ])
                        @else
                            <p class="text-sm text-base-content/60">No fields configured.</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-sm text-base-content/60">No tabs configured.</div>
            @endforelse
        </div>
    </section>
@elseif(in_array($type, ['section', 'wizardStep'], true))
    <fieldset data-form-field="{{ $id }}" class="{{ $widthClass }} grid grid-cols-12 gap-4 rounded-box border border-base-300 p-4">
        <legend class="px-2 font-medium">{{ $label }}</legend>
        @if($description)
            <p class="col-span-12 text-sm text-base-content/70">{{ $description }}</p>
        @endif
        @foreach((array) ($field['fields'] ?? []) as $child)
            @include('daisy::components.forms.partials.field', [
                'field' => $child,
                'value' => $value,
                'errors' => $allErrors,
                'readonly' => $readonly,
                'formId' => $formId,
            ])
        @endforeach
    </fieldset>
@else
    @php
        $errors = new \Illuminate\Support\ViewErrorBag();
    @endphp
    <x-daisy::ui.partials.form-field
        :name="$name"
        :for="$labelFor"
        :label="$label"
        :hint="$description"
        :error="$fieldErrors[0] ?? null"
        data-form-field="{{ $id }}"
        class="{{ $widthClass }}"
    >
        @if($type === 'textarea')
            <x-daisy::ui.inputs.textarea
                id="{{ $controlId }}"
                name="{{ $name }}"
                placeholder="{{ $attrs->get('placeholder') }}"
                data-form-input="{{ $id }}"
                :disabled="$readonly"
                :size="$size"
                :color="$color"
                :rows="$attrs->get('rows', 4)"
                :readonly="$isReadonly && ! $readonly"
                @class([$hasError ? 'textarea-error' : null])
            >{{ is_scalar($fieldValue) ? $fieldValue : '' }}</x-daisy::ui.inputs.textarea>
        @elseif($type === 'select')
            <x-daisy::ui.inputs.select
                id="{{ $controlId }}"
                name="{{ $name }}"
                placeholder="{{ $attrs->get('placeholder') }}"
                data-form-input="{{ $id }}"
                :disabled="$readonly"
                :size="$size"
                :color="$color"
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
                        $optionId = $controlId.'-'.\Illuminate\Support\Str::slug($optionValue !== '' ? $optionValue : (string) $loop->index);
                    @endphp
                    <label class="inline-flex items-center gap-2" for="{{ $optionId }}">
                        <x-daisy::ui.inputs.radio
                            id="{{ $optionId }}"
                            :name="$name"
                            :value="$optionValue"
                            :checked="(string) $fieldValue === $optionValue"
                            :disabled="$readonly"
                            :size="$size"
                            :color="$color"
                            data-form-input="{{ $id }}"
                        />
                        <span>{{ $optionLabel }}</span>
                    </label>
                @endforeach
            </div>
        @elseif($type === 'checkbox')
            <label class="inline-flex items-center gap-2">
                <x-daisy::ui.inputs.checkbox
                    id="{{ $controlId }}"
                    name="{{ $name }}"
                    value="1"
                    :checked="(bool) $fieldValue"
                    :disabled="$readonly"
                    :size="$size"
                    :color="$color"
                    data-form-input="{{ $id }}"
                />
                <span>{{ $label }}</span>
            </label>
        @elseif($type === 'toggle')
            <label class="inline-flex items-center gap-2">
                <x-daisy::ui.inputs.toggle
                    id="{{ $controlId }}"
                    name="{{ $name }}"
                    value="1"
                    :checked="(bool) $fieldValue"
                    :disabled="$readonly"
                    :size="$size"
                    :color="$color"
                    data-form-input="{{ $id }}"
                />
                <span>{{ $label }}</span>
            </label>
        @elseif($type === 'range')
            <x-daisy::ui.inputs.range
                id="{{ $controlId }}"
                name="{{ $name }}"
                :value="$fieldValue"
                :disabled="$readonly"
                :size="$size"
                :color="$color"
                :min="$attrs->get('min', 0)"
                :max="$attrs->get('max', 100)"
                :step="$attrs->get('step', 1)"
                data-form-input="{{ $id }}"
            />
        @elseif($type === 'file')
            <x-daisy::ui.inputs.file-input
                id="{{ $controlId }}"
                name="{{ $name }}"
                accept="{{ $attrs->get('accept') }}"
                :disabled="$readonly"
                :size="$size"
                :color="$color"
                :multiple="(bool) $attrs->get('multiple', false)"
                data-form-input="{{ $id }}"
            />
        @elseif($type === 'signature')
            <x-daisy::ui.inputs.sign
                name="{{ $name }}"
                :value="$fieldValue"
                :width="$attrs->get('width', 400)"
                :height="$attrs->get('height', 200)"
                :pen-color="$attrs->get('penColor', '#000000')"
                :min-width="$attrs->get('minWidth', 0.5)"
                :max-width="$attrs->get('maxWidth', 2.5)"
                :velocity-filter-weight="$attrs->get('velocityFilterWeight', 0.7)"
                :responsive="filter_var($attrs->get('responsive', true), FILTER_VALIDATE_BOOL)"
                :show-actions="filter_var($attrs->get('showActions', ! $readonly), FILTER_VALIDATE_BOOL)"
                :download-format="$attrs->get('downloadFormat', 'png')"
                :download-filename="$attrs->get('downloadFilename', $name)"
                :disabled="$readonly"
                data-form-input="{{ $id }}"
            />
        @elseif($type === 'color')
            <x-daisy::ui.inputs.color-picker
                id="{{ $controlId }}"
                name="{{ $name }}"
                :value="is_scalar($fieldValue) ? $fieldValue : '#563d7c'"
                :mode="$attrs->get('mode', 'advanced')"
                :dropdown="filter_var($attrs->get('dropdown', true), FILTER_VALIDATE_BOOL)"
                :swatches="(array) $attrs->get('swatches', [])"
                :swatches-height="$attrs->get('swatchesHeight', 0)"
                :show-palette="filter_var($attrs->get('showPalette', true), FILTER_VALIDATE_BOOL)"
                :show-inputs="filter_var($attrs->get('showInputs', true), FILTER_VALIDATE_BOOL)"
                :show-format-toggle="filter_var($attrs->get('showFormatToggle', true), FILTER_VALIDATE_BOOL)"
                :show-alpha="filter_var($attrs->get('showAlpha', true), FILTER_VALIDATE_BOOL)"
                :show-hue="filter_var($attrs->get('showHue', true), FILTER_VALIDATE_BOOL)"
                :disabled="$readonly"
                data-form-input="{{ $id }}"
            />
        @else
            <x-daisy::ui.inputs.input
                id="{{ $controlId }}"
                type="{{ in_array($type, ['email', 'tel', 'url', 'password', 'number', 'date', 'time', 'datetime-local', 'month', 'color'], true) ? $type : 'text' }}"
                name="{{ $name }}"
                value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}"
                placeholder="{{ $attrs->get('placeholder') }}"
                autocomplete="{{ $attrs->get('autocomplete') }}"
                min="{{ $attrs->get('min') }}"
                max="{{ $attrs->get('max') }}"
                step="{{ $attrs->get('step') }}"
                :obfuscate="filter_var($attrs->get('obfuscate', false), FILTER_VALIDATE_BOOL)"
                obfuscate-char="{{ $attrs->get('obfuscateChar') }}"
                obfuscate-keep-end="{{ $attrs->get('obfuscateKeepEnd') }}"
                :input-mask="filled($attrs->get('mask')) || filled($attrs->get('customMask')) || filled($attrs->get('customValidator')) || filter_var($attrs->get('inputMask', false), FILTER_VALIDATE_BOOL)"
                :mask="filled($attrs->get('mask')) ? $attrs->get('mask') : null"
                :mask-char-placeholder="filled($attrs->get('maskCharPlaceholder')) ? $attrs->get('maskCharPlaceholder') : null"
                :mask-placeholder="$attrs->has('maskPlaceholder') ? filter_var($attrs->get('maskPlaceholder'), FILTER_VALIDATE_BOOL) : null"
                :input-placeholder="$attrs->has('inputPlaceholder') ? filter_var($attrs->get('inputPlaceholder'), FILTER_VALIDATE_BOOL) : null"
                :clear-incomplete="$attrs->has('clearIncomplete') ? filter_var($attrs->get('clearIncomplete'), FILTER_VALIDATE_BOOL) : null"
                :custom-mask="filled($attrs->get('customMask')) ? $attrs->get('customMask') : null"
                :custom-validator="filled($attrs->get('customValidator')) ? $attrs->get('customValidator') : null"
                :disabled="$readonly"
                :size="$size"
                :color="$color"
                data-form-input="{{ $id }}"
                :readonly="$isReadonly && ! $readonly"
                @class([$hasError ? 'input-error' : null])
            />
        @endif

        <p class="mt-1 hidden text-sm text-error" data-form-errors="{{ $id }}"></p>
    </x-daisy::ui.partials.form-field>
@endif

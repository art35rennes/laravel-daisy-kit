{{--
    Server-rendered form shell hydrated by `data-module="form-viewer"` (`resources/js/modules/form-viewer.js`).

    Pass structured `schema`, `value`, and `errors` bags; nested fields recurse via `forms.partials.field`.
--}}
@props([
    'schema',
    'value' => [],
    'errors' => [],
    'submitMode' => 'event',
    'action' => null,
    'method' => 'POST',
    'readonly' => false,
    'validateOn' => 'submit',
])

@php
    $schema = is_string($schema) ? (json_decode($schema, true) ?: []) : (array) $schema;
    $value = is_string($value) ? (json_decode($value, true) ?: []) : (array) $value;
    $errors = $errors instanceof \Illuminate\Contracts\Support\MessageBag
        ? (new \Art35rennes\DaisyKit\FormKit\FormErrorBagMapper())->map($errors)
        : (array) $errors;
    $method = strtoupper((string) $method);
    $formId = $attributes->get('id') ?? 'daisy-form-viewer-'.uniqid();
    $submit = (array) ($schema['submit'] ?? []);
    $submitLabel = $submit['label'] ?? __('daisy::form.submit');
    $resolvedSubmitMode = $submitMode ?: ($submit['mode'] ?? 'event');
    $layoutType = data_get($schema, 'layout.type', 'one-page');
    $isMultiStep = $layoutType === 'multi-step';
    $topLevelFields = array_values((array) ($schema['fields'] ?? []));
    $steps = array_values(array_filter($topLevelFields, fn ($field) => ($field['type'] ?? null) === 'wizardStep'));
@endphp

<form
    {{ $attributes->merge(['id' => $formId, 'class' => 'daisy-form-viewer space-y-6']) }}
    method="{{ $method }}"
    action="{{ $action ?? '#' }}"
    data-module="form-viewer"
    data-submit-mode="{{ $resolvedSubmitMode }}"
    data-validate-on="{{ $validateOn }}"
>
    @if($method !== 'GET')
        @csrf
    @endif

    @if($method !== 'GET' && $method !== 'POST')
        @method($method)
    @endif

    @if(data_get($schema, 'meta.title'))
        <header>
            <h2 class="text-xl font-semibold">{{ data_get($schema, 'meta.title') }}</h2>
            @if(data_get($schema, 'meta.description'))
                <p class="mt-1 text-sm text-base-content/70">{{ data_get($schema, 'meta.description') }}</p>
            @endif
        </header>
    @endif

    <div class="space-y-4" data-form-fields>
        @if($isMultiStep && count($steps) > 0)
            <div class="steps steps-horizontal w-full">
                @foreach($steps as $index => $step)
                    <div class="step {{ $index === 0 ? 'step-primary' : '' }}" data-form-step-indicator="{{ $index }}">
                        {{ $step['label'] ?? __('daisy::form.step', ['number' => $index + 1]) }}
                    </div>
                @endforeach
            </div>

            @foreach($steps as $index => $step)
                <section
                    class="space-y-4"
                    data-form-step="{{ $step['id'] ?? $index }}"
                    data-form-step-index="{{ $index }}"
                >
                    @if($step['label'] ?? null)
                        <header>
                            <h3 class="text-lg font-semibold">{{ $step['label'] }}</h3>
                            @if($step['description'] ?? null)
                                <p class="mt-1 text-sm text-base-content/70">{{ $step['description'] }}</p>
                            @endif
                        </header>
                    @endif

                    @foreach((array) ($step['fields'] ?? []) as $field)
                        @include('daisy::components.forms.partials.field', [
                            'field' => $field,
                            'value' => $value,
                            'errors' => $errors,
                            'readonly' => $readonly,
                        ])
                    @endforeach
                </section>
            @endforeach
        @else
            @foreach($topLevelFields as $field)
                @include('daisy::components.forms.partials.field', [
                    'field' => $field,
                    'value' => $value,
                    'errors' => $errors,
                    'readonly' => $readonly,
                ])
            @endforeach
        @endif
    </div>

    @if($resolvedSubmitMode !== 'none' && ! $readonly)
        <div class="flex items-center justify-end gap-2 border-t border-base-300 pt-4">
            @if($isMultiStep && count($steps) > 0)
                <x-daisy::ui.inputs.button type="button" color="ghost" data-form-previous>
                    {{ __('daisy::form.previous') }}
                </x-daisy::ui.inputs.button>

                <x-daisy::ui.inputs.button type="button" color="primary" data-form-next>
                    {{ __('daisy::form.next') }}
                </x-daisy::ui.inputs.button>
            @endif

            <x-daisy::ui.inputs.button type="submit" color="primary" data-form-submit>
                {{ $submitLabel }}
            </x-daisy::ui.inputs.button>
        </div>
    @endif

    <script type="application/json" data-form-schema>@json($schema)</script>
    <script type="application/json" data-form-value>@json($value)</script>
    <script type="application/json" data-form-errors-payload>@json($errors)</script>

    @include('daisy::components.partials.assets')
</form>

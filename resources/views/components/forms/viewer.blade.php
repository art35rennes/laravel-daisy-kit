{{--
    Server-rendered form shell hydrated by `data-module="form-viewer"` (`resources/js/modules/form-viewer.js`).

    Pass structured `schema`, `value`, and `errors` bags; nested fields recurse via `forms.partials.field`.
--}}
@props([
    'schema',
    'value' => [],
    'errors' => [],
    'submitMode' => null,
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
    $htmlMethod = $method === 'GET' ? 'GET' : 'POST';
    $formId = $attributes->get('id') ?? 'daisy-form-viewer-'.uniqid();
    $submit = (array) ($schema['submit'] ?? []);
    $submitLabel = $submit['label'] ?? __('daisy::form.submit');
    $submitModes = ['event', 'html', 'fetch', 'none'];
    $schemaSubmitMode = $submit['mode'] ?? null;
    $resolvedSubmitMode = in_array($submitMode, $submitModes, true)
        ? $submitMode
        : (in_array($schemaSubmitMode, $submitModes, true) ? $schemaSubmitMode : 'event');
    $layoutType = data_get($schema, 'layout.type', 'one-page');
    $isMultiStep = $layoutType === 'multi-step';
    $topLevelFields = array_values((array) ($schema['fields'] ?? []));
    $steps = array_values(array_filter($topLevelFields, fn ($field) => ($field['type'] ?? null) === 'wizardStep'));
    $stepItems = array_values(array_map(
        fn (array $step, int $index): array => [
            'label' => $step['label'] ?? __('daisy::form.step', ['number' => $index + 1]),
            'index' => $index + 1,
        ],
        $steps,
        array_keys($steps),
    ));
@endphp

<form
    {{ $attributes->merge(['id' => $formId, 'class' => 'daisy-form-viewer space-y-6']) }}
    method="{{ $htmlMethod }}"
    action="{{ $action ?? '#' }}"
    data-module="form-viewer"
    data-form-id="{{ $formId }}"
    data-form-method="{{ $method }}"
    data-submit-mode="{{ $resolvedSubmitMode }}"
    data-validate-on="{{ $validateOn }}"
    data-readonly="{{ $readonly ? 'true' : 'false' }}"
>
    @if($htmlMethod !== 'GET')
        @csrf
    @endif

    @if(! in_array($method, ['GET', 'POST'], true))
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

    <div class="grid grid-cols-12 gap-4" data-form-fields>
        @if($isMultiStep && count($steps) > 0)
            <x-daisy::ui.navigation.steps
                :items="$stepItems"
                :current="1"
                :horizontal="true"
                class="col-span-12 w-full"
                indicator-attribute="data-form-step-indicator"
                :indicator-offset="-1"
            />

            @foreach($steps as $index => $step)
                <section
                    class="col-span-12 grid grid-cols-12 gap-4"
                    data-form-step="{{ $step['id'] ?? $index }}"
                    data-form-step-index="{{ $index }}"
                >
                    @if($step['label'] ?? null)
                        <header class="col-span-12">
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
                            'formId' => $formId,
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
                    'formId' => $formId,
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

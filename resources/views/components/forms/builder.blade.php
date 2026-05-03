{{--
    Interactive schema authoring surface wired to `data-module="form-builder"`.

    Props hydrate embedded JSON payloads consumed by `resources/js/modules/form-builder.js`.
    Optional `name` mirrors the canonical schema JSON inside a hidden textarea for HTML submissions.
--}}
@props([
    'schema' => null,
    'fieldTypes' => null,
    'functionCatalog' => null,
    'preview' => true,
    'jsonEditor' => true,
    'name' => null,
])

@php
    $schema = is_string($schema) ? (json_decode($schema, true) ?: null) : $schema;
    $fieldTypes = is_string($fieldTypes) ? (json_decode($fieldTypes, true) ?: null) : $fieldTypes;
    $functionCatalog = $functionCatalog ?? config('daisy-kit.forms.jsonata.function_catalog', []);
    $functionCatalog = is_string($functionCatalog) ? (json_decode($functionCatalog, true) ?: []) : (array) $functionCatalog;
    $builderId = $attributes->get('id') ?? 'daisy-form-builder-'.uniqid();
@endphp

<div
    {{ $attributes->merge(['id' => $builderId, 'class' => 'daisy-form-builder grid gap-4 lg:grid-cols-[16rem_minmax(0,1fr)_20rem]']) }}
    data-module="form-builder"
>
    <section class="space-y-3">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-base-content/70">{{ __('daisy::form.builder.palette') }}</h2>
        <div class="grid gap-2" data-builder-palette></div>

        <div>
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-base-content/70">{{ __('daisy::form.builder.functions') }}</h3>
            <ul class="space-y-1 text-xs" data-builder-functions></ul>
        </div>
    </section>

    <section class="space-y-4">
        <div class="rounded-box border border-base-300 bg-base-100 p-3">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h2 class="font-semibold">{{ __('daisy::form.builder.outline') }}</h2>
            </div>
            <div class="space-y-2" data-builder-outline></div>
        </div>

        @if($preview)
            <div class="rounded-box border border-base-300 bg-base-100 p-3">
                <h2 class="mb-3 font-semibold">{{ __('daisy::form.builder.preview') }}</h2>
                <div class="space-y-3" data-builder-preview></div>
            </div>
        @endif

        @if($jsonEditor)
            <div class="rounded-box border border-base-300 bg-base-100 p-3">
                <h2 class="mb-3 font-semibold">{{ __('daisy::form.builder.json') }}</h2>
                <textarea class="textarea textarea-bordered min-h-80 w-full font-mono text-sm" data-builder-json spellcheck="false"></textarea>
            </div>
        @endif
    </section>

    <aside class="space-y-4">
        <div class="rounded-box border border-base-300 bg-base-100 p-3">
            <h2 class="mb-3 font-semibold">{{ __('daisy::form.builder.inspector') }}</h2>
            <div class="space-y-3" data-builder-inspector></div>
        </div>

        <div class="rounded-box border border-error/40 bg-error/5 p-3 hidden">
            <h2 class="mb-2 font-semibold text-error">{{ __('daisy::form.builder.diagnostics') }}</h2>
            <ul class="list-disc space-y-1 ps-4 text-sm text-error" data-builder-diagnostics></ul>
        </div>
    </aside>

    @if($name)
        <textarea name="{{ $name }}" class="hidden" data-builder-hidden></textarea>
    @endif

    <script type="application/json" data-builder-schema>@json($schema)</script>
    <script type="application/json" data-builder-field-types>@json($fieldTypes)</script>
    <script type="application/json" data-builder-function-catalog>@json($functionCatalog)</script>

    @include('daisy::components.partials.assets')
</div>

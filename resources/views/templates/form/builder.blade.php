{{--
    Form Builder Template

    @template-label Form builder
    @template-description Builder and viewer shell for Daisy Form schema authoring.
    @template-tags form,builder,viewer,schema
    @template-type example
    @template-route templates.forms.builder

    Embeddable authoring surface pairing the schema builder with the viewer preview.
    Host applications keep ownership of persistence and submission handling.
--}}

@props([
    'title' => __('daisy::form.builder.title'),
    'description' => __('daisy::form.builder.description'),
    'schema' => null,
    'value' => [],
    'errors' => [],
    'schemaName' => 'schema',
    'fieldTypes' => null,
    'functionCatalog' => null,
    'viewerSubmitMode' => 'none',
    'preview' => true,
    'jsonEditor' => false,
])

<section {{ $attributes->merge(['class' => 'space-y-6']) }}>
    @if($title || $description)
        <header class="space-y-1">
            @if($title)
                <h1 class="text-2xl font-semibold">{{ $title }}</h1>
            @endif

            @if($description)
                <p class="max-w-3xl text-sm text-base-content/70">{{ $description }}</p>
            @endif
        </header>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(22rem,0.85fr)]">
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-base font-semibold">{{ __('daisy::form.builder.surface') }}</h2>
                <span class="badge badge-outline">{{ __('daisy::form.builder.schema_version', ['version' => '1.0']) }}</span>
            </div>

            <x-daisy::forms.builder
                :schema="$schema"
                :field-types="$fieldTypes"
                :function-catalog="$functionCatalog"
                :preview="$preview"
                :json-editor="$jsonEditor"
                :name="$schemaName"
            />
        </div>

        <aside class="space-y-3">
            <h2 class="text-base font-semibold">{{ __('daisy::form.builder.viewer_preview') }}</h2>

            <div class="rounded-box border border-base-300 bg-base-100 p-4">
                <x-daisy::forms.viewer
                    :schema="$schema"
                    :value="$value"
                    :errors="$errors"
                    :submit-mode="$viewerSubmitMode"
                />
            </div>
        </aside>
    </div>
</section>

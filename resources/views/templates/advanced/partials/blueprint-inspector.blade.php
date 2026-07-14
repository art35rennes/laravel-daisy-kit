@props([
    'inspectorId' => 'blueprint-inspector',
])

<div class="grid gap-4" data-module="blueprint-inspector-demo">
    <x-daisy::ui.partials.form-field
        :id="$inspectorId.'-label'"
        :label="__('daisy::components.blueprint_template.fields.name')"
        :hint="__('daisy::components.blueprint_template.contract.inspector_help')"
        hint-mode="icon"
    >
        <x-daisy::ui.inputs.input :id="$inspectorId.'-label'" data-blueprint-demo-field="label" />
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.partials.form-field
        :id="$inspectorId.'-description'"
        :label="__('daisy::components.blueprint_template.fields.description')"
    >
        <x-daisy::ui.inputs.textarea :id="$inspectorId.'-description'" rows="3" data-blueprint-demo-field="description" />
    </x-daisy::ui.partials.form-field>

    <x-daisy::ui.partials.form-field
        :id="$inspectorId.'-category'"
        :label="__('daisy::components.blueprint_template.fields.category')"
    >
        <select id="{{ $inspectorId }}-category" class="select w-full" data-blueprint-demo-field="category">
            @foreach(['start', 'work', 'approval', 'done', 'progress', 'return'] as $category)
                <option value="{{ $category }}">{{ __('daisy::components.blueprint_template.categories.'.$category) }}</option>
            @endforeach
        </select>
    </x-daisy::ui.partials.form-field>

    <div class="grid gap-4 sm:grid-cols-2" data-blueprint-demo-node-fields>
        <x-daisy::ui.partials.form-field
            :id="$inspectorId.'-owner'"
            :label="__('daisy::components.blueprint_template.fields.owner')"
        >
            <x-daisy::ui.inputs.input :id="$inspectorId.'-owner'" data-blueprint-demo-field="owner" />
        </x-daisy::ui.partials.form-field>

        <x-daisy::ui.partials.form-field
            :id="$inspectorId.'-priority'"
            :label="__('daisy::components.blueprint_template.fields.priority')"
        >
            <select id="{{ $inspectorId }}-priority" class="select w-full" data-blueprint-demo-field="priority">
                <option value="normal">{{ __('daisy::components.blueprint_template.fields.normal') }}</option>
                <option value="high">{{ __('daisy::components.blueprint_template.fields.high') }}</option>
            </select>
        </x-daisy::ui.partials.form-field>

        <label class="label cursor-pointer justify-start gap-3 sm:col-span-2">
            <x-daisy::ui.inputs.checkbox data-blueprint-demo-field="expedited" />
            <span>{{ __('daisy::components.blueprint_template.fields.expedited') }}</span>
        </label>
    </div>

    <div data-blueprint-demo-transition-fields hidden>
        <label class="label cursor-pointer justify-start gap-3">
            <x-daisy::ui.inputs.checkbox data-blueprint-demo-field="notify" />
            <span>{{ __('daisy::components.blueprint_template.fields.notify') }}</span>
        </label>
    </div>

    <div class="flex flex-wrap justify-between gap-2 border-t border-base-300 pt-4">
        <button type="button" class="btn btn-error btn-outline" data-blueprint-demo-action="delete">
            {{ __('daisy::components.blueprint_template.actions.delete') }}
        </button>
        <div class="flex gap-2">
            <button type="button" class="btn" data-blueprint-demo-action="cancel">
                {{ __('daisy::components.blueprint_template.actions.cancel') }}
            </button>
            <button type="button" class="btn btn-primary" data-blueprint-demo-action="save">
                {{ __('daisy::components.blueprint_template.actions.save') }}
            </button>
        </div>
    </div>
</div>

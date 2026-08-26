<div data-daisy-kit-livewire-builder>
    <input name="{{ $name }}" type="hidden" value="{{ $this->exportSchema() }}">
    <div data-daisy-kit-builder-authoring>
        @foreach($schema['fields'] as $index => $field)
            <fieldset wire:key="daisy-kit-builder-field-{{ $index }}">
                <legend>{{ __('Field :position', ['position' => $index + 1]) }}</legend>
                <label>
                    {{ __('Name') }}
                    <input wire:model.live="schema.fields.{{ $index }}.name" type="text">
                </label>
                <label>
                    {{ __('Label') }}
                    <input wire:model.live="schema.fields.{{ $index }}.label" type="text">
                </label>
                <label>
                    {{ __('Type') }}
                    <select wire:model.live="schema.fields.{{ $index }}.type">
                        @foreach(['text', 'email', 'number', 'date', 'textarea', 'checkbox', 'select'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </label>
            </fieldset>
        @endforeach

        <button type="button" wire:click="addField">{{ __('Add field') }}</button>
    </div>

    <section aria-label="{{ __('Form preview') }}" data-daisy-kit-builder-preview>
        <h3>{{ __('Form preview') }}</h3>
        <x-daisy-kit::forms.viewer :schema="$schema" />
    </section>
</div>

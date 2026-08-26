<div class="daisy-kit-forms-builder-livewire" data-daisy-kit-livewire-builder>
    <input name="{{ $name }}" type="hidden" value="{{ $this->exportSchema() }}">

    <div class="daisy-kit-forms-builder-livewire__toolbar" aria-label="{{ __('Form authoring tools') }}">
        <section aria-label="{{ __('Field catalogue') }}">
            <h2>{{ __('Field catalogue') }}</h2>
            <div class="daisy-kit-forms-builder-livewire__catalogue">
                @foreach($fieldCatalogue as $fieldType)
                    <button type="button" wire:click="addField('{{ $fieldType['type'] }}')">
                        {{ __($fieldType['label']) }}
                    </button>
                @endforeach
            </div>
        </section>

        <div class="daisy-kit-forms-builder-livewire__history" aria-label="{{ __('History') }}">
            <button type="button" wire:click="addSection">{{ __('Add section') }}</button>
            <button type="button" wire:click="addStep">{{ __('Add step') }}</button>
            <button type="button" wire:click="undo" @disabled($undoStack === [])>{{ __('Undo') }}</button>
            <button type="button" wire:click="redo" @disabled($redoStack === [])>{{ __('Redo') }}</button>
        </div>
    </div>

    <div class="daisy-kit-forms-builder-livewire__workspace">
        <section aria-label="{{ __('Form fields') }}" data-daisy-kit-builder-authoring>
            <h2>{{ __('Form fields') }}</h2>
            @forelse($outline as $index => $item)
                @php($field = $item['field'])
                <article wire:key="daisy-kit-builder-field-{{ $item['identity'] }}" data-daisy-kit-builder-field data-daisy-kit-builder-depth="{{ $item['depth'] }}">
                    <h3>{{ $field['label'] }}</h3>
                    <p>{{ $field['type'] }}</p>
                    <button type="button" wire:click="selectField('{{ $item['identity'] }}')">{{ __('Edit') }}</button>
                    <button type="button" wire:click="moveField('{{ $item['identity'] }}', -1)">{{ __('Move up') }}</button>
                    <button type="button" wire:click="moveField('{{ $item['identity'] }}', 1)">{{ __('Move down') }}</button>
                    <button type="button" wire:click="removeField('{{ $item['identity'] }}')">{{ __('Remove') }}</button>
                </article>
            @empty
                <p role="status">{{ __('Add a field from the catalogue to begin authoring.') }}</p>
            @endforelse
        </section>

        <aside aria-label="{{ __('Field inspector') }}" data-daisy-kit-builder-inspector>
            <h2>{{ __('Field inspector') }}</h2>
            @if($selectedField)
                <label>
                    {{ __('Name') }}
                    <input type="text" value="{{ $selectedField['name'] ?? '' }}" wire:change="updateSelectedPath('name', $event.target.value)">
                </label>
                <label>
                    {{ __('Label') }}
                    <input type="text" value="{{ $selectedField['label'] ?? '' }}" wire:change="updateSelectedPath('label', $event.target.value)">
                </label>
                <label>
                    {{ __('Type') }}
                    <select wire:change="updateSelectedPath('type', $event.target.value)">
                        @foreach($fieldCatalogue as $fieldType)
                            <option value="{{ $fieldType['type'] }}" @selected($selectedField['type'] === $fieldType['type'])>{{ __($fieldType['label']) }}</option>
                        @endforeach
                    </select>
                </label>
                @foreach(['attrs' => 'Attributes', 'options' => 'Options', 'rules' => 'Rules', 'visibleWhen' => 'Visible when (JSONata)', 'computed' => 'Computed (JSONata)'] as $path => $label)
                    <label>
                        {{ __($label) }}
                        <textarea wire:change="updateSelectedJson('{{ $path }}', $event.target.value)">{{ json_encode($selectedField[$path] ?? ($path === 'attrs' ? new stdClass : []), JSON_UNESCAPED_SLASHES) }}</textarea>
                    </label>
                @endforeach
            @else
                <p>{{ __('Select a field to edit its schema properties.') }}</p>
            @endif
        </aside>
    </div>

    @if($jsonEditor)
        <section aria-label="{{ __('Schema JSON') }}" data-daisy-kit-builder-json>
            <h2>{{ __('Schema JSON') }}</h2>
            <label>
                {{ __('Import or edit the complete schema') }}
                <textarea wire:model="schemaJson"></textarea>
            </label>
            <button type="button" wire:click="updateFromJson">{{ __('Apply JSON') }}</button>
            <output aria-live="polite" data-daisy-kit-builder-export>{{ $this->exportSchema() }}</output>
        </section>
    @endif

    <section aria-label="{{ __('Diagnostics') }}" data-daisy-kit-builder-diagnostics>
        <h2>{{ __('Diagnostics') }}</h2>
        @if($diagnostics === [])
            <p>{{ __('The schema is valid.') }}</p>
        @else
            <ul>
                @foreach($diagnostics as $diagnostic)
                    <li data-daisy-kit-builder-diagnostic="{{ $diagnostic['code'] }}">{{ $diagnostic['message'] }}</li>
                @endforeach
            </ul>
        @endif
    </section>

    @if($preview)
        <section aria-label="{{ __('Form preview') }}" data-daisy-kit-builder-preview>
            <h2>{{ __('Form preview') }}</h2>
            <x-daisy-kit::forms.viewer :schema="$schema" :value="$value" :errors="$errors" />
        </section>
    @endif
</div>

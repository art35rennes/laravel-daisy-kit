<?php

namespace Art35rennes\DaisyKit\FormKit;

/**
 * Describes which package field components are eligible for the visual builder.
 *
 * The catalog is intentionally schema-oriented: each definition maps a DaisyFormSchema
 * field type to a package component alias and to the editable properties that make
 * sense for that component. Adding a new builder-eligible field should start here,
 * then the viewer partial can render the same canonical field contract.
 */
class FormFieldCatalog
{
    /**
     * Returns the builder palette definitions grouped later by translated group name.
     *
     * @return array<int, array<string, mixed>>
     */
    public function definitions(): array
    {
        return [
            $this->field('text', 'inputs', 'x-daisy::ui.inputs.input', ['placeholder', 'autocomplete', 'mask', 'size', 'color']),
            $this->field('email', 'inputs', 'x-daisy::ui.inputs.input', ['placeholder', 'autocomplete', 'mask', 'size', 'color']),
            $this->field('tel', 'inputs', 'x-daisy::ui.inputs.input', ['placeholder', 'autocomplete', 'mask', 'size', 'color']),
            $this->field('url', 'inputs', 'x-daisy::ui.inputs.input', ['placeholder', 'autocomplete', 'mask', 'size', 'color']),
            $this->field('password', 'inputs', 'x-daisy::ui.inputs.input', ['placeholder', 'autocomplete', 'mask', 'size', 'color']),
            $this->field('number', 'inputs', 'x-daisy::ui.inputs.input', ['placeholder', 'min', 'max', 'step', 'size', 'color']),
            $this->field('textarea', 'inputs', 'x-daisy::ui.inputs.textarea', ['placeholder', 'rows', 'size', 'color']),
            $this->field('select', 'choices', 'x-daisy::ui.inputs.select', ['placeholder', 'options', 'size', 'color']),
            $this->field('radio', 'choices', 'x-daisy::ui.inputs.radio', ['options']),
            $this->field('checkbox', 'choices', 'x-daisy::ui.inputs.checkbox', ['default']),
            $this->field('toggle', 'choices', 'x-daisy::ui.inputs.toggle', ['default']),
            $this->field('range', 'choices', 'x-daisy::ui.inputs.range', ['min', 'max', 'step', 'size', 'color']),
            $this->field('date', 'date_time', 'x-daisy::ui.inputs.input', ['min', 'max', 'size', 'color']),
            $this->field('time', 'date_time', 'x-daisy::ui.inputs.input', ['min', 'max', 'step', 'size', 'color']),
            $this->field('datetime-local', 'date_time', 'x-daisy::ui.inputs.input', ['min', 'max', 'size', 'color']),
            $this->field('month', 'date_time', 'x-daisy::ui.inputs.input', ['min', 'max', 'size', 'color']),
            $this->field('color', 'inputs', 'x-daisy::ui.inputs.color-picker', ['colorPicker']),
            $this->field('file', 'uploads', 'x-daisy::ui.inputs.file-input', ['accept', 'multiple', 'size', 'color']),
            $this->field('signature', 'uploads', 'x-daisy::ui.inputs.sign', ['signature']),
            $this->field('hidden', 'system', 'input[type=hidden]', ['default']),
            $this->field('staticText', 'content', 'staticText', ['text']),
            $this->field('section', 'layout', 'fieldset', ['children']),
            $this->field('tabs', 'layout', 'fieldset', ['children']),
            $this->field('wizardStep', 'layout', 'fieldset', ['children']),
        ];
    }

    /**
     * Returns field definitions grouped for the "Add element" menu.
     *
     * @return array<int, array{label: string, fields: array<int, array<string, mixed>>}>
     */
    public function groupedDefinitions(): array
    {
        return collect($this->definitions())
            ->groupBy('group')
            ->map(fn ($fields, string $group): array => [
                'label' => $group,
                'fields' => array_values($fields->all()),
            ])
            ->values()
            ->all();
    }

    /**
     * Returns the editable properties exposed for one field type.
     *
     * Generic schema attributes are always present. Component props are appended from
     * the field feature list so future package components can opt into only the
     * controls they support.
     *
     * @return array<int, array<string, mixed>>
     */
    public function propertiesFor(?string $type): array
    {
        $definition = collect($this->definitions())->firstWhere('type', $type);
        $features = $definition['features'] ?? [];
        $properties = [
            $this->property('id', 'text', 'identity', required: true),
            $this->property('name', 'text', 'identity', except: FormSchemaNormalizer::ContainerTypes),
            $this->property('label', 'text', 'identity'),
            $this->property('description', 'textarea', 'identity'),
            $this->property('default', 'json', 'behavior'),
            $this->property('rules', 'json', 'behavior'),
            $this->property('visibleWhen', 'json', 'behavior'),
            $this->property('computed', 'json', 'behavior'),
        ];

        if (in_array('text', $features, true)) {
            $properties[] = $this->property('text', 'textarea', 'content');
        }

        if (in_array('options', $features, true)) {
            $properties[] = $this->property('options', 'options', 'choices');
        }

        foreach ($this->attributeProperties($features) as $property) {
            $properties[] = $property;
        }

        return array_values(array_filter($properties, function (array $property) use ($type): bool {
            if (! isset($property['except'])) {
                return true;
            }

            return ! in_array($type, (array) $property['except'], true);
        }));
    }

    /**
     * Maps component features to `attrs.*`, `ui.*`, and layout property editors.
     *
     * @param  array<int, string>  $features
     * @return array<int, array<string, mixed>>
     */
    protected function attributeProperties(array $features): array
    {
        $properties = [];

        if (in_array('placeholder', $features, true)) {
            $properties[] = $this->property('attrs.placeholder', 'text', 'component_props');
        }

        if (in_array('autocomplete', $features, true)) {
            $properties[] = $this->property('attrs.autocomplete', 'text', 'component_props');
        }

        if (in_array('mask', $features, true)) {
            $properties[] = $this->property('attrs.mask', 'text', 'component_props');
            $properties[] = $this->property('attrs.maskCharPlaceholder', 'text', 'component_props');
            $properties[] = $this->property('attrs.maskPlaceholder', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.inputPlaceholder', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.clearIncomplete', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.customMask', 'text', 'component_props');
            $properties[] = $this->property('attrs.customValidator', 'text', 'component_props');
            $properties[] = $this->property('attrs.obfuscate', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.obfuscateChar', 'text', 'component_props');
            $properties[] = $this->property('attrs.obfuscateKeepEnd', 'number', 'component_props');
        }

        if (in_array('rows', $features, true)) {
            $properties[] = $this->property('attrs.rows', 'number', 'component_props');
        }

        if (in_array('accept', $features, true)) {
            $properties[] = $this->property('attrs.accept', 'text', 'component_props');
        }

        if (in_array('multiple', $features, true)) {
            $properties[] = $this->property('attrs.multiple', 'boolean', 'component_props');
        }

        if (in_array('signature', $features, true)) {
            $properties[] = $this->property('attrs.width', 'number', 'component_props');
            $properties[] = $this->property('attrs.height', 'number', 'component_props');
            $properties[] = $this->property('attrs.penColor', 'text', 'component_props');
            $properties[] = $this->property('attrs.minWidth', 'number', 'component_props');
            $properties[] = $this->property('attrs.maxWidth', 'number', 'component_props');
            $properties[] = $this->property('attrs.velocityFilterWeight', 'number', 'component_props');
            $properties[] = $this->property('attrs.responsive', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.showActions', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.downloadFormat', 'select', 'component_props', options: ['png', 'jpg', 'svg']);
            $properties[] = $this->property('attrs.downloadFilename', 'text', 'component_props');
        }

        if (in_array('colorPicker', $features, true)) {
            $properties[] = $this->property('attrs.mode', 'select', 'component_props', options: ['advanced', 'native']);
            $properties[] = $this->property('attrs.dropdown', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.swatches', 'json', 'component_props');
            $properties[] = $this->property('attrs.swatchesHeight', 'number', 'component_props');
            $properties[] = $this->property('attrs.showPalette', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.showInputs', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.showFormatToggle', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.showAlpha', 'boolean', 'component_props');
            $properties[] = $this->property('attrs.showHue', 'boolean', 'component_props');
        }

        foreach (['min', 'max', 'step'] as $attribute) {
            if (in_array($attribute, $features, true)) {
                $properties[] = $this->property("attrs.{$attribute}", 'text', 'component_props');
            }
        }

        if (in_array('size', $features, true)) {
            $properties[] = $this->property('ui.size', 'select', 'component_props', options: ['xs', 'sm', 'md', 'lg', 'xl']);
        }

        if (in_array('color', $features, true)) {
            $properties[] = $this->property('ui.color', 'select', 'component_props', options: ['', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error', 'neutral']);
        }

        $properties[] = $this->property('ui.width', 'select', 'layout', options: ['full', '1/2', '1/3', '2/3', '1/4', '3/4']);

        return $properties;
    }

    /**
     * Builds one field catalog definition.
     *
     * @param  array<int, string>  $features
     * @return array<string, mixed>
     */
    protected function field(string $type, string $group, string $component, array $features): array
    {
        return [
            'type' => $type,
            'label' => __("daisy::form.builder.field_types.{$type}"),
            'group' => __("daisy::form.builder.field_groups.{$group}"),
            'component' => $component,
            'features' => $features,
        ];
    }

    /**
     * Builds one property editor definition for the responsive field editor modal.
     *
     * @param  array<int, string>|null  $except
     * @param  array<int, string>  $options
     * @return array<string, mixed>
     */
    protected function property(
        string $path,
        string $control,
        string $group,
        bool $required = false,
        ?array $except = null,
        array $options = [],
    ): array {
        $property = [
            'path' => $path,
            'label' => __("daisy::form.builder.properties.{$path}.label"),
            'help' => __("daisy::form.builder.properties.{$path}.help"),
            'control' => $control,
            'group' => __("daisy::form.builder.property_groups.{$group}"),
            'required' => $required,
        ];

        if ($except !== null) {
            $property['except'] = $except;
        }

        if ($options !== []) {
            $property['options'] = $options;
        }

        return $property;
    }
}

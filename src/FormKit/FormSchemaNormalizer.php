<?php

namespace Art35rennes\DaisyKit\FormKit;

/**
 * Produces the canonical DaisyFormSchema shape shared by builder, viewer, and tests.
 *
 * The builder may receive partial JSON from a host, older drafts, or hand-edited
 * snippets from the JSON editor. This normalizer keeps the public schema compact
 * and predictable by applying defaults, trimming empty optional keys, and preserving
 * only structures the viewer/runtime know how to render.
 */
class FormSchemaNormalizer
{
    public const FieldTypes = [
        'text',
        'email',
        'tel',
        'url',
        'password',
        'number',
        'textarea',
        'select',
        'radio',
        'checkbox',
        'toggle',
        'range',
        'date',
        'time',
        'datetime-local',
        'month',
        'color',
        'file',
        'signature',
        'hidden',
        'staticText',
        'section',
        'tabs',
        'wizardStep',
    ];

    public const ContainerTypes = ['section', 'tabs', 'wizardStep'];

    public const NonSubmittingFieldTypes = ['staticText'];

    /**
     * Normalizes an arbitrary schema payload into the canonical package contract.
     *
     * @param  array<string, mixed>|null  $schema
     * @return array<string, mixed>
     */
    public function normalize(?array $schema = null): array
    {
        $source = $schema ?: $this->defaultSchema();

        return $this->compact([
            'version' => (string) ($source['version'] ?? FormSchemaValidator::Version),
            'id' => (string) ($source['id'] ?? 'form'),
            'meta' => $this->normalizeObject($source['meta'] ?? []),
            'jsonata' => $this->normalizeJsonata($source['jsonata'] ?? []),
            'layout' => $this->normalizeLayout($source['layout'] ?? []),
            'fields' => array_values(array_filter(array_map(
                fn (mixed $field): ?array => is_array($field) ? $this->normalizeField($field) : null,
                is_array($source['fields'] ?? null) ? $source['fields'] : [],
            ))),
            'submit' => $this->normalizeSubmit($source['submit'] ?? []),
        ]);
    }

    /**
     * Returns the smallest useful schema for a fresh builder instance.
     *
     * @return array<string, mixed>
     */
    public function defaultSchema(): array
    {
        return [
            'version' => FormSchemaValidator::Version,
            'id' => 'form',
            'meta' => [
                'title' => 'Untitled form',
            ],
            'jsonata' => [
                'engine' => 'jsonata',
                'minVersion' => '2.1.0',
                'functions' => [],
            ],
            'layout' => [
                'type' => 'sections',
            ],
            'fields' => [
                $this->createField('text', 1),
            ],
            'submit' => [
                'mode' => 'event',
                'label' => 'Submit',
            ],
        ];
    }

    /**
     * Creates a builder-ready field definition for the given type.
     *
     * The generated field is intentionally minimal. Rich behavior such as rules,
     * visibility, computed expressions, and component attributes are edited later
     * through the field properties modal.
     *
     * @return array<string, mixed>
     */
    public function createField(string $type, int $index = 1): array
    {
        $safeType = in_array($type, self::FieldTypes, true) ? $type : 'text';
        $id = "{$safeType}-{$index}";

        $field = [
            'id' => $id,
            'type' => $safeType,
            'name' => str_replace('-', '_', $id),
            'label' => $this->labelForType($safeType),
        ];

        if (in_array($safeType, ['select', 'radio'], true)) {
            $field['options'] = [
                ['label' => 'Option A', 'value' => 'a'],
                ['label' => 'Option B', 'value' => 'b'],
            ];
        }

        if (in_array($safeType, ['checkbox', 'toggle'], true)) {
            $field['default'] = false;
        }

        if ($safeType === 'staticText') {
            unset($field['name']);
            $field['text'] = 'Static text';
        }

        if (in_array($safeType, self::ContainerTypes, true)) {
            unset($field['name']);
            $field['fields'] = [];
        }

        return $field;
    }

    public function labelForType(string $type): string
    {
        return ucfirst(trim((string) preg_replace('/([A-Z])/', ' $1', $type)));
    }

    /**
     * Normalizes one field and any nested children.
     *
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    public function normalizeField(array $field): array
    {
        $type = in_array($field['type'] ?? null, self::FieldTypes, true) ? (string) $field['type'] : (string) ($field['type'] ?? 'text');

        return $this->compact([
            'id' => (string) ($field['id'] ?? ''),
            'type' => $type,
            'name' => array_key_exists('name', $field) && $field['name'] !== null ? (string) $field['name'] : null,
            'label' => array_key_exists('label', $field) && $field['label'] !== null ? (string) $field['label'] : null,
            'description' => array_key_exists('description', $field) && $field['description'] !== null ? (string) $field['description'] : null,
            'text' => array_key_exists('text', $field) && $field['text'] !== null ? (string) $field['text'] : null,
            'default' => $field['default'] ?? null,
            'options' => $this->normalizeOptions($field['options'] ?? null),
            'rules' => $this->normalizeRules($field['rules'] ?? null),
            'visibleWhen' => $this->normalizeExpression($field['visibleWhen'] ?? null),
            'computed' => $this->normalizeComputed($field['computed'] ?? null),
            'attrs' => is_array($field['attrs'] ?? null) ? $this->normalizeObject($field['attrs']) : null,
            'ui' => is_array($field['ui'] ?? null) ? $this->normalizeObject($field['ui']) : null,
            'fields' => is_array($field['fields'] ?? null)
                ? array_values(array_filter(array_map(fn (mixed $child): ?array => is_array($child) ? $this->normalizeField($child) : null, $field['fields'])))
                : null,
        ]);
    }

    /**
     * Flattens schema fields while preserving parent references for builder outline,
     * validation diagnostics, runtime step scoping, and tree reordering.
     *
     * @param  array<string, mixed>|null  $schema
     * @return array<int, array<string, mixed>>
     */
    public function flattenFields(?array $schema): array
    {
        return $this->flatten($schema['fields'] ?? []);
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, array<string, mixed>>
     */
    protected function flatten(array $fields, ?string $parent = null): array
    {
        $flat = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $field['_parent'] = $parent;
            $flat[] = $field;

            if (is_array($field['fields'] ?? null)) {
                array_push($flat, ...$this->flatten($field['fields'], $field['id'] ?? null));
            }
        }

        return $flat;
    }

    /**
     * Normalizes layout settings supported by the viewer.
     *
     * @param  array<string, mixed>  $layout
     * @return array<string, mixed>
     */
    protected function normalizeLayout(array $layout): array
    {
        return $this->compact([
            ...$layout,
            'type' => in_array($layout['type'] ?? null, ['one-page', 'multi-step', 'sections'], true) ? $layout['type'] : 'one-page',
        ]);
    }

    /**
     * Normalizes the JSONata declaration consumed by client/runtime integrations.
     *
     * @param  array<string, mixed>  $jsonata
     * @return array<string, mixed>
     */
    protected function normalizeJsonata(array $jsonata): array
    {
        return $this->compact([
            'engine' => $jsonata['engine'] ?? 'jsonata',
            'minVersion' => $jsonata['minVersion'] ?? '2.1.0',
            'functions' => is_array($jsonata['functions'] ?? null)
                ? array_values(array_unique(array_map('strval', $jsonata['functions'])))
                : [],
        ]);
    }

    /**
     * Normalizes submit behavior with the same fallback used by the viewer.
     *
     * @param  array<string, mixed>  $submit
     * @return array<string, mixed>
     */
    protected function normalizeSubmit(array $submit): array
    {
        return $this->compact([
            'mode' => in_array($submit['mode'] ?? null, ['event', 'html', 'fetch', 'none'], true) ? $submit['mode'] : 'event',
            'label' => $submit['label'] ?? 'Submit',
        ]);
    }

    /**
     * Normalizes a conditional JSONata expression block.
     *
     * @return array<string, mixed>|null
     */
    protected function normalizeExpression(mixed $expression): ?array
    {
        if (! is_array($expression)) {
            return null;
        }

        if (($expression['type'] ?? null) !== 'jsonata') {
            return $this->compact($expression);
        }

        return $this->compact([
            'type' => 'jsonata',
            'expression' => (string) ($expression['expression'] ?? ''),
            'dependsOn' => is_array($expression['dependsOn'] ?? null) ? array_map('strval', $expression['dependsOn']) : [],
        ]);
    }

    /**
     * Normalizes a computed field expression block.
     *
     * @return array<string, mixed>|null
     */
    protected function normalizeComputed(mixed $computed): ?array
    {
        $expression = $this->normalizeExpression($computed);

        if (! $expression || ! is_array($computed)) {
            return null;
        }

        return $this->compact([
            ...$expression,
            'mode' => in_array($computed['mode'] ?? null, ['readonly', 'hidden', 'suggested'], true) ? $computed['mode'] : 'readonly',
        ]);
    }

    /**
     * Normalizes select/radio options from scalar or object payloads.
     *
     * @return array<int, array<string, mixed>>|null
     */
    protected function normalizeOptions(mixed $options): ?array
    {
        if (! is_array($options)) {
            return null;
        }

        return array_values(array_filter(array_map(function (mixed $option): ?array {
            if (is_scalar($option)) {
                return ['label' => (string) $option, 'value' => (string) $option];
            }

            if (! is_array($option)) {
                return null;
            }

            return $this->compact([
                'label' => (string) ($option['label'] ?? $option['value'] ?? ''),
                'value' => (string) ($option['value'] ?? $option['label'] ?? ''),
                'disabled' => ($option['disabled'] ?? false) === true ? true : null,
            ]);
        }, $options)));
    }

    /**
     * Normalizes simple Laravel-like rules and JSONata rule objects.
     *
     * @return array<int, mixed>|null
     */
    protected function normalizeRules(mixed $rules): ?array
    {
        if (! is_array($rules)) {
            return null;
        }

        return array_values(array_filter(array_map(function (mixed $rule): mixed {
            if (is_string($rule)) {
                return $rule;
            }

            if (! is_array($rule)) {
                return null;
            }

            if (($rule['type'] ?? null) === 'jsonata') {
                return $this->compact([
                    'type' => 'jsonata',
                    'expression' => (string) ($rule['expression'] ?? ''),
                    'dependsOn' => is_array($rule['dependsOn'] ?? null) ? array_map('strval', $rule['dependsOn']) : [],
                    'message' => array_key_exists('message', $rule) ? (string) $rule['message'] : null,
                ]);
            }

            return $this->compact($rule);
        }, $rules)));
    }

    /**
     * @param  array<string, mixed>  $object
     * @return array<string, mixed>
     */
    protected function normalizeObject(array $object): array
    {
        return $this->compact($object);
    }

    /**
     * Removes empty optional branches after normalization.
     *
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    protected function compact(array $value): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            if ($item === null) {
                continue;
            }

            if (is_array($item)) {
                $item = $this->compact($item);

                if ($item === []) {
                    continue;
                }
            }

            $result[$key] = $item;
        }

        return $result;
    }
}

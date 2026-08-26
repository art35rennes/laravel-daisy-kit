<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Livewire\Support;

/**
 * Normalizes the declarative form schema used by Builder mutations. Its output
 * deliberately remains compatible with the Viewer schema contract.
 */
final class FormSchemaContract
{
    /** @var list<string> */
    public const array FIELD_TYPES = [
        'checkbox', 'color', 'date', 'email', 'file', 'hidden', 'number', 'password',
        'radio', 'range', 'select', 'tel', 'text', 'textarea', 'url', 'section', 'wizardStep',
    ];

    /** @var list<string> */
    public const array CONTAINER_TYPES = ['section', 'wizardStep'];

    /**
     * @param  array<string, mixed>  $schema
     * @return array{layout: array{type: string}, fields: array<int, array<string, mixed>>}
     */
    public function normalizeSchema(array $schema): array
    {
        $layout = $this->object($schema['layout'] ?? null);
        $layoutType = in_array($layout['type'] ?? null, ['one-page', 'sections', 'multi-step'], true)
            ? $layout['type']
            : 'one-page';

        $fields = [];

        foreach ($this->fieldList($schema['fields'] ?? null) as $index => $field) {
            $fields[] = $this->normalizeField($field, $index);
        }

        return [
            'layout' => ['type' => $layoutType],
            'fields' => $fields,
        ];
    }

    /** @return array<string, mixed> */
    public function normalizeField(mixed $raw, int $index): array
    {
        $value = is_array($raw) ? $raw : [];
        $position = $index + 1;
        $type = $this->normalizeFieldType($value['type'] ?? null);
        $field = [
            'name' => $this->stringValue($value['name'] ?? null, "field_{$position}"),
            'label' => $this->stringValue($value['label'] ?? null, "Field {$position}"),
            'type' => $type,
        ];

        if (is_string($value['id'] ?? null) && trim($value['id']) !== '') {
            $field['id'] = trim($value['id']);
        }

        foreach (['attrs', 'options', 'rules', 'visibleWhen', 'computed'] as $key) {
            if (array_key_exists($key, $value)) {
                $field[$key] = in_array($key, ['visibleWhen', 'computed'], true)
                    ? $this->normalizeJsonataDescriptor($value[$key])
                    : $value[$key];
            }
        }

        if ($this->isContainerType($type)) {
            $field['fields'] = [];

            foreach ($this->fieldList($value['fields'] ?? null) as $childIndex => $child) {
                $field['fields'][] = $this->normalizeField($child, $childIndex);
            }
        }

        return $field;
    }

    public function normalizeFieldType(mixed $type): string
    {
        return is_string($type) && in_array($type, self::FIELD_TYPES, true) ? $type : 'text';
    }

    public function normalizeJsonataDescriptor(mixed $expression): mixed
    {
        if (is_string($expression) && trim($expression) !== '') {
            return [
                'type' => 'jsonata',
                'expression' => trim($expression),
            ];
        }

        return $expression;
    }

    public function stringValue(mixed $value, string $fallback): string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
    }

    /** @return array<string, mixed> */
    public function object(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $object = [];

        foreach ($value as $key => $item) {
            if (is_string($key)) {
                $object[$key] = $item;
            }
        }

        return $object;
    }

    /** @return list<array<string, mixed>> */
    public function fieldList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $fields = [];

        foreach ($value as $field) {
            if (is_array($field)) {
                $fields[] = $this->object($field);
            }
        }

        return $fields;
    }

    /** @return array<string, mixed> */
    public function newField(string $type, int $position): array
    {
        $id = strtolower(preg_replace('/[^A-Za-z0-9_-]+/', '_', "{$type}_{$position}") ?? "field_{$position}");
        $field = [
            'name' => $this->isContainerType($type) ? '' : $id,
            'label' => "Field {$position}",
            'type' => $type,
        ];

        if ($this->isContainerType($type)) {
            $field['id'] = $id;
            $field['fields'] = [];
        }

        if ($type === 'select' || $type === 'radio') {
            $field['options'] = [['label' => 'Option 1', 'value' => 'option_1']];
        }

        return $field;
    }

    /** @param array<string, mixed>|null $field */
    public function isContainer(?array $field): bool
    {
        return $field !== null && $this->isContainerType($field['type'] ?? null);
    }

    /** @param array<string, mixed> $field */
    public function identity(array $field): string
    {
        return is_string($field['id'] ?? null) && $field['id'] !== ''
            ? $field['id']
            : $this->stringValue($field['name'] ?? null, '');
    }

    private function isContainerType(mixed $type): bool
    {
        return in_array($type, self::CONTAINER_TYPES, true);
    }
}

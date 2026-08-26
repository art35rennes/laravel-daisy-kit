<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class FormsBuilder extends Component
{
    private const FieldTypes = ['checkbox', 'date', 'email', 'number', 'select', 'textarea', 'text'];

    /** @var array{fields: array<int, array{name: string, label: string, type: string}>} */
    public array $schema = ['fields' => []];

    /** @var array<int, array{fields: array<int, array{name: string, label: string, type: string}>}> */
    public array $undoStack = [];

    /** @var array<int, array{fields: array<int, array{name: string, label: string, type: string}>}> */
    public array $redoStack = [];

    public string $name = 'schema';

    /** @param array<string, mixed> $schema */
    public function mount(array $schema = []): void
    {
        $this->schema = $this->normalizeSchema($schema);
    }

    public function addField(): void
    {
        $this->remember();
        $position = count($this->schema['fields']) + 1;
        $this->schema['fields'][] = [
            'name' => "field_{$position}",
            'label' => "Field {$position}",
            'type' => 'text',
        ];
    }

    public function removeField(int $index): void
    {
        if (! isset($this->schema['fields'][$index])) {
            return;
        }

        $this->remember();
        unset($this->schema['fields'][$index]);
        $this->schema['fields'] = array_values($this->schema['fields']);
    }

    public function moveField(int $index, int $direction): void
    {
        $target = $index + $direction;

        if (! isset($this->schema['fields'][$index], $this->schema['fields'][$target])) {
            return;
        }

        $this->remember();
        [$this->schema['fields'][$index], $this->schema['fields'][$target]] = [$this->schema['fields'][$target], $this->schema['fields'][$index]];
    }

    public function undo(): void
    {
        $previous = array_pop($this->undoStack);

        if ($previous === null) {
            return;
        }

        $this->redoStack[] = $this->schema;
        $this->schema = $previous;
    }

    public function redo(): void
    {
        $next = array_pop($this->redoStack);

        if ($next === null) {
            return;
        }

        $this->undoStack[] = $this->schema;
        $this->schema = $next;
    }

    public function importSchema(string $json): void
    {
        try {
            $schema = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return;
        }

        if (! is_array($schema)) {
            return;
        }

        $this->remember();
        $this->schema = $this->normalizeSchema($schema);
    }

    public function updatedSchema(): void
    {
        $this->schema = $this->normalizeSchema($this->schema);
    }

    public function updateField(int $index, string $property, mixed $value): void
    {
        if (! isset($this->schema['fields'][$index])) {
            return;
        }

        if (! in_array($property, ['label', 'name', 'type'], true)) {
            return;
        }

        $normalizedValue = is_string($value) ? trim($value) : '';
        $this->schema['fields'][$index][$property] = $property === 'type'
            ? $this->normalizeFieldType($normalizedValue)
            : $normalizedValue;
    }

    public function render(): View
    {
        return view('daisy-kit::components.forms.livewire-builder');
    }

    public function exportSchema(): string
    {
        return json_encode($this->schema, JSON_THROW_ON_ERROR);
    }

    /** @param array<string, mixed> $schema
     * @return array{fields: array<int, array{name: string, label: string, type: string}>}
     */
    private function normalizeSchema(array $schema): array
    {
        $fields = is_array($schema['fields'] ?? null) ? $schema['fields'] : [];

        return [
            'fields' => array_values(array_map(
                fn (mixed $field, int $index): array => $this->normalizeField($field, $index),
                $fields,
                array_keys($fields),
            )),
        ];
    }

    /** @return array{name: string, label: string, type: string} */
    private function normalizeField(mixed $field, int $index): array
    {
        $value = is_array($field) ? $field : [];
        $position = $index + 1;
        $name = is_string($value['name'] ?? null) ? trim($value['name']) : "field_{$position}";
        $label = is_string($value['label'] ?? null) ? trim($value['label']) : "Field {$position}";

        $field = [
            'name' => $name === '' ? "field_{$position}" : $name,
            'label' => $label === '' ? "Field {$position}" : $label,
            'type' => $this->normalizeFieldType($value['type'] ?? null),
        ];

        foreach (['options', 'rules', 'visibleWhen'] as $property) {
            if (isset($value[$property]) && (is_array($value[$property]) || is_string($value[$property]))) {
                $field[$property] = $value[$property];
            }
        }

        return $field;
    }

    private function normalizeFieldType(mixed $type): string
    {
        return is_string($type) && in_array($type, self::FieldTypes, true) ? $type : 'text';
    }

    private function remember(): void
    {
        $this->undoStack[] = $this->schema;
        $this->redoStack = [];
    }
}

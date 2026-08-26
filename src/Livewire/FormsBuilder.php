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

    /** @param array<string, mixed> $schema */
    public function mount(array $schema = []): void
    {
        $this->schema = $this->normalizeSchema($schema);
    }

    public function addField(): void
    {
        $position = count($this->schema['fields']) + 1;
        $this->schema['fields'][] = [
            'name' => "field_{$position}",
            'label' => "Field {$position}",
            'type' => 'text',
        ];
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

        return [
            'name' => $name === '' ? "field_{$position}" : $name,
            'label' => $label === '' ? "Field {$position}" : $label,
            'type' => $this->normalizeFieldType($value['type'] ?? null),
        ];
    }

    private function normalizeFieldType(mixed $type): string
    {
        return is_string($type) && in_array($type, self::FieldTypes, true) ? $type : 'text';
    }
}

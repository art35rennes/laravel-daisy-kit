<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Optional Livewire 4 authoring surface for the same declarative schema consumed by
 * x-daisy-kit::forms.viewer. The schema is the sole persisted value; UI state stays
 * local to the component and every mutation records a reversible snapshot.
 */
class FormsBuilder extends Component
{
    private const array FieldTypes = [
        'checkbox', 'color', 'date', 'email', 'file', 'hidden', 'number', 'password',
        'radio', 'range', 'select', 'tel', 'text', 'textarea', 'url', 'section', 'wizardStep',
    ];

    private const array ContainerTypes = ['section', 'wizardStep'];

    /** @var array{layout: array{type: string}, fields: array<int, array<string, mixed>>} */
    public array $schema = ['layout' => ['type' => 'one-page'], 'fields' => []];

    /** @var list<array{type: string, label: string, group: string}> */
    public array $fieldCatalogue = [];

    /** @var list<array{path: string, code: string, message: string}> */
    public array $diagnostics = [];

    /** @var array<string, mixed> */
    public array $value = [];

    /** @var array<string, mixed> */
    public array $errors = [];

    /** @var list<array{layout: array{type: string}, fields: array<int, array<string, mixed>>}> */
    public array $undoStack = [];

    /** @var list<array{layout: array{type: string}, fields: array<int, array<string, mixed>>}> */
    public array $redoStack = [];

    public string $name = 'schema';

    public ?string $selectedId = null;

    public string $schemaJson = '{"layout":{"type":"one-page"},"fields":[]}';

    public ?string $jsonError = null;

    public bool $preview = true;

    public bool $jsonEditor = true;

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $value
     * @param  array<string, mixed>  $errors
     */
    public function mount(
        array $schema = [],
        string $name = 'schema',
        array $value = [],
        array $errors = [],
        bool $preview = true,
        bool $jsonEditor = true,
    ): void {
        $this->schema = $this->normalizeSchema($schema);
        $this->name = trim($name) === '' ? 'schema' : $name;
        $this->value = $value;
        $this->errors = $errors;
        $this->preview = $preview;
        $this->jsonEditor = $jsonEditor;
        $this->fieldCatalogue = $this->catalogue();
        $this->refreshDerivedState();
    }

    public function addField(string $type = 'text'): void
    {
        $type = $this->normalizeFieldType($type);
        $this->remember();
        $field = $this->newField($type, count($this->flattenFields($this->schema['fields'])) + 1);

        if ($this->selectedId !== null && $this->isContainer($this->findField($this->schema['fields'], $this->selectedId))) {
            $this->schema['fields'] = $this->appendChild($this->schema['fields'], $this->selectedId, $field);
        } elseif ($this->selectedId !== null) {
            $this->schema['fields'] = $this->insertAfter($this->schema['fields'], $this->selectedId, $field);
        } else {
            $this->schema['fields'][] = $field;
        }

        $this->selectedId = $this->fieldIdentity($field);
        $this->finishMutation();
    }

    public function addSection(): void
    {
        $this->addField('section');
    }

    public function addStep(): void
    {
        $this->remember();
        $this->schema['layout']['type'] = 'multi-step';
        $field = $this->newField('wizardStep', count($this->flattenFields($this->schema['fields'])) + 1);
        $this->schema['fields'][] = $field;
        $this->selectedId = $this->fieldIdentity($field);
        $this->finishMutation();
    }

    public function selectField(string $id): void
    {
        $field = $this->findField($this->schema['fields'], $id);
        $this->selectedId = $field === null ? null : $this->fieldIdentity($field);
    }

    public function clearSelection(): void
    {
        $this->selectedId = null;
    }

    public function removeField(int|string $reference): void
    {
        $id = $this->resolveReference($reference);

        if ($id === null) {
            return;
        }

        $this->remember();
        $this->schema['fields'] = $this->removeFromTree($this->schema['fields'], $id);
        $this->selectedId = null;
        $this->finishMutation();
    }

    public function moveField(int|string $reference, int $direction): void
    {
        $id = $this->resolveReference($reference);

        if ($id === null || ! in_array($direction, [-1, 1], true)) {
            return;
        }

        $this->remember();
        $moved = false;
        $this->schema['fields'] = $this->moveInTree($this->schema['fields'], $id, $direction, $moved);

        if (! $moved) {
            array_pop($this->undoStack);

            return;
        }

        $this->selectedId = $id;
        $this->finishMutation();
    }

    public function updateField(int $index, string $property, mixed $value): void
    {
        $field = $this->schema['fields'][$index] ?? null;

        if (! is_array($field)) {
            return;
        }

        $this->selectedId = $this->fieldIdentity($field);
        $this->updateSelectedPath($property, $value);
    }

    public function updateSelectedPath(string $path, mixed $value): void
    {
        if ($this->selectedId === null || ! in_array($path, ['name', 'label', 'type', 'attrs', 'options', 'rules', 'visibleWhen', 'computed'], true)) {
            return;
        }

        $this->remember();
        $updated = false;
        $nextSelectedId = null;
        $this->schema['fields'] = $this->mapTree($this->schema['fields'], function (array $field) use ($path, $value, &$nextSelectedId, &$updated): array {
            if ($this->fieldIdentity($field) !== $this->selectedId) {
                return $field;
            }

            $updated = true;

            if ($path === 'type') {
                $field[$path] = $this->normalizeFieldType(is_string($value) ? $value : '');
            } elseif (in_array($path, ['name', 'label'], true)) {
                $field[$path] = is_string($value) ? trim($value) : '';

                if ($path === 'name' && ! isset($field['id'])) {
                    $nextSelectedId = $field[$path];
                }
            } else {
                $field[$path] = $value;
            }

            return $field;
        });

        if (! $updated) {
            array_pop($this->undoStack);

            return;
        }

        $this->selectedId = $nextSelectedId ?? $this->selectedId;
        $this->finishMutation();
    }

    public function updateSelectedJson(string $path, string $json): void
    {
        if (! in_array($path, ['attrs', 'options', 'rules', 'visibleWhen', 'computed'], true)) {
            return;
        }

        try {
            $value = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->jsonError = "{$path}: Invalid JSON.";
            $this->refreshDiagnostics();

            return;
        }

        $this->jsonError = null;
        $this->updateSelectedPath($path, $value);
    }

    public function importSchema(string $json): void
    {
        $this->replaceFromJson($json);
    }

    public function updateFromJson(): void
    {
        $this->replaceFromJson($this->schemaJson);
    }

    public function updateFromJsonPayload(string $json): void
    {
        $this->schemaJson = $json;
        $this->replaceFromJson($json);
    }

    public function undo(): void
    {
        $previous = array_pop($this->undoStack);

        if ($previous === null) {
            return;
        }

        $this->redoStack[] = $this->schema;
        $this->schema = $previous;
        $this->selectedId = null;
        $this->refreshDerivedState();
    }

    public function redo(): void
    {
        $next = array_pop($this->redoStack);

        if ($next === null) {
            return;
        }

        $this->undoStack[] = $this->schema;
        $this->schema = $next;
        $this->selectedId = null;
        $this->refreshDerivedState();
    }

    public function exportSchema(): string
    {
        return json_encode($this->schema, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    }

    public function updatedSchema(): void
    {
        $this->schema = $this->normalizeSchema($this->schema);
        $this->refreshDerivedState();
    }

    public function render(): View
    {
        $selectedField = $this->selectedId === null ? null : $this->findField($this->schema['fields'], $this->selectedId);

        return view('daisy-kit::internal.forms-builder', [
            'outline' => $this->outlineFields($this->schema['fields']),
            'selectedField' => $selectedField,
        ]);
    }

    /** @return list<array{type: string, label: string, group: string}> */
    private function catalogue(): array
    {
        return [
            ['type' => 'text', 'label' => 'Text', 'group' => 'Inputs'],
            ['type' => 'email', 'label' => 'Email', 'group' => 'Inputs'],
            ['type' => 'tel', 'label' => 'Telephone', 'group' => 'Inputs'],
            ['type' => 'url', 'label' => 'URL', 'group' => 'Inputs'],
            ['type' => 'password', 'label' => 'Password', 'group' => 'Inputs'],
            ['type' => 'number', 'label' => 'Number', 'group' => 'Inputs'],
            ['type' => 'date', 'label' => 'Date', 'group' => 'Inputs'],
            ['type' => 'textarea', 'label' => 'Textarea', 'group' => 'Inputs'],
            ['type' => 'select', 'label' => 'Select', 'group' => 'Choices'],
            ['type' => 'radio', 'label' => 'Radio', 'group' => 'Choices'],
            ['type' => 'checkbox', 'label' => 'Checkbox', 'group' => 'Choices'],
            ['type' => 'range', 'label' => 'Range', 'group' => 'Choices'],
            ['type' => 'file', 'label' => 'File', 'group' => 'Uploads'],
            ['type' => 'section', 'label' => 'Section', 'group' => 'Layout'],
            ['type' => 'wizardStep', 'label' => 'Step', 'group' => 'Layout'],
        ];
    }

    /** @param array<string, mixed> $schema
     * @return array{layout: array{type: string}, fields: array<int, array<string, mixed>>}
     */
    private function normalizeSchema(array $schema): array
    {
        $layout = $this->schemaObject($schema['layout'] ?? null);
        $layoutType = in_array($layout['type'] ?? null, ['one-page', 'sections', 'multi-step'], true)
            ? $layout['type']
            : 'one-page';
        $fields = $this->fieldList($schema['fields'] ?? null);

        $normalizedFields = [];

        foreach ($fields as $index => $field) {
            $normalizedFields[] = $this->normalizeField($field, $index);
        }

        return [
            'layout' => ['type' => $layoutType],
            'fields' => $normalizedFields,
        ];
    }

    /** @return array<string, mixed> */
    private function normalizeField(mixed $raw, int $index): array
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

        if (in_array($type, self::ContainerTypes, true)) {
            $children = $this->fieldList($value['fields'] ?? null);
            $field['fields'] = [];

            foreach ($children as $childIndex => $child) {
                $field['fields'][] = $this->normalizeField($child, $childIndex);
            }
        }

        return $field;
    }

    private function normalizeFieldType(mixed $type): string
    {
        return is_string($type) && in_array($type, self::FieldTypes, true) ? $type : 'text';
    }

    private function normalizeJsonataDescriptor(mixed $expression): mixed
    {
        if (is_string($expression) && trim($expression) !== '') {
            return [
                'type' => 'jsonata',
                'expression' => trim($expression),
            ];
        }

        return $expression;
    }

    private function stringValue(mixed $value, string $fallback): string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
    }

    /**
     * @return array<string, mixed>
     */
    private function schemaObject(mixed $value): array
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

    /**
     * @return list<array<string, mixed>>
     */
    private function fieldList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $fields = [];

        foreach ($value as $field) {
            if (is_array($field)) {
                $fields[] = $this->schemaObject($field);
            }
        }

        return $fields;
    }

    /** @return array<string, mixed> */
    private function newField(string $type, int $position): array
    {
        $id = strtolower(preg_replace('/[^A-Za-z0-9_-]+/', '_', "{$type}_{$position}") ?? "field_{$position}");
        $field = [
            'name' => in_array($type, self::ContainerTypes, true) ? '' : $id,
            'label' => "Field {$position}",
            'type' => $type,
        ];

        if (in_array($type, self::ContainerTypes, true)) {
            $field['id'] = $id;
        }

        if ($type === 'select' || $type === 'radio') {
            $field['options'] = [['label' => 'Option 1', 'value' => 'option_1']];
        }

        if (in_array($type, self::ContainerTypes, true)) {
            $field['fields'] = [];
        }

        return $field;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @return list<array<string, mixed>>
     */
    private function flattenFields(array $fields): array
    {
        $flat = [];

        foreach ($fields as $field) {
            $flat[] = $field;

            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                array_push($flat, ...$this->flattenFields($children));
            }
        }

        return $flat;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @return list<array{field: array<string, mixed>, depth: int, identity: string}>
     */
    private function outlineFields(array $fields, int $depth = 1): array
    {
        $outline = [];

        foreach ($fields as $field) {
            $outline[] = [
                'field' => $field,
                'depth' => $depth,
                'identity' => $this->fieldIdentity($field),
            ];

            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                array_push($outline, ...$this->outlineFields($children, $depth + 1));
            }
        }

        return $outline;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @return array<string, mixed>|null
     */
    private function findField(array $fields, string $id): ?array
    {
        foreach ($fields as $field) {
            if ($this->fieldIdentity($field) === $id) {
                return $field;
            }

            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $found = $this->findField($children, $id);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /** @param array<string, mixed>|null $field */
    private function isContainer(?array $field): bool
    {
        return $field !== null && in_array($field['type'] ?? null, self::ContainerTypes, true);
    }

    /** @param array<string, mixed> $field */
    private function fieldIdentity(array $field): string
    {
        return is_string($field['id'] ?? null) && $field['id'] !== ''
            ? $field['id']
            : $this->stringValue($field['name'] ?? null, '');
    }

    private function resolveReference(int|string $reference): ?string
    {
        if (is_int($reference)) {
            if (! isset($this->schema['fields'][$reference])) {
                return null;
            }

            return $this->fieldIdentity($this->schema['fields'][$reference]);
        }

        return $this->findField($this->schema['fields'], $reference) === null ? null : $reference;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @param  array<string, mixed>  $child
     * @return array<int, array<string, mixed>>
     */
    private function appendChild(array $fields, string $parentId, array $child): array
    {
        return $this->mapTree($fields, function (array $field) use ($parentId, $child): array {
            if ($this->fieldIdentity($field) === $parentId) {
                $field['fields'] = [...$this->fieldList($field['fields'] ?? null), $child];
            }

            return $field;
        });
    }

    /** @param array<int, array<string, mixed>> $fields
     * @param  array<string, mixed>  $child
     * @return array<int, array<string, mixed>>
     */
    private function insertAfter(array $fields, string $targetId, array $child): array
    {
        $result = [];

        foreach ($fields as $field) {
            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $field['fields'] = $this->insertAfter($children, $targetId, $child);
            }

            $result[] = $field;

            if ($this->fieldIdentity($field) === $targetId) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @return array<int, array<string, mixed>>
     */
    private function removeFromTree(array $fields, string $targetId): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($this->fieldIdentity($field) === $targetId) {
                continue;
            }

            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $field['fields'] = $this->removeFromTree($children, $targetId);
            }

            $result[] = $field;
        }

        return $result;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @return array<int, array<string, mixed>>
     */
    private function moveInTree(array $fields, string $targetId, int $direction, bool &$moved): array
    {
        foreach ($fields as $index => $field) {
            if ($this->fieldIdentity($field) === $targetId) {
                $target = $index + $direction;

                if (isset($fields[$target])) {
                    [$fields[$index], $fields[$target]] = [$fields[$target], $fields[$index]];
                    $moved = true;
                }

                return $fields;
            }

            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $fields[$index]['fields'] = $this->moveInTree($children, $targetId, $direction, $moved);

                if ($moved) {
                    return $fields;
                }
            }
        }

        return $fields;
    }

    /** @param array<int, array<string, mixed>> $fields
     * @param  callable(array<string, mixed>): array<string, mixed>  $callback
     * @return array<int, array<string, mixed>>
     */
    private function mapTree(array $fields, callable $callback): array
    {
        foreach ($fields as $index => $field) {
            $field = $callback($field);
            $children = $this->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $field['fields'] = $this->mapTree($children, $callback);
            }

            $fields[$index] = $field;
        }

        return $fields;
    }

    private function replaceFromJson(string $json): void
    {
        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $this->jsonError = 'Invalid JSON.';
            $this->refreshDiagnostics();

            return;
        }

        if (! is_array($decoded)) {
            $this->jsonError = 'Schema JSON must decode to an object.';
            $this->refreshDiagnostics();

            return;
        }

        $this->remember();
        $this->schema = $this->normalizeSchema($this->schemaObject($decoded));
        $this->selectedId = null;
        $this->jsonError = null;
        $this->finishMutation();
    }

    private function remember(): void
    {
        $this->undoStack[] = $this->schema;
        $this->redoStack = [];
    }

    private function finishMutation(): void
    {
        $this->schema = $this->normalizeSchema($this->schema);
        $this->refreshDerivedState();
    }

    private function refreshDerivedState(): void
    {
        $this->schemaJson = $this->exportSchema();
        $this->refreshDiagnostics();
    }

    private function refreshDiagnostics(): void
    {
        $diagnostics = [];
        $seenIds = [];
        $seenNames = [];

        foreach ($this->flattenFields($this->schema['fields']) as $index => $field) {
            $path = "/fields/{$index}";
            $identity = $this->fieldIdentity($field);

            if ($identity === '') {
                $diagnostics[] = ['path' => $path, 'code' => 'missing_id', 'message' => 'A field needs a stable id or name.'];
            } elseif (in_array($identity, $seenIds, true)) {
                $diagnostics[] = ['path' => $path, 'code' => 'duplicate_id', 'message' => "Field identity {$identity} is duplicated."];
            }

            $seenIds[] = $identity;
            $type = $this->stringValue($field['type'] ?? null, '');

            $name = $this->stringValue($field['name'] ?? null, '');

            if (! in_array($type, self::ContainerTypes, true) && $type !== 'hidden' && $name === '') {
                $diagnostics[] = ['path' => $path, 'code' => 'missing_name', 'message' => 'A submitting field needs a name.'];
            }

            if ($name !== '' && ! in_array($type, self::ContainerTypes, true) && in_array($name, $seenNames, true)) {
                $diagnostics[] = ['path' => $path, 'code' => 'duplicate_name', 'message' => "Field name {$name} is duplicated."];
            }
            $seenNames[] = $name;

            foreach (['visibleWhen', 'computed'] as $expressionKey) {
                $expression = $field[$expressionKey] ?? null;

                if ($expression !== null && (! is_array($expression) || ($expression['type'] ?? null) !== 'jsonata' || ! is_string($expression['expression'] ?? null) || trim($expression['expression']) === '')) {
                    $diagnostics[] = ['path' => "{$path}/{$expressionKey}", 'code' => 'invalid_jsonata', 'message' => "{$expressionKey} must be a JSONata expression."];
                }
            }
        }

        if ($this->jsonError !== null) {
            $diagnostics[] = ['path' => '/json', 'code' => 'json_parse_error', 'message' => $this->jsonError];
        }

        $this->diagnostics = $diagnostics;
    }
}

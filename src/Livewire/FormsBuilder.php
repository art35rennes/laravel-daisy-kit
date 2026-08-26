<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Livewire;

use Art35rennes\DaisyKit\Livewire\Support\FormSchemaContract;
use Art35rennes\DaisyKit\Livewire\Support\FormSchemaTree;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Optional Livewire 4 authoring surface for the same declarative schema consumed by
 * x-daisy-kit::forms.viewer. The schema is the sole persisted value; UI state stays
 * local to the component and every mutation records a reversible snapshot.
 */
class FormsBuilder extends Component
{
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

    /**
     * Serializable selected-field projection bound by the Livewire 4 inspector.
     *
     * @var array{name: string, label: string, type: string, attrs: string, options: string, rules: string, visibleWhen: string, computed: string}
     */
    public array $inspector = [
        'name' => '',
        'label' => '',
        'type' => '',
        'attrs' => '{}',
        'options' => '[]',
        'rules' => '[]',
        'visibleWhen' => 'null',
        'computed' => 'null',
    ];

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
        $this->schema = $this->contract()->normalizeSchema($schema);
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
        $contract = $this->contract();
        $type = $contract->normalizeFieldType($type);
        $this->remember();
        $field = $contract->newField($type, count(FormSchemaTree::flatten($this->schema['fields'], $contract)) + 1);

        if ($this->selectedId !== null && $contract->isContainer(FormSchemaTree::find($this->schema['fields'], $this->selectedId, $contract))) {
            $this->schema['fields'] = FormSchemaTree::appendChild($this->schema['fields'], $this->selectedId, $field, $contract);
        } elseif ($this->selectedId !== null) {
            $this->schema['fields'] = FormSchemaTree::insertAfter($this->schema['fields'], $this->selectedId, $field, $contract);
        } else {
            $this->schema['fields'][] = $field;
        }

        $this->selectedId = $contract->identity($field);
        $this->finishMutation();
    }

    public function addSection(): void
    {
        $this->addField('section');
    }

    public function addStep(): void
    {
        $contract = $this->contract();
        $this->remember();
        $this->schema['layout']['type'] = 'multi-step';
        $field = $contract->newField('wizardStep', count(FormSchemaTree::flatten($this->schema['fields'], $contract)) + 1);
        $this->schema['fields'][] = $field;
        $this->selectedId = $contract->identity($field);
        $this->finishMutation();
    }

    public function selectField(string $id): void
    {
        $contract = $this->contract();
        $field = FormSchemaTree::find($this->schema['fields'], $id, $contract);
        $this->selectedId = $field === null ? null : $contract->identity($field);
        $this->syncInspector();
    }

    public function clearSelection(): void
    {
        $this->selectedId = null;
        $this->syncInspector();
    }

    public function updatedInspector(mixed $value, string $path): void
    {
        if (! in_array($path, ['name', 'label', 'type', 'attrs', 'options', 'rules', 'visibleWhen', 'computed'], true)) {
            return;
        }

        if (in_array($path, ['attrs', 'options', 'rules', 'visibleWhen', 'computed'], true)) {
            $this->updateSelectedJson($path, is_string($value) ? $value : 'null');

            return;
        }

        $this->updateSelectedPath($path, $value);
    }

    public function removeField(int|string $reference): void
    {
        $id = $this->resolveReference($reference);

        if ($id === null) {
            return;
        }

        $this->remember();
        $this->schema['fields'] = FormSchemaTree::remove($this->schema['fields'], $id, $this->contract());
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
        $this->schema['fields'] = FormSchemaTree::move($this->schema['fields'], $id, $direction, $moved, $this->contract());

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

        $this->selectedId = $this->contract()->identity($field);
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
        $contract = $this->contract();
        $this->schema['fields'] = FormSchemaTree::map($this->schema['fields'], function (array $field) use ($path, $value, &$nextSelectedId, &$updated, $contract): array {
            if ($contract->identity($field) !== $this->selectedId) {
                return $field;
            }

            $updated = true;

            if ($path === 'type') {
                $field[$path] = $contract->normalizeFieldType(is_string($value) ? $value : '');
            } elseif (in_array($path, ['name', 'label'], true)) {
                $field[$path] = is_string($value) ? trim($value) : '';

                if ($path === 'name' && ! isset($field['id'])) {
                    $nextSelectedId = $field[$path];
                }
            } else {
                $field[$path] = $value;
            }

            return $field;
        }, $contract);

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
        $this->schema = $this->contract()->normalizeSchema($this->schema);
        $this->refreshDerivedState();
    }

    public function render(): View
    {
        $contract = $this->contract();
        $selectedField = $this->selectedId === null ? null : FormSchemaTree::find($this->schema['fields'], $this->selectedId, $contract);

        return view('daisy-kit::internal.forms-builder', [
            'outline' => FormSchemaTree::outline($this->schema['fields'], $contract),
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

    private function resolveReference(int|string $reference): ?string
    {
        $contract = $this->contract();

        if (is_int($reference)) {
            if (! isset($this->schema['fields'][$reference])) {
                return null;
            }

            return $contract->identity($this->schema['fields'][$reference]);
        }

        return FormSchemaTree::find($this->schema['fields'], $reference, $contract) === null ? null : $reference;
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
        $this->schema = $this->contract()->normalizeSchema($this->contract()->object($decoded));
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
        $this->schema = $this->contract()->normalizeSchema($this->schema);
        $this->refreshDerivedState();
    }

    private function refreshDerivedState(): void
    {
        $this->schemaJson = $this->exportSchema();
        $this->syncInspector();
        $this->refreshDiagnostics();
    }

    private function syncInspector(): void
    {
        $field = $this->selectedId === null
            ? null
            : FormSchemaTree::find($this->schema['fields'], $this->selectedId, $this->contract());

        if ($field === null) {
            $this->inspector = [
                'name' => '',
                'label' => '',
                'type' => '',
                'attrs' => '{}',
                'options' => '[]',
                'rules' => '[]',
                'visibleWhen' => 'null',
                'computed' => 'null',
            ];

            return;
        }

        $this->inspector = [
            'name' => is_string($field['name'] ?? null) ? $field['name'] : '',
            'label' => is_string($field['label'] ?? null) ? $field['label'] : '',
            'type' => is_string($field['type'] ?? null) ? $field['type'] : '',
            'attrs' => $this->encodeInspectorJson($field['attrs'] ?? new \stdClass),
            'options' => $this->encodeInspectorJson($field['options'] ?? []),
            'rules' => $this->encodeInspectorJson($field['rules'] ?? []),
            'visibleWhen' => $this->encodeInspectorJson($field['visibleWhen'] ?? null),
            'computed' => $this->encodeInspectorJson($field['computed'] ?? null),
        ];
    }

    private function encodeInspectorJson(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
    }

    private function refreshDiagnostics(): void
    {
        $contract = $this->contract();
        $diagnostics = [];
        $seenIds = [];
        $seenNames = [];

        foreach (FormSchemaTree::flatten($this->schema['fields'], $contract) as $index => $field) {
            $path = "/fields/{$index}";
            $identity = $contract->identity($field);

            if ($identity === '') {
                $diagnostics[] = ['path' => $path, 'code' => 'missing_id', 'message' => 'A field needs a stable id or name.'];
            } elseif (in_array($identity, $seenIds, true)) {
                $diagnostics[] = ['path' => $path, 'code' => 'duplicate_id', 'message' => "Field identity {$identity} is duplicated."];
            }

            $seenIds[] = $identity;
            $type = $contract->stringValue($field['type'] ?? null, '');

            $name = $contract->stringValue($field['name'] ?? null, '');

            if (! in_array($type, FormSchemaContract::CONTAINER_TYPES, true) && $type !== 'hidden' && $name === '') {
                $diagnostics[] = ['path' => $path, 'code' => 'missing_name', 'message' => 'A submitting field needs a name.'];
            }

            if ($name !== '' && ! in_array($type, FormSchemaContract::CONTAINER_TYPES, true) && in_array($name, $seenNames, true)) {
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

    private function contract(): FormSchemaContract
    {
        return new FormSchemaContract;
    }
}

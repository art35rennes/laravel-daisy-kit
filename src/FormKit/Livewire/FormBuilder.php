<?php

namespace Art35rennes\DaisyKit\FormKit\Livewire;

use Art35rennes\DaisyKit\FormKit\FormFieldCatalog;
use Art35rennes\DaisyKit\FormKit\FormSchemaNormalizer;
use Art35rennes\DaisyKit\FormKit\FormSchemaValidator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Component;

/**
 * Livewire authoring surface for DaisyFormSchema payloads.
 *
 * The component is the single source of truth for builder state: field creation,
 * property edits, JSON editor updates, diagnostics, undo/redo, selection, and tree
 * reordering all mutate the canonical schema here. JavaScript is limited to
 * transient pointer/drag affordances and calls back into this component for schema
 * mutations.
 */
class FormBuilder extends Component
{
    /** @var array<string, mixed> */
    public array $schema = [];

    /** @var array<int, array<string, mixed>> */
    public array $fieldTypes = [];

    /** @var array<int, array<string, mixed>> */
    public array $functionCatalog = [];

    /** @var array<string, mixed> */
    public array $value = [];

    /** @var array<string, mixed> */
    public array $errors = [];

    public bool $preview = true;

    public bool $jsonEditor = true;

    public ?string $name = null;

    public ?string $selectedId = null;

    public string $schemaJson = '';

    public ?string $jsonError = null;

    public ?string $viewerSubmitMode = null;

    public bool $fieldEditorOpen = false;

    /** @var array<string, mixed>|null */
    public ?array $fieldEditorSnapshot = null;

    public ?string $fieldEditorSelectedIdSnapshot = null;

    public string $fieldSearch = '';

    /** @var array<string, bool> */
    public array $collapsedFieldIds = [];

    public ?string $draggingFieldId = null;

    /** @var array<int, array<string, mixed>> */
    public array $undoStack = [];

    /** @var array<int, array<string, mixed>> */
    public array $redoStack = [];

    protected FormSchemaNormalizer $normalizer;

    protected FormFieldCatalog $catalog;

    public function boot(FormSchemaNormalizer $normalizer, FormFieldCatalog $catalog): void
    {
        $this->normalizer = $normalizer;
        $this->catalog = $catalog;
    }

    /**
     * @param  array<string, mixed>|string|null  $schema
     * @param  array<int, array<string, mixed>>|string|null  $fieldTypes
     * @param  array<int, array<string, mixed>>|string|null  $functionCatalog
     * @param  array<string, mixed>|string|null  $value
     * @param  array<string, mixed>|string|null  $errors
     */
    public function mount(
        array|string|null $schema = null,
        array|string|null $fieldTypes = null,
        array|string|null $functionCatalog = null,
        bool $preview = true,
        bool $jsonEditor = true,
        ?string $name = null,
        array|string|null $value = [],
        array|string|null $errors = [],
        ?string $viewerSubmitMode = null,
    ): void {
        $this->schema = $this->normalizer->normalize($this->decodeArray($schema));
        $this->fieldTypes = $this->normalizeFieldTypes($this->decodeArray($fieldTypes));
        $this->functionCatalog = array_values($this->decodeArray($functionCatalog) ?? []);
        $this->preview = $preview;
        $this->jsonEditor = $jsonEditor;
        $this->name = $name;
        $this->value = $this->decodeArray($value) ?? [];
        $this->errors = $this->decodeArray($errors) ?? [];
        $this->viewerSubmitMode = $viewerSubmitMode;
        $this->selectedId = null;
        $this->syncSchemaJson();
    }

    public function render(): View
    {
        $canonicalSchema = $this->canonicalSchema();
        $diagnostics = (new FormSchemaValidator)->validate($canonicalSchema);
        $allFields = $this->normalizer->flattenFields($canonicalSchema);
        $flatFields = $this->visibleFlatFields($canonicalSchema, $this->fieldSearch);

        if ($this->jsonError) {
            array_unshift($diagnostics, [
                'path' => '/json',
                'code' => 'json_parse_error',
                'message' => $this->jsonError,
            ]);
        }

        $canonicalJson = json_encode($canonicalSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';

        return view('daisy::livewire.form-builder', [
            'canonicalSchema' => $canonicalSchema,
            'canonicalJson' => $canonicalJson,
            'diagnostics' => $diagnostics,
            'flatFields' => $flatFields,
            'allFields' => $allFields,
            'fieldGroups' => $this->groupedFieldTypes(),
            'propertyGroups' => $this->groupProperties($this->catalog->propertiesFor($this->selectedField($canonicalSchema)['type'] ?? null)),
            'selectedField' => $this->selectedField($canonicalSchema),
            'diagnosticsByField' => $this->diagnosticsByField($diagnostics),
        ]);
    }

    /**
     * Adds a new field relative to the current selection.
     *
     * A selected container receives the field as a child, a selected leaf receives
     * the field after itself, and no selection appends at the root. This keeps the
     * authoring workflow predictable without maintaining a separate JS tree state.
     */
    public function addField(string $type): void
    {
        $this->fieldEditorSnapshot = $this->canonicalSchema();
        $this->fieldEditorSelectedIdSnapshot = $this->selectedId;
        $this->rememberSchemaState();
        $this->jsonError = null;
        $field = $this->normalizer->createField($type, count($this->normalizer->flattenFields($this->canonicalSchema())) + 1);
        $selected = $this->selectedField($this->canonicalSchema());

        if ($selected && in_array($selected['type'] ?? null, FormSchemaNormalizer::ContainerTypes, true)) {
            $this->schema['fields'] = $this->appendFieldToTree($this->schema['fields'] ?? [], (string) $selected['id'], $field);
            unset($this->collapsedFieldIds[(string) $selected['id']]);
        } elseif ($selected) {
            $this->schema['fields'] = $this->insertFieldAfterInTree($this->schema['fields'] ?? [], (string) $selected['id'], $field);
        } else {
            $this->schema['fields'][] = $field;
        }

        $this->selectedId = $field['id'];
        $this->fieldEditorOpen = false;
        $this->syncSchemaJson();
    }

    public function addStep(): void
    {
        $this->schema['layout']['type'] = 'multi-step';
        $this->addField('wizardStep');
    }

    public function selectField(string $id): void
    {
        $this->selectedId = $id;
    }

    public function clearSelection(): void
    {
        $this->selectedId = null;
    }

    public function editField(string $id): void
    {
        $this->fieldEditorSnapshot = $this->canonicalSchema();
        $this->fieldEditorSelectedIdSnapshot = $id;
        $this->selectedId = $id;
        $this->fieldEditorOpen = true;
    }

    public function closeFieldEditor(): void
    {
        $this->fieldEditorOpen = false;
        $this->fieldEditorSnapshot = null;
        $this->fieldEditorSelectedIdSnapshot = null;
    }

    public function cancelFieldEditor(): void
    {
        if ($this->fieldEditorSnapshot) {
            $this->schema = $this->normalizer->normalize($this->fieldEditorSnapshot);
            $fieldIds = array_column($this->normalizer->flattenFields($this->schema), 'id');
            $this->selectedId = in_array($this->fieldEditorSelectedIdSnapshot, $fieldIds, true)
                ? $this->fieldEditorSelectedIdSnapshot
                : ($fieldIds[0] ?? null);
            $this->jsonError = null;
            $this->syncSchemaJson();
        }

        $this->fieldEditorOpen = false;
        $this->fieldEditorSnapshot = null;
        $this->fieldEditorSelectedIdSnapshot = null;
    }

    public function toggleFieldCollapsed(string $id): void
    {
        $field = $this->findField($this->canonicalSchema()['fields'] ?? [], $id);

        if (! $field || ! is_array($field['fields'] ?? null)) {
            return;
        }

        $this->selectedId = $id;

        if ($this->collapsedFieldIds[$id] ?? false) {
            unset($this->collapsedFieldIds[$id]);

            return;
        }

        $this->collapsedFieldIds[$id] = true;

        if ($this->selectedId && $this->isDescendantOf($field, $this->selectedId)) {
            $this->selectedId = $id;
        }
    }

    public function removeField(string $id): void
    {
        $this->selectedId = $id;
        $this->rememberSchemaState();
        $this->schema['fields'] = $this->removeFieldFromTree($this->schema['fields'] ?? [], $id);
        unset($this->collapsedFieldIds[$id]);

        $this->selectedId = null;
        $this->fieldEditorOpen = false;

        $this->syncSchemaJson();
    }

    public function moveField(string $id, int $direction): void
    {
        $this->selectedId = $id;
        $this->rememberSchemaState();
        $fields = $this->schema['fields'] ?? [];
        $this->schema['fields'] = $this->moveFieldInSiblings($fields, $id, $direction);
        $this->syncSchemaJson();
    }

    public function startDragging(string $id): void
    {
        $this->selectedId = $id;
        $this->draggingFieldId = $id;
    }

    public function stopDragging(): void
    {
        $this->draggingFieldId = null;
    }

    /**
     * Reorders a field in the canonical schema tree after a pointer drop.
     *
     * The browser bridge sends the dragged id and a target/action pair. This method
     * scopes the mutation to the current schema, records undo state, and normalizes
     * the result before the preview viewer is re-rendered.
     */
    public function dropField(string $targetId, string $position, ?string $draggedId = null): void
    {
        $draggedId = $draggedId ?: $this->draggingFieldId;

        if (! $draggedId || $draggedId === $targetId) {
            $this->draggingFieldId = null;

            return;
        }

        $this->rememberSchemaState();
        $this->selectedId = $draggedId;
        $this->schema['fields'] = $this->reorderFieldInTree(
            $this->schema['fields'] ?? [],
            $draggedId,
            $targetId,
            in_array($position, ['before', 'after', 'inside'], true) ? $position : 'before',
        );

        $this->draggingFieldId = null;
        $this->syncSchemaJson();
    }

    public function updateSchemaKey(string $key, mixed $value): void
    {
        $this->rememberSchemaState();
        data_set($this->schema, $key, $value);
        $this->jsonError = null;
        $this->syncSchemaJson();
    }

    public function updateSchemaJson(string $key, string $json): void
    {
        try {
            $value = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->updateSchemaKey($key, $value);
        } catch (\JsonException $exception) {
            $this->jsonError = "{$key}: {$exception->getMessage()}";
        }
    }

    public function updateSelectedField(string $key, mixed $value): void
    {
        $this->updateSelectedPath($key, $value);
    }

    public function updateSelectedPath(string $path, mixed $value): void
    {
        if (! $this->selectedId) {
            return;
        }

        $this->rememberSchemaState();
        $this->schema['fields'] = $this->mapFieldTree($this->schema['fields'] ?? [], function (array $field) use ($path, $value): array {
            if (($field['id'] ?? null) !== $this->selectedId) {
                return $field;
            }

            if ($value === '' && ! in_array($path, ['id', 'type'], true)) {
                Arr::forget($field, $path);
            } else {
                data_set($field, $path, $value);
            }

            if ($path === 'id') {
                $this->selectedId = (string) $value;
            }

            return $field;
        });

        $this->jsonError = null;
        $this->syncSchemaJson();
    }

    public function updateSelectedJson(string $key, string $json): void
    {
        try {
            $value = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->updateSelectedField($key, $value);
        } catch (\JsonException $exception) {
            $this->jsonError = "{$key}: {$exception->getMessage()}";
        }
    }

    public function addSelectedOption(): void
    {
        $field = $this->selectedField($this->canonicalSchema());
        $options = array_values((array) ($field['options'] ?? []));
        $options[] = ['label' => 'Option '.(count($options) + 1), 'value' => 'option_'.(count($options) + 1)];
        $this->updateSelectedPath('options', $options);
    }

    public function updateSelectedOption(int $index, string $key, string $value): void
    {
        $field = $this->selectedField($this->canonicalSchema());
        $options = array_values((array) ($field['options'] ?? []));

        if (! isset($options[$index])) {
            return;
        }

        $options[$index][$key] = $value;
        $this->updateSelectedPath('options', $options);
    }

    public function removeSelectedOption(int $index): void
    {
        $field = $this->selectedField($this->canonicalSchema());
        $options = array_values((array) ($field['options'] ?? []));
        unset($options[$index]);
        $this->updateSelectedPath('options', array_values($options));
    }

    public function updateSelectedCsvExpression(string $key, string $expression, string $dependsOnCsv = '', ?string $mode = null): void
    {
        if (trim($expression) === '') {
            $this->updateSelectedField($key, null);

            return;
        }

        $payload = [
            'type' => 'jsonata',
            'expression' => $expression,
            'dependsOn' => $this->splitCsv($dependsOnCsv),
        ];

        if ($key === 'computed') {
            $payload['mode'] = in_array($mode, ['readonly', 'hidden', 'suggested'], true) ? $mode : 'readonly';
        }

        $this->updateSelectedField($key, $payload);
    }

    /**
     * Replaces the builder state from the JSON editor payload.
     *
     * Invalid JSON is reported as a diagnostic instead of mutating the current
     * schema, so authors can recover from partial edits without losing the last
     * valid form tree.
     */
    public function updateFromJson(): void
    {
        try {
            $decoded = json_decode($this->schemaJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->jsonError = $exception->getMessage();

            return;
        }

        if (! is_array($decoded)) {
            $this->jsonError = 'Schema JSON must decode to an object.';

            return;
        }

        $this->rememberSchemaState();
        $this->schema = $this->normalizer->normalize($decoded);
        $this->selectedId = null;
        $this->collapsedFieldIds = [];
        $this->fieldEditorOpen = false;
        $this->jsonError = null;
        $this->syncSchemaJson();
    }

    public function updateFromJsonPayload(string $json): void
    {
        $this->schemaJson = $json;
        $this->updateFromJson();
    }

    public function updatedFieldSearch(): void
    {
        if (trim($this->fieldSearch) !== '') {
            $this->collapsedFieldIds = [];
        }
    }

    public function collapseAllFields(): void
    {
        $collapsed = [];

        foreach ($this->normalizer->flattenFields($this->canonicalSchema()) as $field) {
            if (is_array($field['fields'] ?? null) && count((array) $field['fields']) > 0) {
                $collapsed[(string) $field['id']] = true;
            }
        }

        $this->collapsedFieldIds = $collapsed;
    }

    public function expandAllFields(): void
    {
        $this->collapsedFieldIds = [];
    }

    public function undo(): void
    {
        $previous = array_pop($this->undoStack);

        if (! $previous) {
            return;
        }

        $this->redoStack[] = $this->canonicalSchema();
        $this->schema = $this->normalizer->normalize($previous);
        $this->selectedId = null;
        $this->collapsedFieldIds = [];
        $this->jsonError = null;
        $this->syncSchemaJson();
    }

    public function redo(): void
    {
        $next = array_pop($this->redoStack);

        if (! $next) {
            return;
        }

        $this->undoStack[] = $this->canonicalSchema();
        $this->schema = $this->normalizer->normalize($next);
        $this->selectedId = null;
        $this->collapsedFieldIds = [];
        $this->jsonError = null;
        $this->syncSchemaJson();
    }

    /**
     * @return array<string, mixed>
     */
    public function canonicalSchema(): array
    {
        return $this->normalizer->normalize($this->schema);
    }

    /**
     * @param  array<string, mixed>|string|null  $value
     * @return array<string, mixed>|null
     */
    protected function decodeArray(array|string|null $value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $fieldTypes
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeFieldTypes(?array $fieldTypes): array
    {
        if ($fieldTypes) {
            return array_values($fieldTypes);
        }

        return $this->catalog->definitions();
    }

    /**
     * @return array<int, array{label: string, fields: array<int, array<string, mixed>>}>
     */
    protected function groupedFieldTypes(): array
    {
        return collect($this->fieldTypes)
            ->groupBy(fn (array $fieldType): string => (string) ($fieldType['group'] ?? 'Fields'))
            ->map(fn ($fields, string $group): array => [
                'label' => $group,
                'fields' => array_values($fields->all()),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $properties
     * @return array<int, array{label: string, properties: array<int, array<string, mixed>>}>
     */
    protected function groupProperties(array $properties): array
    {
        return collect($properties)
            ->groupBy(fn (array $property): string => (string) ($property['group'] ?? 'Field'))
            ->map(fn ($items, string $group): array => [
                'label' => $group,
                'properties' => array_values($items->all()),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $diagnostics
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function diagnosticsByField(array $diagnostics): array
    {
        $grouped = [];

        foreach ($diagnostics as $diagnostic) {
            $path = (string) ($diagnostic['path'] ?? '');

            if (! preg_match('#/fields/([^/]+)#', $path, $matches)) {
                continue;
            }

            $grouped[$matches[1]][] = $diagnostic;
        }

        return $grouped;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<int, array<string, mixed>>
     */
    protected function visibleFlatFields(array $schema, string $search = ''): array
    {
        return $this->visibleFlatten($schema['fields'] ?? [], search: trim(mb_strtolower($search)));
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, array<string, mixed>>
     */
    protected function visibleFlatten(array $fields, ?string $parent = null, int $depth = 0, string $search = ''): array
    {
        $flat = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $id = (string) ($field['id'] ?? '');
            $field['_parent'] = $parent;
            $field['_depth'] = $depth;
            $children = is_array($field['fields'] ?? null)
                ? $this->visibleFlatten($field['fields'], $id, $depth + 1, $search)
                : [];
            $matchesSearch = $search === '' || $this->fieldMatchesSearch($field, $search) || $children !== [];

            if ($matchesSearch) {
                $flat[] = $field;
            }

            if (is_array($field['fields'] ?? null) && ($search !== '' || ! ($this->collapsedFieldIds[$id] ?? false))) {
                array_push($flat, ...$children);
            }
        }

        return $flat;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function fieldMatchesSearch(array $field, string $search): bool
    {
        $haystack = mb_strtolower(implode(' ', array_filter([
            $field['id'] ?? null,
            $field['name'] ?? null,
            $field['label'] ?? null,
            $field['type'] ?? null,
        ], fn (mixed $value): bool => is_scalar($value))));

        return str_contains($haystack, $search);
    }

    protected function rememberSchemaState(): void
    {
        $this->undoStack[] = $this->canonicalSchema();
        $this->redoStack = [];

        if (count($this->undoStack) > 25) {
            array_shift($this->undoStack);
        }
    }

    /**
     * @param  array<string, mixed>|null  $schema
     * @return array<string, mixed>|null
     */
    protected function selectedField(?array $schema = null): ?array
    {
        if (! $this->selectedId) {
            return null;
        }

        return collect($this->normalizer->flattenFields($schema ?? $this->canonicalSchema()))
            ->firstWhere('id', $this->selectedId);
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<string, mixed>|null
     */
    protected function findField(array $fields, string $id): ?array
    {
        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            if (($field['id'] ?? null) === $id) {
                return $field;
            }

            if (is_array($field['fields'] ?? null)) {
                $match = $this->findField($field['fields'], $id);

                if ($match) {
                    return $match;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function isDescendantOf(array $field, string $id): bool
    {
        foreach ((array) ($field['fields'] ?? []) as $child) {
            if (! is_array($child)) {
                continue;
            }

            if (($child['id'] ?? null) === $id || $this->isDescendantOf($child, $id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, mixed>
     */
    protected function appendFieldToTree(array $fields, string $containerId, array $child): array
    {
        return array_map(function (mixed $field) use ($containerId, $child): mixed {
            if (! is_array($field)) {
                return $field;
            }

            if (($field['id'] ?? null) === $containerId) {
                $field['fields'] = array_values([...(array) ($field['fields'] ?? []), $child]);

                return $field;
            }

            if (is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->appendFieldToTree($field['fields'], $containerId, $child);
            }

            return $field;
        }, $fields);
    }

    /**
     * @param  array<int, mixed>  $fields
     * @param  array<string, mixed>  $child
     * @return array<int, mixed>
     */
    protected function insertFieldAfterInTree(array $fields, string $targetId, array $child): array
    {
        $result = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                $result[] = $field;

                continue;
            }

            if (($field['id'] ?? null) === $targetId) {
                $result[] = $field;
                $result[] = $child;

                continue;
            }

            if (is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->insertFieldAfterInTree($field['fields'], $targetId, $child);
            }

            $result[] = $field;
        }

        return array_values($result);
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, mixed>
     */
    protected function removeFieldFromTree(array $fields, string $id): array
    {
        return array_values(array_map(function (mixed $field) use ($id): mixed {
            if (is_array($field) && is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->removeFieldFromTree($field['fields'], $id);
            }

            return $field;
        }, array_filter($fields, fn (mixed $field): bool => ! is_array($field) || ($field['id'] ?? null) !== $id)));
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, mixed>
     */
    protected function moveFieldInSiblings(array $fields, string $id, int $direction): array
    {
        $index = null;

        foreach ($fields as $position => $field) {
            if (is_array($field) && ($field['id'] ?? null) === $id) {
                $index = $position;

                break;
            }
        }

        if ($index !== null) {
            $target = $index + $direction;

            if ($target < 0 || $target >= count($fields)) {
                return array_values($fields);
            }

            $field = $fields[$index];
            array_splice($fields, $index, 1);
            array_splice($fields, $target, 0, [$field]);

            return array_values($fields);
        }

        return array_values(array_map(function (mixed $field) use ($id, $direction): mixed {
            if (is_array($field) && is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->moveFieldInSiblings($field['fields'], $id, $direction);
            }

            return $field;
        }, $fields));
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, mixed>
     */
    protected function reorderFieldInSiblings(array $fields, string $draggedId, string $targetId, string $position): array
    {
        $draggedIndex = null;
        $targetIndex = null;

        foreach ($fields as $index => $field) {
            if (! is_array($field)) {
                continue;
            }

            if (($field['id'] ?? null) === $draggedId) {
                $draggedIndex = $index;
            }

            if (($field['id'] ?? null) === $targetId) {
                $targetIndex = $index;
            }
        }

        if ($draggedIndex !== null && $targetIndex !== null) {
            $dragged = $fields[$draggedIndex];
            array_splice($fields, $draggedIndex, 1);

            if ($draggedIndex < $targetIndex) {
                $targetIndex--;
            }

            $insertIndex = $position === 'after' ? $targetIndex + 1 : $targetIndex;
            array_splice($fields, $insertIndex, 0, [$dragged]);

            return array_values($fields);
        }

        return array_values(array_map(function (mixed $field) use ($draggedId, $targetId, $position): mixed {
            if (is_array($field) && is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->reorderFieldInSiblings($field['fields'], $draggedId, $targetId, $position);
            }

            return $field;
        }, $fields));
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, mixed>
     */
    protected function reorderFieldInTree(array $fields, string $draggedId, string $targetId, string $position): array
    {
        $dragged = $this->findField($fields, $draggedId);
        $target = $this->findField($fields, $targetId);

        if (! $dragged || ! $target || $draggedId === $targetId) {
            return $fields;
        }

        if (is_array($dragged['fields'] ?? null) && $this->isDescendantOf($dragged, $targetId)) {
            return $fields;
        }

        [$fieldsWithoutDragged, $removed] = $this->extractFieldFromTree($fields, $draggedId);

        if (! $removed) {
            return $fields;
        }

        if ($position === 'inside' && is_array($target['fields'] ?? null)) {
            return $this->appendFieldToTree($fieldsWithoutDragged, $targetId, $removed);
        }

        return $this->insertFieldAroundInTree($fieldsWithoutDragged, $targetId, $removed, $position === 'after' ? 'after' : 'before');
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array{0: array<int, mixed>, 1: array<string, mixed>|null}
     */
    protected function extractFieldFromTree(array $fields, string $id): array
    {
        $result = [];
        $removed = null;

        foreach ($fields as $field) {
            if (! is_array($field)) {
                $result[] = $field;

                continue;
            }

            if (($field['id'] ?? null) === $id) {
                $removed = $field;

                continue;
            }

            if (is_array($field['fields'] ?? null)) {
                [$field['fields'], $nestedRemoved] = $this->extractFieldFromTree($field['fields'], $id);
                $removed ??= $nestedRemoved;
            }

            $result[] = $field;
        }

        return [array_values($result), $removed];
    }

    /**
     * @param  array<int, mixed>  $fields
     * @param  array<string, mixed>  $fieldToInsert
     * @return array<int, mixed>
     */
    protected function insertFieldAroundInTree(array $fields, string $targetId, array $fieldToInsert, string $position): array
    {
        $result = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                $result[] = $field;

                continue;
            }

            if (($field['id'] ?? null) === $targetId) {
                if ($position !== 'after') {
                    $result[] = $fieldToInsert;
                }

                $result[] = $field;

                if ($position === 'after') {
                    $result[] = $fieldToInsert;
                }

                continue;
            }

            if (is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->insertFieldAroundInTree($field['fields'], $targetId, $fieldToInsert, $position);
            }

            $result[] = $field;
        }

        return array_values($result);
    }

    /**
     * @param  array<int, mixed>  $fields
     * @return array<int, mixed>
     */
    protected function mapFieldTree(array $fields, callable $callback): array
    {
        return array_map(function (mixed $field) use ($callback): mixed {
            if (! is_array($field)) {
                return $field;
            }

            $field = $callback($field);

            if (is_array($field['fields'] ?? null)) {
                $field['fields'] = $this->mapFieldTree($field['fields'], $callback);
            }

            return $field;
        }, $fields);
    }

    /**
     * @return array<int, string>
     */
    protected function splitCsv(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    protected function syncSchemaJson(?array $schema = null): void
    {
        $this->schemaJson = json_encode($schema ?? $this->canonicalSchema(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }
}

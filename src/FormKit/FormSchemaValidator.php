<?php

namespace Art35rennes\DaisyKit\FormKit;

/**
 * Structural and semantic validation for Daisy Form Kit JSON schemas on the PHP side.
 *
 * Mirrors constraints enforced by `resources/js/form-kit/schema.js` so persisted schemas stay portable across SSR and JS clients.
 */
class FormSchemaValidator
{
    /**
     * Supported serialized schema version string embedded under `version`.
     */
    public const Version = '1.0';

    /**
     * Layout modes supported by the v1 form viewer.
     *
     * @var array<int, string>
     */
    protected const LayoutTypes = ['one-page', 'multi-step', 'sections'];

    /**
     * Declarative simple validation tokens mirrored by {@see LaravelRuleMapper}.
     *
     * @var array<int, string>
     */
    protected const SimpleRules = [
        'required',
        'nullable',
        'email',
        'min',
        'max',
        'between',
        'length',
        'pattern',
        'in',
        'accepted',
        'same',
    ];

    /**
     * Validates top-level schema metadata, traverses fields, and verifies dependency graphs for cycles.
     *
     * @param  array<string, mixed>  $schema  Raw decoded schema array (typically from JSON).
     * @return array<int, array{path: string, code: string, message: string}>
     */
    public function validate(array $schema): array
    {
        $errors = [];

        if (($schema['version'] ?? null) !== self::Version) {
            $errors[] = $this->error('/version', 'unsupported_version', 'Only DaisyFormSchema version 1.0 is supported.');
        }

        if (! is_string($schema['id'] ?? null) || trim($schema['id']) === '') {
            $errors[] = $this->error('/id', 'invalid_id', 'The form schema needs a stable id.');
        }

        $this->validateLayout($schema['layout'] ?? null, $errors);

        if (! is_array($schema['fields'] ?? null)) {
            $errors[] = $this->error('/fields', 'missing_fields', 'The form schema needs a fields array.');

            return $errors;
        }

        $this->validateFields($schema['fields'], $errors);
        $this->validateDependencies($schema['fields'], $errors);

        return $errors;
    }

    /**
     * @param  array<int, array{path: string, code: string, message: string}>  $errors
     */
    protected function validateLayout(mixed $layout, array &$errors): void
    {
        if ($layout === null) {
            return;
        }

        if (! is_array($layout)) {
            $errors[] = $this->error('/layout', 'invalid_layout', 'The form layout must be an object.');

            return;
        }

        $type = $layout['type'] ?? 'one-page';

        if (! is_string($type) || ! in_array($type, self::LayoutTypes, true)) {
            $errors[] = $this->error('/layout/type', 'unknown_layout_type', "Layout type `{$type}` is not supported.");
        }
    }

    /**
     * Detects whether server-side JSONata evaluation is required for any field-level hook.
     *
     * @param  array<string, mixed>  $schema
     */
    public function hasJsonataExpressions(array $schema): bool
    {
        foreach ($this->flattenFields($schema['fields'] ?? []) as $field) {
            if (($field['visibleWhen']['type'] ?? null) === 'jsonata') {
                return true;
            }

            if (($field['computed']['type'] ?? null) === 'jsonata') {
                return true;
            }

            foreach (($field['rules'] ?? []) as $rule) {
                if (is_array($rule) && ($rule['type'] ?? null) === 'jsonata') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Depth-first traversal attaching `_parent` ids for contextual tooling parity with JS flatten helpers.
     *
     * @param  array<int, mixed>  $fields  Root `fields` array or nested container children.
     * @return array<int, array<string, mixed>>
     */
    public function flattenFields(array $fields, ?string $parent = null): array
    {
        $flat = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $field['_parent'] = $parent;
            $flat[] = $field;

            if (is_array($field['fields'] ?? null)) {
                array_push($flat, ...$this->flattenFields($field['fields'], $field['id'] ?? null));
            }
        }

        return $flat;
    }

    /**
     * @param  array<int, mixed>  $fields
     * @param  array<int, array{path: string, code: string, message: string}>  $errors
     */
    protected function validateFields(array $fields, array &$errors): void
    {
        $ids = [];
        $names = [];

        foreach ($this->flattenFields($fields) as $field) {
            $id = (string) ($field['id'] ?? '');
            $path = "/fields/{$id}";

            if (! preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $id)) {
                $errors[] = $this->error($path, 'invalid_id', 'Field ids must be stable identifiers.');
            }

            if (in_array($id, $ids, true)) {
                $errors[] = $this->error($path, 'duplicate_id', "Field id `{$id}` is duplicated.");
            }

            $ids[] = $id;

            if (! in_array($field['type'] ?? null, FormSchemaNormalizer::FieldTypes, true)) {
                $type = (string) ($field['type'] ?? '');
                $errors[] = $this->error($path, 'unknown_field_type', "Field type `{$type}` is not supported.");
            }

            $isContainer = in_array($field['type'] ?? null, FormSchemaNormalizer::ContainerTypes, true);
            $isNonSubmitting = in_array($field['type'] ?? null, FormSchemaNormalizer::NonSubmittingFieldTypes, true);

            if (! $isContainer && ! $isNonSubmitting) {
                $name = (string) ($field['name'] ?? '');

                if (! preg_match('/^[A-Za-z_][A-Za-z0-9_.\-[\]]*$/', $name)) {
                    $errors[] = $this->error($path, 'invalid_name', "Field `{$id}` needs a valid submit name.");
                }

                if (in_array($name, $names, true)) {
                    $errors[] = $this->error($path, 'duplicate_name', "Field name `{$name}` is duplicated.");
                }

                $names[] = $name;
            }

            $this->validateExpression($field['visibleWhen'] ?? null, "{$path}/visibleWhen", $errors);
            $this->validateExpression($field['computed'] ?? null, "{$path}/computed", $errors);
            $this->validateRules($field, $path, $errors);
        }
    }

    /**
     * @param  array<string, mixed>  $field
     * @param  array<int, array{path: string, code: string, message: string}>  $errors
     */
    protected function validateRules(array $field, string $path, array &$errors): void
    {
        foreach (($field['rules'] ?? []) as $index => $rule) {
            if (is_string($rule)) {
                $name = explode(':', $rule, 2)[0];

                if (! in_array($name, self::SimpleRules, true)) {
                    $errors[] = $this->error("{$path}/rules/{$index}", 'unknown_rule', "Rule `{$name}` is not supported.");
                }

                continue;
            }

            if (! is_array($rule) || ($rule['type'] ?? null) !== 'jsonata') {
                $errors[] = $this->error("{$path}/rules/{$index}", 'unknown_rule', 'Only simple rules and JSONata rules are supported.');

                continue;
            }

            $this->validateExpression($rule, "{$path}/rules/{$index}", $errors, true);
        }
    }

    /**
     * Ensures JSONata blocks declare expressions, structural dependsOn arrays, and validation messages when required.
     *
     * @param  array<int, array{path: string, code: string, message: string}>  $errors
     */
    protected function validateExpression(mixed $expression, string $path, array &$errors, bool $requiresMessage = false): void
    {
        if ($expression === null) {
            return;
        }

        if (! is_array($expression) || ($expression['type'] ?? null) !== 'jsonata') {
            $errors[] = $this->error($path, 'invalid_expression_type', 'Only JSONata expressions are supported.');

            return;
        }

        if (! is_string($expression['expression'] ?? null) || trim($expression['expression']) === '') {
            $errors[] = $this->error($path, 'missing_expression', 'JSONata expression is required.');
        }

        if (! is_array($expression['dependsOn'] ?? null)) {
            $errors[] = $this->error($path, 'missing_depends_on', 'JSONata expressions require dependsOn.');
        }

        if ($requiresMessage && (! is_string($expression['message'] ?? null) || trim($expression['message']) === '')) {
            $errors[] = $this->error($path, 'missing_message', 'JSONata validation rules require a message.');
        }
    }

    /**
     * Verifies declared dependency ids exist and that computed-field dependency chains contain no cycles.
     *
     * @param  array<int, mixed>  $fields
     * @param  array<int, array{path: string, code: string, message: string}>  $errors
     */
    protected function validateDependencies(array $fields, array &$errors): void
    {
        $flat = $this->flattenFields($fields);
        $ids = array_values(array_filter(array_map(fn (array $field) => $field['id'] ?? null, $flat)));
        $graph = array_fill_keys($ids, []);

        foreach ($flat as $field) {
            $fieldId = $field['id'] ?? null;

            if (! is_string($fieldId)) {
                continue;
            }

            foreach ($this->dependenciesFor($field) as $dependency) {
                if (! in_array($dependency, $ids, true)) {
                    $errors[] = $this->error("/fields/{$fieldId}", 'unknown_dependency', "Field `{$fieldId}` depends on unknown field `{$dependency}`.");

                    continue;
                }

                if (is_array($field['computed']['dependsOn'] ?? null) && in_array($dependency, $field['computed']['dependsOn'], true)) {
                    $graph[$fieldId][] = $dependency;
                }
            }
        }

        foreach ($this->findCycles($graph) as $cycle) {
            $errors[] = $this->error('/fields', 'dependency_cycle', 'Field dependency cycle detected: '.implode(' -> ', $cycle).'.');
        }
    }

    /**
     * Collects declared dependency identifiers across visibility, computed blocks, and JSONata validation rules.
     *
     * @param  array<string, mixed>  $field
     * @return array<int, string>
     */
    protected function dependenciesFor(array $field): array
    {
        $dependencies = [];

        foreach (['visibleWhen', 'computed'] as $key) {
            if (is_array($field[$key]['dependsOn'] ?? null)) {
                array_push($dependencies, ...$field[$key]['dependsOn']);
            }
        }

        foreach (($field['rules'] ?? []) as $rule) {
            if (is_array($rule) && is_array($rule['dependsOn'] ?? null)) {
                array_push($dependencies, ...$rule['dependsOn']);
            }
        }

        return array_values(array_unique(array_map('strval', $dependencies)));
    }

    /**
     * DFS-based cycle detection over computed dependency edges (`field -> prerequisite`).
     *
     * @param  array<string, array<int, string>>  $graph  Adjacency lists keyed by field id.
     * @return array<int, array<int, string>>
     */
    protected function findCycles(array $graph): array
    {
        $cycles = [];
        $visited = [];
        $visiting = [];
        $stack = [];

        $visit = function (string $node) use (&$visit, &$cycles, &$visited, &$visiting, &$stack, $graph): void {
            if (in_array($node, $visiting, true)) {
                $cycles[] = array_slice($stack, array_search($node, $stack, true));

                return;
            }

            if (in_array($node, $visited, true)) {
                return;
            }

            $visiting[] = $node;
            $stack[] = $node;

            foreach (($graph[$node] ?? []) as $dependency) {
                $visit($dependency);
            }

            array_pop($stack);
            $visiting = array_values(array_filter($visiting, fn (string $item) => $item !== $node));
            $visited[] = $node;
        };

        foreach (array_keys($graph) as $node) {
            $visit($node);
        }

        return $cycles;
    }

    /**
     * @return array{path: string, code: string, message: string}
     */
    protected function error(string $path, string $code, string $message): array
    {
        return compact('path', 'code', 'message');
    }
}

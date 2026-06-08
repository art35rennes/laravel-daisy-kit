<?php

namespace Art35rennes\DaisyKit\FormKit;

use Art35rennes\DaisyKit\FormKit\Contracts\JsonataEvaluator;
use Throwable;

/**
 * Executes JSONata-driven visibility, computed defaults, and JSONata validation rules against inbound values after structural schema validation succeeds.
 *
 * Intended parity with the browser runtime in `resources/js/form-kit/runtime.js`, scoped to server-side normalization.
 */
class FormSubmissionEvaluator
{
    protected FormSchemaValidator $schemaValidator;

    protected ?JsonataEvaluator $jsonataEvaluator;

    /**
     * @param  FormSchemaValidator|null  $schemaValidator  Defaults to a new instance when omitted (facades/tests).
     * @param  JsonataEvaluator|null  $jsonataEvaluator  Required whenever the schema contains JSONata workloads.
     */
    public function __construct(
        ?FormSchemaValidator $schemaValidator = null,
        ?JsonataEvaluator $jsonataEvaluator = null,
    ) {
        $this->schemaValidator = $schemaValidator ?? new FormSchemaValidator;
        $this->jsonataEvaluator = $jsonataEvaluator;
    }

    /**
     * Validates schema shape, optionally executes JSONata batches, then merges normalized values with field-level errors.
     *
     * Schema failures surface under `_schema`. Missing evaluator surfaces `_jsonata`. Engine failures attach messages under `_jsonata` or per-field keys depending on evaluation results.
     *
     * @param  array<string, mixed>  $schema  Daisy form schema payload (`fields`, `meta`, optional `jsonata` block).
     * @param  array<string, mixed>  $values  Incoming request values keyed primarily by submit `name`.
     * @return array{normalizedData: array<string, mixed>, errors: array<string, array<int, string>>}
     */
    public function evaluate(array $schema, array $values): array
    {
        $schemaErrors = $this->schemaValidator->validate($schema);

        if ($schemaErrors !== []) {
            return [
                'normalizedData' => $values,
                'errors' => [
                    '_schema' => array_map(fn (array $error) => $error['message'], $schemaErrors),
                ],
            ];
        }

        $normalizedValues = $this->canonicalizeSubmissionValues($schema, $values);
        $evaluations = $this->buildEvaluations($schema);

        if ($evaluations === []) {
            return [
                'normalizedData' => $normalizedValues,
                'errors' => [],
            ];
        }

        if (! $this->jsonataEvaluator) {
            return [
                'normalizedData' => $normalizedValues,
                'errors' => [
                    '_jsonata' => ['evaluator_missing'],
                ],
            ];
        }

        try {
            $results = $this->jsonataEvaluator->evaluateBatch($evaluations, [
                'values' => $normalizedValues,
                'visible' => [],
                'meta' => $schema['meta'] ?? [],
                'step' => null,
            ], [
                'schema' => $schema['id'] ?? null,
                'engine' => $schema['jsonata']['engine'] ?? 'jsonata',
                'minVersion' => $schema['jsonata']['minVersion'] ?? null,
                'functions' => $schema['jsonata']['functions'] ?? [],
            ]);
        } catch (Throwable $exception) {
            return [
                'normalizedData' => $normalizedValues,
                'errors' => [
                    '_jsonata' => [$exception->getMessage()],
                ],
            ];
        }

        return $this->applyResults($schema, $normalizedValues, $evaluations, $results);
    }

    /**
     * Canonicalizes inbound values to submit `name` keys while accepting legacy field-id aliases.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    protected function canonicalizeSubmissionValues(array $schema, array $values): array
    {
        $normalized = $values;

        foreach ($this->schemaValidator->flattenFields($schema['fields'] ?? []) as $field) {
            if (in_array($field['type'] ?? null, FormSchemaNormalizer::ContainerTypes, true)
                || in_array($field['type'] ?? null, FormSchemaNormalizer::NonSubmittingFieldTypes, true)) {
                continue;
            }

            $id = (string) ($field['id'] ?? '');
            $name = (string) ($field['name'] ?? $id);

            if ($id === '' || $name === '' || $id === $name) {
                continue;
            }

            if (array_key_exists($name, $normalized)) {
                unset($normalized[$id]);

                continue;
            }

            if (array_key_exists($id, $normalized)) {
                $normalized[$name] = $normalized[$id];
                unset($normalized[$id]);
            }
        }

        return $normalized;
    }

    /**
     * Collects ordered evaluation units so batch engines can optimize execution while preserving deterministic merging.
     *
     * @param  array<string, mixed>  $schema
     * @return array<int, array<string, mixed>>
     */
    protected function buildEvaluations(array $schema): array
    {
        $evaluations = [];

        foreach ($this->schemaValidator->flattenFields($schema['fields'] ?? []) as $field) {
            $fieldId = (string) ($field['id'] ?? '');

            if (($field['visibleWhen']['type'] ?? null) === 'jsonata') {
                $evaluations[] = [
                    'id' => "visible:{$fieldId}",
                    'kind' => 'visible',
                    'field' => $field,
                    'expression' => $field['visibleWhen']['expression'],
                ];
            }

            if (($field['computed']['type'] ?? null) === 'jsonata') {
                $evaluations[] = [
                    'id' => "computed:{$fieldId}",
                    'kind' => 'computed',
                    'field' => $field,
                    'expression' => $field['computed']['expression'],
                ];
            }

            foreach (($field['rules'] ?? []) as $index => $rule) {
                if (! is_array($rule) || ($rule['type'] ?? null) !== 'jsonata') {
                    continue;
                }

                $evaluations[] = [
                    'id' => "rule:{$fieldId}:{$index}",
                    'kind' => 'rule',
                    'field' => $field,
                    'message' => $rule['message'] ?? 'The field value is invalid.',
                    'expression' => $rule['expression'],
                ];
            }
        }

        return $evaluations;
    }

    /**
     * Applies evaluation outcomes: hides invisible submit keys, assigns computed replacements, records validation failures.
     *
     * Note: Unlike the browser runtime, server-side visibility currently removes hidden keys from `normalizedData` only after expressions succeed.
     *
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $values
     * @param  array<int, array<string, mixed>>  $evaluations
     * @param  array<string, mixed>  $results  Map keyed by evaluation id from {@see JsonataEvaluator::evaluateBatch()}.
     * @return array{normalizedData: array<string, mixed>, errors: array<string, array<int, string>>}
     */
    protected function applyResults(array $schema, array $values, array $evaluations, array $results): array
    {
        $normalizedData = $values;
        $errors = [];
        $visible = [];

        foreach ($evaluations as $evaluation) {
            $id = $evaluation['id'];
            $field = $evaluation['field'];
            $name = $field['name'] ?? $field['id'];
            $result = $this->normalizeResult($results[$id] ?? null);

            if ($result['error']) {
                $errors[$name][] = $result['error'];

                continue;
            }

            if ($evaluation['kind'] === 'visible') {
                $visible[$field['id']] = (bool) $result['value'];

                if (! $visible[$field['id']]) {
                    unset($normalizedData[$name]);
                }

                continue;
            }

            if ($evaluation['kind'] === 'computed') {
                $normalizedData[$name] = $result['value'];

                continue;
            }

            if ($evaluation['kind'] === 'rule' && $result['value'] !== true) {
                $errors[$name][] = (string) ($evaluation['message'] ?? 'The field value is invalid.');
            }
        }

        return [
            'normalizedData' => $normalizedData,
            'errors' => $errors,
        ];
    }

    /**
     * Normalizes heterogeneous evaluator return payloads into a uniform `{value, error}` tuple.
     *
     * @return array{value: mixed, error: ?string}
     */
    protected function normalizeResult(mixed $result): array
    {
        if (is_array($result) && array_key_exists('error', $result) && $result['error']) {
            $error = is_array($result['error'])
                ? (string) ($result['error']['code'] ?? $result['error']['message'] ?? 'evaluation_error')
                : (string) $result['error'];

            return [
                'value' => null,
                'error' => $error,
            ];
        }

        if (is_array($result) && array_key_exists('value', $result)) {
            return [
                'value' => $result['value'],
                'error' => null,
            ];
        }

        return [
            'value' => $result,
            'error' => null,
        ];
    }
}

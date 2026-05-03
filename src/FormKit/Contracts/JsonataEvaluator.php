<?php

namespace Art35rennes\DaisyKit\FormKit\Contracts;

/**
 * Abstraction over a JSONata runtime used to evaluate visibility, computed fields, and JSONata validation rules server-side.
 */
abstract class JsonataEvaluator
{
    /**
     * Runs the given evaluations and returns a map keyed by evaluation `id`.
     *
     * Implementations should return per-id shapes understood by {@see FormSubmissionEvaluator::normalizeResult()}
     * (for example `['value' => mixed, 'error' => null|array|string]`).
     *
     * @param  array<int, array{id: string, expression: string, field?: array<string, mixed>, kind?: string, message?: string}>  $evaluations
     * @param  array<string, mixed>  $context  Typical keys: `values`, `visible`, `meta`, `step`.
     * @param  array<string, mixed>  $options  Schema hints such as `engine`, `minVersion`, `functions`.
     * @return array<string, mixed>
     */
    abstract public function evaluateBatch(array $evaluations, array $context, array $options = []): array;
}

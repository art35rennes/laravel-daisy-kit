<?php

namespace Art35rennes\DaisyKit\FormKit;

use Illuminate\Support\Facades\Validator;

/**
 * Translates Daisy Form Kit declarative simple rules into Laravel validation rule strings for {@see Validator}.
 *
 * JSONata-backed rules are intentionally omitted because Laravel validators cannot execute them natively.
 */
class LaravelRuleMapper
{
    /**
     * Daisy rule tokens mirrored by {@see FormSchemaValidator} on the PHP side.
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
     * @param  array<int, mixed>  $rules  Mixed entries from a field definition `rules` array.
     * @return array<int, string>
     */
    public function mapRules(array $rules): array
    {
        return array_values(array_filter(array_map(
            fn (mixed $rule) => $this->mapRule($rule),
            $rules,
        )));
    }

    /**
     * Maps one Daisy simple rule string to Laravel syntax, or returns null when the entry is unsupported or not a string.
     *
     * Notable mappings: `length` becomes Laravel `size`, `pattern` becomes `regex`.
     */
    public function mapRule(mixed $rule): ?string
    {
        if (! is_string($rule)) {
            return null;
        }

        [$name, $parameters] = array_pad(explode(':', $rule, 2), 2, null);

        if (! in_array($name, self::SimpleRules, true)) {
            return null;
        }

        if ($name === 'length') {
            return "size:{$parameters}";
        }

        if ($name === 'pattern') {
            return "regex:{$parameters}";
        }

        return $parameters === null ? $name : "{$name}:{$parameters}";
    }

    /**
     * Builds an attribute rule map keyed by submit field name for use with `Validator::make($data, $rules)`.
     *
     * @param  array<string, mixed>  $schema  Canonical Daisy form schema containing `fields`.
     * @return array<string, array<int, string>>
     */
    public function mapSchema(array $schema): array
    {
        $validator = new FormSchemaValidator;
        $rules = [];

        foreach ($validator->flattenFields($schema['fields'] ?? []) as $field) {
            if (! is_string($field['name'] ?? null)) {
                continue;
            }

            $mappedRules = $this->mapRules($field['rules'] ?? []);

            if ($mappedRules === []) {
                continue;
            }

            $rules[$field['name']] = $mappedRules;
        }

        return $rules;
    }
}

<?php

namespace Art35rennes\DaisyKit\Support;

use InvalidArgumentException;

class DaisyTableActions
{
    public const Variants = [
        'neutral' => 'btn-neutral',
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'accent' => 'btn-accent',
        'info' => 'btn-info',
        'success' => 'btn-success',
        'warning' => 'btn-warning',
        'error' => 'btn-error',
        'ghost' => 'btn-ghost',
    ];

    public static function normalize(mixed $value): array
    {
        if ($value === null || $value === []) {
            return [];
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException('Daisy table actions must use a structured descriptor or a list of descriptors.');
        }

        $values = array_is_list($value) ? $value : [$value];

        return collect($values)
            ->map(function (mixed $action): array {
                if (! is_array($action)) {
                    throw new InvalidArgumentException('Each Daisy table action must be a structured descriptor.');
                }

                $name = is_string($action['action'] ?? null) ? trim($action['action']) : '';

                if ($name === '') {
                    throw new InvalidArgumentException('Each Daisy table action requires a non-empty action.');
                }

                return [
                    'action' => $name,
                    'label' => is_string($action['label'] ?? null) ? $action['label'] : $name,
                    'variant' => is_string($action['variant'] ?? null) && array_key_exists($action['variant'], self::Variants)
                        ? $action['variant']
                        : 'ghost',
                    'disabled' => ($action['disabled'] ?? false) === true,
                    'ariaLabel' => is_string($action['ariaLabel'] ?? null) ? $action['ariaLabel'] : '',
                ];
            })
            ->values()
            ->all();
    }
}

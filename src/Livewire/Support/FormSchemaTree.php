<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Livewire\Support;

final class FormSchemaTree
{
    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return list<array<string, mixed>>
     */
    public static function flatten(array $fields, FormSchemaContract $contract): array
    {
        $flat = [];

        foreach ($fields as $field) {
            $flat[] = $field;
            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                array_push($flat, ...self::flatten($children, $contract));
            }
        }

        return $flat;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return list<array{field: array<string, mixed>, depth: int, identity: string}>
     */
    public static function outline(array $fields, FormSchemaContract $contract, int $depth = 1): array
    {
        $outline = [];

        foreach ($fields as $field) {
            $outline[] = [
                'field' => $field,
                'depth' => $depth,
                'identity' => $contract->identity($field),
            ];
            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                array_push($outline, ...self::outline($children, $contract, $depth + 1));
            }
        }

        return $outline;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<string, mixed>|null
     */
    public static function find(array $fields, string $id, FormSchemaContract $contract): ?array
    {
        foreach ($fields as $field) {
            if ($contract->identity($field) === $id) {
                return $field;
            }

            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $found = self::find($children, $id, $contract);

                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $child
     * @return array<int, array<string, mixed>>
     */
    public static function appendChild(array $fields, string $parentId, array $child, FormSchemaContract $contract): array
    {
        return self::map($fields, function (array $field) use ($parentId, $child, $contract): array {
            if ($contract->identity($field) === $parentId) {
                $field['fields'] = [...$contract->fieldList($field['fields'] ?? null), $child];
            }

            return $field;
        }, $contract);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $child
     * @return array<int, array<string, mixed>>
     */
    public static function insertAfter(array $fields, string $targetId, array $child, FormSchemaContract $contract): array
    {
        $result = [];

        foreach ($fields as $field) {
            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $field['fields'] = self::insertAfter($children, $targetId, $child, $contract);
            }

            $result[] = $field;

            if ($contract->identity($field) === $targetId) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, array<string, mixed>>
     */
    public static function remove(array $fields, string $targetId, FormSchemaContract $contract): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($contract->identity($field) === $targetId) {
                continue;
            }

            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $field['fields'] = self::remove($children, $targetId, $contract);
            }

            $result[] = $field;
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, array<string, mixed>>
     */
    public static function move(array $fields, string $targetId, int $direction, bool &$moved, FormSchemaContract $contract): array
    {
        foreach ($fields as $index => $field) {
            if ($contract->identity($field) === $targetId) {
                $target = $index + $direction;

                if (isset($fields[$target])) {
                    [$fields[$index], $fields[$target]] = [$fields[$target], $fields[$index]];
                    $moved = true;
                }

                return $fields;
            }

            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $fields[$index]['fields'] = self::move($children, $targetId, $direction, $moved, $contract);

                if ($moved) {
                    return $fields;
                }
            }
        }

        return $fields;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  callable(array<string, mixed>): array<string, mixed>  $callback
     * @return array<int, array<string, mixed>>
     */
    public static function map(array $fields, callable $callback, FormSchemaContract $contract): array
    {
        foreach ($fields as $index => $field) {
            $field = $callback($field);
            $children = $contract->fieldList($field['fields'] ?? null);

            if ($children !== []) {
                $field['fields'] = self::map($children, $callback, $contract);
            }

            $fields[$index] = $field;
        }

        return $fields;
    }
}

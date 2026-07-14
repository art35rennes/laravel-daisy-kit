<?php

namespace Art35rennes\DaisyKit\Support;

use Illuminate\Support\HtmlString;

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

    public static function render(mixed $value, mixed $rowId, string $columnId): HtmlString
    {
        $actions = self::normalize($value);
        $buttons = array_map(static function (array $action) use ($rowId, $columnId): string {
            $variant = self::Variants[$action['variant']] ?? self::Variants['ghost'];
            $disabled = $action['disabled'] ? ' disabled aria-disabled="true"' : '';
            $ariaLabel = $action['ariaLabel'] !== '' ? ' aria-label="'.e($action['ariaLabel']).'"' : '';

            return '<button type="button" class="btn btn-xs '.$variant.'" data-table-row-action="'.e($action['action']).'" data-table-row-id="'.e((string) $rowId).'" data-table-column-id="'.e($columnId).'"'.$ariaLabel.$disabled.'>'.e($action['label']).'</button>';
        }, $actions);

        return new HtmlString(implode('', $buttons));
    }

    public static function normalize(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $values = array_is_list($value) ? $value : [$value];

        return collect($values)
            ->filter(fn ($action) => is_array($action) && is_string($action['action'] ?? null) && filled($action['action']))
            ->map(fn (array $action) => [
                'action' => trim($action['action']),
                'label' => is_string($action['label'] ?? null) ? $action['label'] : trim($action['action']),
                'variant' => is_string($action['variant'] ?? null) && array_key_exists($action['variant'], self::Variants)
                    ? $action['variant']
                    : 'ghost',
                'disabled' => ($action['disabled'] ?? false) === true,
                'ariaLabel' => is_string($action['ariaLabel'] ?? null) ? $action['ariaLabel'] : '',
            ])
            ->values()
            ->all();
    }
}

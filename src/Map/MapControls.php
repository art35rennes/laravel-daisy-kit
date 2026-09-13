<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Map;

use InvalidArgumentException;

final readonly class MapControls
{
    /**
     * @param  list<MapControl>  $items
     */
    private function __construct(
        public array $items,
        public string $position,
    ) {}

    /** @param array<int, mixed> $items */
    public static function make(array $items, string $position = 'topright'): self
    {
        if (! in_array($position, ['topleft', 'topright', 'bottomleft', 'bottomright'], true)) {
            throw new InvalidArgumentException('Map control position is invalid.');
        }

        $items = self::controlItems($items);

        $identifiers = [];
        $nodeCount = 0;

        self::validateItems($items, $identifiers, $nodeCount);

        if ($nodeCount > 100) {
            throw new InvalidArgumentException('A map control tree cannot contain more than 100 nodes.');
        }

        return new self($items, $position);
    }

    /** @return array{enabled: true, position: string, items: list<array<string, mixed>>} */
    public function toArray(): array
    {
        return [
            'enabled' => true,
            'position' => $this->position,
            'items' => array_map(fn (MapControl $item): array => $item->toArray(), $this->items),
        ];
    }

    /**
     * @param  list<MapControl>  $items
     * @param  array<string, true>  $identifiers
     */
    private static function validateItems(array $items, array &$identifiers, int &$nodeCount, int $menuDepth = 0): void
    {
        foreach ($items as $item) {
            $nodeCount++;

            if (isset($identifiers[$item->id])) {
                throw new InvalidArgumentException("Every map control requires a unique id; [{$item->id}] is duplicated.");
            }

            $identifiers[$item->id] = true;
            $nextMenuDepth = $menuDepth + ($item->type === 'menu' ? 1 : 0);

            if ($nextMenuDepth > 3) {
                throw new InvalidArgumentException('Map control menus support at most three levels.');
            }

            self::validateItems($item->items, $identifiers, $nodeCount, $nextMenuDepth);
        }
    }

    /**
     * @param  array<int, mixed>  $items
     * @return list<MapControl>
     */
    private static function controlItems(array $items): array
    {
        if (! array_is_list($items)) {
            throw new InvalidArgumentException('Map controls must be a list of MapControl instances.');
        }

        $controls = [];

        foreach ($items as $item) {
            if (! $item instanceof MapControl) {
                throw new InvalidArgumentException('Map controls must be a list of MapControl instances.');
            }

            $controls[] = $item;
        }

        return $controls;
    }
}

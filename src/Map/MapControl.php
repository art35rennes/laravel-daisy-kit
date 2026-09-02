<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Map;

use InvalidArgumentException;

final readonly class MapControl
{
    /**
     * @param  list<MapControl>  $items
     */
    private function __construct(
        public string $id,
        public string $type,
        public ?string $label = null,
        public ?string $action = null,
        public ?string $customId = null,
        public ?string $slot = null,
        public ?string $icon = null,
        public bool $enabled = true,
        public bool $visible = true,
        public array $items = [],
    ) {}

    /** @param array<int, mixed> $items */
    public static function menu(
        string $id,
        string $label,
        array $items,
        bool $enabled = true,
        bool $visible = true,
        ?string $icon = null,
    ): self {
        $items = self::controlItems($id, $label, $items);

        return new self($id, 'menu', $label, icon: $icon, enabled: $enabled, visible: $visible, items: $items);
    }

    /** @param array<int, mixed> $items */
    public static function group(
        string $id,
        string $label,
        array $items,
        bool $enabled = true,
        bool $visible = true,
    ): self {
        $items = self::controlItems($id, $label, $items);

        return new self($id, 'group', $label, enabled: $enabled, visible: $visible, items: $items);
    }

    public static function slot(string $name, bool $enabled = true, bool $visible = true): self
    {
        self::validateIdentifier($name, 'slot');

        return new self("slot:{$name}", 'slot', slot: $name, enabled: $enabled, visible: $visible);
    }

    public static function basemaps(bool $enabled = true, bool $visible = true): self
    {
        return self::standard('basemaps', 'collection', enabled: $enabled, visible: $visible);
    }

    public static function businessLayers(bool $enabled = true, bool $visible = true): self
    {
        return self::standard('businessLayers', 'collection', enabled: $enabled, visible: $visible);
    }

    public static function drawingLayers(bool $enabled = true, bool $visible = true): self
    {
        return self::standard('drawingLayers', 'collection', enabled: $enabled, visible: $visible);
    }

    public static function objectTypeSelector(bool $enabled = true, bool $visible = true): self
    {
        return self::standard('objectTypeSelector', 'selector', enabled: $enabled, visible: $visible);
    }

    public static function drawLayerSelector(bool $enabled = true, bool $visible = true): self
    {
        return self::standard('drawLayerSelector', 'selector', enabled: $enabled, visible: $visible);
    }

    public static function drawPoint(bool $enabled = true, bool $visible = true): self
    {
        return self::action('drawPoint', enabled: $enabled, visible: $visible);
    }

    public static function drawLine(bool $enabled = true, bool $visible = true): self
    {
        return self::action('drawLine', enabled: $enabled, visible: $visible);
    }

    public static function drawPolygon(bool $enabled = true, bool $visible = true): self
    {
        return self::action('drawPolygon', enabled: $enabled, visible: $visible);
    }

    public static function drawRectangle(bool $enabled = true, bool $visible = true): self
    {
        return self::action('drawRectangle', enabled: $enabled, visible: $visible);
    }

    public static function edit(bool $enabled = true, bool $visible = true): self
    {
        return self::action('edit', enabled: $enabled, visible: $visible);
    }

    public static function select(bool $enabled = true, bool $visible = true): self
    {
        return self::action('select', enabled: $enabled, visible: $visible);
    }

    public static function selectFeature(bool $enabled = true, bool $visible = true): self
    {
        return self::action('selectFeature', enabled: $enabled, visible: $visible);
    }

    public static function selectByArea(bool $enabled = true, bool $visible = true): self
    {
        return self::action('selectByArea', enabled: $enabled, visible: $visible);
    }

    public static function deleteSelected(bool $enabled = true, bool $visible = true): self
    {
        return self::action('deleteSelected', enabled: $enabled, visible: $visible);
    }

    public static function clearSelection(bool $enabled = true, bool $visible = true): self
    {
        return self::action('clearSelection', enabled: $enabled, visible: $visible);
    }

    public static function undo(bool $enabled = true, bool $visible = true): self
    {
        return self::action('undo', enabled: $enabled, visible: $visible);
    }

    public static function redo(bool $enabled = true, bool $visible = true): self
    {
        return self::action('redo', enabled: $enabled, visible: $visible);
    }

    public static function export(bool $enabled = true, bool $visible = true): self
    {
        return self::action('export', enabled: $enabled, visible: $visible);
    }

    public static function fitBounds(bool $enabled = true, bool $visible = true): self
    {
        return self::action('fitBounds', enabled: $enabled, visible: $visible);
    }

    public static function geolocate(bool $enabled = true, bool $visible = true): self
    {
        return self::action('geolocate', enabled: $enabled, visible: $visible);
    }

    public static function fullscreen(bool $enabled = true, bool $visible = true): self
    {
        return self::action('fullscreen', enabled: $enabled, visible: $visible);
    }

    public static function customAction(
        string $id,
        string $label,
        ?string $icon = null,
        bool $enabled = true,
        bool $visible = true,
    ): self {
        self::validateIdentifier($id, 'custom action');

        if (trim($label) === '') {
            throw new InvalidArgumentException('A map custom action requires a non-empty label.');
        }

        return new self("custom:{$id}", 'action', $label, 'custom', $id, icon: $icon, enabled: $enabled, visible: $visible);
    }

    /**
     * @return array{
     *     id: string,
     *     type: string,
     *     label: ?string,
     *     action: ?string,
     *     customId: ?string,
     *     slot: ?string,
     *     icon: ?string,
     *     enabled: bool,
     *     visible: bool,
     *     items: list<array<string, mixed>>
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
            'action' => $this->action,
            'customId' => $this->customId,
            'slot' => $this->slot,
            'icon' => $this->icon,
            'enabled' => $this->enabled,
            'visible' => $this->visible,
            'items' => array_map(fn (self $item): array => $item->toArray(), $this->items),
        ];
    }

    private static function action(string $action, bool $enabled, bool $visible): self
    {
        return new self($action, 'action', action: $action, enabled: $enabled, visible: $visible);
    }

    private static function standard(string $id, string $type, bool $enabled, bool $visible): self
    {
        return new self($id, $type, action: $id, enabled: $enabled, visible: $visible);
    }

    /**
     * @param  array<int, mixed>  $items
     * @return list<MapControl>
     */
    private static function controlItems(string $id, string $label, array $items): array
    {
        self::validateIdentifier($id, 'container');

        if (trim($label) === '') {
            throw new InvalidArgumentException('A map control container requires a non-empty label.');
        }

        if ($items === [] || ! array_is_list($items)) {
            throw new InvalidArgumentException('A map control container requires at least one item.');
        }

        foreach ($items as $item) {
            if (! $item instanceof self) {
                throw new InvalidArgumentException('Map control items must be MapControl instances.');
            }
        }

        return $items;
    }

    private static function validateIdentifier(string $id, string $context): void
    {
        if (preg_match('/^[A-Za-z][A-Za-z0-9._:-]*$/', $id) !== 1) {
            throw new InvalidArgumentException("Map {$context} id [{$id}] is invalid.");
        }
    }
}

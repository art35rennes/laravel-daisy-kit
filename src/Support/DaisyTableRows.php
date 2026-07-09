<?php

namespace Art35rennes\DaisyKit\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;

class DaisyTableRows
{
    protected mixed $table = null;

    protected $mapper = null;

    protected ?string $rowDetailView = null;

    public function __construct(
        protected iterable $items,
        protected array $columns,
    ) {}

    public static function for(iterable $items, array $columns): self
    {
        return new self($items, $columns);
    }

    public function map(callable $mapper): self
    {
        $this->mapper = $mapper;

        return $this;
    }

    public function table(mixed $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function rowDetailView(?string $view): self
    {
        $this->rowDetailView = filled($view) ? $view : null;

        return $this;
    }

    public function renderCells(): array
    {
        return Collection::make($this->items)
            ->map(fn ($item) => $this->renderItem($item))
            ->values()
            ->all();
    }

    protected function renderItem(mixed $item): array
    {
        $row = $this->mapper
            ? ($this->mapper)($item)
            : $this->normalizeRow($item);

        $row = $this->normalizeRow($row);

        foreach ($this->columns as $column) {
            $cell = $this->normalizeCell($column);

            if (($cell['renderer'] ?? null) !== 'blade') {
                continue;
            }

            $view = $cell['view'] ?? null;

            if (! is_string($view) || $view === '' || ! View::exists($view)) {
                throw new InvalidArgumentException("Daisy table cell view [{$view}] does not exist.");
            }

            $key = (string) ($column['key'] ?? '');
            $value = data_get($row, $key);
            $row[$key] = trim(View::make($view, [
                'item' => $item,
                'row' => $row,
                'value' => $value,
                'column' => $column,
                'table' => $this->table,
            ])->render());
        }

        if ($this->rowDetailView !== null) {
            if (! View::exists($this->rowDetailView)) {
                throw new InvalidArgumentException("Daisy table row detail view [{$this->rowDetailView}] does not exist.");
            }

            $row['_detailHtml'] = trim(View::make($this->rowDetailView, [
                'item' => $item,
                'row' => $row,
                'table' => $this->table,
            ])->render());
        }

        return $row;
    }

    protected function normalizeRow(mixed $row): array
    {
        if ($row instanceof Collection) {
            return $row->all();
        }

        if (is_array($row)) {
            return $row;
        }

        if (is_object($row) && method_exists($row, 'toArray')) {
            return $row->toArray();
        }

        return (array) $row;
    }

    protected function normalizeCell(array $column): array
    {
        return DaisyTableColumns::normalizeCell($column);
    }
}

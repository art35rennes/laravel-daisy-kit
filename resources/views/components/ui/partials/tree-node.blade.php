@php
    $id = is_array($node) && array_key_exists('id', $node) ? (string) $node['id'] : '';
    $label = is_array($node) ? (string) ($node['label'] ?? $id) : '';
    $children = is_array($node) && is_array($node['children'] ?? null) ? $node['children'] : [];
    $isLazy = is_array($node) && ($node['lazy'] ?? false) === true;
    $hasChildren = $isLazy || count($children) > 0;
    $expanded = $hasChildren && ! $isLazy && (bool) ($node['expanded'] ?? false);
    $nodeDisabled = (bool) ($disabledParent ?? false) || (bool) ($node['disabled'] ?? false);
    $selectedSet = array_fill_keys($selectedValues, true);

    $leafIds = function (array $items) use (&$leafIds): array {
        $ids = [];

        foreach ($items as $item) {
            if (! is_array($item) || ! array_key_exists('id', $item)) {
                continue;
            }

            $itemChildren = is_array($item['children'] ?? null) ? $item['children'] : [];

            if (($item['lazy'] ?? false) === true || count($itemChildren) === 0) {
                $ids[] = (string) $item['id'];
            } else {
                $ids = [...$ids, ...$leafIds($itemChildren)];
            }
        }

        return $ids;
    };

    $descendantLeafIds = $hasChildren && ! $isLazy ? $leafIds($children) : [];
    $isDirectlySelected = isset($selectedSet[$id]);
    $selectedLeafCount = count(array_filter($descendantLeafIds, fn ($leafId) => isset($selectedSet[$leafId])));
    $isSelected = $selection === 'single'
        ? $isDirectlySelected
        : ($isDirectlySelected || ($hasChildren && ! $isLazy && count($descendantLeafIds) > 0 && $selectedLeafCount === count($descendantLeafIds)));
    $isMixed = $selection === 'multiple'
        && ! $isSelected
        && ! $isLazy
        && count($descendantLeafIds) > 0
        && $selectedLeafCount > 0
        && $selectedLeafCount < count($descendantLeafIds);
    $indentLevel = min(64, max(0, $level - 1));
    $domId = $treeId.'-item-'.substr(hash('sha256', $id.'-'.$level), 0, 16);
    $state = $isMixed ? 'mixed' : ($isSelected ? 'true' : 'false');
@endphp

<li id="{{ $domId }}" role="treeitem" aria-level="{{ $level }}" @if($hasChildren) aria-expanded="{{ $expanded ? 'true' : 'false' }}" @endif @if($selection === 'multiple') aria-checked="{{ $state }}" @else aria-selected="{{ $isSelected ? 'true' : 'false' }}" @endif data-id="{{ $id }}" data-level="{{ $level }}" @if($isLazy) data-lazy="1" @endif @if($nodeDisabled) aria-disabled="true" @endif tabindex="-1" class="outline-none focus-visible:ring-2 focus-visible:ring-primary">
    <div class="flex items-center gap-2 rounded px-2 py-1 hover:bg-base-200 daisy-tree-indent-{{ $indentLevel }}" data-node-header="1">
        @if($hasChildren)
            <button type="button" class="btn btn-ghost btn-xs btn-square shrink-0" aria-label="{{ $expanded ? __('daisy::components.tree-view-collapse', ['label' => $label]) : __('daisy::components.tree-view-expand', ['label' => $label]) }}" data-tree-toggle="1" tabindex="-1">
                <x-bi-chevron-right class="size-4 {{ $expanded ? 'hidden' : '' }}" data-tree-collapsed-icon="1" />
                <x-bi-chevron-down class="size-4 {{ $expanded ? '' : 'hidden' }}" data-tree-expanded-icon="1" />
            </button>
        @else
            <span class="inline-block w-6 shrink-0" aria-hidden="true"></span>
        @endif

        @if($selection === 'single')
            <x-daisy::ui.inputs.radio :name="$name" :value="$id" :checked="$isSelected" :disabled="$nodeDisabled" :size="$controlSize" class="shrink-0" tabindex="-1" data-tree-control="1" />
        @else
            <x-daisy::ui.inputs.checkbox :name="!$hasChildren && $name ? $name.'[]' : null" :value="$id" :checked="$isSelected" :indeterminate="$isMixed" :disabled="$nodeDisabled" :bind-old="false" :size="$controlSize" class="shrink-0" tabindex="-1" data-tree-control="1" />
        @endif

        <span class="min-w-0 flex-1 select-none break-words {{ $nodeDisabled ? 'opacity-50' : 'cursor-default' }}" data-tree-label="1">{{ $label }}</span>
    </div>

    @if($hasChildren)
        <ul role="group" class="ml-4 border-l pl-2 {{ $expanded ? '' : 'hidden' }}" data-tree-group="1">
            @if($isLazy)
                <li role="presentation" class="hidden px-2 py-1 text-sm opacity-60" data-tree-lazy-placeholder="1"></li>
            @else
                @foreach($children as $child)
                    @include('daisy::components.ui.partials.tree-node', [
                        'node' => $child,
                        'level' => $level + 1,
                        'treeId' => $treeId,
                        'selection' => $selection,
                        'valueMode' => $valueMode,
                        'selectedValues' => $selectedValues,
                        'name' => $name,
                        'controlSize' => $controlSize,
                        'disabledParent' => $nodeDisabled,
                    ])
                @endforeach
            @endif
        </ul>
    @endif
</li>

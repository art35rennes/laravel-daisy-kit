@php
    $id = is_array($node) ? ($node[$itemKey] ?? null) : null;
    $label = is_array($node) ? ($node[$labelKey] ?? '') : '';
    $children = is_array($node) ? ($node[$childrenKey] ?? []) : [];
    $isLazy = is_array($node) ? (bool)($node[$lazyKey] ?? false) : false;
    $hasChildren = $isLazy || (is_array($children) && count($children) > 0);
    $expanded = (bool)($node['expanded'] ?? false);
    $selected = (bool)($node['selected'] ?? false);
    $nodeDisabled = (bool)($node['disabled'] ?? false) || (bool)($disabledParent ?? false);
    $isMulti = $selection === 'multiple';
    $liId = $treeId.'-item-'.($id ?? uniqid());
    $levelPad = max(0, ($level - 1)) * 16; // indentation visuelle
@endphp

<li id="{{ $liId }}"
    role="treeitem"
    aria-level="{{ $level }}"
    aria-expanded="{{ $hasChildren ? ($expanded ? 'true' : 'false') : 'false' }}"
    aria-selected="{{ $selected ? 'true' : 'false' }}"
    data-id="{{ $id }}"
    class="outline-none">

    <div class="flex items-center gap-2 px-2 py-1 rounded hover:bg-base-200 focus:bg-base-200"
         style="padding-left: {{ $levelPad }}px"
         data-node-header="1">
        @if($hasChildren)
            <button type="button"
                    class="btn btn-ghost btn-xs btn-square"
                    aria-label="Toggle"
                    data-toggle="1"
                    tabindex="-1">
                <span data-icon-collapsed class="@if($expanded) hidden @endif">
                    <x-heroicon-o-chevron-right class="size-4" />
                </span>
                <span data-icon-expanded class="@if(!$expanded) hidden @endif">
                    <x-heroicon-o-chevron-down class="size-4" />
                </span>
            </button>
        @else
            <span class="inline-block w-6"></span>
        @endif

        @if($selection === 'single')
            <x-daisy::ui.radio name="{{ $name }}" :checked="$selected" :disabled="$nodeDisabled" :size="$controlSize" class="shrink-0" tabindex="-1" />
        @elseif($selection === 'multiple')
            <x-daisy::ui.checkbox :checked="$selected" :disabled="$nodeDisabled" :size="$controlSize" class="shrink-0" tabindex="-1" />
        @endif

        <span class="flex-1 cursor-default select-none @if($nodeDisabled) opacity-50 @endif" data-label="1">{{ $label }}</span>
    </div>

    @if($hasChildren)
        <ul role="group" class="pl-2 ml-4 border-l border-base-300 @if(!$expanded) hidden @endif" data-children="1">
            @if(!$isLazy)
                @foreach($children as $child)
                    @include('daisy::components.ui.partials.tree-node', [
                        'node' => $child,
                        'level' => $level + 1,
                        'treeId' => $treeId,
                        'selection' => $selection,
                        'name' => $name,
                        'itemKey' => $itemKey,
                        'labelKey' => $labelKey,
                        'childrenKey' => $childrenKey,
                        'lazyKey' => $lazyKey,
                        'controlSize' => $controlSize,
                        'disabledParent' => $nodeDisabled,
                    ])
                @endforeach
            @else
                <li class="px-2 py-1 text-sm opacity-60" data-lazy-placeholder="1">Loading…</li>
            @endif
        </ul>
    @endif
</li>



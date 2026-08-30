<div class="daisy-kit-tree__toolbar">
    @if($searchable)
        <label class="daisy-kit-tree__search">
            <span class="text-sm font-medium">{{ $treeLabels['search'] }}</span>
            <span class="daisy-kit-tree__search-controls">
                <input class="input input-sm w-full" data-daisy-kit-tree-search type="search" autocomplete="off" placeholder="{{ $treeLabels['searchPlaceholder'] }}">
            </span>
        </label>
        <button class="btn btn-outline btn-sm" data-tree-command="applySearch" type="button" @if($searchMode !== 'manual') hidden @endif>{{ $treeLabels['applySearch'] }}</button>
        <button class="btn btn-ghost btn-sm" data-tree-command="clearSearch" type="button">{{ $treeLabels['clearSearch'] }}</button>
    @endif
</div>
<div class="daisy-kit-tree__actions">
    <button class="btn btn-ghost btn-sm" data-tree-command="expandAll" type="button">{{ $treeLabels['expandAll'] }}</button>
    <button class="btn btn-ghost btn-sm" data-tree-command="collapseAll" type="button">{{ $treeLabels['collapseAll'] }}</button>
    @if($multiple)
        <button class="btn btn-outline btn-sm" data-tree-command="selectVisible" type="button" @disabled($disabled)>{{ $treeLabels['selectVisible'] }}</button>
    @endif
    <button class="btn btn-ghost btn-sm" data-tree-command="clear" type="button" @disabled($disabled)>{{ $treeLabels['clear'] }}</button>
</div>

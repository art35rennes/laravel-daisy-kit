@props([
    /**
     * items: [
     *   ['label' => 'Inputs', 'children' => [
     *      ['label' => 'Button', 'href' => '/docs/inputs/button'],
     *   ]],
     * ]
     */
    'items' => [],
    'current' => null,
    'searchable' => true,
])

@php
    $isActive = function (?string $href) use ($current): bool {
        if (!$href) return false;
        $cur = '/'.ltrim((string)($current ?? request()->path()), '/');
        return str_starts_with($cur, $href);
    };

    $hasActive = function (array $node) use (&$hasActive, $isActive): bool {
        if (!empty($node['href']) && $isActive($node['href'])) {
            return true;
        }
        foreach (($node['children'] ?? []) as $child) {
            if ($hasActive($child)) return true;
        }
        return false;
    };

    $renderItems = function (array $nodes, int $level = 0) use (&$renderItems, $isActive, $hasActive) {
        echo '<ul class="menu '.($level === 0 ? 'menu-sm' : 'menu-xs').'">';
        foreach ($nodes as $node) {
            $label = (string)($node['label'] ?? '');
            $href = $node['href'] ?? null;
            $children = $node['children'] ?? [];
            $active = $isActive($href);
            echo '<li>';
            if (!empty($children)) {
                $open = $hasActive($node) ? ' open' : '';
                echo "<details$open>";
                echo '<summary class="opacity-70">'.e($label).'</summary>';
                $renderItems($children, $level + 1);
                echo '</details>';
            } else {
                // Feuille sans enfants
                if ($href) {
                    echo '<a class="'.($active ? 'menu-active font-semibold' : '').'" href="'.e($href).'">'.e($label).'</a>';
                } else {
                    echo '<span class="opacity-70">'.e($label).'</span>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
    };
@endphp

<aside data-module="sidebar" data-searchable="{{ $searchable ? 'true' : 'false' }}" class="w-full">
    @if($searchable)
        <div class="px-2 py-2 border-b border-base-content/10 mb-2">
            <label class="input input-sm">
                <x-daisy::ui.advanced.icon name="search" size="sm" class="opacity-50" />
                <input type="search" 
                       class="grow" 
                       placeholder="Rechercher..."
                       data-sidebar-search
                       aria-label="Rechercher dans le menu">
            </label>
        </div>
    @endif
    <nav aria-label="Navigation de la documentation">
        <div data-sidebar-menu>
            {!! $renderItems($items, 0) !!}
        </div>
    </nav>
</aside>



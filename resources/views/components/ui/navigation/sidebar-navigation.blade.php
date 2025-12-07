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
    'hideMarker' => true,
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
        $html = '<ul class="menu '.($level === 0 ? 'menu-sm' : 'menu-xs').'">';
        foreach ($nodes as $node) {
            $label = (string)($node['label'] ?? '');
            $href = $node['href'] ?? null;
            $children = $node['children'] ?? [];
            $active = $isActive($href);
            $html .= '<li>';
            if (!empty($children)) {
                $open = $hasActive($node) ? ' open' : '';
                $isOpen = !empty($open);
                $html .= "<details$open>";
                $html .= '<summary class="opacity-70 w-full flex justify-between items-center list-none [&::-webkit-details-marker]:hidden [&::marker]:hidden [&::-webkit-details-marker]:hidden">';
                $html .= '<span>'.e($label).'</span>';
                $rotateClass = $isOpen ? 'rotate-180' : '';
                $html .= '<span class="shrink-0 transition-transform '.$rotateClass.'">';
                $html .= view('daisy::components.ui.advanced.icon', ['name' => 'chevron-down', 'size' => 'xs'])->render();
                $html .= '</span>';
                $html .= '</summary>';
                $html .= $renderItems($children, $level + 1);
                $html .= '</details>';
            } else {
                // Feuille sans enfants
                if ($href) {
                    $html .= '<a class="'.($active ? 'menu-active font-semibold' : '').'" href="'.e($href).'">'.e($label).'</a>';
                } else {
                    $html .= '<span class="opacity-70">'.e($label).'</span>';
                }
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
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
    <style>
        [data-sidebar-menu] details[open] > summary > span:last-child {
            transform: rotate(180deg);
        }
    </style>
    @if($hideMarker)
        <style>
            [data-sidebar-menu] details > summary {
                list-style: none !important;
                padding-left: 0 !important;
            }
            [data-sidebar-menu] details > summary::-webkit-details-marker,
            [data-sidebar-menu] details > summary::marker,
            [data-sidebar-menu] details > summary::before {
                display: none !important;
                content: '' !important;
                width: 0 !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        </style>
    @endif
</aside>

